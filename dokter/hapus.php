<?php

session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$id = $_GET['id'];

$hapus = mysqli_query($conn,"
DELETE FROM dokter
WHERE id_dokter='$id'
");

if($hapus){

    echo "
    <script>
    alert('Data dokter berhasil dihapus');
    window.location='index.php';
    </script>
    ";

}else{

    echo "
    <script>
    alert('Dokter tidak dapat dihapus karena masih digunakan pada jadwal atau pendaftaran.');
    window.location='index.php';
    </script>
    ";

}