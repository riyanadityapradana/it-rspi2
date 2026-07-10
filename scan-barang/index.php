<?php
require_once(__DIR__ . '/scan_helpers.php');

$pin_required = scan_required_pin() !== '';
$lokasi_q = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
$lokasi_list = array();
while ($row = mysqli_fetch_assoc($lokasi_q)) {
    $lokasi_list[] = $row;
}
$now_date = date('Y-m-d');
$now_datetime = date('Y-m-d\TH:i');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Scan Barang | IT-RSPI</title>
  <link rel="icon" href="../assets/img/icon.png">
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;
      background: #f4f6f8;
      color: #17202a;
    }
    button, input, select, textarea { font: inherit; }
    .app {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .topbar {
      height: 56px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #800000;
      color: #fff;
      font-weight: 700;
      letter-spacing: 0;
    }
    .screen {
      width: 100%;
      max-width: 560px;
      margin: 0 auto;
      padding: 18px;
      flex: 1;
    }
    .choice {
      min-height: calc(100vh - 56px);
      display: grid;
      align-content: center;
      gap: 14px;
    }
    .main-button {
      width: 100%;
      min-height: 112px;
      border: 0;
      border-radius: 8px;
      color: #fff;
      font-size: 22px;
      font-weight: 700;
      box-shadow: 0 10px 22px rgba(0,0,0,.16);
    }
    .main-button.move { background: #1f7a4d; }
    .main-button.repair { background: #9a6a00; }
    .panel {
      background: #fff;
      border: 1px solid #dde3ea;
      border-radius: 8px;
      padding: 14px;
      margin-bottom: 14px;
      box-shadow: 0 6px 18px rgba(20, 31, 43, .08);
    }
    .panel-title {
      margin: 0 0 12px;
      font-size: 18px;
      font-weight: 700;
    }
    #reader {
      width: 100%;
      min-height: 280px;
      overflow: hidden;
      border-radius: 8px;
      background: #101820;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
    }
    #reader video {
      width: 100%;
      max-height: 360px;
      object-fit: cover;
    }
    .row {
      display: grid;
      gap: 10px;
    }
    .form-group { margin-bottom: 12px; }
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 700;
      font-size: 14px;
    }
    input, select, textarea {
      width: 100%;
      border: 1px solid #c8d0d9;
      border-radius: 6px;
      padding: 11px 12px;
      background: #fff;
      min-height: 44px;
    }
    textarea { min-height: 92px; resize: vertical; }
    .button-row {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 12px;
    }
    .btn {
      border: 0;
      border-radius: 6px;
      padding: 11px 14px;
      min-height: 44px;
      font-weight: 700;
    }
    .btn-primary { background: #1f6feb; color: #fff; }
    .btn-success { background: #1f7a4d; color: #fff; }
    .btn-secondary { background: #d8dee6; color: #17202a; }
    .btn-warning { background: #9a6a00; color: #fff; }
    .detail {
      display: grid;
      grid-template-columns: 120px 1fr;
      gap: 8px 12px;
      font-size: 14px;
    }
    .detail .key { color: #5b6672; }
    .detail .value { font-weight: 700; word-break: break-word; }
    .message {
      display: none;
      border-radius: 6px;
      padding: 10px 12px;
      margin-bottom: 12px;
      font-weight: 700;
    }
    .message.show { display: block; }
    .message.ok { background: #dff3e8; color: #17583a; }
    .message.err { background: #fde2e2; color: #8a1c1c; }
    .hidden { display: none !important; }
    .manual-line {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 8px;
      margin-top: 10px;
    }
    @media (min-width: 520px) {
      .row.two { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>
<body>
<div class="app">
  <div class="topbar">IT-RSPI</div>

  <main class="screen">
    <section id="choiceView" class="choice">
      <button type="button" class="main-button move" data-action="pindah">Pindah Barang</button>
      <button type="button" class="main-button repair" data-action="perbaikan">Perbaikan Barang</button>
    </section>

    <section id="workView" class="hidden">
      <div id="message" class="message"></div>

      <div class="panel">
        <h1 id="workTitle" class="panel-title">Scan Barang</h1>
        <?php if ($pin_required): ?>
          <div class="form-group">
            <label>PIN</label>
            <input type="password" id="pinInput" inputmode="numeric" autocomplete="off">
          </div>
        <?php endif; ?>
        <div id="reader">Memuat kamera...</div>
        <div class="manual-line">
          <input type="text" id="manualCode" placeholder="Kode inventaris / ID barang">
          <button type="button" id="manualButton" class="btn btn-primary">Cari</button>
        </div>
        <div class="button-row">
          <button type="button" id="rescanButton" class="btn btn-secondary">Scan Ulang</button>
          <button type="button" id="backButton" class="btn btn-secondary">Kembali</button>
        </div>
      </div>

      <div id="detailPanel" class="panel hidden">
        <h2 class="panel-title">Detail Barang</h2>
        <div class="detail">
          <div class="key">Kode</div><div class="value" id="detailKode">-</div>
          <div class="key">Nama</div><div class="value" id="detailNama">-</div>
          <div class="key">SN</div><div class="value" id="detailSerial">-</div>
          <div class="key">Jenis</div><div class="value" id="detailJenis">-</div>
          <div class="key">Lokasi</div><div class="value" id="detailLokasi">-</div>
          <div class="key">Kondisi</div><div class="value" id="detailKondisi">-</div>
        </div>
      </div>

      <form id="formPindah" class="panel hidden">
        <h2 class="panel-title">Form Pemindahan</h2>
        <input type="hidden" name="barang_id" class="barang-id-field">
        <div class="form-group">
          <label>Nama Petugas</label>
          <input type="text" name="petugas" maxlength="100" required>
        </div>
        <div class="form-group">
          <label>Lokasi Tujuan</label>
          <select name="lokasi_tujuan" required>
            <option value="">-- Pilih Lokasi --</option>
            <?php foreach ($lokasi_list as $lokasi): ?>
              <option value="<?= htmlspecialchars($lokasi['lokasi_id']) ?>"><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Tanggal Mutasi</label>
          <input type="date" name="tanggal_mutasi" value="<?= htmlspecialchars($now_date) ?>" required>
        </div>
        <div class="form-group">
          <label>Keterangan</label>
          <textarea name="keterangan"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Simpan Pemindahan</button>
      </form>

      <form id="formPerbaikan" class="panel hidden" enctype="multipart/form-data">
        <h2 class="panel-title">Form Perbaikan</h2>
        <input type="hidden" name="barang_id" class="barang-id-field">
        <input type="hidden" name="unit_melapor" id="unitMelaporField">
        <div class="row two">
          <div class="form-group">
            <label>Nama Petugas</label>
            <input type="text" name="petugas" maxlength="100" required>
          </div>
          <div class="form-group">
            <label>Tanggal Lapor</label>
            <input type="datetime-local" name="tanggal_lapor" value="<?= htmlspecialchars($now_datetime) ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label>Deskripsi Kerusakan</label>
          <textarea name="deskripsi_kerusakan" required></textarea>
        </div>
        <div class="row two">
          <div class="form-group">
            <label>Tindakan</label>
            <select name="tindakan_perbaikan" id="tindakanField" required>
              <option value="service_sendiri">Service sendiri</option>
              <option value="service_luar">Service luar</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status_perbaikan" id="statusField" required disabled>
              <option value="diajukan">Diajukan</option>
              <option value="proses">Proses</option>
              <option value="selesai">Selesai</option>
              <option value="tidak_dapat_diperbaiki">Tidak Dapat Diperbaiki</option>
            </select>
          </div>
        </div>
        <div class="form-group hidden" id="buktiGroup">
          <label>Bukti Struk / Kuitansi</label>
          <input type="file" name="bukti_struk" id="buktiField" accept="image/*">
          <small>Opsional untuk service luar, dapat diupload belakangan.</small>
        </div>
        <div class="form-group">
          <label>Keterangan</label>
          <textarea name="keterangan_perbaikan"></textarea>
        </div>
        <button type="submit" class="btn btn-warning">Simpan Perbaikan</button>
      </form>
    </section>
  </main>
</div>

<script src="html5-qrcode.min.js"></script>
<script>
(function() {
  var selectedAction = '';
  var scanner = null;
  var nativeStream = null;
  var nativeTimer = null;
  var scanLocked = false;
  var scanSession = 0;
  var pinRequired = <?= $pin_required ? 'true' : 'false' ?>;

  var choiceView = document.getElementById('choiceView');
  var workView = document.getElementById('workView');
  var reader = document.getElementById('reader');
  var message = document.getElementById('message');
  var detailPanel = document.getElementById('detailPanel');
  var formPindah = document.getElementById('formPindah');
  var formPerbaikan = document.getElementById('formPerbaikan');

  function showMessage(text, ok) {
    message.textContent = text;
    message.className = 'message show ' + (ok ? 'ok' : 'err');
  }

  function clearMessage() {
    message.textContent = '';
    message.className = 'message';
  }

  function getPin() {
    var input = document.getElementById('pinInput');
    return input ? input.value : '';
  }

  function resetPanels() {
    detailPanel.classList.add('hidden');
    formPindah.classList.add('hidden');
    formPerbaikan.classList.add('hidden');
  }

  function isLocalAddress() {
    return window.location.hostname === 'localhost' ||
      window.location.hostname === '127.0.0.1' ||
      window.location.hostname === '[::1]';
  }

  function canUseCamera() {
    return window.isSecureContext || isLocalAddress();
  }

  function setReaderText(text) {
    reader.innerHTML = '';
    reader.appendChild(document.createTextNode(text));
  }

  function stopScanner() {
    if (scanner) {
      scanner.stop().catch(function() {}).then(function() {
        try { scanner.clear(); } catch (e) {}
      });
      scanner = null;
    }
    if (nativeTimer) {
      window.clearInterval(nativeTimer);
      nativeTimer = null;
    }
    if (nativeStream) {
      nativeStream.getTracks().forEach(function(track) { track.stop(); });
      nativeStream = null;
    }
  }

  function startScanner() {
    var sessionId = ++scanSession;
    scanLocked = false;
    clearMessage();
    resetPanels();
    setReaderText('Memuat kamera...');
    stopScanner();

    if (!canUseCamera()) {
      setReaderText('Kamera diblokir browser');
      showMessage('Buka halaman ini memakai HTTPS, atau aktifkan izin insecure origin khusus untuk alamat ini di Chrome Android.', false);
      return;
    }

    if (window.Html5Qrcode) {
      scanner = new Html5Qrcode('reader');
      var formats = [];
      if (window.Html5QrcodeSupportedFormats) {
        formats = [
          Html5QrcodeSupportedFormats.QR_CODE,
          Html5QrcodeSupportedFormats.CODE_128
        ];
      }
      var scannerConfig = { fps: 10, qrbox: { width: 240, height: 240 } };
      if (formats.length > 0) {
        scannerConfig.formatsToSupport = formats;
      }
      scanner.start(
        { facingMode: 'environment' },
        scannerConfig,
        function(decodedText) {
          if (sessionId === scanSession) {
            handleScanResult(decodedText);
          }
        }
      ).catch(function(error) {
        if (sessionId !== scanSession) return;
        startNativeScanner(sessionId, error);
      });
      return;
    }

    startNativeScanner(sessionId);
  }

  function startNativeScanner(sessionId, previousError) {
    if (sessionId !== scanSession) return;

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia || !window.BarcodeDetector) {
      setReaderText('Scanner kamera tidak tersedia');
      showMessage(previousError ? 'Kamera tidak dapat dibuka. Gunakan input kode / ID barang manual.' : 'Gunakan input kode / ID barang manual.', false);
      return;
    }

    var detector = new BarcodeDetector({ formats: ['qr_code', 'code_128'] });
    var video = document.createElement('video');
    video.setAttribute('playsinline', 'playsinline');
    video.muted = true;
    setReaderText('');
    reader.appendChild(video);

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
      .then(function(stream) {
        if (sessionId !== scanSession) {
          stream.getTracks().forEach(function(track) { track.stop(); });
          return Promise.reject(new Error('Scanner dibatalkan'));
        }
        nativeStream = stream;
        video.srcObject = stream;
        return video.play();
      })
      .then(function() {
        if (sessionId !== scanSession) return;
        nativeTimer = window.setInterval(function() {
          detector.detect(video).then(function(codes) {
            if (sessionId !== scanSession) return;
            if (codes && codes.length > 0) {
              handleScanResult(codes[0].rawValue || '');
            }
          }).catch(function() {});
        }, 450);
      })
      .catch(function() {
        if (sessionId !== scanSession) return;
        setReaderText('Kamera tidak dapat dibuka');
        showMessage('Gunakan input kode / ID barang manual.', false);
      });
  }

  function handleScanResult(decodedText) {
    if (scanLocked) return;
    var kode = String(decodedText || '').trim();
    if (!kode) return;
    scanLocked = true;
    stopScanner();
    lookupBarang(kode);
  }

  function lookupBarang(kode) {
    clearMessage();
    if (pinRequired && !getPin()) {
      showMessage('PIN wajib diisi', false);
      scanLocked = false;
      return;
    }

    fetch('api_barang.php?kode=' + encodeURIComponent(kode) + '&pin=' + encodeURIComponent(getPin()))
      .then(function(response) { return response.json(); })
      .then(function(data) {
        if (!data.success) {
          showMessage(data.message || 'Barang tidak ditemukan', false);
          scanLocked = false;
          return;
        }
        renderBarang(data.barang);
      })
      .catch(function() {
        showMessage('Gagal mengambil data barang', false);
        scanLocked = false;
      });
  }

  function renderBarang(barang) {
    document.getElementById('detailKode').textContent = barang.kode_inventaris || '-';
    document.getElementById('detailNama').textContent = barang.nama_barang || '-';
    document.getElementById('detailSerial').textContent = barang.nomor_seri || '-';
    document.getElementById('detailJenis').textContent = barang.jenis_barang || '-';
    document.getElementById('detailLokasi').textContent = barang.lokasi_saat_ini || barang.last_penyerahan_lokasi || '-';
    document.getElementById('detailKondisi').textContent = barang.kondisi || '-';

    Array.prototype.forEach.call(document.querySelectorAll('.barang-id-field'), function(input) {
      input.value = barang.barang_id;
    });
    document.getElementById('unitMelaporField').value = barang.last_penyerahan_lokasi_id || barang.lokasi_id || '';

    detailPanel.classList.remove('hidden');
    if (selectedAction === 'pindah') {
      formPindah.classList.remove('hidden');
    } else {
      syncRepairAction();
      formPerbaikan.classList.remove('hidden');
    }
  }

  function submitForm(form, url) {
    clearMessage();
    var fd = new FormData(form);
    fd.append('pin', getPin());
    if (form === formPerbaikan) {
      fd.set('status_perbaikan', document.getElementById('statusField').value);
    }

    fetch(url, { method: 'POST', body: fd })
      .then(function(response) { return response.json(); })
      .then(function(data) {
        showMessage(data.message || (data.success ? 'Data berhasil disimpan' : 'Gagal menyimpan data'), !!data.success);
        if (data.success) {
          form.reset();
          resetPanels();
          scanLocked = false;
        }
      })
      .catch(function() {
        showMessage('Gagal mengirim data', false);
      });
  }

  function syncRepairAction() {
    var tindakan = document.getElementById('tindakanField').value;
    var status = document.getElementById('statusField');
    var group = document.getElementById('buktiGroup');
    var input = document.getElementById('buktiField');
    status.disabled = false;
    if (tindakan === 'service_luar') {
      status.value = 'diajukan';
      group.classList.remove('hidden');
    } else {
      status.value = 'proses';
      group.classList.add('hidden');
      input.value = '';
    }
    status.disabled = true;
    input.required = false;
  }

  Array.prototype.forEach.call(document.querySelectorAll('[data-action]'), function(button) {
    button.addEventListener('click', function() {
      selectedAction = button.getAttribute('data-action');
      document.getElementById('workTitle').textContent = selectedAction === 'pindah' ? 'Pindah Barang' : 'Perbaikan Barang';
      choiceView.classList.add('hidden');
      workView.classList.remove('hidden');
      startScanner();
    });
  });

  document.getElementById('manualButton').addEventListener('click', function() {
    var input = document.getElementById('manualCode');
    lookupBarang(input.value);
  });

  document.getElementById('rescanButton').addEventListener('click', startScanner);
  document.getElementById('backButton').addEventListener('click', function() {
    scanSession++;
    stopScanner();
    window.location.href = 'index.php';
  });
  formPindah.addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(formPindah, 'simpan_pindah.php');
  });
  formPerbaikan.addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(formPerbaikan, 'simpan_perbaikan.php');
  });
  document.getElementById('tindakanField').addEventListener('change', syncRepairAction);
  document.getElementById('tindakanField').addEventListener('input', syncRepairAction);
  window.addEventListener('pageshow', syncRepairAction);
  syncRepairAction();
})();
</script>
</body>
</html>
