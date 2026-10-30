<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/koneksi.php';

/* ===========================
    AUTO GENERATE NO RM
=========================== */
$q = mysqli_query($conn,"SELECT MAX(no_rm) as max_rm FROM pasien");
$d = mysqli_fetch_assoc($q);

if($d['max_rm']==""){
    $no_rm="RM000001";
}else{
    $angka = intval(substr($d['max_rm'],2));
    $angka++;
    $no_rm = "RM".str_pad($angka,6,"0",STR_PAD_LEFT);
}

include '../layout/header.php';
include '../layout/navbar.php';
include '../layout/sidebar.php';
?>

<div class="content">

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Tambah Data Pasien</h3>
        <p class="text-muted small m-0">Daftarkan rekam medis baru beserta data domisili lengkap pasien</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom border-secondary border-opacity-10 py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill text-primary"></i> Form Profil Rekam Medis Pasien
            </h5>
        </div>

        <div class="card-body p-4">
            <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">No RM</label>
                        <input
                            type="text"
                            name="no_rm"
                            class="form-control form-control-lg rounded-3 fs-6 font-monospace fw-bold"
                            value="<?= $no_rm ?>"
                            style="background-color: #e2e8f0; border-color: #cbd5e1; color: #475569;"
                            readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">NIK</label>
                        <input
                            type="text"
                            name="nik"
                            maxlength="16"
                            placeholder="Masukkan 16 digit NIK"
                            class="form-control form-control-lg rounded-3 fs-6"
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
                            placeholder="Masukkan nama lengkap pasien"
                            class="form-control form-control-lg rounded-3 fs-6"
                            style="background-color: #f8fafc; border-color: #e2e8f0;"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Tempat Lahir</label>
                        <input
                            type="text"
                            name="tempat_lahir"
                            placeholder="Contoh: Bekasi"
                            class="form-control form-control-lg rounded-3 fs-6"
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
                            style="background-color: #f8fafc; border-color: #e2e8f0;"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- ... kode sebelumnya (row tanggal lahir & jenis kelamin) ... -->

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Foto Pasien (Opsional)</label>
                        <input 
                            type="file" 
                            name="foto_pasien" 
                            accept="image/png, image/jpeg, image/jpg"
                            class="form-control form-control-lg rounded-3 fs-6" 
                            style="background-color: #f8fafc; border-color: #e2e8f0;">
                        <div class="form-text small">Format yang diizinkan: JPG, JPEG, PNG. Maksimal 2MB.</div>
                    </div>
                </div>


                <div class="border-top border-secondary border-opacity-10 pt-3 mb-3">
                    <span class="text-uppercase tracking-wider text-primary fw-bold" style="font-size: 0.8rem;">Data Wilayah & Tempat Tinggal</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Provinsi</label>
                        <select id="provinsi" name="provinsi" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Kabupaten / Kota</label>
                        <select id="kabupaten" name="kabupaten" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option value="">Pilih Kabupaten</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Kecamatan</label>
                        <select id="kecamatan" name="kecamatan" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Kelurahan</label>
                        <select id="kelurahan" name="kelurahan" class="form-select form-select-lg rounded-3 fs-6" style="background-color: #f8fafc; border-color: #e2e8f0;" required>
                            <option value="">Pilih Kelurahan</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">Kode Pos</label>
                        <input
                            type="text"
                            id="kodepos"
                            name="kodepos"
                            maxlength="5"
                            placeholder="Ketik kodepos / terisi otomatis"
                            class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                            style="background-color: #f8fafc; border-color: #e2e8f0;"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small text-uppercase">No HP / Telp</label>
                        <input
                            type="text"
                            name="no_hp"
                            placeholder="Contoh: 081234567890"
                            class="form-control form-control-lg rounded-3 fs-6 font-monospace"
                            style="background-color: #f8fafc; border-color: #e2e8f0;">
                    </div>
                </div>


                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Alamat Detail (Jalan, RT/RW, No. Rumah)</label>
                    <textarea
                        name="alamat_detail"
                        placeholder="Masukkan alamat lengkap rumah pasien..."
                        class="form-control rounded-3 fs-6"
                        rows="3"
                        style="background-color: #f8fafc; border-color: #e2e8f0;"></textarea>
                </div>

                <input type="hidden" name="id_kodepos" id="id_kodepos">

                <div class="d-flex align-items-center gap-2 pt-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm fw-medium">
                        <i class="bi bi-save me-1"></i> Simpan Pasien
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
const provinsi = document.getElementById('provinsi');
const kabupaten = document.getElementById('kabupaten');
const kecamatan = document.getElementById('kecamatan');
const kelurahan = document.getElementById('kelurahan');
const kodepos = document.getElementById('kodepos');
const id_kodepos = document.getElementById('id_kodepos');

