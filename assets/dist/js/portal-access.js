(() => {
  'use strict';
  const form = document.getElementById('portal-access-form');
  if (!form) return;
  const submit = document.getElementById('portal-access-submit');
  const feedback = document.getElementById('portal-access-feedback');
  const refresh = document.getElementById('portal-refresh-captcha');
  const available = form.dataset.available === '1';
  const until = Date.now() + Number(form.dataset.wait) * 1000;
  let submitting = false;
  function update() {
    const seconds = Math.max(0, Math.ceil((until - Date.now()) / 1000));
    submit.disabled = !available || submitting || seconds > 0;
    feedback.textContent = seconds > 0 ? `Coba lagi dalam ${seconds} detik.` : '';
    if (seconds === 0) clearInterval(timer);
  }
  const timer = setInterval(update, 250); update();
  form.addEventListener('submit', event => {
    if (submit.disabled) { event.preventDefault(); return; }
    submitting = true; submit.disabled = true;
  });
  refresh?.addEventListener('click', async () => {
    refresh.disabled = true;
    try {
      const response = await fetch(form.action, {cache: 'no-store', credentials: 'same-origin'});
      if (!response.ok || response.redirected) { window.location.reload(); return; }
      const page = new DOMParser().parseFromString(await response.text(), 'text/html');
      const id = page.querySelector('[name="captcha_id"]');
      const image = page.querySelector('.captcha-image');
      if (!id || !image) throw new Error('CAPTCHA unavailable');
      form.elements.captcha_id.value = id.value;
      form.querySelector('.captcha-image').src = image.getAttribute('src');
      form.elements.csrf_token.value = page.querySelector('[name="csrf_token"]').value;
      form.elements.captcha.value = ''; form.elements.captcha.focus();
    } catch (_) { feedback.textContent = 'Kode belum dapat diperbarui. Silakan coba lagi atau muat ulang halaman.'; }
    finally { refresh.disabled = false; }
  });
})();
