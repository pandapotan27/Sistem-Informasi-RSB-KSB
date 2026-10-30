<?php

session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$id = $_GET['id'];

$hapus = mysqli_query($conn,"
DELETE FROM jadwal_dokter
WHERE id_jadwal='$id'
");

if($hapus){

    echo "
    <script>

    alert('Jadwal berhasil dihapus');

    window.location='index.php';

    </script>
    ";

}else{

    echo "
    <script>

    alert('Jadwal gagal dihapus');

    window.location='index.php';

    </script>
    ";

}