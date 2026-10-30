<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_klinik";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal : " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// Base URL Project
$base_url = "http://localhost/RSBSummarecon/";