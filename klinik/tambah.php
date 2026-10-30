<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

// Ambil kode klinik terakhir
$query = mysqli_query($conn,"
SELECT kode_klinik
FROM klinik
ORDER BY id_klinik DESC 
LIMIT 1
");

$data = mysqli_fetch_assoc($query);

if(!$data){
    $kode_klinik = "KL01";
}else{
    $angka = (int) substr($data['kode_klinik'],2);
    $angka++;
    $kode_klinik = "KL".str_pad($angka,2,"0",STR_PAD_LEFT);
}

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Tambah Data Klinik</h3>
        <p class="text-muted small m-0">Daftarkan unit poliklinik baru ke dalam sistem rumah sakit</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="max-width: 650px;">
        <div class="card-header bg-white border-bottom border-secondary border-opacity-10 py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill text-primary"></i> Form Tambah Klinik
            </h5>
        </div>

        <div class="card-body p-4">
            <form action="proses_tambah.php" method="POST">

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Kode Klinik</label>
                    <input
                        type="text"
                        name="kode_klinik"
                        class="form-control form-control-lg rounded-3 fs-6"
                        value="<?= $kode_klinik; ?>"
                        style="background-color: #e2e8f0; border-color: #cbd5e1;"
                        readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Nama Klinik</label>
                    <input
                        type="text"
                        name="nama_klinik"
                        class="form-control form-control-lg rounded-3 fs-6"
                        placeholder="Masukkan nama klinik"
                        style="background-color: #f8fafc; border-color: #e2e8f0;"
                        required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Status</label>
                    <select name="status" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2 pt-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm fw-medium">
                        <i class="bi bi-save me-1"></i> Simpan Klinik
                    </button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill text-secondary fw-medium">
                        Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

<?php include '../layout/footer.php'; ?>