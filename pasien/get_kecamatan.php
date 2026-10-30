<?php

require '../config/koneksi.php';

header('Content-Type: application/json');

$provinsi = mysqli_real_escape_string($conn,$_GET['provinsi']);
$kabupaten = mysqli_real_escape_string($conn,$_GET['kabupaten']);

$data=[];

$query=mysqli_query($conn,"
SELECT DISTINCT kecamatan
FROM tbl_kodepos
WHERE provinsi='$provinsi'
AND kabupaten='$kabupaten'
ORDER BY kecamatan ASC
");

while($row=mysqli_fetch_assoc($query)){
    $data[]=$row;
}

echo json_encode($data);