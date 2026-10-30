<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$no_rm           = mysqli_real_escape_string($conn, $_POST['no_rm']);
$nik             = mysqli_real_escape_string($conn, $_POST['nik']);
$nama_pasien     = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
$tempat_lahir    = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
$tanggal_lahir   = $_POST['tanggal_lahir'];
$jenis_kelamin   = $_POST['jenis_kelamin'];
$id_kodepos      = $_POST['id_kodepos'];
$alamat_detail   = mysqli_real_escape_string($conn, $_POST['alamat_detail']);
$no_hp           = mysqli_real_escape_string($conn, $_POST['no_hp']);

// 1. Validasi NIK
$cek = mysqli_query($conn, "SELECT * FROM pasien WHERE nik='$nik'");
if(mysqli_num_rows($cek) > 0){
    echo "
    <script>
        alert('NIK sudah terdaftar!');
        window.history.back();
    </script>
    ";
    exit;
}

// 2. Proses Upload Foto
$nama_file_foto = NULL; // Nilai default jika tidak ada foto yang diupload

if (isset($_FILES['foto_pasien']) && $_FILES['foto_pasien']['error'] === 0) {
    $file_tmp   = $_FILES['foto_pasien']['tmp_name'];
    $nama_asli  = $_FILES['foto_pasien']['name'];
    $ukuran     = $_FILES['foto_pasien']['size'];
    
    // Ambil ekstensi file
    $x = explode('.', $nama_asli);
    $ekstensi = strtolower(end($x));
    $ekstensi_diperbolehkan = ['jpg', 'jpeg', 'png'];
    
    // Validasi Ekstensi
    if (!in_array($ekstensi, $ekstensi_diperbolehkan)) {
        echo "<script>alert('Gagal! Ekstensi foto hanya boleh JPG, JPEG, atau PNG.'); window.history.back();</script>";
        exit;
    }
    
    // Validasi Ukuran (Maksimal 2MB = 2097152 Bytes)
    if ($ukuran > 2097152) {
        echo "<script>alert('Gagal! Ukuran foto maksimal 2MB.'); window.history.back();</script>";
        exit;
    }
    
    // Generate nama file baru yang unik (Format: RMXXXXXX_timestamp.jpg)
    $nama_file_foto = $no_rm . '_' . time() . '.' . $ekstensi;
    $folder_tujuan = '../uploads/pasien/';
    
    // Cek apakah folder uploads/pasien sudah ada, jika belum buat otomatis
    if (!is_dir($folder_tujuan)) {
        mkdir($folder_tujuan, 0777, true);
    }
    
    // Pindahkan file ke folder tujuan
    move_uploaded_file($file_tmp, $folder_tujuan . $nama_file_foto);
}

// 3. Simpan Data ke Database
// Menyiapkan variabel foto untuk query (jika null, biarkan NULL tanpa tanda kutip)
$val_foto = $nama_file_foto ? "'$nama_file_foto'" : "NULL";

$query = mysqli_query($conn,"
INSERT INTO pasien
(
    no_rm,
    nik,
    nama_pasien,
    tempat_lahir,
    tanggal_lahir,
    jenis_kelamin,
    foto,
    id_kodepos,
    alamat_detail,
    no_hp
)
VALUES
(
    '$no_rm',
    '$nik',
    '$nama_pasien',
    '$tempat_lahir',
    '$tanggal_lahir',
    '$jenis_kelamin',
    $val_foto,
    '$id_kodepos',
    '$alamat_detail',
    '$no_hp'
)
");

if($query){
    echo "
    <script>
        alert('Data pasien berhasil ditambahkan');
        window.location='index.php';
    </script>
    ";
}else{
    // Jika query gagal tapi foto sudah terlanjur terupload, hapus fotonya
    if($nama_file_foto && file_exists('../uploads/pasien/' . $nama_file_foto)){
        unlink('../uploads/pasien/' . $nama_file_foto);
    }
    
    echo "
    <script>
        alert('Data gagal disimpan');
        window.history.back();
    </script>
    ";
}
?>