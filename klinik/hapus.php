<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// Cek apakah data ada
$cek = mysqli_query($conn, "SELECT * FROM klinik WHERE id_klinik = '$id'");

if (mysqli_num_rows($cek) == 0) {

    echo "
    <script>
        alert('Data klinik tidak ditemukan.');
        window.location='index.php';
    </script>
    ";

    exit;
}

// Hapus data
$hapus = mysqli_query($conn, "DELETE FROM klinik WHERE id_klinik='$id'");

if ($hapus) {

    echo "
    <script>
        alert('Data klinik berhasil dihapus.');
        window.location='index.php';
    </script>
    ";

} else {

    echo "
    <script>
        alert('Data klinik tidak dapat dihapus karena masih digunakan pada data lain.');
        window.location='index.php';
    </script>
    ";

}
?>