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

// Tangkap nama foto lama dari form hidden
$foto_lama       = $_POST['foto_lama']; 

// =========================
// 1. Cek NIK apakah dipakai pasien lain
// =========================
$cek = mysqli_query($conn,"
SELECT *
FROM pasien
WHERE nik='$nik'
AND no_rm<>'$no_rm'
");

if(mysqli_num_rows($cek)>0){
    echo "
    <script>
        alert('NIK sudah digunakan pasien lain!');
        window.history.back();
    </script>
    ";
    exit;
}

// =========================
// 2. Proses Upload Foto Baru (Jika Ada)
// =========================
$nama_file_foto = $foto_lama; // Default: gunakan nama foto lama

// Cek apakah user mengupload file foto baru
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
    
    // Validasi Ukuran (Maks 2MB = 2097152 Bytes)
    if ($ukuran > 2097152) {
        echo "<script>alert('Gagal! Ukuran foto maksimal 2MB.'); window.history.back();</script>";
        exit;
    }
    
    // Generate nama file baru
    $nama_file_baru = $no_rm . '_' . time() . '.' . $ekstensi;
    $folder_tujuan = '../uploads/pasien/';
    
    // Cek folder, buat jika belum ada
    if (!is_dir($folder_tujuan)) {
        mkdir($folder_tujuan, 0777, true);
    }
    
    // Pindahkan foto baru ke folder
    if (move_uploaded_file($file_tmp, $folder_tujuan . $nama_file_baru)) {
        // Jika berhasil upload foto baru, HAPUS foto lama dari server agar tidak nyampah
        if (!empty($foto_lama) && file_exists($folder_tujuan . $foto_lama)) {
            unlink($folder_tujuan . $foto_lama);
        }
        
        // Update variabel nama_file_foto untuk query database
        $nama_file_foto = $nama_file_baru;
    }
}

// =========================
// 3. Update Data Database
// =========================

// Siapkan nilai untuk kolom foto (handle jika NULL)
$val_foto = $nama_file_foto ? "'$nama_file_foto'" : "NULL";

$query = mysqli_query($conn,"
UPDATE pasien
SET
    nik='$nik',
    nama_pasien='$nama_pasien',
    tempat_lahir='$tempat_lahir',
    tanggal_lahir='$tanggal_lahir',
    jenis_kelamin='$jenis_kelamin',
    foto=$val_foto,
    id_kodepos='$id_kodepos',
    alamat_detail='$alamat_detail',
    no_hp='$no_hp'
WHERE no_rm='$no_rm'
");

if($query){
    echo "
    <script>
    alert('Data pasien berhasil diupdate');
    window.location='index.php';
    </script>
    ";
}else{
    // Jika query gagal tapi terlanjur upload foto baru, batalkan (hapus foto barunya)
    if (isset($nama_file_baru) && file_exists('../uploads/pasien/' . $nama_file_baru)) {
        unlink('../uploads/pasien/' . $nama_file_baru);
    }

    echo "
    <script>
    alert('Data gagal diupdate');
    window.history.back();
    </script>
    ";
}
?>