(() => {
  const form = document.querySelector('form[data-ticket-form]');
  if (!form) return;
  const employeeStatus = document.getElementById('pegawai-status');
  const save = document.getElementById('save-request');
  if (!window.jQuery || !jQuery.fn.select2) {
    employeeStatus.textContent = 'Pencarian pegawai gagal dimuat. Muat ulang halaman untuk melanjutkan.';
    save.disabled = true;
    return;
  }
  jQuery('#pegawai-id').select2({
    width: '100%', placeholder: 'Ketik minimal 3 huruf nama pegawai', minimumInputLength: 3,
    language: {
      inputTooShort: () => 'Ketik minimal 3 huruf nama pegawai.',
      searching: () => 'Mencari pegawai...',
      noResults: () => 'Pegawai tidak ditemukan.',
      errorLoading: () => 'Pencarian gagal. Silakan coba lagi.',
      loadingMore: () => 'Memuat nama lainnya...'
    },
    ajax: {
      url: 'pegawai.php', dataType: 'json', delay: 350,
      headers: {'X-CSRF-Token': form.elements.csrf.value},
      data: params => ({q: params.term || '', page: params.page || 1}),
      transport(params, success, failure) {
        if ((params.data.q.match(/\p{L}/gu) || []).length < 3) {
          success({results: [], pagination: {more: false}});
          return {abort() {}};
        }
        employeeStatus.textContent = '';
        const request = jQuery.ajax(params);
        request.then(success);
        request.fail(xhr => {
          if (xhr.statusText === 'abort') return;
          if (xhr.responseJSON?.captcha_required && xhr.responseJSON.captcha_url) {
            window.location.assign(xhr.responseJSON.captcha_url); return;
          }
          employeeStatus.textContent = xhr.responseJSON?.message || 'Pencarian pegawai gagal. Silakan coba lagi.';
          failure(xhr);
        });
        return request;
      }
    }
  });
  const canvas = document.getElementById('signature-canvas');
  const data = document.getElementById('signature-data');
  const status = document.getElementById('signature-status');
  const error = document.getElementById('form-client-error');
  const ctx = canvas.getContext('2d');
  form.noValidate = true;
  let drawing = false, hasInk = false, previous = null;
  function clear() {
    ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.strokeStyle = '#172033'; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
    hasInk = false; drawing = false; previous = null; data.value = '';
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
  document.getElementById('clear-signature').addEventListener('click', clear);
  form.addEventListener('submit', event => {
    if (!form.checkValidity()) {
      event.preventDefault();
      error.hidden = false; error.textContent = 'Lengkapi semua kolom wajib sebelum menyimpan pengajuan.';
      const invalid = form.querySelector(':invalid');
      if (invalid?.id === 'pegawai-id') jQuery('#pegawai-id').select2('open');
      else invalid?.reportValidity();
      return;
    }
    if (!hasInk) {
      event.preventDefault(); error.hidden = false;
      error.textContent = 'Bubuhkan tanda tangan pemohon sebelum menyimpan pengajuan.';
      error.focus(); canvas.focus(); return;
    }
    error.hidden = true; data.value = canvas.toDataURL('image/png');
    save.disabled = true; save.textContent = 'Menyimpan...';
  });
  window.addEventListener('pageshow', () => { save.disabled = !form.querySelector('#id-sistem option[value]:not([value=""])') || !form.querySelector('#unit-id option[value]:not([value=""])'); save.textContent = 'SIMPAN'; });
})();
