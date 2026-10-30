<?php

session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$id          = $_POST['id_jadwal'];

$id_dokter   = $_POST['id_dokter'];
$id_klinik   = $_POST['id_klinik'];
$room        = mysqli_real_escape_string($conn,$_POST['room']);
$hari        = $_POST['hari'];
$jam_mulai   = $_POST['jam_mulai'];
$jam_selesai = $_POST['jam_selesai'];


// cek bentrok selain data sendiri

$cek = mysqli_query($conn,"
SELECT *
FROM jadwal_dokter
WHERE id_dokter='$id_dokter'
AND hari='$hari'
AND id_jadwal<>'$id'
AND
(
('$jam_mulai' BETWEEN jam_mulai AND jam_selesai)
OR
('$jam_selesai' BETWEEN jam_mulai AND jam_selesai)
OR
(jam_mulai BETWEEN '$jam_mulai' AND '$jam_selesai')
)
");

if(mysqli_num_rows($cek)>0){

echo "<script>

alert('Dokter sudah memiliki jadwal pada jam tersebut!');

history.back();

</script>";

exit;

}


mysqli_query($conn,"
UPDATE jadwal_dokter SET

id_dokter='$id_dokter',
id_klinik='$id_klinik',
room='$room',
hari='$hari',
jam_mulai='$jam_mulai',
jam_selesai='$jam_selesai'

WHERE id_jadwal='$id'
");

header("Location:index.php");
exit;