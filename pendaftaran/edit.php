<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$id = intval($_GET['id']);

$query_pendaftaran = mysqli_query($conn, "SELECT * FROM pendaftaran WHERE id_pendaftaran = '$id'");
$data = mysqli_fetch_assoc($query_pendaftaran);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

$pasien = mysqli_query($conn, "SELECT no_rm, nama_pasien FROM pasien ORDER BY nama_pasien ASC");
$klinik = mysqli_query($conn, "SELECT id_klinik, kode_klinik, nama_klinik FROM klinik WHERE status='Active' ORDER BY nama_klinik ASC");

$id_klinik_terpilih = $data['id_klinik'];
$dokter_terpilih = mysqli_query($conn, "
    SELECT DISTINCT d.id_dokter, d.kode_dokter, d.gelar_depan, d.nama_dokter, d.gelar_belakang
    FROM jadwal_dokter j
    INNER JOIN dokter d ON j.id_dokter = d.id_dokter
    WHERE j.id_klinik = '$id_klinik_terpilih' AND d.status='Active'
");

$id_dokter_terpilih = $data['id_dokter'];
$jadwal_terpilih = mysqli_query($conn, "
    SELECT id_jadwal, hari, jam_mulai, jam_selesai, room
    FROM jadwal_dokter
    WHERE id_klinik = '$id_klinik_terpilih' AND id_dokter = '$id_dokter_terpilih'
");

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Edit Pendaftaran Pasien</h3>
        <p class="text-muted small m-0">Perbarui informasi registrasi kunjungan, keluhan, atau jadwal periksa pasien</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom border-secondary border-opacity-10 py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-warning"></i>
                Form Edit Pendaftaran Pasien
            </h5>
        </div>

        <div class="card-body p-4">
            <form action="proses_edit.php?id=" method="POST" onsubmit="return confirm('Apakah Anda yakin untuk mengupdate data ini?');">
                <input type="hidden" name="id_pendaftaran" value="<?= $data['id_pendaftaran']; ?>">

                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">No Registrasi</label>
                            <input type="text" class="form-control rounded-3 fs-6" name="no_registrasi" value="<?= $data['no_registrasi']; ?>" style="background-color:#f1f5f9;" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Tanggal Kunjungan</label>
                            <input type="date" class="form-control rounded-3 fs-6" name="tanggal_kunjungan" id="tanggal_kunjungan" value="<?= $data['tanggal_kunjungan']; ?>" style="background-color:#f8fafc;border-color:#e2e8f0;" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Pasien</label>
                            <select name="no_rm" id="no_rm" class="form-select rounded-3 fs-6" style="background-color:#f8fafc;border-color:#e2e8f0;" required>
                                <option value="">-- Pilih Pasien --</option>
                                <?php while($p = mysqli_fetch_assoc($pasien)){ ?>
                                    <option value="<?= $p['no_rm']; ?>" <?= ($p['no_rm'] == $data['no_rm']) ? 'selected' : ''; ?>>
                                        <?= $p['no_rm']; ?> - <?= $p['nama_pasien']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Poli Tujuan</label>
                            <select name="id_klinik" id="id_klinik" class="form-select rounded-3 fs-6" style="background-color:#f8fafc;border-color:#e2e8f0;" required>
                                <option value="">-- Pilih Poli --</option>
                                <?php while($k = mysqli_fetch_assoc($klinik)){ ?>
                                    <option value="<?= $k['id_klinik']; ?>" <?= ($k['id_klinik'] == $data['id_klinik']) ? 'selected' : ''; ?>>
                                        <?= $k['kode_klinik']; ?> - <?= $k['nama_klinik']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Dokter</label>
                            <select name="id_dokter" id="id_dokter" class="form-select rounded-3 fs-6" style="background-color:#f8fafc;border-color:#e2e8f0;" required>
                                <option value="">-- Pilih Dokter --</option>
                                <?php while($d = mysqli_fetch_assoc($dokter_terpilih)){ 
                                    $gDepan = $d['gelar_depan'] ? $d['gelar_depan'] . ' ' : '';
                                    $gBelakang = $d['gelar_belakang'] ? ', ' . $d['gelar_belakang'] : '';
                                ?>
                                    <option value="<?= $d['id_dokter']; ?>" <?= ($d['id_dokter'] == $data['id_dokter']) ? 'selected' : ''; ?>>
                                        <?= $d['kode_dokter']; ?> - <?= $gDepan . $d['nama_dokter'] . $gBelakang; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Jadwal Praktik</label>
                            <select name="id_jadwal" id="id_jadwal" class="form-select rounded-3 fs-6" style="background-color:#f8fafc;border-color:#e2e8f0;" required>
                                <option value="">-- Pilih Jadwal --</option>
                                <?php while($j = mysqli_fetch_assoc($jadwal_terpilih)){ ?>
                                    <option value="<?= $j['id_jadwal']; ?>" <?= ($j['id_jadwal'] == $data['id_jadwal']) ? 'selected' : ''; ?>>
                                        <?= $j['hari']; ?> | <?= substr($j['jam_mulai'], 0, 5); ?> - <?= substr($j['jam_selesai'], 0, 5); ?> | Room <?= $j['room']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Nomor Antrian</label>
                            <input type="text" name="no_antrian" id="no_antrian" class="form-control rounded-3 fs-6" value="<?= $data['no_antrian']; ?>" style="background-color:#f1f5f9;" readonly>
                        </div>
                    </div>

                    <!-- KOLOM TANGGUNGAN BARU -->
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Tanggungan</label>
                            <select name="tanggungan" class="form-select rounded-3 fs-6" style="background-color:#f8fafc;border-color:#e2e8f0;" required>
                                <option value="Pribadi" <?= (isset($data['tanggungan']) && $data['tanggungan'] == 'Pribadi') ? 'selected' : ''; ?>>Pribadi</option>
                                <option value="BPJS" <?= (isset($data['tanggungan']) && $data['tanggungan'] == 'BPJS') ? 'selected' : ''; ?>>BPJS</option>
                                <option value="Rekanan" <?= (isset($data['tanggungan']) && $data['tanggungan'] == 'Rekanan') ? 'selected' : ''; ?>>Rekanan</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control rounded-3 fs-6" value="<?= htmlspecialchars($data['keterangan']); ?>" style="background-color:#f8fafc;border-color:#e2e8f0;" placeholder="Contoh : Pasien Baru">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Keluhan</label>
                            <textarea name="keluhan" rows="4" class="form-control rounded-3 fs-6" style="background-color:#f8fafc;border-color:#e2e8f0;" placeholder="Masukkan keluhan pasien..."><?= htmlspecialchars($data['keluhan']); ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-12 d-flex align-items-center gap-2 pt-2">
                        <button type="submit" class="btn btn-warning text-dark px-4 rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-save me-1"></i> Update
                        </button>
                        <a href="index.php" class="btn btn-light border px-4 rounded-pill text-secondary fw-medium"> Kembali </a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    let currentJadwal = "<?= $data['id_jadwal']; ?>";
    let currentTanggal = "<?= $data['tanggal_kunjungan']; ?>";
    let currentAntrian = "<?= $data['no_antrian']; ?>";

    $("#tanggal_kunjungan").change(function(){
        $("#id_jadwal").trigger('change');
    });

    // POLI -> DOKTER
    $("#id_klinik").change(function(){
        let id_klinik = $(this).val();
        $("#id_dokter").html('<option value="">Loading...</option>');
        $("#id_jadwal").html('<option value="">-- Pilih Jadwal --</option>');
        $("#no_antrian").val("");

        if(id_klinik === "") {
            $("#id_dokter").html('<option value="">-- Pilih Poli Terlebih Dahulu --</option>');
            return;
        }

        $.ajax({
            url: "get_dokter.php",
            type: "GET",
            data: { id_klinik: id_klinik },
            dataType: "json",
            success: function(data){
                let html = '<option value="">-- Pilih Dokter --</option>';
                if(data.length === 0){
                    html = '<option value="">-- Tidak ada dokter di Poli ini --</option>';
                } else {
                    $.each(data, function(i, row){
                        let gDepan = row.gelar_depan ? row.gelar_depan + ' ' : '';
                        let gBelakang = row.gelar_belakang ? ', ' + row.gelar_belakang : '';
                        html += `<option value="${row.id_dokter}">${row.kode_dokter} - ${gDepan}${row.nama_dokter}${gBelakang}</option>`;
                    });
                }
                $("#id_dokter").html(html);
            }
        });
    });

    // DOKTER -> JADWAL
    $("#id_dokter").change(function(){
        let id_klinik = $("#id_klinik").val();
        let id_dokter = $(this).val();
        $("#id_jadwal").html('<option value="">Loading...</option>');
        $("#no_antrian").val("");

        if(id_dokter === "") {
            $("#id_jadwal").html('<option value="">-- Pilih Jadwal --</option>');
            return;
        }

        $.ajax({
            url: "get_jadwal.php",
            type: "GET",
            data: { id_klinik: id_klinik, id_dokter: id_dokter },
            dataType: "json",
            success: function(data){
                let html = '<option value="">-- Pilih Jadwal --</option>';
                if(data.length === 0){
                    html = '<option value="">-- Jadwal tidak ditemukan --</option>';
                } else {
                    $.each(data, function(i, row){
                        html += `<option value="${row.id_jadwal}">${row.hari} | ${row.jam_mulai.substring(0,5)} - ${row.jam_selesai.substring(0,5)} | Room ${row.room}</option>`;
                    });
                }
                $("#id_jadwal").html(html);
            }
        });
    });

    // JADWAL -> NO ANTRIAN
    $("#id_jadwal").change(function(){
        let id_jadwal = $(this).val();
        let tgl_kunjungan = $("#tanggal_kunjungan").val();

        if(id_jadwal === "" || tgl_kunjungan === ""){
            $("#no_antrian").val("");
            return;
        }

        if(id_jadwal === currentJadwal && tgl_kunjungan === currentTanggal) {
            $("#no_antrian").val(currentAntrian);
            return;
        }

        $.ajax({
            url: "get_antrian.php",
            type: "GET",
            data: { id_jadwal: id_jadwal, tanggal_kunjungan: tgl_kunjungan },
            dataType: "json",
            success: function(data){
                $("#no_antrian").val(data.no_antrian);
            }
        });
    });
});
</script>

<?php include '../layout/footer.php'; ?>