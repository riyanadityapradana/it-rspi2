<?php
require_once("../config/koneksi.php");
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>DATA PEMINDAHAN BARANG KE UNIT LAIN</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_admin.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Pemindahan Barang</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
      <div class="card">
          <div class="card-header">
          <div class="card-tools" style="float: left; text-align: left;">
            <!-- <a href="?unit=create_pemindahan" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
              <i class="fas fa-plus-square" style="color: white;"> Tambah Pemindahan</i>
            </a> -->
            <button type="button" class="btn btn-tool btn-sm" style="background:rgba(40, 167, 69, 1); margin-left: 8px;" data-toggle="modal" data-target="#modalPrint">
              <i class="fas fa-print" style="color: white;"> Print</i>
            </button>
          </div>
            <div class="card-tools" style="float: right; text-align: right;">
              <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                <i class="fas fa-bars"></i>
              </a>
            </div>
          </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                <thead style="background:rgb(52, 58, 64, 1)">
                    <tr>
                      <th style="font-size: 14px; color: white;" responsive>No</th>
                      <th style="font-size: 14px; color: white;" responsive>Nama Barang</th>
                      <th style="font-size: 14px; color: white;" responsive>Lokasi Asal</th>
                      <th style="font-size: 14px; color: white;" responsive>Lokasi Tujuan</th>
                      <th style="font-size: 14px; color: white;" responsive>Tanggal Mutasi</th>
                      <th style="font-size: 14px; color: white;" responsive>Staff Memindahkan</th>
                      <th style="font-size: 14px; color: white;" responsive>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $q = mysqli_query($config, "SELECT m.*, b.nama_barang, b.kode_inventaris, b.foto, l1.nama_lokasi AS lokasi_asal_nama, l2.nama_lokasi AS lokasi_tujuan_nama, u.nama_lengkap as nama_staff, (SELECT penyerahan_id FROM tb_penyerahan WHERE barang_id = m.barang_id ORDER BY penyerahan_id DESC LIMIT 1) as penyerahan_id 
                      FROM tb_mutasi_barang m 
                      LEFT JOIN tb_barang b ON m.barang_id = b.barang_id 
                      LEFT JOIN tb_lokasi l1 ON m.lokasi_asal = l1.lokasi_id 
                      LEFT JOIN tb_lokasi l2 ON m.lokasi_tujuan = l2.lokasi_id 
                      LEFT JOIN tb_user u ON m.id_user = u.id_user 
                      ORDER BY m.tanggal_mutasi DESC");
                    while ($row = mysqli_fetch_assoc($q)) : ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td>
                        <?= htmlspecialchars($row['nama_barang']); ?>
                        <br><small style="color: #666;">Kode Inventaris: <b><?= htmlspecialchars($row['kode_inventaris'] ?? '') ?></b></small>
                      </td>
                      <td><?= htmlspecialchars($row['lokasi_asal_nama']); ?></td>
                      <td><?= htmlspecialchars($row['lokasi_tujuan_nama']); ?></td>
                      <td><?= !empty($row['tanggal_mutasi']) ? date('d/m/Y', strtotime($row['tanggal_mutasi'])) : '-'; ?></td>
                      <td><?= htmlspecialchars($row['nama_staff']); ?></td>
                      <td>
                      <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal" onclick="showDetail(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                        <i class="fa fa-eye"></i> Detail
                      </button>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
              </table>
<div class="modal fade" id="modalPrint" tabindex="-1" role="dialog" aria-labelledby="modalPrintLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
    <form id="formPrint" method="get" target="_blank" action="unit/pemindahan/print_pemindahan.php">
      <div class="modal-content">
        <div class="modal-header" style="background: #1976d2; color: white;">
          <h5 class="modal-title" id="modalPrintLabel"><i class="fas fa-print"></i> Cetak Data Pemindahan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Pilihan Cetak</label>
            <select class="form-control" id="printType" name="printType" required>
              <option value="all">Cetak Semua</option>
              <option value="jenis">Cetak Berdasarkan Jenis Barang</option>
            </select>
          </div>
          <div class="form-group" id="jenisBarangGroup" style="display:none;">
            <label>Jenis Barang</label>
            <select class="form-control select2" name="jenis_barang" id="jenis_barang">
              <option value="">-- Pilih Jenis Barang --</option>
              <option value="Komputer & Laptop">Komputer & Laptop</option>
              <option value="Komponen Komputer & Laptop">Komponen Komputer & Laptop</option>
              <option value="Printer & Scanner">Printer & Scanner</option>
              <option value="Komponen Printer & Scanner">Komponen Printer & Scanner</option>
              <option value="Komponen Network">Komponen Network</option>
            </select>
          </div>
          <div class="form-group">
            <label>Bulan</label>
            <select class="form-control" name="bulan" required>
              <?php for($i=1;$i<=12;$i++): ?>
                <option value="<?= $i ?>"><?= date('F', mktime(0,0,0,$i,10)) ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tahun</label>
            <select class="form-control" name="tahun" required>
              <?php for($y=date('Y')-5;$y<=date('Y');$y++): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> Cetak</button>
        </div>
      </div>
    </form>
  </div>
</div>
</section>
<style>
  .detail-photo-wrapper {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 14px;
  }

  .detail-photo-frame {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 280px;
    max-height: 380px;
    overflow: auto;
    background: linear-gradient(135deg, #fdfdfd 0%, #f1f3f5 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
  }

  .detail-photo-frame.is-zoomed {
    cursor: grab;
  }

  .detail-photo-frame.is-dragging {
    cursor: grabbing;
  }

  .detail-photo-frame img {
    max-width: 100%;
    transform-origin: center center;
    transition: transform 0.2s ease;
    cursor: zoom-in;
    user-select: none;
    -webkit-user-drag: none;
  }

  .detail-photo-frame.is-zoomed img {
    cursor: grab;
  }

  .detail-photo-frame.is-dragging img {
    cursor: grabbing;
  }

  .detail-photo-placeholder {
    width: 100%;
    min-height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #8a8f98;
    background: #f8f9fa;
    border: 1px dashed #c9ced6;
    border-radius: 8px;
    padding: 24px;
  }

  .detail-photo-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 12px;
  }

  .detail-photo-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .detail-photo-actions button {
    min-width: 42px;
  }

  @media (max-width: 767.98px) {
    #detailModal .detail-grid-two {
      display: block !important;
    }

    #detailModal .detail-grid-two > div {
      margin-bottom: 15px;
    }

    .detail-photo-frame {
      min-height: 220px;
      max-height: 280px;
    }
  }
