<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| Ambil Data
|--------------------------------------------------------------------------
*/

$tanggal_kunjungan = $_POST['tanggal_kunjungan'];
$no_rm             = mysqli_real_escape_string($conn, $_POST['no_rm']);
$id_klinik         = intval($_POST['id_klinik']);
$id_dokter         = intval($_POST['id_dokter']);
$id_jadwal         = intval($_POST['id_jadwal']);
$tanggungan        = mysqli_real_escape_string($conn, $_POST['tanggungan']); // VARIABEL BARU
$keluhan           = mysqli_real_escape_string($conn, $_POST['keluhan']);
$keterangan        = mysqli_real_escape_string($conn, $_POST['keterangan']);


/*
|--------------------------------------------------------------------------
| Cek pasien sudah daftar hari ini?
|--------------------------------------------------------------------------
*/

$cek = mysqli_query($conn,"
SELECT id_pendaftaran
FROM pendaftaran
WHERE no_rm='$no_rm'
AND tanggal_kunjungan='$tanggal_kunjungan'
");

if(mysqli_num_rows($cek) > 0){

    echo "
    <script>
        alert('Pasien sudah terdaftar pada tanggal tersebut.');
        window.history.back();
    </script>
    ";

    exit;
}


/*
|--------------------------------------------------------------------------
| Generate Nomor Registrasi
|--------------------------------------------------------------------------
*/

$prefix = date('Ymd', strtotime($tanggal_kunjungan));

$q = mysqli_query($conn,"
SELECT MAX(no_registrasi) AS terakhir
FROM pendaftaran
WHERE tanggal_kunjungan='$tanggal_kunjungan'
");

$d = mysqli_fetch_assoc($q);

if($d['terakhir'] == NULL){

    $counter = 1;

}else{

    $counter = intval(substr($d['terakhir'],8,5)) + 1;

}

$no_registrasi = $prefix . str_pad($counter,5,'0',STR_PAD_LEFT);


/*
|--------------------------------------------------------------------------
| Generate Nomor Antrian
|--------------------------------------------------------------------------
*/

$q = mysqli_query($conn,"
SELECT MAX(no_antrian) AS terakhir
FROM pendaftaran
WHERE tanggal_kunjungan='$tanggal_kunjungan'
AND id_jadwal='$id_jadwal'
");

$d = mysqli_fetch_assoc($q);

if($d['terakhir'] == NULL){

    $no_antrian = 1;

}else{

    $no_antrian = $d['terakhir'] + 1;

}


/*
|--------------------------------------------------------------------------
| Simpan
|--------------------------------------------------------------------------
*/

$simpan = mysqli_query($conn,"
INSERT INTO pendaftaran(
    no_registrasi,
    tanggal_kunjungan,
    no_rm,
    id_klinik,
    id_dokter,
    id_jadwal,
    no_antrian,
    tanggungan,   /* KOLOM BARU */
    keluhan,
    keterangan
)
VALUES(
    '$no_registrasi',
    '$tanggal_kunjungan',
    '$no_rm',
    '$id_klinik',
    '$id_dokter',
    '$id_jadwal',
    '$no_antrian',
    '$tanggungan', /* VALUE BARU */
    '$keluhan',
    '$keterangan'
)
");


if($simpan){

    echo "
    <script>

        alert('Pendaftaran berhasil disimpan.');

        window.location='index.php';

    </script>
    ";

}else{

    echo "
    <script>

        alert('Pendaftaran gagal disimpan.');

        window.history.back();

    </script>
    ";

}