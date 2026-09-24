// Run the actual submit handler with a named control shadowing form.action.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const endpoint = '/it-rspi2/staff/unit/monev/action.php';
const destination = '/it-rspi2/staff/dashboard_staff.php?unit=monev&id=123';
const handlers = {};
const message = {hidden: true, focus() {}};
const form = {
    action: {name: 'action', value: 'create'},
    dataset: {},
    getAttribute(name) { return name === 'action' ? endpoint : null; },
    addEventListener(name, handler) { handlers[name] = handler; }
};
const selectors = [0,1].map(() => ({dataset: {employeeSearch: '/it-rspi2/staff/unit/monev/pegawai.php'}, closest() { return form; }}));
const searchConfigs = [];
const root = {querySelectorAll(selector) { return selector === '.mn-form' ? [form] : selector === '[data-employee-search]' ? selectors : []; }};
let requested, redirected;
vm.runInNewContext(fs.readFileSync(require.resolve('../staff/unit/monev/monev.js'), 'utf8'), {
    document: {
        querySelector() { return root; },
        getElementById(id) { return id === 'mn-message' ? message : null; }
    },
    window: {
        addEventListener() {}, location: {assign(url) { redirected = url; }},
        jQuery() { return {select2(config) { searchConfigs.push(config); return {on() {}}; }}; }
    },
    FormData: class {
        constructor(source) { assert.equal(source, form); }
        entries() { return [['action', 'create']][Symbol.iterator](); }
    },
    File: class {},
    async fetch(url, options) {
        requested = url;
        assert.equal(url, endpoint, 'POST must use URL attribute, not named input');
        assert.equal(options.method, 'POST');
        assert.equal(options.credentials, 'same-origin');
        return {ok: true, redirected: false, async json() { return {ok: true, redirect: destination}; }};
    }
});
(async () => {
    // Select2 creates a SPAN.select2. Dashboard ready handlers must never initialize that span again.
    for (const dashboard of ['staff/dashboard_staff.php', 'admin/dashboard_admin.php']) {
        const source = fs.readFileSync(require.resolve('../' + dashboard), 'utf8');
        const readyScript = [...source.matchAll(/<script>\s*([\s\S]*?)<\/script>/g)]
            .map(match => match[1]).find(script => script.includes("$('.select2')") || script.includes("$('select.select2')"));
        assert.ok(readyScript, 'dashboard Select2 initializer found');
        const generatedSpan = {tag: 'SPAN', classes: ['select2']};
        const plainSelect = {tag: 'SELECT', classes: ['select2bs4']};
        const initialized = [];
        vm.runInNewContext(readyScript, {$: selector => {
            if (typeof selector === 'function') return selector();
            const [tag, className] = selector.split('.');
            const matches = [generatedSpan, plainSelect].filter(el => (!tag || el.tag === tag.toUpperCase()) && el.classes.includes(className));
            return {select2() { initialized.push(...matches); }};
        }});
        assert.ok(!initialized.includes(generatedSpan), dashboard + ' must not reinitialize generated Select2 container');
        assert.ok(initialized.includes(plainSelect), 'normal dashboard selects still initialized');
    }
    assert.equal(searchConfigs.length, 2);
    for (const config of searchConfigs) {
        assert.equal(config.minimumInputLength, 3);
        assert.equal(config.ajax.delay, 300);
        assert.equal(config.ajax.url, '/it-rspi2/staff/unit/monev/pegawai.php');
        const query = config.ajax.data({term: 'ALI', page: 2});
        assert.equal(query.q, 'ALI'); assert.equal(query.page, 2);
    }
    await handlers.submit({preventDefault() {}});
    assert.equal(requested, endpoint);
    assert.equal(redirected, destination, 'successful create must navigate to checklist');
    assert.equal(message.hidden, true);
    console.log('MONEV_FRONTEND_OK (AJAX selectors, submit URL and checklist redirect)');
})().catch(error => { console.error(error); process.exitCode = 1; });
