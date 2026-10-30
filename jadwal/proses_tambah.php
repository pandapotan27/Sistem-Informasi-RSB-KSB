<?php

session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$id_dokter   = $_POST['id_dokter'];
$id_klinik   = $_POST['id_klinik'];
$room        = mysqli_real_escape_string($conn,$_POST['room']);
$hari        = $_POST['hari'];
$jam_mulai   = $_POST['jam_mulai'];
$jam_selesai = $_POST['jam_selesai'];


// cek jadwal bentrok

$cek = mysqli_query($conn,"
SELECT *
FROM jadwal_dokter
WHERE id_dokter='$id_dokter'
AND hari='$hari'
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
INSERT INTO jadwal_dokter
(
id_dokter,
id_klinik,
room,
hari,
jam_mulai,
jam_selesai
)

VALUES
(
'$id_dokter',
'$id_klinik',
'$room',
'$hari',
'$jam_mulai',
'$jam_selesai'
)
");

header("Location:index.php");
exit;