<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Tambah Data Dokter</h3>
        <p class="text-muted small m-0">Daftarkan dokter spesialis baru ke dalam sistem rumah sakit</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="max-width: 650px;">
        <div class="card-header bg-white border-bottom border-secondary border-opacity-10 py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill text-primary"></i> Form Tambah Dokter
            </h5>
        </div>

        <div class="card-body p-4">
            <form action="proses_tambah.php" method="POST">

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Gelar Depan</label>
                        <input
                            type="text"
                            name="gelar_depan"
                            class="form-control form-control-lg rounded-3 fs-6"
                            placeholder="Contoh: dr."
                            style="background-color: #f8fafc; border-color: #e2e8f0;">
                    </div>

                    <div class="col-md-8 mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Nama Dokter</label>
                        <input
                            type="text"
                            name="nama_dokter"
                            class="form-control form-control-lg rounded-3 fs-6"
                            placeholder="Masukkan nama tanpa gelar"
                            style="background-color: #f8fafc; border-color: #e2e8f0;"
                            required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Gelar Belakang</label>
                    <input
                        type="text"
                        name="gelar_belakang"
                        class="form-control form-control-lg rounded-3 fs-6"
                        placeholder="Contoh: Sp.A"
                        style="background-color: #f8fafc; border-color: #e2e8f0;">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Spesialisasi</label>
                    <input
                        type="text"
                        name="spesialisasi"
                        class="form-control form-control-lg rounded-3 fs-6"
                        placeholder="Contoh: Anak / Kebidanan & Kandungan"
                        style="background-color: #f8fafc; border-color: #e2e8f0;">
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
                        <i class="bi bi-save me-1"></i> Simpan Dokter
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