<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$id = $_GET['id'];

$jadwal = mysqli_query($conn,"
SELECT *
FROM jadwal_dokter
WHERE id_jadwal='$id'
");

$data = mysqli_fetch_assoc($jadwal);

$dokter = mysqli_query($conn,"
SELECT *
FROM dokter
WHERE status='Active'
ORDER BY nama_dokter
");

$klinik = mysqli_query($conn,"
SELECT
    id_klinik,
    kode_klinik,
    nama_klinik
FROM klinik
WHERE status='Active'
ORDER BY nama_klinik
");

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Edit Jadwal Dokter</h3>
        <p class="text-muted small m-0">Perbarui pengaturan waktu praktik dan lokasi room dokter</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="max-width: 650px;">
        <div class="card-header bg-white border-bottom border-secondary border-opacity-10 py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-warning"></i>
                Form Edit Jadwal Dokter
            </h5>
        </div>

        <div class="card-body p-4">

            <form action="proses_edit.php" method="POST">

                <input type="hidden" name="id_jadwal" value="<?= $data['id_jadwal']; ?>">

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">
                        Dokter
                    </label>

                    <select
                        name="id_dokter"
                        class="form-select form-select-lg rounded-3 fs-6"
                        style="background-color:#f8fafc;border-color:#e2e8f0;"
                        required>

                        <?php while($d=mysqli_fetch_assoc($dokter)){ ?>

                        <option
                            value="<?= $d['id_dokter']; ?>"
                            <?= ($d['id_dokter']==$data['id_dokter']) ? 'selected' : ''; ?>>

                            <?= $d['kode_dokter']; ?>
                            -
                            <?= $d['gelar_depan']; ?>
                            <?= $d['nama_dokter']; ?>
                            <?= $d['gelar_belakang']; ?>

                        </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="mb-4">

                    <label class="form-label fw-semibold text-secondary small text-uppercase">
                        Klinik
                    </label>

                    <select
                        name="id_klinik"
                        class="form-select form-select-lg rounded-3 fs-6"
                        style="background-color:#f8fafc;border-color:#e2e8f0;"
                        required>

                        <?php while($k=mysqli_fetch_assoc($klinik)){ ?>

                        <option
                            value="<?= $k['id_klinik']; ?>"
                            <?= ($k['id_klinik']==$data['id_klinik']) ? 'selected' : ''; ?>>

                            <?= $k['kode_klinik']; ?>
                            -
                            <?= $k['nama_klinik']; ?>

                        </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary small text-uppercase">
                            Room / Ruangan
                        </label>

                        <input
                            type="text"
                            name="room"
                            class="form-control form-control-lg rounded-3 fs-6"
                            value="<?= htmlspecialchars($data['room']); ?>"
                            placeholder="Contoh: Poli 1"
                            style="background-color:#f8fafc;border-color:#e2e8f0;"
                            required>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary small text-uppercase">
                            Hari Praktik
                        </label>

                        <select
                            name="hari"
                            class="form-select form-select-lg rounded-3 fs-6"
                            style="background-color:#f8fafc;border-color:#e2e8f0;">

                            <?php
                            $hari = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

                            foreach($hari as $h){
                            ?>

                            <option
                                value="<?= $h; ?>"
                                <?= ($h==$data['hari']) ? 'selected' : ''; ?>>

                                <?= $h; ?>

                            </option>

                            <?php } ?>

                        </select>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary small text-uppercase">
                            Jam Mulai
                        </label>

                        <input
                            type="time"
                            name="jam_mulai"
                            class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                            value="<?= $data['jam_mulai']; ?>"
                            style="background-color:#f8fafc;border-color:#e2e8f0;"
                            required>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary small text-uppercase">
                            Jam Selesai
                        </label>

                        <input
                            type="time"
                            name="jam_selesai"
                            class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                            value="<?= $data['jam_selesai']; ?>"
                            style="background-color:#f8fafc;border-color:#e2e8f0;"
                            required>

                    </div>

                </div>

                <div class="d-flex align-items-center gap-2 pt-2">

                    <button
                        type="submit"
                        class="btn btn-primary px-4 rounded-pill shadow-sm fw-medium">

                        <i class="bi bi-save me-1"></i>
                        Update Jadwal

                    </button>

                    <a
                        href="index.php"
                        class="btn btn-light border px-4 rounded-pill text-secondary fw-medium">

                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include '../layout/footer.php'; ?>