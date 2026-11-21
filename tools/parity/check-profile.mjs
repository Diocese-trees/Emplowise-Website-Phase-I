#!/usr/bin/env node
/*
 * Parity check for the browser profile optimiser.
 *
 * Loads public/js/toolkit/profile.js into a bare vm context with a fake window,
 * runs every tools/parity/fixtures.json profile input, and deep-compares the
 * result against the output committed from the PHP engine.
 *
 *   node tools/parity/check-profile.mjs
 */

import { readFileSync } from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import { fileURLToPath } from 'node:url';

const here = path.dirname(fileURLToPath(import.meta.url));
const repo = path.resolve(here, '..', '..');
const scriptPath = path.join(repo, 'public', 'js', 'toolkit', 'profile.js');
const fixturePath = path.join(repo, 'tools', 'parity', 'fixtures.json');

function loadApi() {
    const source = readFileSync(scriptPath, 'utf8');
    const sandbox = { window: {} };
    vm.createContext(sandbox);
    new vm.Script(source, { filename: path.relative(repo, scriptPath) }).runInContext(sandbox, {
        timeout: 5000,
    });

    const profile = sandbox.window.EmplowiseToolkit && sandbox.window.EmplowiseToolkit.profile;

    if (!profile || typeof profile.optimize !== 'function') {
        throw new Error('window.EmplowiseToolkit.profile.optimize is not a function');
    }

    return profile;
}

function describe(value) {
    return JSON.stringify(value) === undefined ? String(value) : JSON.stringify(value);
}

/* Returns the first mismatch as { path, expected, actual }, or null when equal. */
function diff(expected, actual, at) {
    if (Array.isArray(expected) || Array.isArray(actual)) {
        if (!Array.isArray(expected) || !Array.isArray(actual)) {
            return { path: at, expected: describe(expected), actual: describe(actual) };
        }
        if (expected.length !== actual.length) {
            return { path: at + '.length', expected: describe(expected.length), actual: describe(actual.length) };
        }
        for (let i = 0; i < expected.length; i++) {
            const found = diff(expected[i], actual[i], at + '[' + i + ']');
            if (found) return found;
        }
        return null;
    }

    if (expected && actual && typeof expected === 'object' && typeof actual === 'object') {
        const keys = new Set([...Object.keys(expected), ...Object.keys(actual)]);
        for (const key of [...keys].sort()) {
            const found = diff(expected[key], actual[key], at === '' ? key : at + '.' + key);
            if (found) return found;
        }
        return null;
    }

    if (expected !== actual) {
        return { path: at, expected: describe(expected), actual: describe(actual) };
    }

    return null;
}

const fixtures = JSON.parse(readFileSync(fixturePath, 'utf8'));
const inputs = fixtures.inputs.profile;
const expected = fixtures.profile;
const profile = loadApi();

let failures = 0;
let checked = 0;

for (const name of Object.keys(inputs)) {
    const input = inputs[name];
    const actual = profile.optimize(input.headline, input.summary, input.bullets, input.target_role);
    const mismatch = diff(expected[name], actual, name);

    checked++;

    if (mismatch) {
        failures++;
        console.log('FAIL ' + name);
        console.log('  path:     ' + mismatch.path);
        console.log('  expected: ' + mismatch.expected);
        console.log('  actual:   ' + mismatch.actual);
    } else {
        console.log('PASS ' + name);
    }
}

console.log(failures === 0
    ? 'profile fixtures: ' + checked + '/' + checked + ' passed, 0 mismatches'
    : 'profile fixtures: ' + (checked - failures) + '/' + checked + ' passed, ' + failures + ' mismatching');

process.exit(failures === 0 ? 0 : 1);
