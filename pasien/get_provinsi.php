<?php

require '../config/koneksi.php';

header('Content-Type: application/json');

$data = [];

$query = mysqli_query($conn,"
SELECT DISTINCT provinsi
FROM tbl_kodepos
ORDER BY provinsi ASC
");

while($row=mysqli_fetch_assoc($query)){
    $data[] = $row;
}

echo json_encode($data);