</style>
<script>
document.getElementById('printType').addEventListener('change', function() {
  document.getElementById('jenisBarangGroup').style.display = (this.value === 'jenis') ? '' : 'none';
});

var currentPhotoZoom = 1;
var photoPanState = {
  isDragging: false,
  startX: 0,
  startY: 0,
  scrollLeft: 0,
  scrollTop: 0
};

function formatTanggalIndonesia(dateString) {
  if (!dateString) {
    return '-';
  }

  var date = new Date(dateString);
  if (isNaN(date.getTime())) {
    return dateString;
  }

  var day = String(date.getDate()).padStart(2, '0');
  var month = String(date.getMonth() + 1).padStart(2, '0');
  var year = date.getFullYear();
  return day + '/' + month + '/' + year;
}

function centerPhotoFrame() {
  var frame = document.getElementById('detailPhotoFrame');

  if (!frame) {
    return;
  }

  frame.scrollLeft = Math.max(0, (frame.scrollWidth - frame.clientWidth) / 2);
  frame.scrollTop = Math.max(0, (frame.scrollHeight - frame.clientHeight) / 2);
}

function setPhotoZoom(value) {
  var img = document.getElementById('detailFotoBarang');
  var frame = document.getElementById('detailPhotoFrame');
  var indicator = document.getElementById('detailZoomLevel');

  if (!img || img.style.display === 'none') {
    return;
  }

  currentPhotoZoom = Math.min(3, Math.max(0.5, value));
  img.style.width = (currentPhotoZoom * 100) + '%';
  img.style.maxWidth = currentPhotoZoom > 1 ? 'none' : '100%';
  indicator.textContent = Math.round(currentPhotoZoom * 100) + '%';

  if (frame) {
    frame.classList.toggle('is-zoomed', currentPhotoZoom > 1);

    if (currentPhotoZoom <= 1) {
      stopPhotoDrag();
      frame.scrollLeft = 0;
      frame.scrollTop = 0;
    } else {
      centerPhotoFrame();
    }
  }
}

function zoomInPhoto() {
  setPhotoZoom(currentPhotoZoom + 0.25);
}

