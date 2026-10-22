<?php
require_once __DIR__ . '/portal_url.php';

function pt_it_signature($ticket, $kind, $record)
{
    if (!in_array($kind, ['analisis','serah_terima'], true)) throw new DomainException('Jenis verifikasi tidak valid.',404);
    $analysis = $kind === 'analisis';
    $name = $record[$analysis ? 'nama_staf_it' : 'nama_staf_penyerah'];
    if (!is_string($name) || trim($name) === '') return null;
    $date = $record[$analysis ? 'tgl_analisis' : 'tgl_serah_terima'];
    $identity = ['versi'=>1,'jenis'=>$kind,'id_tiket'=>(int)$ticket['id_tiket'],'no_tiket'=>$ticket['no_tiket'],
        'id_catatan'=>(int)$record['id'],'id_pegawai'=>(int)$record[$analysis ? 'id_staf_it' : 'id_staf_penyerah'],
        'nama'=>$name,'tanggal'=>$date,'isi'=>$analysis ? [$record['prioritas'],$record['analisis_unit_it'],$record['keputusan']] : [$record['deskripsi_penyelesaian']]];
    return ['kind'=>$kind,'id'=>(int)$record['id'],'nama'=>$name,'tanggal'=>$date,
        'hash'=>hash('sha256',json_encode($identity,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR))];
}

function pt_signature_url($signature, $token, $server)
{
    return pt_portal_base_url($server) . '/verifikasi-ttd.php?' . http_build_query(['token'=>$token,'jenis'=>$signature['kind'],'id'=>$signature['id'],'hash'=>$signature['hash']]);
}

function pt_signature_qr($url)
{
    // QR lokal: tidak mengirim bearer token/identitas ke layanan QR eksternal.
    require_once __DIR__ . '/../library/tcpdf/tcpdf_barcodes_2d.php';
    $barcode = (new TCPDF2DBarcode($url,'QRCODE,M'))->getBarcodeArray();
    if (!$barcode || empty($barcode['bcode'])) throw new RuntimeException('QR tidak dapat dibuat.');
    $width = $barcode['num_cols'] + 8; $height = $barcode['num_rows'] + 8; $path = '';
    foreach ($barcode['bcode'] as $y=>$row) foreach ($row as $x=>$filled) if ($filled) $path .= 'M'.($x+4).' '.($y+4).'h1v1h-1z';
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$width.' '.$height.'" shape-rendering="crispEdges"><rect width="100%" height="100%" fill="#fff"/><path d="'.$path.'" fill="#000"/></svg>';
    return 'data:image/svg+xml;base64,'.base64_encode($svg);
}

function pt_prepare_signatures(&$review, $token, $server)
{
    foreach (['analyses'=>'analisis','handovers'=>'serah_terima'] as $group=>$kind) {
        foreach ($review[$group] as &$record) {
            $signature = pt_it_signature($review['ticket'],$kind,$record);
            if ($signature) { $signature['url']=pt_signature_url($signature,$token,$server);$signature['qr']=pt_signature_qr($signature['url']); }
            $record['signature']=$signature;
        } unset($record);
    }
}

function pt_verify_signature($review, $kind, $id, $hash)
{
    if (!in_array($kind,['analisis','serah_terima'],true) || !is_int($id) || $id<1 || !is_string($hash) || !preg_match('/^[a-f0-9]{64}$/D',$hash)) throw new DomainException('Verifikasi tidak valid atau catatan telah berubah.',404);
    foreach ($review[$kind==='analisis' ? 'analyses' : 'handovers'] as $record) {
        if ((int)$record['id']!==$id) continue;
        $signature=pt_it_signature($review['ticket'],$kind,$record);
        if ($signature && hash_equals($signature['hash'],$hash)) return $signature;
    }
    throw new DomainException('Verifikasi tidak valid atau catatan telah berubah.',404);
}
