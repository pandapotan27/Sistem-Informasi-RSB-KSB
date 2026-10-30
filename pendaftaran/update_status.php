<?php
session_start();

// Validasi Header Json & Koneksi
header('Content-Type: application/json');
require '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Pastikan request method adalah POST dan data tersedia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pendaftaran']) && isset($_POST['status'])) {
    
    $id_pendaftaran = mysqli_real_escape_string($conn, $_POST['id_pendaftaran']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Validasi agar status yang masuk hanya Open atau Cancel
    if (!in_array($status_baru, ['Open', 'Cancel'])) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid.']);
        exit;
    }

    // Eksekusi Update ke Database
    $query = "UPDATE pendaftaran SET status = '$status_baru' WHERE id_pendaftaran = '$id_pendaftaran'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update database: ' . mysqli_error($conn)]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request']);
}
?>