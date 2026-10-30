<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

// Panggil koneksi database
require '../config/koneksi.php';

// Ambil parameter 'id' dari URL dan amankan dengan mysqli_real_escape_string
// Menggunakan $_GET['id'] karena pada index.php linknya adalah href="hapus.php?id=..."
$id_pendaftaran = mysqli_real_escape_string($conn, $_GET['id']);

// Jalankan query DELETE untuk tabel pendaftaran
$query = mysqli_query($conn, "
    DELETE FROM pendaftaran 
    WHERE id_pendaftaran = '$id_pendaftaran'
");

// Cek apakah proses hapus berhasil
if($query){
    echo "
    <script>
        alert('Data pendaftaran berhasil dihapus');
        window.location='index.php';
    </script>
    ";
} else {
    echo "
    <script>
        alert('Data pendaftaran gagal dihapus');
        window.location='index.php';
    </script>
    ";
}
?>