<?php
session_start();

require 'config/koneksi.php';

// Menangkap data dari form dan mencegah SQL Injection dasar
$nama = mysqli_real_escape_string($conn, $_POST['nama']);
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];
$konfirmasi_password = $_POST['konfirmasi_password'];

// 1. Cek apakah konfirmasi password sesuai
if($password !== $konfirmasi_password){
    header("Location: register.php?pesan=password_tidak_cocok");
    exit;
}

// 2. Cek apakah username sudah ada di database
$cek_username = mysqli_query($conn, "SELECT username FROM users WHERE username='$username'");
if(mysqli_num_rows($cek_username) > 0){
    header("Location: register.php?pesan=username_ada");
    exit;
}

// 3. Enkripsi password menggunakan MD5 agar sinkron dengan proses_login.php
$password_enkripsi = md5($password);

// 4. Masukkan data ke dalam database
$query_insert = "INSERT INTO users (nama, username, password) VALUES ('$nama', '$username', '$password_enkripsi')";
$simpan = mysqli_query($conn, $query_insert);

// 5. Redirect sesuai hasil query
if($simpan){
    // Jika berhasil, kembalikan ke halaman login dengan pesan sukses
    header("Location: login.php?pesan=berhasil_register");
} else {
    // Jika gagal insert ke database
    header("Location: register.php?pesan=gagal_register");
}
?>