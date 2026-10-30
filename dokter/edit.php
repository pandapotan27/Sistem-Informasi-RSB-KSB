<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($conn,"SELECT * FROM dokter WHERE id_dokter='$id'");
$data = mysqli_fetch_assoc($query);

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Edit Data Dokter</h3>
        <p class="text-muted small m-0">Perbarui profil dan status praktik dokter spesialis</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="max-width: 650px;">
        <div class="card-header bg-white border-bottom border-secondary border-opacity-10 py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-warning"></i> Form Edit Dokter
            </h5>
        </div>

        <div class="card-body p-4">
            <form action="proses_edit.php" method="POST">

                <input type="hidden" name="id_dokter" value="<?= $data['id_dokter']; ?>">

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Kode Dokter</label>
                    <input
                        type="text"
                        class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                        value="<?= htmlspecialchars($data['kode_dokter']); ?>"
                        style="background-color: #e2e8f0; border-color: #cbd5e1; color: #475569;"
                        readonly>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Gelar Depan</label>
                        <input
                            type="text"
                            name="gelar_depan"
                            class="form-control form-control-lg rounded-3 fs-6"
                            value="<?= htmlspecialchars($data['gelar_depan']); ?>"
                            placeholder="Contoh: dr."
                            style="background-color: #f8fafc; border-color: #e2e8f0;">
                    </div>

                    <div class="col-md-8 mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Nama Dokter</label>
                        <input
                            type="text"
                            name="nama_dokter"
                            class="form-control form-control-lg rounded-3 fs-6"
                            value="<?= htmlspecialchars($data['nama_dokter']); ?>"
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
                        value="<?= htmlspecialchars($data['gelar_belakang']); ?>"
                        placeholder="Contoh: Sp.A"
                        style="background-color: #f8fafc; border-color: #e2e8f0;">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Spesialisasi</label>
                    <input
                        type="text"
                        name="spesialisasi"
                        class="form-control form-control-lg rounded-3 fs-6"
                        value="<?= htmlspecialchars($data['spesialisasi']); ?>"
                        style="background-color: #f8fafc; border-color: #e2e8f0;">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Status</label>
                    <select name="status" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;">
                        <option value="Active" <?= (strcasecmp(trim($data['status']), 'Active') === 0) ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?= (strcasecmp(trim($data['status']), 'Inactive') === 0) ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2 pt-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm fw-medium">
                        <i class="bi bi-save me-1"></i> Update Data
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