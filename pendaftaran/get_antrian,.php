<?php
require '../config/koneksi.php';

header('Content-Type: application/json');

$id_jadwal = intval($_GET['id_jadwal']);
$tanggal_kunjungan = isset($_GET['tanggal_kunjungan']) ? mysqli_real_escape_string($conn, $_GET['tanggal_kunjungan']) : date('Y-m-d');

$query = mysqli_query($conn,"
    SELECT MAX(no_antrian) AS terakhir
    FROM pendaftaran
    WHERE tanggal_kunjungan = '$tanggal_kunjungan'
    AND id_jadwal = '$id_jadwal'
");

$data = mysqli_fetch_assoc($query);

if($data['terakhir'] == NULL){
    $antrian = 1;
}else{
    $antrian = intval($data['terakhir']) + 1;
}

echo json_encode([
    "no_antrian" => $antrian
]);