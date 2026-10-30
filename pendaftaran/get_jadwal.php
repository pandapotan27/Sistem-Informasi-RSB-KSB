<?php

require '../config/koneksi.php';

header('Content-Type: application/json');

$id_klinik = intval($_GET['id_klinik']);
$id_dokter = intval($_GET['id_dokter']);

$data=[];

$query=mysqli_query($conn,"
SELECT

id_jadwal,
hari,
jam_mulai,
jam_selesai,
room

FROM jadwal_dokter

WHERE

id_klinik='$id_klinik'
AND id_dokter='$id_dokter'

ORDER BY FIELD(hari,

'Senin',
'Selasa',
'Rabu',
'Kamis',
'Jumat',
'Sabtu',
'Minggu'
),
jam_mulai
");

while($row=mysqli_fetch_assoc($query)){
    $data[]=$row;
}

echo json_encode($data);