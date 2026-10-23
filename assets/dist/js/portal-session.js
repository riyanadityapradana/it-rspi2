// Halaman dari back/forward cache harus memeriksa ulang sesi pada server.
window.addEventListener('pageshow', event => { if (event.persisted) window.location.reload(); });
