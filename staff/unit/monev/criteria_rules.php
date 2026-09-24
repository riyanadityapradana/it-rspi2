<?php
function mn_server_not_applicable($unitName, $category) {
    return strcasecmp(trim((string)$unitName), 'IT') !== 0
        && strcasecmp(trim((string)$category), 'Ruang Server') === 0;
}

function mn_visible_bundle_details($bundle) {
    return array_filter($bundle['details'], function ($detail) use ($bundle) {
        return !mn_server_not_applicable($bundle['nama_lokasi'], $detail['kategori_snapshot']);
    });
}
