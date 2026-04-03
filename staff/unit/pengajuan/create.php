<?php
require_once("../config/koneksi.php");
require_once("../config/telegram.php");
$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = trim($_POST['nama_barang']);
    $unit = 'Unit IT';
    $jumlah = isset($_POST['jumlah']) ? (int) $_POST['jumlah'] : 0;
    $perkiraan_harga = isset($_POST['perkiraan_harga']) ? (float) $_POST['perkiraan_harga'] : 0;
    $keterangan = trim($_POST['keterangan']);

    if ($nama_barang === '' || $unit === '' || $jumlah <= 0 || $perkiraan_harga <= 0) {
        header('Location: dashboard_staff.php?unit=pengajuan&err=Data tidak lengkap!');
        exit;
    }

    $stmt = $config->prepare("INSERT INTO tb_pengajuan (id_user, nama_barang, unit, jumlah, perkiraan_harga, keterangan, status, tanggal_pengajuan) VALUES (?, ?, ?, ?, ?, ?, 'diajukan', CURDATE())");
    $stmt->bind_param('issids', $id_user, $nama_barang, $unit, $jumlah, $perkiraan_harga, $keterangan);
    $success = $stmt->execute();

    if ($success) {
        $pengajuan_id = $stmt->insert_id;
        $nama_staff = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'Staff IT';
        $nip_staff = isset($_SESSION['nip']) ? $_SESSION['nip'] : '-';
        $pesan = "PENGAJUAN BARANG BARU\n";
        $pesan .= "ID Pengajuan: " . $pengajuan_id . "\n";
        $pesan .= "Nama Staff: " . $nama_staff . "\n";
        $pesan .= "NIP: " . $nip_staff . "\n";
        $pesan .= "Unit: " . $unit . "\n";
        $pesan .= "Nama Barang: " . $nama_barang . "\n";
        $pesan .= "Jumlah: " . $jumlah . "\n";
        $pesan .= "Perkiraan Harga: Rp " . number_format($perkiraan_harga, 0, ',', '.') . "\n";
        $pesan .= "Keterangan: " . ($keterangan !== '' ? $keterangan : '-') . "\n";
        $pesan .= "Status: Diajukan";
        telegram_send_channel_message($pesan);

        $stmt->close();
        header('Location: dashboard_staff.php?unit=pengajuan&msg=Pengajuan barang berhasil ditambahkan!');
        exit;
    }

    $stmt->close();
    header('Location: dashboard_staff.php?unit=pengajuan&err=Gagal menambah pengajuan!');
    exit;
}
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Tambah Pengajuan Barang</h1>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <form method="post">
          <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required maxlength="150">
          </div>
          <div class="form-group">
            <label>Unit</label>
            <input type="text" name="unit" class="form-control" value="Unit IT" readonly required maxlength="50">
          </div>
          <div class="form-group">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
          </div>
          <div class="form-group">
            <label>Perkiraan Harga</label>
            <input type="number" name="perkiraan_harga" class="form-control" min="0" step="0.01" required>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="dashboard_staff.php?unit=pengajuan" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
