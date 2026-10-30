<?php
// Pastikan tidak ada spasi/enter sebelum tag php ini
ob_start();
require '../config/koneksi.php';

// Bersihkan output buffer untuk mencegah error JSON akibat string liar
if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

$id_klinik = isset($_GET['id_klinik']) ? intval($_GET['id_klinik']) : 0;
$data = [];

if ($id_klinik > 0) {
    // Mengambil dokter yang mempunya jadwal AKTIF di klinik terpilih
    $query = mysqli_query($conn, "
        SELECT DISTINCT
            d.id_dokter,
            d.kode_dokter,
            d.gelar_depan,
            d.nama_dokter,
            d.gelar_belakang
        FROM jadwal_dokter j
        INNER JOIN dokter d ON j.id_dokter = d.id_dokter
        WHERE j.id_klinik = '$id_klinik'
        AND d.status = 'Active'
        ORDER BY d.nama_dokter ASC
    ");

    if ($query) {
        while($row = mysqli_fetch_assoc($query)){
            $data[] =$row;
        }
    }
}

echo json_encode($data);
exit;