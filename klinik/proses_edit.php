<?php

require '../config/koneksi.php';

$id = $_POST['id_klinik'];
$nama = mysqli_real_escape_string($conn, $_POST['nama_klinik']);
$status = $_POST['status'];

// Cek apakah nama klinik sudah dipakai klinik lain
$cek = mysqli_query($conn, "SELECT * FROM klinik
WHERE nama_klinik='$nama'
AND id_klinik != '$id'");

if (mysqli_num_rows($cek) > 0) {

    echo "
    <script>
        alert('Nama klinik sudah digunakan!');
        history.back();
    </script>
    ";

    exit;
}

mysqli_query($conn, "UPDATE klinik SET
nama_klinik='$nama',
status='$status'
WHERE id_klinik='$id'");

header("Location: index.php");
exit;