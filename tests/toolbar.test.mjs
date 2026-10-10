import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import { runInNewContext } from 'node:vm';

const script = readFileSync(new URL('../resources/js/toolbar.js', import.meta.url), 'utf8');

function toolbarWithStorage(storage) {
    const attributes = new Map([
        ['aria-expanded', 'false'],
        ['aria-label', 'Expand toolbar'],
    ]);
    const menu = { hidden: true };
    let click;
    const toggle = {
        setAttribute: (name, value) => attributes.set(name, value),
        getAttribute: (name) => attributes.get(name),
        addEventListener: (name, handler) => {
            assert.equal(name, 'click');
            click = handler;
        },
    };

    runInNewContext(script, {
        localStorage: storage,
        document: {
            currentScript: {
                previousElementSibling: {
                    querySelector: (selector) => selector === '.sst-toggle' ? toggle : menu,
                },
            },
        },
    });

    return { attributes, menu, click: () => click() };
}

test('starts closed and toggles visibility, accessible state, and saved preference', () => {
    const saved = new Map();
    const toolbar = toolbarWithStorage({
        getItem: (key) => saved.get(key),
        setItem: (key, value) => saved.set(key, value),
    });

    assert.equal(toolbar.menu.hidden, true);
    assert.equal(toolbar.attributes.get('aria-expanded'), 'false');
    assert.equal(toolbar.attributes.get('aria-label'), 'Expand toolbar');

    toolbar.click();
    assert.equal(toolbar.menu.hidden, false);
    assert.equal(toolbar.attributes.get('aria-expanded'), 'true');
    assert.equal(toolbar.attributes.get('aria-label'), 'Collapse toolbar');
    assert.equal(saved.get('statamic-toolbar-open'), 'true');

    toolbar.click();
    assert.equal(toolbar.menu.hidden, true);
    assert.equal(toolbar.attributes.get('aria-expanded'), 'false');
    assert.equal(toolbar.attributes.get('aria-label'), 'Expand toolbar');
    assert.equal(saved.get('statamic-toolbar-open'), 'false');
});

test('restores a saved open preference', () => {
    const toolbar = toolbarWithStorage({ getItem: () => 'true' });

    assert.equal(toolbar.menu.hidden, false);
    assert.equal(toolbar.attributes.get('aria-expanded'), 'true');
    assert.equal(toolbar.attributes.get('aria-label'), 'Collapse toolbar');
});

test('still opens and closes when storage is unavailable', () => {
    const unavailable = () => { throw new Error('Storage unavailable'); };
    const toolbar = toolbarWithStorage({ getItem: unavailable, setItem: unavailable });

    assert.equal(toolbar.menu.hidden, true);
    toolbar.click();
    assert.equal(toolbar.menu.hidden, false);
    assert.equal(toolbar.attributes.get('aria-expanded'), 'true');
    toolbar.click();
    assert.equal(toolbar.menu.hidden, true);
    assert.equal(toolbar.attributes.get('aria-expanded'), 'false');
});
