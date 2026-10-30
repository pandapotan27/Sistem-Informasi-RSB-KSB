<?php
require '../config/koneksi.php';

$kodepos = mysqli_real_escape_string($conn, $_GET['kodepos']);

// Sesuaikan nama tabel dan kolom dengan database Anda. 
// Asumsi tabel bernama: tabel_kodepos
$query = mysqli_query($conn, "SELECT id, provinsi, kabupaten, kecamatan, kelurahan 
                              FROM tbl_kodepos 
                              WHERE kodepos = '$kodepos'");

$data = [];
if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }
}

// Kembalikan dalam format JSON agar bisa dibaca JavaScript
header('Content-Type: application/json');
echo json_encode($data);
?>