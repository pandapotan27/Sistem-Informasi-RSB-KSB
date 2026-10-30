<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

// PERBAIKAN: Ambil MURNI seluruh dokter yang aktif tanpa INNER JOIN ke jadwal
$dokter = mysqli_query($conn, "
    SELECT 
        id_dokter,
        kode_dokter,
        gelar_depan,
        nama_dokter,
        gelar_belakang
    FROM dokter
    WHERE status='Active'
    ORDER BY nama_dokter ASC
");

$klinik = mysqli_query($conn, "
    SELECT
        id_klinik,
        kode_klinik,
        nama_klinik
    FROM klinik
    WHERE status='Active'
    ORDER BY nama_klinik ASC
");

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Tambah Jadwal Dokter</h3>
        <p class="text-muted small m-0">Buat alokasi waktu praktik dan ruangan baru untuk dokter spesialis</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="max-width:650px;">

        <div class="card-header bg-white border-bottom border-secondary border-opacity-10 py-3 px-4">

            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">

                <i class="bi bi-plus-circle-fill text-primary"></i>

                Form Tambah Jadwal Dokter

            </h5>

        </div>

        <div class="card-body p-4">

            <form action="proses_tambah.php" method="POST">

                <div class="mb-4">

                    <label class="form-label fw-semibold text-secondary small text-uppercase">

                        Dokter

                    </label>

                    <select name="id_dokter"
                            class="form-select form-select-lg rounded-3 fs-6"
                            style="background-color:#f8fafc;border-color:#e2e8f0;"
                            required>

                        <option value="">-- Pilih Dokter --</option>

                        <?php while($d=mysqli_fetch_assoc($dokter)){ ?>

                        <option value="<?= $d['id_dokter']; ?>">

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

                    <select name="id_klinik"
                            class="form-select form-select-lg rounded-3 fs-6"
                            style="background-color:#f8fafc;border-color:#e2e8f0;"
                            required>

                        <option value="">-- Pilih Klinik --</option>

                        <?php while($k=mysqli_fetch_assoc($klinik)){ ?>

                        <option value="<?= $k['id_klinik']; ?>">

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
                            placeholder="Contoh : Poli 1"
                            style="background-color:#f8fafc;border-color:#e2e8f0;"
                            required>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary small text-uppercase">

                            Hari Praktik

                        </label>

                        <select name="hari"
                                class="form-select form-select-lg rounded-3 fs-6"
                                style="background-color:#f8fafc;border-color:#e2e8f0;">

                            <option>Senin</option>
                            <option>Selasa</option>
                            <option>Rabu</option>
                            <option>Kamis</option>
                            <option>Jumat</option>
                            <option>Sabtu</option>
                            <option>Minggu</option>

                        </select>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary small text-uppercase">

                            Jam Mulai

                        </label>

                        <input type="time"
                               name="jam_mulai"
                               class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                               style="background-color:#f8fafc;border-color:#e2e8f0;"
                               required>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary small text-uppercase">

                            Jam Selesai

                        </label>

                        <input type="time"
                               name="jam_selesai"
                               class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                               style="background-color:#f8fafc;border-color:#e2e8f0;"
                               required>

                    </div>

                </div>

                <div class="d-flex align-items-center gap-2 pt-2">

                    <button type="submit"
                            class="btn btn-primary px-4 rounded-pill shadow-sm fw-medium">

                        <i class="bi bi-save me-1"></i>

                        Simpan Jadwal

                    </button>

                    <a href="index.php"
                       class="btn btn-light border px-4 rounded-pill text-secondary fw-medium">

                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include '../layout/footer.php'; ?>