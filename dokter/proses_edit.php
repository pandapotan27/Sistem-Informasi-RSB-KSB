<?php

session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$id = $_POST['id_dokter'];

$gelar_depan = mysqli_real_escape_string($conn,$_POST['gelar_depan']);
$nama = mysqli_real_escape_string($conn,$_POST['nama_dokter']);
$gelar_belakang = mysqli_real_escape_string($conn,$_POST['gelar_belakang']);
$spesialisasi = mysqli_real_escape_string($conn,$_POST['spesialisasi']);
$status = $_POST['status'];

mysqli_query($conn,"
UPDATE dokter SET

gelar_depan='$gelar_depan',
nama_dokter='$nama',
gelar_belakang='$gelar_belakang',
spesialisasi='$spesialisasi',
status='$status'

WHERE id_dokter='$id'
");

header("Location:index.php");
exit;