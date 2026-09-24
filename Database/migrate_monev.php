<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__.'/../config/koneksi.php';
$sql=file_get_contents(__DIR__.'/2026-09-22_monev_keamanan.sql');
$config->multi_query($sql);
do { if ($result=$config->store_result()) $result->free(); } while ($config->more_results() && $config->next_result());
$db=$config->query('SELECT DATABASE() db')->fetch_assoc()['db'];
$col=$config->prepare("SELECT COUNT(*) n FROM information_schema.columns WHERE table_schema=? AND table_name='inspeksi_pengesahan' AND column_name='path_tanda_tangan'");
$col->bind_param('s',$db); $col->execute();
if (!(int)$col->get_result()->fetch_assoc()['n']) {
    $config->query('ALTER TABLE inspeksi_pengesahan ADD path_tanda_tangan VARCHAR(255) NULL AFTER tgl_pengesahan');
}
$config->query("ALTER TABLE inspeksi_header MODIFY status_inspeksi ENUM('Draft','Menunggu Validasi','Menunggu Pengesahan','Selesai') NOT NULL DEFAULT 'Draft'");
$config->query("UPDATE inspeksi_header SET status_inspeksi='Menunggu Pengesahan' WHERE status_inspeksi='Menunggu Validasi'");
$config->query("ALTER TABLE inspeksi_header MODIFY status_inspeksi ENUM('Draft','Menunggu Pengesahan','Selesai') NOT NULL DEFAULT 'Draft'");
foreach (['pic_area_nik'=>'VARCHAR(30)', 'pic_area_nama'=>'VARCHAR(100)', 'verifikator_nik'=>'VARCHAR(30)', 'verifikator_nama'=>'VARCHAR(100)'] as $field=>$type) {
    $stmt=$config->prepare("SELECT COUNT(*) n FROM information_schema.columns WHERE table_schema=? AND table_name='inspeksi_header' AND column_name=?");
    $stmt->bind_param('ss',$db,$field); $stmt->execute();
    if (!(int)$stmt->get_result()->fetch_assoc()['n']) $config->query("ALTER TABLE inspeksi_header ADD `$field` $type NULL");
}
$config->query('ALTER TABLE inspeksi_header MODIFY pic_area_id INT NULL, MODIFY verifikator_id INT NULL');
$columns=$config->query('SHOW COLUMNS FROM inspeksi_detail_checklist')->fetch_all(MYSQLI_ASSOC);
if (!in_array('disimpan_pada',array_column($columns,'Field'),true)) $config->query('ALTER TABLE inspeksi_detail_checklist ADD disimpan_pada DATETIME NULL');
$indexes=$config->query('SHOW INDEX FROM inspeksi_detail_checklist')->fetch_all(MYSQLI_ASSOC);
foreach (['idx_inspeksi_details'=>'inspeksi_id','idx_kriteria_details'=>'kriteria_id'] as $name=>$field) {
    if (!in_array($name,array_column($indexes,'Key_name'),true)) $config->query("ALTER TABLE inspeksi_detail_checklist ADD INDEX $name ($field)");
}
$unique=[];
foreach ($indexes as $index) if (!$index['Non_unique']) $unique[$index['Key_name']][(int)$index['Seq_in_index']]=$index['Column_name'];
foreach ($unique as $name=>$fields) {
    ksort($fields);
    if (array_values($fields)===['inspeksi_id','kriteria_id']) $config->query('ALTER TABLE inspeksi_detail_checklist DROP INDEX `'.str_replace('`','``',$name).'`');
}
foreach (['nama_penanda'=>'VARCHAR(100) NULL','nik_penanda'=>'VARCHAR(30) NULL','metode'=>"VARCHAR(20) NOT NULL DEFAULT 'akun'"] as $field=>$definition) {
    $stmt=$config->prepare("SELECT COUNT(*) n FROM information_schema.columns WHERE table_schema=? AND table_name='inspeksi_pengesahan' AND column_name=?");
    $stmt->bind_param('ss',$db,$field); $stmt->execute();
    if (!(int)$stmt->get_result()->fetch_assoc()['n']) $config->query("ALTER TABLE inspeksi_pengesahan ADD `$field` $definition");
}
require_once __DIR__.'/monev_bundle_upgrade.php';
mn_upgrade_bundles($config);
echo "MONEV_MIGRATION_OK\n";
