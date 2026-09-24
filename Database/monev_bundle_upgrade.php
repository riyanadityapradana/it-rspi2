<?php
// Called after the base schema migration. Preserve every historical row and signature.
function mn_upgrade_bundles($config) {
    foreach (['inspeksi_detail_checklist','inspeksi_pengesahan'] as $table) {
        $columns=$config->query("SHOW COLUMNS FROM $table")->fetch_all(MYSQLI_ASSOC);
        if (!in_array('bundle_id',array_column($columns,'Field'),true)) $config->query("ALTER TABLE $table ADD bundle_id INT NULL, ADD INDEX (bundle_id)");
    }
    $config->begin_transaction();
    try {
        $config->query("INSERT INTO inspeksi_bundle (inspeksi_id,unit_area_id,pic_area_id,pic_area_nik,pic_area_nama,ruang_diperiksa,komputer_diperiksa,dok_printer_diperiksa,objek_belum_diperiksa_alasan,alasan_ta_bv,disimpan_pada,legacy_header_id)
            SELECT h.id,h.unit_area_id,h.pic_area_id,h.pic_area_nik,h.pic_area_nama,c.ruang_diperiksa,c.komputer_diperiksa,c.dok_printer_diperiksa,c.objek_belum_diperiksa_alasan,c.alasan_ta_bv,
                (SELECT MAX(d.disimpan_pada) FROM inspeksi_detail_checklist d WHERE d.inspeksi_id=h.id),h.id
            FROM inspeksi_header h LEFT JOIN inspeksi_cakupan c ON c.inspeksi_id=h.id
            WHERE NOT EXISTS (SELECT 1 FROM inspeksi_bundle b WHERE b.inspeksi_id=h.id)");
        $config->query('UPDATE inspeksi_detail_checklist d JOIN inspeksi_bundle b ON b.legacy_header_id=d.inspeksi_id SET d.bundle_id=b.id WHERE d.bundle_id IS NULL');
        $config->query("UPDATE inspeksi_pengesahan p JOIN inspeksi_bundle b ON b.legacy_header_id=p.inspeksi_id SET p.bundle_id=b.id WHERE p.peran='PIC/Kepala Unit' AND p.bundle_id IS NULL");
        $config->commit();
    } catch (Throwable $e) { $config->rollback(); throw $e; }
}
