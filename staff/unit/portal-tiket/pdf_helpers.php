<?php
require_once __DIR__.'/queries.php';
require_once __DIR__.'/../../../portal-tiket/signature_helpers.php';

function pti_pdf_load($db, $id, $pegawaiDb = null)
{
    try { $id = pt_positive_id($id, 'tiket'); }
    catch (DomainException $e) { throw new DomainException('Tiket tidak ditemukan.', 404); }
    // Satu snapshot konsisten untuk seluruh siklus, tanpa mengubah baris bisnis.
    $db->begin_transaction(MYSQLI_TRANS_START_READ_ONLY | MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
    try {
        $ticket = pt_rows($db, 'SELECT p.id_tiket,p.no_tiket,p.jenis_tiket,p.tgl_pengajuan,p.unit_pemohon,p.nama_pengaju,p.ttd_pengaju,p.status_tiket,s.nama_sistem FROM tiket_pengajuan p LEFT JOIN master_sistem s ON s.id_sistem=p.id_sistem WHERE p.id_tiket=?', [$id])[0] ?? null;
        if (!$ticket) throw new DomainException('Tiket tidak ditemukan.',404);
        if ($ticket['status_tiket'] !== 'Selesai') throw new DomainException('PDF hanya tersedia untuk tiket dengan status Selesai.',409);
        pt_request_definition($ticket['jenis_tiket']);
        $details = pt_rows($db, 'SELECT nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul,latar_belakang,deskripsi_alur_kerja,target_pengguna,output_diharapkan FROM tiket_detail_request WHERE id_tiket=? ORDER BY id', [$id]);
        $analyses = pt_rows($db, 'SELECT id,id_staf_it,nama_staf_it,tgl_analisis,prioritas,analisis_unit_it,keputusan FROM tiket_analisis_it WHERE id_tiket=? ORDER BY id', [$id]);
        $handovers = pt_rows($db, 'SELECT id,id_staf_penyerah,nama_staf_penyerah,tgl_serah_terima,deskripsi_penyelesaian,status_pengujian_uat,catatan_pengujian,nama_penerima,ttd_penerima FROM tiket_serah_terima WHERE id_tiket=? ORDER BY id', [$id]);
        $db->commit();
    } catch (Throwable $e) { $db->rollback(); throw $e; }
    try { $ticket['ttd_pengaju'] = pt_signature($ticket['ttd_pengaju']); }
    catch (DomainException $e) { $ticket['ttd_pengaju'] = null; }
    $names = [];
    foreach (['analyses'=>['id_staf_it','nama_staf_it'],'handovers'=>['id_staf_penyerah','nama_staf_penyerah']] as $group=>$keys) {
        $records = $group === 'analyses' ? $analyses : $handovers;
        foreach ($records as &$record) {
            if (!is_string($record[$keys[1]]) || trim($record[$keys[1]]) === '') {
                $staffId = $record[$keys[0]];
                if (!array_key_exists($staffId,$names)) {
                    try { $names[$staffId] = pt_rows($pegawaiDb ?? pt_connection(true), 'SELECT nama FROM pegawai WHERE id=? LIMIT 1', [$staffId])[0]['nama'] ?? null; }
                    catch (Throwable $e) { $names[$staffId] = null; }
                }
                $record[$keys[1]] = $names[$staffId];
            }
            if ($group === 'handovers') {
                try { $record['ttd_penerima'] = pt_signature($record['ttd_penerima']); }
                catch (DomainException $e) { $record['ttd_penerima'] = null; }
            }
        } unset($record);
        if ($group === 'analyses') $analyses=$records; else $handovers=$records;
    }
    return ['ticket'=>$ticket,'details'=>$details,'analyses'=>$analyses,'handovers'=>$handovers];
}

function pti_pdf_qr_payload($ticket, $kind, $record)
{
    $signature = pt_it_signature($ticket,$kind,$record);
    if (!$signature) return null;
    // QR arsip tetap terbaca setelah masa UAT; tidak membawa bearer token.
    return json_encode(['dokumen'=>'IT-RSPI','versi'=>1,'no_tiket'=>$ticket['no_tiket'],'jenis'=>$kind,
        'id_catatan'=>$signature['id'],'nama'=>$signature['nama'],'tanggal'=>$signature['tanggal'],'sha256'=>$signature['hash']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
}
