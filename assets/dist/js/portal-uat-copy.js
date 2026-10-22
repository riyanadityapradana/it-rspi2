'use strict';
document.querySelectorAll('[data-uat-card]').forEach(function (card) {
  const input = card.querySelector('#pti-uat-url');
  const button = card.querySelector('[data-uat-copy]');
  const label = card.querySelector('[data-uat-expiry-label]');
  const feedback = card.querySelector('[data-uat-copy-status]');
  const expires = Number(card.dataset.uatExpires);
  const serverNow = Number(card.dataset.uatServerNow);
  const started = performance.now();
  const currentTime = function () { return serverNow + performance.now() - started; };
  const expire = function () {
    if (!expires || !input.value || currentTime() < expires) return false;
    button.disabled = true;
    button.classList.remove('btn-primary'); button.classList.add('btn-secondary');
    label.textContent = 'Tautan telah kedaluwarsa. Hubungi tim IT untuk tindak lanjut.';
    label.classList.remove('text-muted'); label.classList.add('text-danger');
    feedback.textContent = '';
    return true;
  };
  const fallbackCopy = function () {
    input.focus(); input.select(); input.setSelectionRange(0, input.value.length);
    if (!document.execCommand('copy')) throw new Error('Clipboard unavailable');
  };
  button.addEventListener('click', async function () {
    if (expire() || button.disabled) return;
    try {
      if (window.isSecureContext && navigator.clipboard && navigator.clipboard.writeText) {
        try { await navigator.clipboard.writeText(input.value); } catch (error) { fallbackCopy(); }
      } else { fallbackCopy(); }
      if (!expire()) { feedback.className = 'd-block mt-2 small text-success'; feedback.textContent = 'Tautan disalin!'; }
    } catch (error) {
      feedback.className = 'd-block mt-2 small text-danger';
      feedback.textContent = 'Tautan belum dapat disalin otomatis. Pilih kotak tautan dan salin secara manual.';
    }
  });
  expire();
  if (expires && input.value) {
    const timer = window.setInterval(function () { if (expire()) window.clearInterval(timer); }, 1000);
    document.addEventListener('visibilitychange', expire);
    window.addEventListener('focus', expire);
  }
});
