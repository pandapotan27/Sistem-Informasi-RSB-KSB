<?php

require '../config/koneksi.php';

header('Content-Type: application/json');

$id = intval($_GET['id']);

$query = mysqli_query($conn,"
SELECT id,kodepos
FROM tbl_kodepos
WHERE id='$id'
");

echo json_encode(mysqli_fetch_assoc($query));