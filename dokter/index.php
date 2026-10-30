<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

// 1. Ambil data untuk Filter Dropdown 
// Ambil daftar spesialisasi yang unik (tidak kosong)
$q_filter_spes = mysqli_query($conn, "SELECT DISTINCT spesialisasi FROM dokter WHERE spesialisasi != '' ORDER BY spesialisasi ASC");

// Ambil semua data dokter untuk filter kode dan nama (simpan di array untuk di-looping)
$q_filter_dokter = mysqli_query($conn, "SELECT kode_dokter, nama_dokter, spesialisasi FROM dokter ORDER BY nama_dokter ASC");
$list_dokter = [];
while($row = mysqli_fetch_assoc($q_filter_dokter)){
    $list_dokter[] = $row;
}

// 2. Ambil parameter filter dari URL
$filter_kode = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
$filter_nama = isset($_GET['nama']) ? mysqli_real_escape_string($conn, $_GET['nama']) : '';
$filter_spes = isset($_GET['spesialisasi']) ? mysqli_real_escape_string($conn, $_GET['spesialisasi']) : '';

// 3. Query utama untuk menampilkan data di tabel
$sql = "SELECT * FROM dokter WHERE 1=1";

if($filter_kode != ''){
    $sql .= " AND kode_dokter = '$filter_kode'";
}

if($filter_nama != ''){
    $sql .= " AND nama_dokter LIKE '%$filter_nama%'";
}

if($filter_spes != ''){
    $sql .= " AND spesialisasi = '$filter_spes'";
}

