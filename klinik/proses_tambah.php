<?php

require '../config/koneksi.php';

$kode   = $_POST['kode_klinik'];
$nama   = mysqli_real_escape_string($conn,$_POST['nama_klinik']);
$status = $_POST['status'];

// Cek nama klinik
$cek = mysqli_query($conn,"
SELECT *
FROM klinik
WHERE nama_klinik='$nama'
");

if(mysqli_num_rows($cek)>0){

    echo "<script>

    alert('Nama Klinik sudah ada!');

    history.back();

    </script>";

    exit;
}

// Simpan
mysqli_query($conn,"
INSERT INTO klinik
(
kode_klinik,
nama_klinik,
status
)

VALUES
(
'$kode',
'$nama',
'$status'
)
");

header("Location:index.php");