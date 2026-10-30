<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

$no_rm = $_GET['no_rm'];

$query = mysqli_query($conn, "
SELECT
    p.*,
    k.provinsi,
    k.kabupaten,
    k.kecamatan,
    k.kelurahan,
    k.kodepos
FROM pasien p
JOIN tbl_kodepos k
ON p.id_kodepos = k.id
WHERE p.no_rm='$no_rm'
");

$data = mysqli_fetch_assoc($query);

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Edit Data Pasien</h3>
        <p class="text-muted small m-0">Perbarui profil identitas dan data domisili lengkap pasien</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom border-secondary border-opacity-10 py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-warning"></i> Form Profil Rekam Medis Pasien
            </h5>
        </div>

        <div class="card-body p-4">
            <!-- WAJIB TAMBAH ENCTYPE UNTUK UPLOAD FILE -->
            <form action="proses_edit.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="no_rm" value="<?= $data['no_rm']; ?>">
                <!-- Kirim juga nama foto lama untuk referensi jika user tidak upload foto baru -->
                <input type="hidden" name="foto_lama" value="<?= $data['foto']; ?>">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">No RM</label>
                        <input
                            type="text"
                            class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                            value="<?= $data['no_rm']; ?>"
                            style="background-color: #e2e8f0; border-color: #cbd5e1; color: #475569;"
                            readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">NIK</label>
                        <input
                            type="text"
                            name="nik"
                            class="form-control form-control-lg rounded-3 fs-6"
                            maxlength="16"
                            value="<?= $data['nik']; ?>"
                            style="background-color: #f8fafc; border-color: #e2e8f0;"
                            required>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Nama Pasien</label>
                        <input
                            type="text"
                            name="nama_pasien"
                            class="form-control form-control-lg rounded-3 fs-6"
                            value="<?= $data['nama_pasien']; ?>"
                            style="background-color: #f8fafc; border-color: #e2e8f0;"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Tempat Lahir</label>
                        <input
                            type="text"
                            name="tempat_lahir"
                            class="form-control form-control-lg rounded-3 fs-6"
                            value="<?= $data['tempat_lahir']; ?>"
                            style="background-color: #f8fafc; border-color: #e2e8f0;">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Tanggal Lahir</label>
                        <input
                            type="date"
                            name="tanggal_lahir"
                            class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                            value="<?= $data['tanggal_lahir']; ?>"
                            style="background-color: #f8fafc; border-color: #e2e8f0;"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option value="L" <?= ($data['jenis_kelamin'] == "L") ? "selected" : ""; ?>>Laki-laki</option>
                            <option value="P" <?= ($data['jenis_kelamin'] == "P") ? "selected" : ""; ?>>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- BAGIAN EDIT FOTO -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Foto Pasien</label>
                        <div class="d-flex align-items-center gap-3">
                            <?php 
                            $path_foto = '../uploads/pasien/' . $data['foto'];
                            if (!empty($data['foto']) && file_exists($path_foto)) { ?>
                                <img src="<?= $path_foto ?>" alt="Foto Pasien" class="rounded-3 border border-2 shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                            <?php } else { ?>
                                <div class="bg-secondary bg-opacity-10 rounded-3 d-flex justify-content-center align-items-center border mx-auto" style="width: 70px; height: 70px;">
                                    <i class="bi bi-person-fill text-secondary fs-3"></i>
                                </div>
                            <?php } ?>
                            
                            <div class="flex-grow-1">
                                <input 
                                    type="file" 
                                    name="foto_pasien" 
                                    accept="image/png, image/jpeg, image/jpg"
                                    class="form-control rounded-3 fs-6" 
                                    style="background-color: #f8fafc; border-color: #e2e8f0;">
                                <div class="form-text small mt-1">Format: JPG, JPEG, PNG. Maks. 2MB. <strong>Biarkan kosong jika tidak ingin mengubah foto.</strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top border-secondary border-opacity-10 pt-3 mb-3">
                    <span class="text-uppercase tracking-wider text-primary fw-bold" style="font-size: 0.8rem;">Data Wilayah & Tempat Tinggal</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Provinsi</label>
                        <select id="provinsi" name="provinsi" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option selected><?= $data['provinsi']; ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Kabupaten / Kota</label>
                        <select id="kabupaten" name="kabupaten" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option selected><?= $data['kabupaten']; ?></option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Kecamatan</label>
                        <select id="kecamatan" name="kecamatan" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option selected><?= $data['kecamatan']; ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Kelurahan</label>
                        <select id="kelurahan" name="kelurahan" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option value="<?= $data['id_kodepos']; ?>" selected><?= $data['kelurahan']; ?></option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Kode Pos</label>
                        <input
                            type="text"
                            id="kodepos"
                            class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                            value="<?= $data['kodepos']; ?>"
                            style="background-color: #e2e8f0; border-color: #cbd5e1; color: #475569;"
                            readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">No HP / Telp</label>
                        <input
                            type="text"
                            name="no_hp"
                            class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                            value="<?= $data['no_hp']; ?>"
                            style="background-color: #f8fafc; border-color: #e2e8f0;">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Alamat Detail (Jalan, RT/RW, No. Rumah)</label>
                    <textarea
                        name="alamat_detail"
                        class="form-control rounded-3 fs-6"
                        rows="3"
                        style="background-color: #f8fafc; border-color: #e2e8f0;"><?= $data['alamat_detail']; ?></textarea>
                </div>

                <input type="hidden" name="id_kodepos" id="id_kodepos" value="<?= $data['id_kodepos']; ?>">

                <div class="d-flex align-items-center gap-2 pt-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm fw-medium">
                        <i class="bi bi-save me-1"></i> Update Data Pasien
                    </button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill text-secondary fw-medium">
                        Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
const provinsi = document.getElementById("provinsi");
const kabupaten = document.getElementById("kabupaten");
const kecamatan = document.getElementById("kecamatan");
const kelurahan = document.getElementById("kelurahan");
const kodepos = document.getElementById("kodepos");
const id_kodepos = document.getElementById("id_kodepos");

provinsi.onchange = function() {
    fetch("get_kabupaten.php?provinsi=" + this.value)
        .then(r => r.json())
        .then(data => {
            kabupaten.innerHTML = "<option value=''>Pilih</option>";
            data.forEach(x => {
                kabupaten.innerHTML += `<option value="${x.kabupaten}">${x.kabupaten}</option>`;
            });
        });
}

kabupaten.onchange = function() {
    fetch("get_kecamatan.php?provinsi=" + provinsi.value + "&kabupaten=" + this.value)
        .then(r => r.json())
        .then(data => {
            kecamatan.innerHTML = "<option value=''>Pilih</option>";
            data.forEach(x => {
                kecamatan.innerHTML += `<option value="${x.kecamatan}">${x.kecamatan}</option>`;
            });
        });
}

kecamatan.onchange = function() {
    fetch("get_kelurahan.php?provinsi=" + provinsi.value + "&kabupaten=" + kabupaten.value + "&kecamatan=" + this.value)
        .then(r => r.json())
        .then(data => {
            kelurahan.innerHTML = "<option value=''>Pilih</option>";
            data.forEach(x => {
                kelurahan.innerHTML += `<option value="${x.id}">${x.kelurahan}</option>`;
            });
        });
}

kelurahan.onchange = function() {
    fetch("get_kodepos.php?id=" + this.value)
        .then(r => r.json())
        .then(data => {
            kodepos.value = data.kodepos;
            id_kodepos.value = data.id;
        });
}
</script>

<?php include '../layout/footer.php'; ?>