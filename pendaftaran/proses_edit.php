<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/
$id_pendaftaran    = intval($_POST['id_pendaftaran']);
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
| Proteksi: Ambil Data Lama
|--------------------------------------------------------------------------
*/
$data_lama_q = mysqli_query($conn, "SELECT tanggal_kunjungan, id_jadwal, no_antrian FROM pendaftaran WHERE id_pendaftaran='$id_pendaftaran'");
$data_lama   = mysqli_fetch_assoc($data_lama_q);

/*
|--------------------------------------------------------------------------
| Validasi Double Pendaftaran Pasien pada Hari yang Sama
|--------------------------------------------------------------------------
*/
$cek = mysqli_query($conn,"
    SELECT id_pendaftaran 
    FROM pendaftaran 
    WHERE no_rm='$no_rm' 
    AND tanggal_kunjungan='$tanggal_kunjungan'
    AND id_pendaftaran != '$id_pendaftaran'
");

if(mysqli_num_rows($cek) > 0){
    echo "<script>alert('Pasien sudah terdaftar pada tanggal tersebut.'); window.history.back();</script>";
    exit;
}

/*
|--------------------------------------------------------------------------
| Penentuan Nomor Antrian Baru (Jika Berubah Waktu/Tanggal)
|--------------------------------------------------------------------------
*/
if ($data_lama['tanggal_kunjungan'] == $tanggal_kunjungan && $data_lama['id_jadwal'] == $id_jadwal) {
    // Jika waktu/tanggal booking tetap sama, pakai antrian lama
    $no_antrian = $data_lama['no_antrian'];
} else {
    // Jika diganti, hitung ulang Max Antrian untuk slot jadwal hari baru tersebut
    $q_antrian = mysqli_query($conn, "
        SELECT MAX(no_antrian) AS terakhir 
        FROM pendaftaran 
        WHERE tanggal_kunjungan='$tanggal_kunjungan' 
        AND id_jadwal='$id_jadwal'
    ");
    $d_antrian = mysqli_fetch_assoc($q_antrian);
    $no_antrian = ($d_antrian['terakhir'] == NULL) ? 1 : intval($d_antrian['terakhir']) + 1;
}

/*
|--------------------------------------------------------------------------
| Update Data Ke Database
|--------------------------------------------------------------------------
*/
$update = mysqli_query($conn, "
    UPDATE pendaftaran SET
        tanggal_kunjungan = '$tanggal_kunjungan',
        no_rm             = '$no_rm',
        id_klinik         = '$id_klinik',
        id_dokter         = '$id_dokter',
        id_jadwal         = '$id_jadwal',
        no_antrian        = '$no_antrian',
        tanggungan        = '$tanggungan',  /* KOLOM BARU DIUPDATE */
        keluhan           = '$keluhan',
        keterangan        = '$keterangan'
    WHERE id_pendaftaran  = '$id_pendaftaran'
");

if($update){
    echo "<script>alert('Data pendaftaran berhasil diperbarui.'); window.location='index.php';</script>";
} else {
    echo "<script>alert('Gagal memperbarui data.'); window.history.back();</script>";
}
exit;