(() => {
  'use strict';
  const dialog = document.getElementById('uat-verification');
  const opener = document.querySelector('[data-uat-open]');
  if (!dialog || !opener) return;
  const form = document.getElementById('uat-verification-form');
  const submit = document.getElementById('uat-submit');
  const notes = document.getElementById('uat-notes');
  const canvas = document.getElementById('uat-signature');
  const data = document.getElementById('uat-signature-data');
  const status = document.getElementById('uat-signature-status');
  const error = document.getElementById('uat-client-error');
  const receiver = document.getElementById('uat-receiver-name');
  const receiverStatus = document.getElementById('uat-receiver-status');
  const ctx = canvas.getContext('2d');
  let drawing = false, hasInk = false, previous = null, submitting = false;
  function updateNotes() {
    notes.required = form.elements.status_pengujian_uat.value === 'Diterima dengan catatan';
    document.getElementById('uat-notes-required').textContent = notes.required ? '*' : '(opsional)';
  }
  form.querySelectorAll('[name="status_pengujian_uat"]').forEach(radio => radio.addEventListener('change', updateNotes));
  updateNotes();
  function open() {
    if (!dialog.open) dialog.showModal();
    const serverError = dialog.querySelector('[data-uat-server-error]');
    (serverError || form.querySelector('input[type="radio"]')).focus();
  }
  function close() { if (!submitting) { dialog.close(); opener.focus(); } }
  opener.addEventListener('click', open);
  dialog.querySelectorAll('[data-uat-close]').forEach(button => button.addEventListener('click', close));
  dialog.addEventListener('cancel', event => { if (submitting) event.preventDefault(); });
  let receiverReady = false;
  if (window.jQuery && jQuery.fn.select2) {
    jQuery(receiver).select2({
      width: '100%', dropdownParent: jQuery(receiver).closest('.portal-receiver-field'), allowClear: true, tags: false,
      placeholder: 'Ketik minimal 3 huruf nama pegawai', minimumInputLength: 3,
      language: {
        inputTooShort: () => 'Ketik minimal 3 huruf nama pegawai.', searching: () => 'Mencari pegawai...',
        noResults: () => 'Pegawai tidak ditemukan.', errorLoading: () => 'Pencarian gagal. Silakan coba lagi.', loadingMore: () => 'Memuat nama lainnya...'
      },
      ajax: {
        url: receiver.dataset.searchUrl, dataType: 'json', delay: 350,
        headers: {'X-CSRF-Token': form.elements.csrf.value},
        data: params => ({q: params.term || '', page: params.page || 1, token: receiver.dataset.uatToken}),
        transport(params, success, failure) {
          if ((params.data.q.match(/\p{L}/gu) || []).length < 3) {
            success({results: [], pagination: {more: false}}); return {abort() {}};
          }
          receiverStatus.textContent = '';
          const request = jQuery.ajax(params);
          request.then(success);
          request.fail(xhr => {
            if (xhr.statusText === 'abort') return;
            receiverStatus.textContent = xhr.responseJSON?.message || 'Pencarian pegawai gagal. Silakan coba lagi.';
            failure(xhr);
          });
          return request;
        }
      }
    });
    receiverReady = true;
  } else {
    receiverStatus.textContent = 'Dropdown pegawai gagal dimuat. Muat ulang halaman sebelum menyimpan.';
    submit.disabled = true;
  }
  function clear() {
    ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.strokeStyle = '#172033'; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
    drawing = false; hasInk = false; previous = null; data.value = '';
    status.textContent = 'Tanda tangan belum diisi.';
  }
  clear();
  function point(event) {
    const bounds = canvas.getBoundingClientRect();
    return {x: (event.clientX - bounds.left) * canvas.width / bounds.width,
      y: (event.clientY - bounds.top) * canvas.height / bounds.height};
  }
  canvas.addEventListener('pointerdown', event => {
    if (!event.isPrimary || event.button !== 0) return;
    event.preventDefault(); drawing = true; previous = point(event);
    ctx.lineWidth = Math.max(3, 2.5 * canvas.width / canvas.getBoundingClientRect().width);
    canvas.setPointerCapture(event.pointerId);
  });
  canvas.addEventListener('pointermove', event => {
    if (!drawing) return;
    event.preventDefault();
    const next = point(event);
    ctx.beginPath(); ctx.moveTo(previous.x, previous.y); ctx.lineTo(next.x, next.y); ctx.stroke();
    if (Math.hypot(next.x - previous.x, next.y - previous.y) > 1) hasInk = true;
    previous = next;
    if (hasInk) status.textContent = 'Tanda tangan sudah diisi.';
  });
  for (const type of ['pointerup', 'pointercancel', 'lostpointercapture']) canvas.addEventListener(type, () => { drawing = false; });
  document.getElementById('uat-clear-signature').addEventListener('click', clear);
  form.noValidate = true;
  form.addEventListener('submit', event => {
    if (submitting) { event.preventDefault(); return; }
    updateNotes();
    if (!receiverReady || !receiver.value) {
      event.preventDefault(); error.hidden = false; error.textContent = 'Pilih Nama Penerima dari daftar pegawai.';
      if (receiverReady) jQuery(receiver).select2('open'); else error.focus();
      return;
    }
    if (!form.reportValidity()) { event.preventDefault(); return; }
    if (!hasInk) {
      event.preventDefault(); error.hidden = false;
      error.textContent = 'Bubuhkan tanda tangan penerima sebelum menyimpan verifikasi.';
      error.focus(); canvas.focus(); return;
    }
    error.hidden = true; data.value = canvas.toDataURL('image/png');
    submitting = true; submit.disabled = true; submit.textContent = 'Menyimpan...';
  });
  window.addEventListener('pageshow', () => { submitting = false; submit.disabled = !receiverReady; submit.textContent = 'Simpan Verifikasi'; });
  if (dialog.dataset.openOnError === 'true') open();
})();
