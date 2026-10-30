<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

// 1. Ambil data untuk filter dropdown Klinik
$klinik = mysqli_query($conn, "SELECT id_klinik, kode_klinik, nama_klinik FROM klinik WHERE status='Active' ORDER BY nama_klinik ASC");

// 2. PERBAIKAN: Ambil data dokter beserta id_klinik dari tabel jadwal_dokter menggunakan INNER JOIN
$dokter = mysqli_query($conn, "
    SELECT DISTINCT
        d.id_dokter,
        jd.id_klinik,
        d.kode_dokter,
        d.gelar_depan,
        d.nama_dokter,
        d.gelar_belakang
    FROM dokter d
    INNER JOIN jadwal_dokter jd ON d.id_dokter = jd.id_dokter
    WHERE d.status = 'Active'
    ORDER BY d.nama_dokter ASC
");

// Inisialisasi parameter filter dari URL (jika ada)
$tgl_mulai   = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01'); // Awal bulan ini
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-d'); // Hari ini
$status      = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$id_klinik   = isset($_GET['id_klinik']) ? intval($_GET['id_klinik']) : '';
$id_dokter   = isset($_GET['id_dokter']) ? intval($_GET['id_dokter']) : '';
$tanggungan  = isset($_GET['tanggungan']) ? mysqli_real_escape_string($conn, $_GET['tanggungan']) : ''; // FILTER TANGGUNGAN BARU
$f_spes      = isset($_GET['spesialisasi']) ? mysqli_real_escape_string($conn, $_GET['spesialisasi']) : '';

// Bangun Query Utama Berdasarkan Filter
$sql = "SELECT 
            p.*, 
            pas.nama_pasien, 
            k.nama_klinik, 
            d.gelar_depan, d.nama_dokter, d.gelar_belakang,
            j.hari, j.jam_mulai, j.jam_selesai
        FROM pendaftaran p
        INNER JOIN pasien pas ON p.no_rm = pas.no_rm
        INNER JOIN klinik k ON p.id_klinik = k.id_klinik
        INNER JOIN dokter d ON p.id_dokter = d.id_dokter
        INNER JOIN jadwal_dokter j ON p.id_jadwal = j.id_jadwal
        WHERE p.tanggal_kunjungan BETWEEN '$tgl_mulai' AND '$tgl_selesai'";

if ($status != '') {
    $sql .= " AND p.status = '$status'";
}
if ($id_klinik > 0) {
    $sql .= " AND p.id_klinik = '$id_klinik'";
}
if ($id_dokter > 0) {
    $sql .= " AND p.id_dokter = '$id_dokter'";
}
if ($tanggungan != '') {
    $sql .= " AND p.tanggungan = '$tanggungan'";
}

$sql .= " ORDER BY p.tanggal_kunjungan ASC, p.no_antrian ASC";
$laporan = mysqli_query($conn, $sql);

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">
    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Laporan Pendaftaran Pasien</h3>
        <p class="text-muted small m-0">Filter dan pantau data pendaftaran pasien per klinik serta dokter secara berkala</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded">
            <form method="GET" action="" class="row g-3 align-items-end">
                
                <!-- ROW 1 : Tanggal, Status, Tanggungan -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Mulai Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai; ?>" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai; ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Status Laporan</label>
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="Open" <?= ($status == 'Open') ? 'selected' : ''; ?>>Open</option>
                        <option value="Cancel" <?= ($status == 'Cancel') ? 'selected' : ''; ?>>Cancel</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Tanggungan</label>
                    <select name="tanggungan" class="form-select">
                        <option value="">-- Semua Tanggungan --</option>
                        <option value="Pribadi" <?= ($tanggungan == 'Pribadi') ? 'selected' : ''; ?>>Pribadi</option>
                        <option value="BPJS" <?= ($tanggungan == 'BPJS') ? 'selected' : ''; ?>>BPJS</option>
                        <option value="Rekanan" <?= ($tanggungan == 'Rekanan') ? 'selected' : ''; ?>>Rekanan</option>
                    </select>
                </div>

                <!-- ROW 2 : Klinik, Dokter, Tombol -->
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1">Klinik / Poli</label>
                    <select name="id_klinik" id="id_klinik" class="form-select">
                        <option value="">-- Semua Klinik --</option>
                        <?php while($k = mysqli_fetch_assoc($klinik)){ ?>
                            <option value="<?= $k['id_klinik']; ?>" <?= ($id_klinik == $k['id_klinik']) ? 'selected' : ''; ?>>
                                <?= $k['kode_klinik']; ?> - <?= $k['nama_klinik']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1">Nama Dokter</label>
                    <select name="id_dokter" id="id_dokter" class="form-select">
                        <option value="">-- Pilih Klinik Terlebih Dahulu --</option>
                        <?php 
                        while($d = mysqli_fetch_assoc($dokter)){ 
                            // Logika agar saat di-search, pilihan dokter tidak reset
                            $selected_dokter = ($id_dokter == $d['id_dokter']) ? 'selected' : '';
                        ?>
                            <option value="<?= $d['id_dokter']; ?>" class="opsi-dokter" data-klinik="<?= $d['id_klinik']; ?>" <?= $selected_dokter; ?>>
                                <?= $d['kode_dokter']; ?> - <?= trim($d['gelar_depan'] . ' ' . $d['nama_dokter'] . ' ' . $d['gelar_belakang']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="index.php" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="card overflow-hidden border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-white small text-uppercase fw-bold text-center" style="background: #0d6efd !important;">
                        <tr>
                            <th class="py-3" width="50">No</th>
                            <th class="py-3 text-nowrap">No. Reg</th>
                            <th class="py-3 text-nowrap">Tgl Kunjungan</th>
                            <th class="py-3 text-nowrap">No. RM</th>
                            <th class="py-3 min-vw-25 text-nowrap">Nama Pasien</th>
                            <th class="py-3 text-nowrap">Klinik / Poli</th>
                            <th class="py-3 text-nowrap">Dokter</th>
                            <th class="py-3" width="90">Antrian</th>
                            <th class="py-3 text-nowrap">Waktu</th>
                            <th class="py-3 text-nowrap">Tanggungan</th> <!-- HEADER BARU -->
                            <th class="py-3">Keterangan</th>
                            <th class="py-3 text-nowrap" width="130">Status</th> 
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($laporan) > 0) {
                            while($row = mysqli_fetch_assoc($laporan)){ 
                                $gDepan = $row['gelar_depan'] ? $row['gelar_depan'] . ' ' : '';
                                $gBelakang = $row['gelar_belakang'] ? ', ' . $row['gelar_belakang'] : '';
                                
                                $rowStatus = $row['status'] ?? 'Open';
                                $badgeClass = ($rowStatus == 'Open') ? 'bg-success' : 'bg-danger';
                        ?>
                            <tr>
                                <td class="text-center fw-medium text-secondary"><?= $no++; ?></td>
                                
                                <td class="text-center text-nowrap">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-2 py-1.5 font-monospace fw-semibold">
                                        <?= $row['no_registrasi']; ?>
                                    </span>
                                </td>
                                
                                <td class="text-center text-dark fw-medium font-monospace text-nowrap">
                                    <?= date('d-m-Y', strtotime($row['tanggal_kunjungan'])); ?>
                                </td>
                                
                                <td class="text-center text-nowrap">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 px-2 py-1.5 font-monospace">
                                        <?= $row['no_rm']; ?>
                                    </span>
                                </td>
                                
                                <td class="fw-semibold text-dark ps-3 text-nowrap"><?= $row['nama_pasien']; ?></td>
                                
                                <td class="text-secondary fw-medium ps-3 text-nowrap"><?= $row['nama_klinik']; ?></td>
                                
                                <td class="text-secondary small ps-3 text-nowrap">
                                    <span class="fw-medium text-dark"><?= trim($gDepan . $row['nama_dokter'] . $gBelakang); ?></span>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge bg-secondary text-white rounded-pill px-2.5 py-2 font-monospace fs-6 fw-bold shadow-sm" style="min-width: 40px;">
                                        <?= $row['no_antrian']; ?>
                                    </span>
                                </td>

                                <td class="text-center text-nowrap text-muted font-monospace small">
                                    <?php 
                                    if(!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) {
                                        echo date('H:i', strtotime($row['jam_mulai'])) . ' - ' . date('H:i', strtotime($row['jam_selesai']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>

                                <!-- KOLOM TANGGUNGAN BARU -->
                                <td class="text-center">
                                    <?php 
                                    $warna_tanggungan = 'bg-secondary';
                                    $val_tanggungan = !empty($row['tanggungan']) ? $row['tanggungan'] : 'Pribadi';
                                    
                                    if($val_tanggungan == 'BPJS') $warna_tanggungan = 'bg-success';
                                    else if($val_tanggungan == 'Pribadi') $warna_tanggungan = 'bg-primary';
                                    else if($val_tanggungan == 'Rekanan') $warna_tanggungan = 'bg-info text-dark';
                                    ?>
                                    <span class="badge <?= $warna_tanggungan; ?> px-2 py-1">
                                        <?= $val_tanggungan; ?>
                                    </span>
                                </td>
                                
                                <td class="ps-3 text-muted small">
                                    <?= !empty($row['keterangan']) ? htmlspecialchars($row['keterangan']) : '-'; ?>
                                </td>
                                
                                <td class="text-center text-nowrap">
                                    <span class="badge <?= $badgeClass; ?> px-3 py-2 rounded-pill fw-bold w-100 shadow-sm">
                                        <?= $rowStatus; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            } 
                        } else { 
                        ?>
                            <tr>
                                <!-- Disesuaikan menjadi colspan="12" karena ada tambahan 1 kolom -->
                                <td colspan="12" class="text-center text-muted py-5">
                                    <i class="bi bi-folder-x display-6"></i>
                                    <p class="mt-2 small">Tidak ada data pendaftaran yang sesuai dengan filter kriteria.</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    
    // Fungsi untuk menampilkan dokter berdasarkan klinik
    function filterDokter(idKlinik) {
        if(idKlinik !== ''){
            $('#id_dokter option:first').text('-- Semua Dokter di Poli Ini --');
            $('.opsi-dokter').hide();
            $('.opsi-dokter[data-klinik="'+idKlinik+'"]').show();
        } else {
            $('#id_dokter option:first').text('-- Pilih Klinik Terlebih Dahulu --');
            $('.opsi-dokter').hide();
        }
    }

    // 1. Jalankan saat halaman di-load
    let currentKlinik = $('#id_klinik').val();
    if (currentKlinik === '') {
        $('.opsi-dokter').hide();
    } else {
        filterDokter(currentKlinik);
    }

    // 2. Jalankan saat dropdown klinik diubah
    $('#id_klinik').change(function(){
        let idKlinik = $(this).val();
        $('#id_dokter').val('');
        filterDokter(idKlinik);
    });

});
</script>

<?php include '../layout/footer.php'; ?>