<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

// 1. Ambil data untuk Filter Dropdown (Dinamis dari Database)
$q_filter_kode   = mysqli_query($conn, "SELECT DISTINCT kode_dokter FROM dokter ORDER BY kode_dokter ASC");
$q_filter_nama   = mysqli_query($conn, "SELECT DISTINCT nama_dokter FROM dokter ORDER BY nama_dokter ASC");
$q_filter_spes   = mysqli_query($conn, "SELECT DISTINCT spesialisasi FROM dokter ORDER BY spesialisasi ASC");
$q_filter_klinik = mysqli_query($conn, "SELECT DISTINCT nama_klinik FROM klinik ORDER BY nama_klinik ASC");
$q_filter_room   = mysqli_query($conn, "SELECT DISTINCT room FROM jadwal_dokter ORDER BY room ASC");

// 2. Ambil parameter filter dari URL
$f_kode   = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
$f_nama   = isset($_GET['nama']) ? mysqli_real_escape_string($conn, $_GET['nama']) : '';
$f_spes   = isset($_GET['spesialisasi']) ? mysqli_real_escape_string($conn, $_GET['spesialisasi']) : '';
$f_klinik = isset($_GET['klinik']) ? mysqli_real_escape_string($conn, $_GET['klinik']) : '';
$f_room   = isset($_GET['room']) ? mysqli_real_escape_string($conn, $_GET['room']) : '';

// 3. Query utama dengan INNER JOIN dan kondisi filter
$sql = "
SELECT 
    j.*, 
    d.kode_dokter, 
    d.gelar_depan, 
    d.nama_dokter, 
    d.gelar_belakang, 
    d.spesialisasi, 
    k.kode_klinik, 
    k.nama_klinik 
FROM jadwal_dokter j
INNER JOIN dokter d ON j.id_dokter = d.id_dokter
INNER JOIN klinik k ON j.id_klinik = k.id_klinik
WHERE 1=1
";

// Tambahkan kondisi pencarian jika dropdown dipilih
if ($f_kode != '')   $sql .= " AND d.kode_dokter = '$f_kode'";
if($f_nama != ''){
    $sql .= " AND nama_dokter LIKE '%$f_nama%'";
}
if ($f_spes != '')   $sql .= " AND d.spesialisasi = '$f_spes'";
if ($f_klinik != '') $sql .= " AND k.nama_klinik = '$f_klinik'";
if ($f_room != '')   $sql .= " AND j.room = '$f_room'";

$sql .= " ORDER BY j.hari, j.jam_mulai ASC";
$query = mysqli_query($conn, $sql);

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-dark m-0">Master Jadwal Dokter</h3>
            <p class="text-muted small m-0">Pengaturan waktu praktik dokter di setiap unit poliklinik</p>
        </div>
        <a href="tambah.php" class="btn btn-primary px-3 rounded-pill shadow-sm fw-medium d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Tambah Jadwal
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded">
            <form method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1">Filter Kode Dokter</label>
                    <select name="kode" class="form-select">
                        <option value="">-- Semua Kode --</option>
                        <?php while($r = mysqli_fetch_assoc($q_filter_kode)) { if(!empty(trim($r['kode_dokter']))) { ?>
                            <option value="<?= $r['kode_dokter']; ?>" <?= $f_kode == $r['kode_dokter'] ? 'selected' : ''; ?>>
                                <?= $r['kode_dokter']; ?>
                            </option>
                        <?php }} ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Nama Dokter</label>
                    <input type="text"
                    name="nama"
                    class="form-control"
                    placeholder="Masukan nama..."
                    value="<?= $f_nama; ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1">Filter Spesialisasi</label>
                    <select name="spesialisasi" class="form-select">
                        <option value="">-- Semua Spesialisasi --</option>
                        <?php while($r = mysqli_fetch_assoc($q_filter_spes)) { if(!empty(trim($r['spesialisasi']))) { ?>
                            <option value="<?= $r['spesialisasi']; ?>" <?= $f_spes == $r['spesialisasi'] ? 'selected' : ''; ?>>
                                <?= $r['spesialisasi']; ?>
                            </option>
                        <?php }} ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1">Filter Klinik</label>
                    <select name="klinik" class="form-select">
                        <option value="">-- Semua Klinik --</option>
                        <?php while($r = mysqli_fetch_assoc($q_filter_klinik)) { if(!empty(trim($r['nama_klinik']))) { ?>
                            <option value="<?= $r['nama_klinik']; ?>" <?= $f_klinik == $r['nama_klinik'] ? 'selected' : ''; ?>>
                                <?= $r['nama_klinik']; ?>
                            </option>
                        <?php }} ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1">Filter Room</label>
                    <select name="room" class="form-select">
                        <option value="">-- Semua Ruangan --</option>
                        <?php while($r = mysqli_fetch_assoc($q_filter_room)) { if(!empty(trim($r['room']))) { ?>
                            <option value="<?= $r['room']; ?>" <?= $f_room == $r['room'] ? 'selected' : ''; ?>>
                                <?= $r['room']; ?>
                            </option>
                        <?php }} ?>
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

    <div class="card overflow-hidden shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-white small text-uppercase fw-bold text-center" style="background:#0d6efd !important;">
                        <tr>
                            <th class="py-3" width="50">No</th>
                            <th class="py-3 text-nowrap" width="90">Kode</th>
                            <th class="py-3 text-nowrap">Nama Dokter</th>
                            <th class="py-3 text-nowrap">Spesialisasi</th>
                            <th class="py-3 text-nowrap">Klinik</th>
                            <th class="py-3 text-nowrap" width="90">Room</th>
                            <th class="py-3 text-nowrap" width="100">Hari</th>
                            <th class="py-3 text-nowrap" width="130">Jam</th>
                            <th class="py-3 text-nowrap" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query)){
                        ?>
                        <tr>
                            <td class="text-center fw-medium text-secondary">
                                <?= $no++; ?>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 px-2 py-1 font-monospace">
                                    <?= $row['kode_dokter']; ?>
                                </span>
                            </td>
                            <td class="fw-semibold text-dark ps-3 text-nowrap">
                                <?= trim($row['gelar_depan'].' '.$row['nama_dokter'].' '.$row['gelar_belakang']); ?>
                            </td>
                            <td class="text-secondary fw-medium ps-3 text-nowrap">
                                <?= $row['spesialisasi']; ?>
                            </td>
                            <td class="text-secondary fw-medium ps-3 text-nowrap">
                                <span class="fw-bold text-primary">
                                    <?= $row['kode_klinik']; ?>
                                </span>
                                - <?= $row['nama_klinik']; ?>
                            </td>
                            <td class="text-center fw-semibold text-nowrap">
                                <?= $row['room']; ?>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-1">
                                    <?= $row['hari']; ?>
                                </span>
                            </td>
                            <td class="text-center font-monospace text-nowrap">
                                <?= substr($row['jam_mulai'],0,5); ?> - <?= substr($row['jam_selesai'],0,5); ?>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-outline-warning btn-sm px-3 rounded-pill fw-medium">
                                        Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-medium" onclick="return confirm('Yakin ingin menghapus?')">
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

<?php include '../layout/footer.php'; ?>