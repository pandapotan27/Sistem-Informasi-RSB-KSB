<?php
require '../config/koneksi.php';

header('Content-Type: application/json');

$data = [];

$query = mysqli_query($conn,"
SELECT
    no_rm,
    nama_pasien
FROM pasien
ORDER BY nama_pasien ASC
");

while($row = mysqli_fetch_assoc($query)){
    $data[] = $row;
}

echo json_encode($data);