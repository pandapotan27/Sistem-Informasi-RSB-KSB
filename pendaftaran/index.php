<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

// 1. Ambil data terpadu untuk Filter Dropdown (Mengambil riwayat riil dari pendaftaran)
$q_filter = mysqli_query($conn, "
    SELECT DISTINCT 
        k.nama_klinik, 
        ps.no_rm, 
        ps.nama_pasien
    FROM pendaftaran p
    INNER JOIN pasien ps ON p.no_rm = ps.no_rm
    INNER JOIN klinik k ON p.id_klinik = k.id_klinik
");

// Susun dan kelompokkan data (karena 1 RM bisa ke banyak klinik)
$list_poli = []; $list_rm = []; $list_nama = [];
while($r = mysqli_fetch_assoc($q_filter)){
    $poli = $r['nama_klinik'];
    $rm = $r['no_rm'];
    $nama = $r['nama_pasien'];

    // Simpan Poli
    if(!empty($poli)) $list_poli[$poli] = $poli;

    // Simpan relasi RM dengan Poli & Nama
    if(!empty($rm)) {
        if(!isset($list_rm[$rm])) $list_rm[$rm] = ['nama' => $nama, 'poli' => []];
        if(!in_array($poli, $list_rm[$rm]['poli'])) $list_rm[$rm]['poli'][] = $poli;
    }

    // Simpan relasi Nama dengan Poli & RM
    if(!empty($nama)) {
        if(!isset($list_nama[$nama])) $list_nama[$nama] = ['rm' => $rm, 'poli' => []];
        if(!in_array($poli, $list_nama[$nama]['poli'])) $list_nama[$nama]['poli'][] = $poli;
    }
}

// Urutkan alfabet
ksort($list_poli); ksort($list_rm); ksort($list_nama); 

// 2. Ambil parameter filter dari URL
$f_poli       = isset($_GET['poli']) ? mysqli_real_escape_string($conn, $_GET['poli']) : '';
$f_norm       = isset($_GET['no_rm']) ? mysqli_real_escape_string($conn, $_GET['no_rm']) : '';
$f_nama       = isset($_GET['nama_pasien']) ? mysqli_real_escape_string($conn, $_GET['nama_pasien']) : '';
$f_tanggungan = isset($_GET['tanggungan']) ? mysqli_real_escape_string($conn, $_GET['tanggungan']) : '';

// 3. Query Utama dengan Filter
$sql = "
SELECT 
    p.id_pendaftaran, 
    p.no_registrasi, 
    p.tanggal_kunjungan, 
    p.no_antrian, 
    p.tanggungan, 
    ps.no_rm, 
    ps.nama_pasien, 
    ps.jenis_kelamin, 
    ps.tanggal_lahir, 
    k.kode_klinik, 
    k.nama_klinik, 
    d.kode_dokter, 
    d.gelar_depan, 
    d.nama_dokter, 
    d.gelar_belakang, 
    p.keluhan, 
    p.keterangan, 
    p.status,
    jd.jam_mulai,
    jd.jam_selesai
FROM pendaftaran p
LEFT JOIN pasien ps ON p.no_rm = ps.no_rm
LEFT JOIN klinik k ON p.id_klinik = k.id_klinik
LEFT JOIN dokter d ON p.id_dokter = d.id_dokter
LEFT JOIN jadwal_dokter jd ON p.id_jadwal = jd.id_jadwal 
WHERE 1=1
";

// Kondisi filter
if ($f_poli != '') {
    $sql .= " AND k.nama_klinik = '$f_poli'";
}
if ($f_norm != '') {
    $sql .= " AND ps.no_rm = '$f_norm'";
}
if ($f_nama != '') {
    $sql .= " AND ps.nama_pasien LIKE '%$f_nama%'";
}
if ($f_tanggungan != '') {
    $sql .= " AND p.tanggungan = '$f_tanggungan'";
}

$sql .= " ORDER BY p.tanggal_kunjungan DESC, p.no_antrian ASC";
$query = mysqli_query($conn, $sql);

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-dark m-0">Data Pendaftaran Pasien</h3>
            <p class="text-muted small m-0">Log antrian transaksi pendaftaran rawat jalan</p>
        </div>
        <a href="tambah.php" class="btn btn-primary px-3 rounded-pill shadow-sm fw-medium d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Pendaftaran Baru
        </a>
    </div>

    <!-- BAGIAN FILTER -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded">
            <form method="GET" class="row g-2 align-items-end">
                
                <!-- 1. Klinik/Poli (Lebar diubah jadi col-md-2) -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary mb-1">Klinik/Poli</label>
                    <select name="poli" id="filter_poli" class="form-select">
                        <option value="">-- Semua --</option>
                        <?php foreach($list_poli as $p) { ?>
                            <option value="<?= $p; ?>" <?= $f_poli == $p ? 'selected' : ''; ?>>
                                <?= $p; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- 2. No RM (Lebar diubah jadi col-md-2) -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary mb-1">No RM</label>
                    <select name="no_rm" id="filter_rm" class="form-select">
                        <option value="">-- Semua --</option>
                        <?php foreach($list_rm as $rm => $data) { 
                            $str_poli = '|' . implode('|', $data['poli']) . '|';
                        ?>
                            <option value="<?= $rm; ?>" class="opsi-rm" data-poli="<?= $str_poli; ?>" data-nama="<?= $data['nama']; ?>" <?= $f_norm == $rm ? 'selected' : ''; ?>>
                                <?= $rm; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- 3. Nama Pasien -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Nama Pasien</label>
                    <input type="text" name="nama_pasien" id="filter_nama" class="form-control" placeholder="Masukan nama..." value="<?= $f_nama; ?>">
                </div>

                <!-- 4. FILTER TANGGUNGAN BARU -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary mb-1">Tanggungan</label>
                    <select name="tanggungan" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="Pribadi" <?= $f_tanggungan == 'Pribadi' ? 'selected' : ''; ?>>Pribadi</option>
                        <option value="BPJS" <?= $f_tanggungan == 'BPJS' ? 'selected' : ''; ?>>BPJS</option>
                        <option value="Rekanan" <?= $f_tanggungan == 'Rekanan' ? 'selected' : ''; ?>>Rekanan</option>
                    </select>
                </div>

                <!-- TOMBOL -->
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="index.php" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
    <!-- AKHIR BAGIAN FILTER -->


    <!-- BAGIAN TABEL -->
    <div class="card overflow-hidden border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-white small text-uppercase fw-bold text-center" style="background: #0d6efd !important;">
                        <tr>
                            <th class="py-3" width="50">No</th>
                            <th class="py-3 text-nowrap">No Registrasi</th>
                            <th class="py-3 text-nowrap">Tanggal</th> 
                            <th class="py-3 text-nowrap">No RM</th>
                            <th class="py-3 min-vw-25">Nama Pasien</th>
                            <th class="py-3">Tanggungan</th>
                            <th class="py-3" width="70">JK</th>
                            <th class="py-3 text-nowrap">Tgl Lahir</th> 
                            <th class="py-3">Klinik/Poli</th>
                            <th class="py-3 text-nowrap">Dokter</th>
                            <th class="py-3" width="90">Antrian</th>
                            <th class="py-3 text-nowrap">Waktu</th>
                            <th class="py-3">Keterangan</th>
                            <th class="py-3">Keluhan</th> 
                            <th class="py-3 text-nowrap" width="130">Status</th>
                            <th class="py-3 text-nowrap" width="160">Aksi</th> 
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php
                        $no = 1;
                        if(mysqli_num_rows($query) == 0) {
                            echo '<tr><td colspan="16" class="text-center py-4 text-muted">Data pendaftaran tidak ditemukan.</td></tr>';
                        }
                        
                        while($row = mysqli_fetch_assoc($query)){
                            $status = !empty($row['status']) ? $row['status'] : 'Open';
                            $btn_class = (strcasecmp($status, 'Open') == 0) ? 'btn-outline-success' : 'btn-outline-danger';
                            
                            // Logika Gelar Dokter
                            $gDepan = !empty($row['gelar_depan']) ? $row['gelar_depan'] . ' ' : '';
                            $gBelakang = !empty($row['gelar_belakang']) ? ', ' . $row['gelar_belakang'] : '';
                            $nama_dokter_lengkap = isset($row['nama_dokter']) ? trim($gDepan . $row['nama_dokter'] . $gBelakang) : '<span class="text-danger">Dokter Nonaktif</span>';
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

                            <td class="fw-semibold text-dark ps-3 text-nowrap"><?= $row['nama_pasien'] ?? '<span class="text-danger">Pasien Terhapus</span>'; ?></td>
                            
                            <!-- KOLOM TANGGUNGAN -->
                            <td class="text-center">
                                <?php 
                                $warna_tanggungan = 'bg-secondary';
                                if($row['tanggungan'] == 'BPJS') $warna_tanggungan = 'bg-success';
                                else if($row['tanggungan'] == 'Pribadi') $warna_tanggungan = 'bg-primary';
                                else if($row['tanggungan'] == 'Rekanan') $warna_tanggungan = 'bg-info text-dark';
                                ?>
                                <span class="badge <?= $warna_tanggungan; ?> px-2 py-1">
                                    <?= !empty($row['tanggungan']) ? $row['tanggungan'] : 'Pribadi'; ?>
                                </span>
                            </td>

                            <td class="text-center text-secondary fw-medium">
                                <?= !empty($row['jenis_kelamin']) ? $row['jenis_kelamin'] : '-'; ?>
                            </td>

                            <td class="text-center text-secondary font-monospace small text-nowrap">
                                <?= isset($row['tanggal_lahir']) && $row['tanggal_lahir'] != '0000-00-00' ? date('d-m-Y', strtotime($row['tanggal_lahir'])) : '-'; ?>
                            </td>

                            <td class="text-secondary fw-medium ps-3 text-nowrap">
                                <?php if(isset($row['kode_klinik']) && isset($row['nama_klinik'])) { ?>
                                    <span class="fw-bold text-primary"><?= $row['kode_klinik']; ?></span> - <?= $row['nama_klinik']; ?>
                                <?php } else { ?>
                                    <span class="text-danger">Klinik Nonaktif</span>
                                <?php } ?>
                            </td>

                            <td class="text-secondary small ps-3 text-nowrap">
                                <span class="fw-medium text-dark"><?= $nama_dokter_lengkap; ?></span><br>
                                <span class="text-muted font-monospace text-uppercase" style="font-size: 0.75rem;"><?= $row['kode_dokter'] ?? '-'; ?></span>
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

                            <td class="ps-3 text-muted small">
                                <?= !empty($row['keterangan']) ? htmlspecialchars($row['keterangan']) : '-'; ?>
                            </td>

                            <td class="ps-3 text-muted small">
                                <?= !empty($row['keluhan']) ? htmlspecialchars($row['keluhan']) : '-'; ?>
                            </td>

                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn <?= $btn_class; ?> btn-sm dropdown-toggle rounded-pill px-3 fw-bold btn-status-dropdown w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?= $status; ?>
                                    </button>
                                    <ul class="dropdown-menu shadow border-0 text-center min-w-100">
                                        <li><a class="dropdown-item text-success fw-bold ubah-status" href="#" data-id="<?= $row['id_pendaftaran']; ?>" data-status="Open">Open</a></li>
                                        <li><a class="dropdown-item text-danger fw-bold ubah-status" href="#" data-id="<?= $row['id_pendaftaran']; ?>" data-status="Cancel">Cancel</a></li>
                                    </ul>
                                </div>
                            </td>

                            <td class="text-center text-nowrap">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit.php?id=<?= $row['id_pendaftaran']; ?>" class="btn btn-outline-warning btn-sm px-3 rounded-pill fw-medium">
                                        Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $row['id_pendaftaran']; ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-medium" onclick="return confirm('Apakah Anda yakin untuk menghapus data pendaftaran ini?');">
                                    Hapus
                                </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- AKHIR BAGIAN TABEL -->

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    
    // --- SCRIPT FILTER CASCADING ---
    function applyCascadingFilter() {
        let valPoli = $('#filter_poli').val();

        // Tampilkan semua opsi terlebih dahulu
        $('.opsi-rm, .opsi-nama').show();

        // Jika Poli dipilih, sembunyikan yang tidak terkait
        if (valPoli !== '') {
            $('.opsi-rm, .opsi-nama').each(function() {
                let dataPoli = $(this).attr('data-poli') || '';
                if (dataPoli.indexOf('|' + valPoli + '|') === -1) {
                    $(this).hide();
                }
            });
        }
    }

    applyCascadingFilter();

    // 1. Logika Jika Poli Diubah
    $('#filter_poli').change(function() {
        $('#filter_rm, #filter_nama').val(''); // Reset field di bawahnya
        applyCascadingFilter();
    });

    // 2. Logika Jika No RM Diubah
    $('#filter_rm').change(function() {
        let val = $(this).val();
        if (val !== '') {
            let targetNama = $('.opsi-rm[value="'+val+'"]').attr('data-nama');
            $('#filter_nama').val(targetNama); // Otomatis isi nama
        }
        applyCascadingFilter();
    });

    // 3. Logika Jika Nama Diubah
    $('#filter_nama').change(function() {
        let val = $(this).val();
        if (val !== '') {
            let targetRm = $('.opsi-nama[value="'+val+'"]').attr('data-rm');
            $('#filter_rm').val(targetRm); // Otomatis isi RM
        }
        applyCascadingFilter();
    });
    // -------------------------------


    // --- SCRIPT UBAH STATUS (AJAX) ---
    $(document).on('click', '.ubah-status', function(e){
        e.preventDefault();
        
        let id_pendaftaran = $(this).data('id');
        let status_baru = $(this).data('status');
        let tombol_dropdown = $(this).closest('.dropdown').find('.btn-status-dropdown');

        tombol_dropdown.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        $.ajax({
            url: "update_status.php", 
            type: "POST",
            data: {
                id_pendaftaran: id_pendaftaran,
                status: status_baru
            },
            dataType: "json",
            success: function(response){
                if(response.success){
                    tombol_dropdown.text(status_baru);
                    if(status_baru === 'Open'){
                        tombol_dropdown.removeClass('btn-outline-danger').addClass('btn-outline-success');
                    } else {
                        tombol_dropdown.removeClass('btn-outline-success').addClass('btn-outline-danger');
                    }
                } else {
                    alert("Gagal memperbarui: " + response.message);
                    tombol_dropdown.text("Error"); 
                }
            },
            error: function(xhr, status, error){
                console.error("AJAX Error:", xhr.responseText);
                alert("Gagal menghubungi server. Pastikan update_status.php ada.");
                tombol_dropdown.text("Error");
            }
        });
    });

});
</script>

<?php include '../layout/footer.php'; ?>