function zoomOutPhoto() {
  setPhotoZoom(currentPhotoZoom - 0.25);
}

function resetPhotoZoom() {
  setPhotoZoom(1);
}

function stopPhotoDrag() {
  var frame = document.getElementById('detailPhotoFrame');

  photoPanState.isDragging = false;

  if (frame) {
    frame.classList.remove('is-dragging');
  }
}

function attachPhotoPanHandlers() {
  var frame = document.getElementById('detailPhotoFrame');
  var photo = document.getElementById('detailFotoBarang');

  if (!frame || !photo) {
    return;
  }

  photo.setAttribute('draggable', 'false');

  frame.addEventListener('mousedown', function(event) {
    if (currentPhotoZoom <= 1 || photo.style.display === 'none') {
      return;
    }

    photoPanState.isDragging = true;
    photoPanState.startX = event.clientX;
    photoPanState.startY = event.clientY;
    photoPanState.scrollLeft = frame.scrollLeft;
    photoPanState.scrollTop = frame.scrollTop;

    frame.classList.add('is-dragging');
    event.preventDefault();
  });

  frame.addEventListener('mousemove', function(event) {
    if (!photoPanState.isDragging || currentPhotoZoom <= 1) {
      return;
    }

    var deltaX = event.clientX - photoPanState.startX;
    var deltaY = event.clientY - photoPanState.startY;

    frame.scrollLeft = photoPanState.scrollLeft - deltaX;
    frame.scrollTop = photoPanState.scrollTop - deltaY;
  });

  frame.addEventListener('mouseleave', stopPhotoDrag);
  frame.addEventListener('mouseup', stopPhotoDrag);
  photo.addEventListener('dragstart', function(event) {
    event.preventDefault();
  });

  document.addEventListener('mouseup', stopPhotoDrag);
}

function showBarangPhoto(filename) {
  var img = document.getElementById('detailFotoBarang');
  var frame = document.getElementById('detailPhotoFrame');
  var emptyState = document.getElementById('detailFotoEmpty');
  var controls = document.getElementById('detailFotoControls');
  var zoomLabel = document.getElementById('detailZoomLevel');
  var baseUrl = '/it-rspi2/staff/unit/barang/foto-barang/';

  if (!img || !emptyState || !controls || !zoomLabel) {
    return;
  }

  if (filename) {
    img.src = baseUrl + encodeURIComponent(filename);
    img.alt = 'Foto ' + (document.getElementById('detailNamaBarang').textContent || 'Barang');
    img.style.display = 'block';
    img.style.width = '100%';
    img.style.maxWidth = '100%';
    emptyState.style.display = 'none';
    controls.style.display = 'flex';
    resetPhotoZoom();
  } else {
    img.removeAttribute('src');
    img.style.display = 'none';
    emptyState.style.display = 'flex';
    controls.style.display = 'none';
    zoomLabel.textContent = '100%';
    stopPhotoDrag();
    if (frame) {
      frame.classList.remove('is-zoomed');
      frame.scrollLeft = 0;
      frame.scrollTop = 0;
    }
  }
}

function showDetail(data) {
  document.getElementById('detailNamaBarang').textContent = data.nama_barang || '-';
  document.getElementById('detailKodeInventaris').textContent = data.kode_inventaris || '-';
  document.getElementById('detailLokasiAsal').textContent = data.lokasi_asal_nama || '-';
  document.getElementById('detailLokasiTujuan').textContent = data.lokasi_tujuan_nama || '-';
  document.getElementById('detailTanggalMutasi').textContent = formatTanggalIndonesia(data.tanggal_mutasi);
  document.getElementById('detailStaffMemindahkan').textContent = data.nama_staff || '-';
  showBarangPhoto(data.foto || '');
}

function copyToClipboard(elementId) {
  const text = document.getElementById(elementId).textContent;
  navigator.clipboard.writeText(text).then(function() {
    alert('Data berhasil disalin: ' + text);
  }).catch(function(err) {
    alert('Gagal menyalin data');
  });
}