/* ========================================================
   ALUR 1: DARI ATAS KE BAWAH (PROVINSI -> KODEPOS)
======================================================== */

// Load Provinsi saat halaman pertama kali dibuka
fetch("get_provinsi.php")
.then(r=>r.json())
.then(data=>{
    data.forEach(p=>{
        provinsi.innerHTML += `<option value="${p.provinsi}">${p.provinsi}</option>`;
    });
});

provinsi.onchange=function(){
    kabupaten.innerHTML="<option value=''>Pilih Kabupaten</option>";
    kecamatan.innerHTML="<option value=''>Pilih Kecamatan</option>";
    kelurahan.innerHTML="<option value=''>Pilih Kelurahan</option>";
    kodepos.value="";
    id_kodepos.value="";
    
    if(!this.value) return;

    fetch("get_kabupaten.php?provinsi="+this.value)
    .then(r=>r.json())
    .then(data=>{
        data.forEach(k=>{
            kabupaten.innerHTML+=`<option value="${k.kabupaten}">${k.kabupaten}</option>`;
        });
    });
}

kabupaten.onchange=function(){
    kecamatan.innerHTML="<option value=''>Pilih Kecamatan</option>";
    kelurahan.innerHTML="<option value=''>Pilih Kelurahan</option>";
    kodepos.value="";
    id_kodepos.value="";

    if(!this.value) return;

    fetch("get_kecamatan.php?provinsi="+provinsi.value+"&kabupaten="+this.value)
    .then(r=>r.json())
    .then(data=>{
        data.forEach(k=>{
            kecamatan.innerHTML+=`<option value="${k.kecamatan}">${k.kecamatan}</option>`;
        });
    });
}

kecamatan.onchange=function(){
    kelurahan.innerHTML="<option value=''>Pilih Kelurahan</option>";
    kodepos.value="";
    id_kodepos.value="";

    if(!this.value) return;

    fetch("get_kelurahan.php?provinsi="+provinsi.value+"&kabupaten="+kabupaten.value+"&kecamatan="+this.value)
    .then(r=>r.json())
    .then(data=>{
        data.forEach(k=>{
            kelurahan.innerHTML+=`<option value="${k.id}">${k.kelurahan}</option>`;
        });
    });
}

kelurahan.onchange=function(){
    if(!this.value) {
        kodepos.value="";
        id_kodepos.value="";
        return;
    }

    // Set input hidden untuk proses_tambah.php
    id_kodepos.value = this.value;

    fetch("get_kodepos.php?id="+this.value)
    .then(r=>r.json())
    .then(data=>{
        // Isi input kodepos secara otomatis
        kodepos.value=data.kodepos; 
    });
}

/* ========================================================
   ALUR 2: DARI BAWAH KE ATAS (KODEPOS -> PROVINSI)
======================================================== */
kodepos.addEventListener('input', function() {
    let kp = this.value.trim();
    
    // Hanya memicu pencarian jika user mengetik tepat 5 angka
    if(kp.length === 5) {
        fetch(`get_wilayah_by_kodepos.php?kodepos=${kp}`)
        .then(r => r.json())
        .then(data => {
            if(data.length > 0) {
                let first = data[0];
                
                // Otomatis terisi ke atas
                provinsi.innerHTML = `<option value="${first.provinsi}">${first.provinsi}</option>`;
                kabupaten.innerHTML = `<option value="${first.kabupaten}">${first.kabupaten}</option>`;
                kecamatan.innerHTML = `<option value="${first.kecamatan}">${first.kecamatan}</option>`;
                
                // Cek jumlah kelurahan seperti sebelumnya
                if(data.length === 1) {
                    kelurahan.innerHTML = `<option value="${first.id}">${first.kelurahan}</option>`;
                    id_kodepos.value = first.id;
                } else {
                    kelurahan.innerHTML = "<option value=''>-- Pilih Kelurahan --</option>";
                    data.forEach(k => {
                        kelurahan.innerHTML += `<option value="${k.id}">${k.kelurahan}</option>`;
                    });
                    id_kodepos.value = ""; 
                }
            } else {
                alert('Kode pos tidak ditemukan!');
            }
        })
        .catch(err => console.error("Error:", err));
    }
});
</script>

<?php include '../layout/footer.php'; ?>

<?php include '../layout/footer.php'; ?>