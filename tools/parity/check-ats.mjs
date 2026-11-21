/*
 * Proves the browser ATS engine matches the PHP engine.
 *
 * Loads public/js/toolkit/ats.js into a node:vm sandbox with a fake window,
 * runs every inputs.ats case from the committed fixtures, and deep-compares the
 * result against the PHP output recorded in fixtures.ats. Key order is part of
 * the comparison because the PHP arrays are the specification.
 *
 *   node tools/parity/check-ats.mjs
 */
import { readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', '..');
const enginePath = path.join(root, 'public', 'js', 'toolkit', 'ats.js');
const fixturesPath = path.join(root, 'tools', 'parity', 'fixtures.json');

const sandbox = { window: {} };
vm.createContext(sandbox);
vm.runInContext(readFileSync(enginePath, 'utf8'), sandbox, { filename: enginePath });

const toolkit = sandbox.window.EmplowiseToolkit;

if (!toolkit || !toolkit.ats || typeof toolkit.ats.score !== 'function') {
    console.error('FAIL ats.js did not define window.EmplowiseToolkit.ats.score');
    process.exit(1);
}

const fixtures = JSON.parse(readFileSync(fixturesPath, 'utf8'));
const cases = Object.keys(fixtures.inputs.ats);
const expectedKeys = ['score', 'verdict', 'breakdown', 'title', 'title_match', 'matched', 'missing', 'structure', 'format', 'fixes', 'source'];

let failures = 0;

for (const name of cases) {
    const input = fixtures.inputs.ats[name];
    const expected = fixtures.ats[name];
    const actual = toolkit.ats.score(input.resume, input.job_description);
    const mismatch = describe(diff(expected, actual, 'ats.' + name));

    if (mismatch === null) {
        console.log('PASS ats.' + name);
        continue;
    }

    failures++;
    console.log('FAIL ' + mismatch);
}

if (cases.length === 0) {
    console.error('FAIL no inputs.ats cases found in fixtures.json');
    process.exit(1);
}

if (failures > 0) {
    console.log(failures + ' of ' + cases.length + ' ATS fixtures failed');
    process.exit(1);
}

const top = Object.keys(toolkit.ats.score(fixtures.inputs.ats[cases[0]].resume, fixtures.inputs.ats[cases[0]].job_description));
if (top.join(',') !== expectedKeys.join(',')) {
    console.log('FAIL result key order: got ' + top.join(', ') + ' expected ' + expectedKeys.join(', '));
    process.exit(1);
}

console.log('All ' + cases.length + ' ATS fixtures match PHP output exactly');

// ----------------------------------------------------------------------
// Deep comparison
// ----------------------------------------------------------------------

function diff(expected, actual, where) {
    if (Array.isArray(expected) || Array.isArray(actual)) {
        if (!Array.isArray(expected) || !Array.isArray(actual)) return typeMismatch(expected, actual, where);
        if (expected.length !== actual.length) {
            return { path: where, reason: 'length', expected: expected.length, actual: actual.length };
        }
        for (let i = 0; i < expected.length; i++) {
            const found = diff(expected[i], actual[i], where + '[' + i + ']');
            if (found) return found;
        }
        return null;
    }

    if (isPlainObject(expected) || isPlainObject(actual)) {
        if (!isPlainObject(expected) || !isPlainObject(actual)) return typeMismatch(expected, actual, where);

        const want = Object.keys(expected);
        const have = Object.keys(actual);

        if (want.join('\u0000') !== have.join('\u0000')) {
            return { path: where, reason: 'keys', expected: want, actual: have };
        }

        for (const key of want) {
            const found = diff(expected[key], actual[key], where + '.' + key);
            if (found) return found;
        }

        return null;
    }

    if (typeof expected === 'number' && typeof actual === 'number') {
        return expected === actual ? null : { path: where, reason: 'value', expected, actual };
    }

    if (typeof expected !== typeof actual) return typeMismatch(expected, actual, where);

    return expected === actual ? null : { path: where, reason: 'value', expected, actual };
}

function isPlainObject(value) {
    return value !== null && typeof value === 'object' && !Array.isArray(value);
}

function typeMismatch(expected, actual, where) {
    return {
        path: where,
        reason: 'type',
        expected: (expected === null ? 'null' : Array.isArray(expected) ? 'array' : typeof expected),
        actual: (actual === null ? 'null' : Array.isArray(actual) ? 'array' : typeof actual)
    };
}

function show(value) {
    if (typeof value === 'string') return JSON.stringify(value);
    if (Array.isArray(value)) return '[' + value.map(show).join(', ') + ']';
    if (value && typeof value === 'object') return JSON.stringify(value);
    return String(value);
}

function describe(mismatch) {
    if (mismatch === null) return null;
    if (mismatch.reason === 'keys') {
        return mismatch.path + ' key order: expected ' + show(mismatch.expected) + ' but got ' + show(mismatch.actual);
    }
    return mismatch.path + ' (' + mismatch.reason + '): expected ' + show(mismatch.expected) + ' but got ' + show(mismatch.actual);
}
