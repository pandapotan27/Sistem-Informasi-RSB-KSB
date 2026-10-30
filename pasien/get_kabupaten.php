<?php

require '../config/koneksi.php';

header('Content-Type: application/json');

$provinsi = mysqli_real_escape_string($conn,$_GET['provinsi']);

$data = [];

$query = mysqli_query($conn,"
SELECT DISTINCT kabupaten
FROM tbl_kodepos
WHERE provinsi='$provinsi'
ORDER BY kabupaten ASC
");

while($row=mysqli_fetch_assoc($query)){
    $data[] = $row;
}

echo json_encode($data);