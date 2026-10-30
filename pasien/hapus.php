<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$no_rm = mysqli_real_escape_string($conn,$_GET['no_rm']);

$query = mysqli_query($conn,"
DELETE FROM pasien
WHERE no_rm='$no_rm'
");

if($query){

    echo "
    <script>

    alert('Data pasien berhasil dihapus');

    window.location='index.php';

    </script>
    ";

}else{

    echo "
    <script>

    alert('Data gagal dihapus');

    window.location='index.php';

    </script>
    ";

}