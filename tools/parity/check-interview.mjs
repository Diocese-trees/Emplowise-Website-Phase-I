/**
 * Parity check for the browser interview engine.
 *
 * Loads public/js/toolkit/interview.js in a bare node:vm realm with a fake
 * `window`, replays every interview and questions input committed in
 * tools/parity/fixtures.json, and deep-compares against the outputs the PHP
 * service produced with no API key. Key order is part of the comparison.
 *
 *   node tools/parity/check-interview.mjs
 */

import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';

const here = dirname(fileURLToPath(import.meta.url));
const repoRoot = resolve(here, '..', '..');
const browserFile = resolve(repoRoot, 'public/js/toolkit/interview.js');
const fixtureFile = resolve(repoRoot, 'tools/parity/fixtures.json');

const fixtures = JSON.parse(readFileSync(fixtureFile, 'utf8'));

// The browser file must run in a realm with nothing but a window object:
// no DOM, no fetch, no module system, no Node built-ins.
const sandbox = { window: {} };
vm.createContext(sandbox);
vm.runInContext(readFileSync(browserFile, 'utf8'), sandbox, { filename: browserFile });

const interview = sandbox.window.EmplowiseToolkit && sandbox.window.EmplowiseToolkit.interview;

if (!interview || typeof interview.questions !== 'function' || typeof interview.feedback !== 'function') {
    console.error('FAIL window.EmplowiseToolkit.interview.questions/feedback is not exposed');
    process.exit(1);
}

function typeName(value) {
    if (value === null) return 'null';
    if (Array.isArray(value)) return 'array';
    return typeof value;
}

function describe(value) {
    const text = JSON.stringify(value);
    if (text === undefined) return String(value);
    return text.length > 220 ? text.slice(0, 220) + '...(' + text.length + ' chars)' : text;
}

/**
 * First structural difference between expected (PHP) and actual (JS), or null.
 * Object key order is compared as well as key sets and values.
 */
function firstDiff(expected, actual, path) {
    if (typeName(expected) !== typeName(actual)) {
        return { path, expected: describe(expected), actual: describe(actual), note: 'type ' + typeName(expected) + ' vs ' + typeName(actual) };
    }

    if (Array.isArray(expected)) {
        if (expected.length !== actual.length) {
            return { path, expected: 'length ' + expected.length, actual: 'length ' + actual.length, note: 'array length' };
        }

        for (let i = 0; i < expected.length; i++) {
            const diff = firstDiff(expected[i], actual[i], path + '[' + i + ']');
            if (diff) return diff;
        }

        return null;
    }

    if (typeName(expected) === 'object') {
        const expectedKeys = Object.keys(expected);
        const actualKeys = Object.keys(actual);

        if (expectedKeys.join('\u0000') !== actualKeys.join('\u0000')) {
            return { path, expected: '[' + expectedKeys.join(', ') + ']', actual: '[' + actualKeys.join(', ') + ']', note: 'key order or key set' };
        }

        for (const key of expectedKeys) {
            const diff = firstDiff(expected[key], actual[key], path + '.' + key);
            if (diff) return diff;
        }

        return null;
    }

    if (expected !== actual) {
        return { path, expected: describe(expected), actual: describe(actual), note: typeof expected };
    }

    return null;
}

const cases = [];

Object.keys(fixtures.inputs.interview).forEach((name) => {
    const input = fixtures.inputs.interview[name];
    cases.push({
        label: 'interview.' + name,
        expected: fixtures.interview[name],
        run: () => interview.feedback(input.role, input.level, input.question, input.answer)
    });
});

fixtures.inputs.questions.forEach((input, index) => {
    cases.push({
        label: 'questions[' + index + '] ' + input.role + ' / ' + input.level,
        expected: fixtures.questions[index],
        run: () => interview.questions(input.role, input.level)
    });
});

let passed = 0;
let failed = 0;

for (const testCase of cases) {
    let actual;

    try {
        actual = testCase.run();
    } catch (error) {
        failed++;
        console.log('FAIL ' + testCase.label);
        console.log('     threw: ' + error.message);
        continue;
    }

    const diff = firstDiff(testCase.expected, actual, '$');

    if (diff === null) {
        passed++;
        console.log('PASS ' + testCase.label);
    } else {
        failed++;
        console.log('FAIL ' + testCase.label);
        console.log('     at        : ' + diff.path + ' (' + diff.note + ')');
        console.log('     expected  : ' + diff.expected);
        console.log('     actual    : ' + diff.actual);
    }
}

console.log('');
console.log(cases.length + ' fixtures checked against ' + browserFile.replace(repoRoot + '/', '') + ': ' + passed + ' passed, ' + failed + ' mismatched');

if (failed > 0) {
    process.exit(1);
}

console.log('ALL PASS');
