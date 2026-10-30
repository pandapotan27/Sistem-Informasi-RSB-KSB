<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

// 1. Ambil data untuk Filter Dropdown (Cukup 1 Query untuk Kode dan Nama)
$query_filter = mysqli_query($conn, "SELECT kode_klinik, nama_klinik FROM klinik ORDER BY nama_klinik ASC");
// Simpan ke dalam array agar bisa di-looping dua kali di form dropdown
$list_klinik = [];
while($row = mysqli_fetch_assoc($query_filter)){
    $list_klinik[] = $row;
}

// 2. Ambil parameter filter dari URL
$filter_kode = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
$filter_nama = isset($_GET['nama']) ? mysqli_real_escape_string($conn, $_GET['nama']) : '';

// 3. Query utama untuk menampilkan data di tabel
$sql = "SELECT * FROM klinik WHERE 1=1";

if($filter_kode != ''){
    $sql .= " AND kode_klinik = '$filter_kode'";
}

if($filter_nama != ''){
    $sql .= " AND nama_klinik = '$filter_nama'";
}

$sql .= " ORDER BY id_klinik ASC";
$data = mysqli_query($conn, $sql);

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-dark m-0">Master Klinik</h3>
            <p class="text-muted small m-0">Manajemen data unit poliklinik rumah sakit</p>
        </div>
        <a href="tambah.php" class="btn btn-primary px-3 rounded-pill shadow-sm fw-medium d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Tambah Klinik
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded">
            <form method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1">Kode Klinik</label>
                    <select name="kode" id="filter_kode" class="form-select">
                        <option value="">-- Semua Kode --</option>
                        <?php foreach($list_klinik as $k) { ?>
                            <!-- Tambahkan atribut data-nama dan class opsi-kode -->
                            <option value="<?= $k['kode_klinik']; ?>" class="opsi-kode" data-nama="<?= $k['nama_klinik']; ?>" <?= $filter_kode == $k['kode_klinik'] ? 'selected' : ''; ?>>
                                <?= $k['kode_klinik']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1">Nama Klinik</label>
                    <select name="nama" id="filter_nama" class="form-select">
                        <option value="">-- Semua Klinik --</option>
                        <?php foreach($list_klinik as $k) { ?>
                            <!-- Tambahkan atribut data-kode dan class opsi-nama -->
                            <option value="<?= $k['nama_klinik']; ?>" class="opsi-nama" data-kode="<?= $k['kode_klinik']; ?>" <?= $filter_nama == $k['nama_klinik'] ? 'selected' : ''; ?>>
                                <?= $k['nama_klinik']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="index.php" class="btn btn-secondary px-4">
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
                            <th class="py-3" width="80">No</th>
                            <th class="py-3" width="150">Kode Klinik</th>
                            <th class="py-3">Nama Klinik</th>
                            <th class="py-3" width="150">Status</th>
                            <th class="py-3" width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php
                        $no = 1;
                        // Cek jika data kosong
                        if(mysqli_num_rows($data) == 0){
                            echo '<tr><td colspan="5" class="text-center text-muted py-4">Data klinik tidak ditemukan.</td></tr>';
                        }
                        
                        while($row = mysqli_fetch_assoc($data)){
                            $is_active = (strcasecmp(trim($row['status']), 'Active') === 0);
                            $badge_class = $is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                        ?>
                        <tr>
                            <td class="text-center fw-medium text-secondary"><?= $no++; ?></td>
                            
                            <td class="text-center fw-bold text-primary">
                                <?= $row['kode_klinik']; ?>
                            </td>

                            <td class="fw-semibold text-dark ps-3">
                                <?= $row['nama_klinik']; ?>
                            </td>
                            
                            <td class="text-center">
                                <span class="badge <?= $badge_class; ?> rounded-pill px-3 py-2 fw-semibold">
                                    <?= $row['status']; ?>
                                </span>
                            </td>
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit.php?id=<?= $row['id_klinik']; ?>" class="btn btn-outline-warning btn-sm px-3 rounded-pill fw-medium">
                                        Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $row['id_klinik']; ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-medium" onclick="return confirm('Yakin ingin menghapus?')">
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

    // 1. Jika user memilih Kode Klinik
    $('#filter_kode').change(function() {
        let selectedKode = $(this).val();

        if (selectedKode !== '') {
            // Sembunyikan semua opsi Nama Klinik
            $('.opsi-nama').hide();
            // Tampilkan hanya Nama Klinik yang berpasangan dengan Kode ini
            $('.opsi-nama[data-kode="' + selectedKode + '"]').show();
            // Otomatis pilih Nama Klinik-nya (biar instan)
            $('#filter_nama').val($('.opsi-nama[data-kode="' + selectedKode + '"]').val());
        } else {
            // Jika dikembalikan ke "-- Semua Kode --", tampilkan semua & reset
            $('.opsi-nama').show();
            $('#filter_nama').val('');
        }
    });

    // 2. Jika user memilih Nama Klinik (Logika kebalikannya)
    $('#filter_nama').change(function() {
        let selectedNama = $(this).val();

        if (selectedNama !== '') {
            // Sembunyikan semua opsi Kode Klinik
            $('.opsi-kode').hide();
            // Tampilkan hanya Kode Klinik yang berpasangan dengan Nama ini
            $('.opsi-kode[data-nama="' + selectedNama + '"]').show();
            // Otomatis pilih Kode Klinik-nya
            $('#filter_kode').val($('.opsi-kode[data-nama="' + selectedNama + '"]').val());
        } else {
             // Jika dikembalikan ke "-- Semua Klinik --", tampilkan semua & reset
            $('.opsi-kode').show();
            $('#filter_kode').val('');
        }
    });

    // 3. Trigger otomatis saat halaman di-refresh setelah menekan "Search"
    if ($('#filter_kode').val() !== '') {
        // Hanya tampilkan yang bersangkutan tanpa mengubah isian
        let aktifKode = $('#filter_kode').val();
        $('.opsi-nama').hide();
        $('.opsi-nama[data-kode="' + aktifKode + '"]').show();
    } else if ($('#filter_nama').val() !== '') {
        let aktifNama = $('#filter_nama').val();
        $('.opsi-kode').hide();
        $('.opsi-kode[data-nama="' + aktifNama + '"]').show();
    }

});
</script>

<?php include '../layout/footer.php'; ?>