// Compares public/js/chat.js intent routing against the PHP oracle corpus.
//
//   php tools/parity/generate-chat.php
//   php artisan emplowise:export-static /tmp/chat-parity --base=http://127.0.0.1:8116
//   php -S 0.0.0.0:8116 -t /tmp/chat-parity &
//   chromium --headless=new --remote-debugging-port=9316 --no-sandbox --user-data-dir=/tmp/chat-parity-profile &
//   node tools/parity/check-chat-routing.mjs
//
// Override the two URLs with argv: <site> <devtools>.

import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const here = dirname(fileURLToPath(import.meta.url));
const SITE = process.argv[2] || 'http://127.0.0.1:8116';
const DEVTOOLS = process.argv[3] || 'http://127.0.0.1:9316';

const cases = JSON.parse(readFileSync(join(here, 'chat-routes.json'), 'utf8'));

const created = await fetch(`${DEVTOOLS}/json/new?url=about:blank`, { method: 'PUT' }).then((r) => r.json());
const ws = new WebSocket(created.webSocketDebuggerUrl);
await new Promise((resolve, reject) => { ws.onopen = resolve; ws.onerror = reject; });

let id = 0;
const pending = new Map();
ws.onmessage = (event) => {
  const message = JSON.parse(event.data);
  if (message.id && pending.has(message.id)) {
    const { resolve, reject } = pending.get(message.id);
    pending.delete(message.id);
    message.error ? reject(new Error(JSON.stringify(message.error))) : resolve(message.result);
  }
};
const send = (method, params = {}) => {
  const mid = ++id;
  ws.send(JSON.stringify({ id: mid, method, params }));
  return new Promise((resolve, reject) => pending.set(mid, { resolve, reject }));
};
const evaluate = async (expression) => {
  const result = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
  if (result.exceptionDetails) throw new Error(result.exceptionDetails.exception?.description || result.exceptionDetails.text);
  return result.result.value;
};

await send('Page.enable');
await send('Runtime.enable');
await send('Page.navigate', { url: `${SITE}/student-hub/` });
await new Promise((r) => setTimeout(r, 1500));

const ready = await evaluate('!!(window.EmplowiseChat && window.EmplowiseChat.intent)');
if (!ready) {
  console.error('FAIL chat.js did not expose window.EmplowiseChat');
  process.exit(1);
}

let failed = 0;
for (const item of cases) {
  const actual = await evaluate(
    `window.EmplowiseChat.intent(${JSON.stringify(item.message)}, ${JSON.stringify(item.previous)})`
  );
  const ok = actual === item.intent;
  if (!ok) {
    failed += 1;
    console.log(`FAIL ${JSON.stringify(item.message)} (prev=${item.previous || 'none'}): expected ${item.intent} but got ${actual}`);
  }
}
console.log(`\n${cases.length - failed}/${cases.length} routing cases match PHP`);
ws.close();
process.exit(failed ? 1 : 0);
