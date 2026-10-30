<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$gelar_depan     = mysqli_real_escape_string($conn, $_POST['gelar_depan']);
$nama_dokter     = mysqli_real_escape_string($conn, trim($_POST['nama_dokter']));
$gelar_belakang  = mysqli_real_escape_string($conn, $_POST['gelar_belakang']);
$spesialisasi    = mysqli_real_escape_string($conn, $_POST['spesialisasi']);
$status          = $_POST['status'];

// Ambil 2 huruf pertama nama dokter
$prefix = strtoupper(substr($nama_dokter, 0, 2));

// Cari nomor terbesar berdasarkan prefix yang sama
$query = mysqli_query($conn, "
SELECT MAX(CAST(SUBSTRING(kode_dokter,3) AS UNSIGNED)) AS nomor
FROM dokter
WHERE kode_dokter LIKE '$prefix%'
");

$data = mysqli_fetch_assoc($query);

$nomor = ($data['nomor'] == NULL) ? 1 : $data['nomor'] + 1;

// Format 2 digit
$kode_dokter = $prefix . str_pad($nomor, 2, "0", STR_PAD_LEFT);
// Simpan
mysqli_query($conn,"
INSERT INTO dokter
(
kode_dokter,
gelar_depan,
nama_dokter,
gelar_belakang,
spesialisasi,
status
)
VALUES
(
'$kode_dokter',
'$gelar_depan',
'$nama_dokter',
'$gelar_belakang',
'$spesialisasi',
'$status'
)
");

header("Location:index.php");
exit;