document.addEventListener('DOMContentLoaded', function() {
  attachPhotoPanHandlers();
  var photo = document.getElementById('detailFotoBarang');

  if (photo) {
    photo.addEventListener('dblclick', function() {
      if (currentPhotoZoom > 1) {
        resetPhotoZoom();
      } else {
        setPhotoZoom(2);
      }
    });

    photo.addEventListener('error', function() {
      showBarangPhoto('');
    });
  }

  $('#detailModal').on('hidden.bs.modal', function() {
    currentPhotoZoom = 1;
    showBarangPhoto('');
  });
});
</script>

<!-- Modal Detail Pemindahan -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <div>
          <h5 class="modal-title" id="detailModalLabel" style="font-size: 18px; font-weight: 600;">Detail Pemindahan Barang</h5>
          <small class="text-muted" style="font-size: 13px;">Informasi lengkap pemindahan barang ke unit lain</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <!-- Informasi Barang Section -->
        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
          <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Barang</h6>

          <div class="detail-grid-two" style="display: grid; grid-template-columns: minmax(260px, 340px) 1fr; gap: 20px; align-items: start;">
            <div>
              <p style="font-size: 13px; color: #999; margin-bottom: 8px;">Foto Barang</p>
              <div class="detail-photo-wrapper">
                <div class="detail-photo-frame" id="detailPhotoFrame">
                  <div class="detail-photo-stage">
                    <img id="detailFotoBarang" src="" alt="Foto Barang" style="display: none;">
                    <div id="detailFotoEmpty" class="detail-photo-placeholder">
                      Foto barang belum tersedia.
                    </div>
                  </div>
                </div>
                <div id="detailFotoControls" class="detail-photo-controls" style="display: none;">
                  <small class="text-muted">Double click pada foto untuk zoom cepat.</small>
                  <div class="detail-photo-actions">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="zoomOutPhoto()" title="Zoom Out">
                      <i class="fas fa-search-minus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetPhotoZoom()" title="Reset Zoom">
                      <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="zoomInPhoto()" title="Zoom In">
                      <i class="fas fa-search-plus"></i>
                    </button>
                    <span id="detailZoomLevel" class="badge badge-light" style="display: inline-flex; align-items: center; padding: 0 12px;">100%</span>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <div style="margin-bottom: 15px;">
                <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Nama Barang</p>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                  <strong id="detailNamaBarang" style="font-size: 15px;">-</strong>
                  <button type="button" onclick="copyToClipboard('detailNamaBarang')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666; flex-shrink: 0;">
                    <i class="fas fa-copy" style="font-size: 14px;"></i>
                  </button>
                </div>
              </div>

              <div style="margin-bottom: 15px;">
                <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Kode Inventaris</p>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                  <strong id="detailKodeInventaris" style="font-size: 15px;">-</strong>
                  <button type="button" onclick="copyToClipboard('detailKodeInventaris')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666; flex-shrink: 0;">
                    <i class="fas fa-copy" style="font-size: 14px;"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Informasi Lokasi Section -->
        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
          <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Lokasi</h6>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
            <div>
              <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Lokasi Asal</p>
              <div style="display: flex; align-items: center; justify-content: space-between;">
                <strong id="detailLokasiAsal" style="font-size: 15px;">-</strong>
                <button type="button" onclick="copyToClipboard('detailLokasiAsal')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                  <i class="fas fa-copy" style="font-size: 14px;"></i>
                </button>
              </div>
            </div>
            <div>
              <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Lokasi Tujuan</p>
              <div style="display: flex; align-items: center; justify-content: space-between;">
                <strong id="detailLokasiTujuan" style="font-size: 15px;">-</strong>
                <button type="button" onclick="copyToClipboard('detailLokasiTujuan')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                  <i class="fas fa-copy" style="font-size: 14px;"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Informasi Mutasi Section -->
        <div>
          <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Mutasi</h6>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
              <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Tanggal Mutasi</p>
              <div style="display: flex; align-items: center; justify-content: space-between;">
                <strong id="detailTanggalMutasi" style="font-size: 15px;">-</strong>
                <button type="button" onclick="copyToClipboard('detailTanggalMutasi')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                  <i class="fas fa-copy" style="font-size: 14px;"></i>
                </button>
              </div>
            </div>
            <div>
              <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Staff Memindahkan</p>
              <div style="display: flex; align-items: center; justify-content: space-between;">
                <strong id="detailStaffMemindahkan" style="font-size: 15px;">-</strong>
                <button type="button" onclick="copyToClipboard('detailStaffMemindahkan')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                  <i class="fas fa-copy" style="font-size: 14px;"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>





