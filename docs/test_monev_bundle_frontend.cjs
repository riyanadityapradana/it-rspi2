const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const elements = {};
function element(id, extra = {}) {
    return elements[id] = {handlers: {}, hidden: false, disabled: false,
        addEventListener(event, fn) { this.handlers[event] = fn; }, ...extra};
}
const oldValue = 'Catatan bundle yang belum disimpan';
const checklist = element('mn-checklist-form', {dataset: {inspection: '12', version: '3'}, value: oldValue});
const selects = [{disabled: false}, {disabled: false}];
const newBundle = element('mn-new-bundle', {hidden: true, disabled: true,
    querySelectorAll() { return selects; }, scrollIntoView() {}});
for (const id of ['mn-add-bundle','mn-cancel-bundle','mn-local','mn-local-status','mn-restore','mn-clear','mn-message']) element(id);
elements['mn-local'].checked = false;
const windowHandlers = {};
const root = {dataset: {user: '1'}, querySelectorAll() { return []; }};
vm.runInNewContext(fs.readFileSync(require.resolve('../staff/unit/monev/monev.js'), 'utf8'), {
    document: {querySelector() { return root; }, getElementById(id) { return elements[id] || null; }},
    window: {addEventListener(event, fn) { windowHandlers[event] = fn; }, jQuery() { return {trigger() {}}; }}
});
assert.ok(newBundle.disabled && newBundle.hidden && selects.every(select => select.disabled));
elements['mn-add-bundle'].handlers.click();
assert.ok(!newBundle.disabled && !newBundle.hidden && selects.every(select => !select.disabled));
assert.equal(checklist.value, oldValue, 'opening new bundle must preserve existing form values');
assert.ok(elements['mn-add-bundle'].hidden);
let prevented = false;
windowHandlers.beforeunload({preventDefault() { prevented = true; }});
assert.ok(prevented, 'unsaved bundle prompts before leaving');
elements['mn-cancel-bundle'].handlers.click();
assert.ok(newBundle.disabled && newBundle.hidden && selects.every(select => select.disabled));
assert.equal(checklist.value, oldValue, 'cancelling new bundle preserves existing values');
assert.ok(!elements['mn-add-bundle'].hidden);
const outer = {tagName: 'DETAILS', open: false, parentElement: checklist};
checklist.handlers.invalid({target: {parentElement: outer}});
assert.ok(outer.open, 'invalid controls inside collapsed bundles become visible');
console.log('MONEV_BUNDLE_FRONTEND_OK (open/cancel, preserved input, unsaved warning, validation)');
