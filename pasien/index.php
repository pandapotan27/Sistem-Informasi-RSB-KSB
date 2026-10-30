<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

// 1. Ambil parameter filter dari URL
$f_norm = isset($_GET['no_rm']) ? mysqli_real_escape_string($conn, $_GET['no_rm']) : '';
$f_nama = isset($_GET['nama_pasien']) ? mysqli_real_escape_string($conn, $_GET['nama_pasien']) : '';
$f_tgl  = isset($_GET['tanggal_lahir']) ? mysqli_real_escape_string($conn, $_GET['tanggal_lahir']) : '';

// 2. Query utama dengan WHERE 1=1 untuk menampung kondisi filter 
$sql = "
SELECT 
    p.*, 
    k.provinsi, 
    k.kabupaten, 
    k.kecamatan, 
    k.kelurahan, 
    k.kodepos 
FROM pasien p
JOIN tbl_kodepos k ON p.id_kodepos = k.id
WHERE 1=1
";

// Tambahkan filter pencarian jika form diisi
if ($f_norm != '') {
    $sql .= " AND p.no_rm LIKE '%$f_norm%'";
}
if ($f_nama != '') {
    $sql .= " AND p.nama_pasien LIKE '%$f_nama%'";
}
if ($f_tgl != '') {
    $sql .= " AND p.tanggal_lahir = '$f_tgl'";
}

$sql .= " ORDER BY p.no_rm ASC";
$query = mysqli_query($conn, $sql);

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-dark m-0">Master Data Pasien</h3>
            <p class="text-muted small m-0">Rekam medis dan data domisili lengkap pasien rawat jalan</p>
        </div>
        <a href="tambah.php" class="btn btn-primary px-3 rounded-pill shadow-sm fw-medium d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Tambah Pasien
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded">
            <form method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">No RM</label>
                    <input type="text" name="no_rm" class="form-control" placeholder="Contoh: RM-001..." value="<?= $f_norm; ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Nama Pasien</label>
                    <input type="text" name="nama_pasien" class="form-control" placeholder="Masukan nama..." value="<?= $f_nama; ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="<?= $f_tgl; ?>">
                </div>

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

    <div class="card overflow-hidden border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-white small text-uppercase fw-bold text-center" style="background: #0d6efd !important;">
                        <tr>
                            <th class="py-3 text-nowrap" width="110">No RM</th>
                            <th class="py-3 text-nowrap">Nama Pasien</th>
                            <th class="py-3 text-nowrap" width="70">Foto</th>
                            <th class="py-3 text-nowrap" width="80">JK</th>
                            <th class="py-3 text-nowrap" width="130">Tanggal Lahir</th>
                            <th class="py-3 text-nowrap" width="130">No HP</th>
                            <th class="py-3">Alamat Lengkap</th> 
                            <th class="py-3 text-nowrap" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php while($row = mysqli_fetch_assoc($query)){ ?>
                        <tr>
                            <td class="text-center text-nowrap">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 px-2.5 py-1.5 font-monospace fw-bold">
                                    <?= $row['no_rm']; ?>
                                </span>
                            </td>

                            <td class="fw-semibold text-dark ps-3 text-nowrap">
                                <?= $row['nama_pasien']; ?>
                            </td>

                            <!-- KOLOM FOTO -->
                            <td class="text-center">
                                <?php 
                                // Cek apakah foto ada di database dan filenya ada di folder
                                $path_foto = '../uploads/pasien/' . $row['foto'];
                                if (!empty($row['foto']) && file_exists($path_foto)) { ?>
                                    <!-- Foto Kecil, Modalnya dipindah ke bawah agar tidak kedip/error -->
                                    <img src="<?= $path_foto; ?>" 
                                         alt="Foto" 
                                         class="rounded-circle border border-2 shadow-sm" 
                                         style="width: 45px; height: 45px; object-fit: cover; cursor: pointer;"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#modalLihatFoto"
                                         data-foto="<?= $path_foto; ?>"
                                         data-nama="<?= $row['nama_pasien']; ?>"
                                         data-norm="<?= $row['no_rm']; ?>"
                                         title="Klik untuk memperbesar">
                                <?php } else { ?>
                                    <!-- Tampilan Avatar Default Jika Tidak Ada Foto -->
                                    <div class="bg-secondary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center border mx-auto" style="width: 45px; height: 45px;">
                                        <i class="bi bi-person-fill text-secondary fs-5"></i>
                                    </div>
                                <?php } ?>
                            </td>

                            <td class="text-center text-secondary fw-medium text-nowrap">
                                <?= $row['jenis_kelamin']; ?>
                            </td>

                            <td class="text-center text-dark fw-medium font-monospace text-nowrap">
                                <?= date('d-m-Y', strtotime($row['tanggal_lahir'])); ?>
                            </td>

                            <td class="text-center text-secondary font-monospace text-nowrap">
                                <?= $row['no_hp']; ?>
                            </td>

                            <td class="text-muted small ps-3 py-2" style="line-height: 1.4;">
                                <span class="fw-medium text-dark"><?= $row['alamat_detail']; ?></span><br>
                                <span><?= $row['kelurahan']; ?>, <?= $row['kecamatan']; ?></span><br>
                                <span><?= $row['kabupaten']; ?>, <?= $row['provinsi']; ?> (<?= $row['kodepos']; ?>)</span>
                            </td>

                            <td class="text-center text-nowrap">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit.php?no_rm=<?= $row['no_rm']; ?>" class="btn btn-outline-warning btn-sm px-3 rounded-pill fw-medium">
                                        Edit
                                    </a>
                                    <a href="hapus.php?no_rm=<?= $row['no_rm']; ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-medium" onclick="return confirm('Yakin ingin menghapus pasien ini?')">
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

<!-- MASTER MODAL (Diletakkan di luar tabel agar tidak error/kedip) -->
<div class="modal fade" id="modalLihatFoto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pb-4">
        <!-- Elemen img dan text ini akan diubah isinya secara dinamis oleh JavaScript -->
        <img id="imgModalTampil" src="" class="img-fluid rounded mb-3 shadow" style="max-height: 250px; object-fit: contain;" alt="Foto Pasien">
        <h6 id="namaModalTampil" class="mb-0 fw-bold text-dark"></h6>
        <p id="normModalTampil" class="text-muted small mb-0"></p>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT UNTUK MENANGANI PERUBAHAN FOTO DI MODAL -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    var modalFoto = document.getElementById('modalLihatFoto');
    if(modalFoto) {
        modalFoto.addEventListener('show.bs.modal', function (event) {
            // Gambar mana yang barusan diklik user?
            var triggerImage = event.relatedTarget;
            
            // Ambil data yang dititipkan di atribut gambar (data-foto, data-nama, data-norm)
            var foto_url = triggerImage.getAttribute('data-foto');
            var nama_pasien = triggerImage.getAttribute('data-nama');
            var nomor_rm = triggerImage.getAttribute('data-norm');
            
            // Masukkan data tersebut ke dalam isi Modal
            document.getElementById('imgModalTampil').src = foto_url;
            document.getElementById('namaModalTampil').textContent = nama_pasien;
            document.getElementById('normModalTampil').textContent = nomor_rm;
        });
    }
});
</script>

<?php include '../layout/footer.php'; ?>