$sql .= " ORDER BY nama_dokter ASC";
$query = mysqli_query($conn, $sql);

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-dark m-0">Master Dokter</h3>
            <p class="text-muted small m-0">Daftar dokter spesialis beserta status praktik aktif</p>
        </div>
        <a href="tambah.php" class="btn btn-primary px-3 rounded-pill shadow-sm fw-medium d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Tambah Dokter
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded">
            <form method="GET" class="row g-3 align-items-end">
                
                <!-- URUTAN 1: SPESIALISASI -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Spesialisasi</label>
                    <select name="spesialisasi" id="filter_spes" class="form-select">
                        <option value="">-- Semua Spesialisasi --</option>
                        <?php while($row_spes = mysqli_fetch_assoc($q_filter_spes)) { ?>
                            <option value="<?= $row_spes['spesialisasi']; ?>" <?= $filter_spes == $row_spes['spesialisasi'] ? 'selected' : ''; ?>>
                                <?= $row_spes['spesialisasi']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- URUTAN 2: KODE DOKTER -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Kode Dokter</label>
                    <select name="kode" id="filter_kode" class="form-select">
                        <option value="">-- Semua Kode --</option>
                        <?php foreach($list_dokter as $d) { 
                            if(!empty(trim($d['kode_dokter']))) {
                        ?>
                            <!-- Sematkan data spesialisasi dan nama untuk logic JS -->
                            <option value="<?= $d['kode_dokter']; ?>" class="opsi-kode" data-spes="<?= $d['spesialisasi']; ?>" data-nama="<?= $d['nama_dokter']; ?>" <?= $filter_kode == $d['kode_dokter'] ? 'selected' : ''; ?>>
                                <?= $d['kode_dokter']; ?>
                            </option>
                        <?php }} ?>
                    </select>
                </div>

                <!-- URUTAN 3: NAMA DOKTER -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Nama Dokter</label>
                    <input type="text"
                    name="nama"
                    class="form-control"
                    placeholder="Masukan nama..."
                    value="<?= $filter_nama; ?>">
                </div>

                <!-- TOMBOL -->
                <div class="col-md-3">
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

    <div class="card overflow-hidden shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-white small text-uppercase fw-bold text-center" style="background: #0d6efd !important;">
                        <tr>
                            <th class="py-3" width="70">No</th>
                            <th class="py-3" width="120">Kode</th>
                            <th class="py-3">Nama Lengkap Dokter</th>
                            <th class="py-3">Spesialisasi</th>
                            <th class="py-3" width="140">Status</th>
                            <th class="py-3" width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php
                        $no = 1;
                        if(mysqli_num_rows($query) == 0){
                            echo '<tr><td colspan="6" class="text-center text-muted py-4">Data dokter tidak ditemukan.</td></tr>';
                        }

                        while($row = mysqli_fetch_assoc($query)){
                            $is_active = (strcasecmp(trim($row['status']), 'Active') === 0);
                            $badge_class = $is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                        ?>
                        <tr>
                            <td class="text-center fw-medium text-secondary"><?= $no++; ?></td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 px-2 py-1.5 font-monospace">
                                    <?= $row['kode_dokter']; ?>
                                </span>
                            </td>
                            
                            <td class="fw-semibold text-dark ps-3">
                                <?= trim($row['gelar_depan'] . ' ' . $row['nama_dokter'] . ' ' . $row['gelar_belakang']); ?>
                            </td>
                            <td class="text-secondary fw-medium ps-3">
                                <?= $row['spesialisasi']; ?>
                            </td>
                            
                            <td class="text-center">
                                <span class="badge <?= $badge_class; ?> rounded-pill px-3 py-2 fw-semibold">
                                    <?= $row['status']; ?>
                                </span>
                            </td>
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit.php?id=<?= $row['id_dokter']; ?>" class="btn btn-outline-warning btn-sm px-3 rounded-pill fw-medium">
                                        Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $row['id_dokter']; ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-medium" onclick="return confirm('Yakin ingin menghapus?')">
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

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // 1. Logika ketika Filter Spesialisasi diubah
    $('#filter_spes').change(function() {
        let valSpes = $(this).val();
        
        // Kosongkan isian filter kode dan nama
        $('#filter_kode').val('');
        $('#filter_nama').val('');

        if (valSpes !== '') {
            // Sembunyikan semua opsi kode dan nama
            $('.opsi-kode, .opsi-nama').hide();
            // Tampilkan hanya yang atribut data-spes-nya cocok
            $('.opsi-kode[data-spes="' + valSpes + '"]').show();
            $('.opsi-nama[data-spes="' + valSpes + '"]').show();
        } else {
            // Tampilkan semua jika kembali ke "-- Semua Spesialisasi --"
            $('.opsi-kode, .opsi-nama').show();
        }
    });

    // 2. Logika ketika Filter Kode diubah
    $('#filter_kode').change(function() {
        let valKode = $(this).val();
        if (valKode !== '') {
            // Cari data spesialisasi dan nama dari option yang dipilih
            let targetNama = $('.opsi-kode[value="' + valKode + '"]').data('nama');
            let targetSpes = $('.opsi-kode[value="' + valKode + '"]').data('spes');
            
            // Otomatis pilih Nama dan Spesialisasi-nya
            $('#filter_nama').val(targetNama);
            $('#filter_spes').val(targetSpes);
            
            // Rapikan list opsi agar sesuai dengan spesialisasi tersebut
            $('.opsi-kode, .opsi-nama').hide();
            $('.opsi-kode[data-spes="' + targetSpes + '"]').show();
            $('.opsi-nama[data-spes="' + targetSpes + '"]').show();
        } else {
            $('#filter_spes').val('').trigger('change'); 
        }
    });

    // 3. Logika ketika Filter Nama diubah
    $('#filter_nama').change(function() {
        let valNama = $(this).val();
        if (valNama !== '') {
            // Cari data spesialisasi dan kode dari option yang dipilih
            let targetKode = $('.opsi-nama[value="' + valNama + '"]').data('kode');
            let targetSpes = $('.opsi-nama[value="' + valNama + '"]').data('spes');
            
            // Otomatis pilih Kode dan Spesialisasi-nya
            $('#filter_kode').val(targetKode);
            $('#filter_spes').val(targetSpes);
            
            // Rapikan list opsi
            $('.opsi-kode, .opsi-nama').hide();
            $('.opsi-kode[data-spes="' + targetSpes + '"]').show();
            $('.opsi-nama[data-spes="' + targetSpes + '"]').show();
        } else {
            $('#filter_spes').val('').trigger('change'); 
        }
    });

    // 4. Trigger saat halaman dimuat (agar filter tidak reset setelah klik Search)
    if ($('#filter_kode').val() !== '') {
        // Trigger perubahan pada kode tanpa reset isian (menggunakan data dari URL yang terpilih)
        let aktifSpes = $('.opsi-kode[value="' + $('#filter_kode').val() + '"]').data('spes');
        $('.opsi-kode, .opsi-nama').hide();
        $('.opsi-kode[data-spes="' + aktifSpes + '"]').show();
        $('.opsi-nama[data-spes="' + aktifSpes + '"]').show();
    } else if ($('#filter_spes').val() !== '') {
        let aktifSpes = $('#filter_spes').val();
        $('.opsi-kode, .opsi-nama').hide();
        $('.opsi-kode[data-spes="' + aktifSpes + '"]').show();
        $('.opsi-nama[data-spes="' + aktifSpes + '"]').show();
    }

});
</script>

<?php include '../layout/footer.php'; ?>