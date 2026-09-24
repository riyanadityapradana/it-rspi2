(() => {
    'use strict';
    const root = document.querySelector('.mn');
    if (!root) return;
    const message = document.getElementById('mn-message');
    const dirty = new Set();
    let leaving = false;
    const newBundle = document.getElementById('mn-new-bundle');
    if (newBundle) newBundle.querySelectorAll('select').forEach(select => { select.disabled = true; });
    function serverCriteria(select) {
        const bundle = select.closest('.mn-bundle');
        if (!bundle) return;
        const unitName = select.value ? select.options[select.selectedIndex].text.trim() : '';
        const hide = unitName !== '' && unitName.toUpperCase() !== 'IT';
        bundle.querySelectorAll('[data-server-criterion]').forEach(card => {
            card.hidden = hide;
            card.querySelectorAll('input, textarea, select, button').forEach(input => {
                input.disabled = hide;
                if (!hide) return;
                if (input.type === 'radio') input.checked = input.value === 'TA';
                else if (input.name.endsWith('[lokasi]')) input.value = unitName;
                else if (input.name.endsWith('[catatan]')) input.value = 'Ruang server tidak tersedia';
            });
        });
    }
    root.querySelectorAll('[data-unit-search]').forEach(select => {
        serverCriteria(select);
        window.jQuery(select).select2({theme: 'bootstrap4', width: '100%', placeholder: 'Cari unit/area'}).on('change', () => {
            const form = select.closest('form');
            dirty.add(form);
            const bundle = select.closest('.mn-bundle');
            if (!bundle || !select.value || select.disabled) return;
            const unitName = select.options[select.selectedIndex].text.trim();
            bundle.querySelectorAll('input[name$="[lokasi]"]').forEach(input => {
                input.value = unitName;
            });
            serverCriteria(select);
            // Use the existing unsaved-change indicator and optional local backup.
            form.dispatchEvent(new Event('input', {bubbles: true}));
        });
    });
    root.querySelectorAll('[data-employee-search]').forEach(select => {
        window.jQuery(select).select2({
            theme: 'bootstrap4', width: '100%', minimumInputLength: 3,
            placeholder: 'Cari NIK atau nama pegawai', allowClear: true,
            language: {
                inputTooShort: () => 'Ketik minimal 3 karakter.',
                searching: () => 'Mencari pegawai...',
                noResults: () => 'Pegawai aktif tidak ditemukan.',
                errorLoading: () => 'Data pegawai gagal dimuat. Coba kembali.',
                loadingMore: () => 'Memuat pegawai berikutnya...'
            },
            ajax: {
                url: select.dataset.employeeSearch, dataType: 'json', delay: 300,
                data: params => ({q: params.term, page: params.page || 1}),
                processResults: data => data
            }
        }).on('change', () => { dirty.add(select.closest('form')); });
    });
    function error(text) { message.textContent = text; message.hidden = false; message.focus(); }
    const checklist = document.getElementById('mn-checklist-form');
    const localToggle = document.getElementById('mn-local');
    const localStatus = document.getElementById('mn-local-status');
    const key = checklist ? `it-rspi2:monev:${root.dataset.user}:${checklist.dataset.inspection}` : '';
    function localSave() {
        if (!checklist || !localToggle.checked) return;
        const data = {};
        for (const [name, value] of new FormData(checklist)) {
            if (typeof value === 'string' && !name.startsWith('signature_') && !['csrf', 'action', 'id', 'version'].includes(name)) data[name] = value;
        }
        try { localStorage.setItem(key, JSON.stringify({version: checklist.dataset.version, data})); localStatus.textContent = 'Cadangan teks tersimpan di perangkat.'; }
        catch (_) { localStatus.textContent = 'Cadangan gagal disimpan; penyimpanan perangkat tidak tersedia.'; }
    }
    function hints() {
        root.querySelectorAll('.mn-results').forEach(el => {
            const selected = el.querySelector('input:checked');
            el.nextElementSibling.hidden = !selected || selected.value !== 'TS';
        });
        root.querySelectorAll('.mn-bundle').forEach(bundle => {
            const counts = {S: 0, TS: 0, TA: 0, BV: 0};
            bundle.querySelectorAll('.mn-results input:checked').forEach(input => counts[input.value]++);
            bundle.querySelectorAll('[data-count]').forEach(label => { label.textContent = `${label.dataset.count}: ${counts[label.dataset.count]}`; });
        });
    }
    function initSignaturePad(box) {
        const canvas = box.querySelector('canvas');
        const input = box.querySelector('input[type="hidden"]');
        const clear = box.querySelector('[data-signature-clear]');
        const ctx = canvas.getContext('2d');
        let drawing = false, written = false, last = null;
        function resize(reset = false) {
            if (!box.open) return;
            const rect = canvas.getBoundingClientRect();
            if (rect.width < 20 || rect.height < 20) return;
            const ratio = Math.min(window.devicePixelRatio || 1, 2);
            const previous = document.createElement('canvas');
            previous.width = canvas.width; previous.height = canvas.height;
            if (written && !reset) previous.getContext('2d').drawImage(canvas, 0, 0);
            canvas.width = Math.max(1, Math.round(rect.width * ratio));
            canvas.height = Math.max(1, Math.round(rect.height * ratio));
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
            ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.lineWidth = 2.2; ctx.strokeStyle = '#111827';
            ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, rect.width, rect.height);
            if (written && !reset) ctx.drawImage(previous, 0, 0, rect.width, rect.height);
            if (reset) { written = false; input.value = ''; }
            drawing = false; last = null;
        }
        function point(event) {
            const rect = canvas.getBoundingClientRect();
            return {x: event.clientX - rect.left, y: event.clientY - rect.top};
        }
        canvas.addEventListener('pointerdown', event => {
            event.preventDefault(); canvas.setPointerCapture(event.pointerId);
            drawing = true; last = point(event);
        });
        canvas.addEventListener('pointermove', event => {
            if (!drawing) return;
            event.preventDefault();
            const next = point(event);
            written = true;
            dirty.add(box.closest('form'));
            ctx.beginPath(); ctx.moveTo(last.x, last.y); ctx.lineTo(next.x, next.y); ctx.stroke();
            last = next;
        });
        ['pointerup', 'pointercancel', 'pointerleave'].forEach(type => canvas.addEventListener(type, () => { drawing = false; last = null; }));
        clear.addEventListener('click', () => { resize(true); });
        box.addEventListener('toggle', () => { if (box.open) resize(); });
        window.addEventListener('resize', () => { if (box.open) resize(); });
        if (box.open) resize();
        if (window.ResizeObserver) new ResizeObserver(() => { if (box.open) resize(); }).observe(canvas);
        return () => { input.value = written ? canvas.toDataURL('image/png') : ''; };
    }
    const prepareSignatures = new Map();
    root.querySelectorAll('[data-signature-pad]').forEach(box => {
        const form = box.closest('form');
        if (!form) return;
        if (!prepareSignatures.has(form)) prepareSignatures.set(form, []);
        prepareSignatures.get(form).push(initSignaturePad(box));
    });
    async function compress(file) {
        if (!file.size) return file;
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) throw new Error('Pilih foto JPG, PNG, atau WebP.');
        if (file.size > 25 * 1024 * 1024) throw new Error('Foto sumber terlalu besar (maksimal 25 MB).');
        const url = URL.createObjectURL(file);
        try {
            const img = new Image(); img.src = url; await img.decode();
            if (img.width * img.height > 40000000) throw new Error('Foto melebihi 40 megapiksel.');
            const scale = Math.min(1, 1600 / Math.max(img.width, img.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.max(1, Math.round(img.width * scale)); canvas.height = Math.max(1, Math.round(img.height * scale));
            const ctx = canvas.getContext('2d'); ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height); ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', .8));
            if (!blob || blob.size > 5 * 1024 * 1024) throw new Error('Foto gagal dikompres hingga di bawah 5 MB.');
            return new File([blob], 'bukti.jpg', {type: 'image/jpeg'});
        } finally { URL.revokeObjectURL(url); }
    }
    root.querySelectorAll('.mn-form').forEach(form => {
        form.addEventListener('input', () => {
            dirty.add(form); hints();
            const indicator = form.querySelector('.mn-dirty'); if (indicator) indicator.hidden = false;
            if (form === checklist) localSave();
        });
        form.addEventListener('submit', async event => {
            event.preventDefault();
            if ([...dirty].some(other => other !== form) && !window.confirm('Ada perubahan pada formulir lain yang belum disimpan. Lanjutkan menyimpan formulir ini dan abaikan perubahan pada formulir lain?')) return;
            if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) return;
            const buttons = [...root.querySelectorAll('button')]; buttons.forEach(b => b.disabled = true);
            message.hidden = true;
            try {
                (prepareSignatures.get(form) || []).forEach(prepare => prepare());
                const data = new FormData(form);
                for (const [name, value] of [...data.entries()]) {
                    if (value instanceof File) {
                        if (!value.size) data.delete(name);
                        else data.set(name, await compress(value));
                    }
                }
                // The hidden input named "action" shadows the form.action URL property.
                const response = await fetch(form.getAttribute('action'), {method: 'POST', body: data, credentials: 'same-origin', headers: {'Accept': 'application/json'}});
                if (response.redirected) throw new Error('Sesi login berakhir. Masuk kembali pada tab lain, lalu coba simpan lagi.');
                let result; try { result = await response.json(); } catch (_) { throw new Error('Respons server tidak valid. Periksa batas unggahan atau sesi login.'); }
                if (!response.ok || !result.ok) throw new Error(result.message || 'Penyimpanan gagal.');
                if (form === checklist) { try { localStorage.removeItem(key); } catch (_) {} }
                dirty.delete(form); leaving = true; window.location.assign(result.redirect);
            } catch (e) { error(e.message || 'Koneksi terputus. Isian tetap ada di halaman; coba simpan kembali saat online.'); localSave(); }
            finally { buttons.forEach(b => b.disabled = false); }
        });
    });
    window.addEventListener('beforeunload', event => { if (dirty.size && !leaving) { event.preventDefault(); event.returnValue = ''; } });
    if (checklist) {
        checklist.addEventListener('invalid', event => {
            let parent = event.target.parentElement;
            while (parent && parent !== checklist) { if (parent.tagName === 'DETAILS') parent.open = true; parent = parent.parentElement; }
        }, true);
        function toggleNewBundle(show) {
            if (!newBundle) return;
            newBundle.hidden = !show; newBundle.disabled = !show;
            newBundle.querySelectorAll('select').forEach(select => { select.disabled = !show; window.jQuery(select).trigger('change.select2'); });
            document.getElementById('mn-add-bundle').hidden = show;
            dirty.add(checklist);
            if (show) newBundle.scrollIntoView({behavior: 'smooth', block: 'start'});
        }
        document.getElementById('mn-add-bundle')?.addEventListener('click', () => toggleNewBundle(true));
        document.getElementById('mn-cancel-bundle')?.addEventListener('click', () => toggleNewBundle(false));
        localToggle.addEventListener('change', () => { if (localToggle.checked) localSave(); });
        document.getElementById('mn-clear').addEventListener('click', () => {
            try { localStorage.removeItem(key); localToggle.checked = false; localStatus.textContent = 'Cadangan dihapus.'; } catch (_) { error('Penyimpanan perangkat tidak tersedia.'); }
        });
        document.getElementById('mn-restore').addEventListener('click', () => {
            try {
                const saved = JSON.parse(localStorage.getItem(key) || 'null');
                if (!saved) { localStatus.textContent = 'Tidak ada cadangan.'; return; }
                if (saved.version !== checklist.dataset.version) { error('Cadangan berasal dari versi lama. Salin ulang isian yang diperlukan setelah memeriksa data server.'); return; }
                if (saved.data.add_bundle === '1') toggleNewBundle(true);
                for (const el of checklist.elements) {
                    if (!(el.name in saved.data) || ['hidden', 'file', 'submit', 'button'].includes(el.type)) continue;
                    if (el.type === 'radio') el.checked = saved.data[el.name] === el.value; else el.value = saved.data[el.name];
                }
                dirty.add(checklist); hints(); localToggle.checked = true;
                localStatus.textContent = 'Cadangan dipulihkan. Pilih ulang foto jika perlu lalu simpan ke server.';
            } catch (_) { error('Cadangan tidak dapat dibaca.'); }
        });
        window.addEventListener('offline', () => { localStatus.textContent = 'Koneksi terputus. Halaman ini tetap dapat diisi; simpan saat online.'; localSave(); });
        window.addEventListener('online', () => { localStatus.textContent = 'Koneksi tersedia. Tekan Simpan untuk mengirim isian ke server.'; });
    }
    hints();
})();
