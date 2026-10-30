<?php

require '../config/koneksi.php';

header('Content-Type: application/json');

$provinsi = mysqli_real_escape_string($conn,$_GET['provinsi']);
$kabupaten = mysqli_real_escape_string($conn,$_GET['kabupaten']);
$kecamatan = mysqli_real_escape_string($conn,$_GET['kecamatan']);

$data=[];

$query=mysqli_query($conn,"
SELECT id,kelurahan
FROM tbl_kodepos
WHERE provinsi='$provinsi'
AND kabupaten='$kabupaten'
AND kecamatan='$kecamatan'
ORDER BY kelurahan ASC
");

while($row=mysqli_fetch_assoc($query)){
    $data[]=$row;
}

echo json_encode($data);