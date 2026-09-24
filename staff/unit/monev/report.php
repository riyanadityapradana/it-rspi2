<?php
function mn_pdf_load_report($id) {
    global $config;
    $inTransaction = false;
    try {
        @$config->query('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        if (!@$config->query('START TRANSACTION READ ONLY, WITH CONSISTENT SNAPSHOT')) {
            $config->begin_transaction();
        }
        $inTransaction = true;

        $h = mn_header($id);
        $unit = mn_one('SELECT nama_lokasi FROM tb_lokasi WHERE lokasi_id=?', [$h['unit_area_id']]);
        $names = [];
        foreach (mn_all('SELECT id_user,nama_lengkap FROM tb_user') as $r) {
            $names[(int)$r['id_user']] = $r['nama_lengkap'];
        }
        $details = mn_all('SELECT * FROM inspeksi_detail_checklist WHERE inspeksi_id=? ORDER BY id', [$id]);
        $coverage = mn_one('SELECT * FROM inspeksi_cakupan WHERE inspeksi_id=?', [$id]);
        $findings = mn_all('SELECT t.*,d.deskripsi_snapshot,d.lokasi_id_perangkat,l.nama_lokasi FROM inspeksi_temuan_perbaikan t JOIN inspeksi_detail_checklist d ON d.id=t.detail_checklist_id JOIN inspeksi_bundle b ON b.id=d.bundle_id JOIN tb_lokasi l ON l.lokasi_id=b.unit_area_id WHERE t.inspeksi_id=? ORDER BY t.id', [$id]);
        $signRows = mn_all('SELECT p.*,COALESCE(p.nama_penanda,u.nama_lengkap) nama_lengkap FROM inspeksi_pengesahan p JOIN tb_user u ON u.id_user=p.user_id WHERE p.inspeksi_id=? ORDER BY p.id', [$id]);
        $activeSigns = [];
        foreach ($signRows as $sign) {
            if ($sign['dibatalkan_pada'] === null && $sign['bundle_id']===null) $activeSigns[$sign['peran']] = $sign;
        }
        $bundles=mn_bundles($id);
        foreach ($bundles as &$bundle) {
            $bundle['details']=array_values(array_filter($details,function($d) use ($bundle) { return (int)$d['bundle_id']===(int)$bundle['id']; }));
            $bundle['signature']=null;
            foreach ($signRows as $sign) if ((int)$sign['bundle_id']===(int)$bundle['id'] && $sign['peran']==='PIC/Kepala Unit' && $sign['dibatalkan_pada']===null) $bundle['signature']=$sign;
        }
        unset($bundle);

        $config->commit();
        return [
            'header' => $h,
            'bundles' => $bundles,
            'unit' => $unit ?: ['nama_lokasi' => '-'],
            'names' => $names,
            'details' => $details,
            'coverage' => $coverage ?: [],
            'findings' => $findings,
            'signatures' => $signRows,
            'active_signatures' => $activeSigns,
        ];
    } catch (Throwable $e) {
        if ($inTransaction) @$config->rollback();
        throw $e;
    }
}
