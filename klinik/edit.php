<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM klinik WHERE id_klinik='$id'");
$data = mysqli_fetch_assoc($query);

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Edit Data Klinik</h3>
        <p class="text-muted small m-0">Perbarui informasi unit poliklinik rumah sakit</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="max-width: 600px;">
        <div class="card-header bg-white border-bottom border-secondary border-opacity-10 py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-warning"></i> Form Edit Klinik
            </h5>
        </div>

        <div class="card-body p-4">
            <form action="proses_edit.php" method="POST">

                <input type="hidden" name="id_klinik" value="<?= $data['id_klinik']; ?>">

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">
                        Kode Klinik
                    </label>

                    <input
                        type="text"
                        class="form-control form-control-lg rounded-3 fs-6"
                        value="<?= htmlspecialchars($data['kode_klinik']); ?>"
                        style="background-color: #f8fafc; border-color: #e2e8f0;"
                        readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">
                        Nama Klinik / Poli
                    </label>

                    <input
                        type="text"
                        name="nama_klinik"
                        class="form-control form-control-lg rounded-3 fs-6"
                        value="<?= htmlspecialchars($data['nama_klinik']); ?>"
                        style="border-color: #e2e8f0;"
                        placeholder="Masukkan nama poli"
                        required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Status</label>
                    <select name="status" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;">
                        <option value="Active" <?= (strcasecmp(trim($data['status']), 'Active') === 0) ? 'selected' : ''; ?>>
                            Active
                        </option>
                        <option value="Inactive" <?= (strcasecmp(trim($data['status']), 'Inactive') === 0) ? 'selected' : ''; ?>>
                            Inactive
                        </option>
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