<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: login.php");
    exit;
}

// 1. Koneksi database
include 'config/koneksi.php'; 

// ==========================================
// 2. QUERY HITUNG DATA DARI DATABASE 
// ==========================================

//QUERY MENGHITUNG JUMLAH KLINIK
$query_klinik = mysqli_query($conn, "SELECT COUNT(*) as total FROM klinik");
$total_klinik = mysqli_fetch_assoc($query_klinik)['total'];

//QUERY MENGHITUNG JUMLAH DOKTER
$query_dokter = mysqli_query($conn, "SELECT COUNT(*) as total FROM dokter");
$total_dokter = mysqli_fetch_assoc($query_dokter)['total'];

//QUERY MENGHITUNG JUMLAH PASIEN
$query_pasien = mysqli_query($conn, "SELECT COUNT(*) as total FROM pasien");
$total_pasien = mysqli_fetch_assoc($query_pasien)['total'];

//QUERY MENGHITUNG JUMLAH PENDAFTARAN HARI INI
$hari_ini = date('Y-m-d');
$query_pendaftaran = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran WHERE DATE(tanggal_kunjungan) = '$hari_ini'");
$total_pendaftaran = mysqli_fetch_assoc($query_pendaftaran)['total'];


// ==========================================
// 3. QUERY DATA TABEL LIST
// ==========================================

//Mengambil kolom nama_klinik dan status dari tabel klinik dan mengambil kolom nama_dokter, spesialisasi dan status dari tabel dokter 
$query_list_klinik = mysqli_query($conn, "SELECT nama_klinik, status FROM klinik ORDER BY nama_klinik ASC");
$query_list_dokter = mysqli_query($conn, "SELECT nama_dokter, spesialisasi, status FROM dokter ORDER BY nama_dokter ASC");


// ==========================================
// 4. QUERY UNTUK VISUALISASI DATA (GRAFIK)
// ==========================================

// A. Query Kunjungan Pasien (7 Hari Terakhir)
//GROUP BY DATE(tanggal_kunjungan) Mengelompokkan data berdasarkan tanggal
//ORDER BY tanggal ASC Mengurutkan hasil dari tanggal paling lama ke paling baru (dari kiri ke kanan)
$query_kunjungan = mysqli_query($conn, "
    SELECT DATE(tanggal_kunjungan) as tanggal, COUNT(*) as jumlah 
    FROM pendaftaran 
    WHERE tanggal_kunjungan >= DATE(NOW()) - INTERVAL 7 DAY
    GROUP BY DATE(tanggal_kunjungan)
    ORDER BY tanggal ASC
");
$label_kunjungan = [];
$data_kunjungan = [];
//Mengambil setiap baris hasil query satu per satu.
//label_ kunjungan Mengubah format tanggal menjadi lebih mudah dibaca (nilai di sumbu x dan jumlah di sumbu y).
while($row = mysqli_fetch_assoc($query_kunjungan)){
    $label_kunjungan[] = date('d M', strtotime($row['tanggal'])); 
    $data_kunjungan[] = $row['jumlah'];
}

// B. Query Distribusi Pasien per Poli
//LEFT JOIN Menghubungkan tabel klinik dengan pendaftaran melalui id_klinik
//COUNT(p.id_pendaftaran) Menghitung jumlah pendaftaran pada masing-masing poli.
$query_distribusi = mysqli_query($conn, "
    SELECT k.nama_klinik, COUNT(p.id_pendaftaran) as jumlah 
    FROM klinik k 
    LEFT JOIN pendaftaran p ON k.id_klinik = p.id_klinik 
    WHERE k.status = 'Active'
    GROUP BY k.id_klinik, k.nama_klinik
    HAVING jumlah > 0 
    ORDER BY jumlah DESC
");
$label_distribusi = [];
$data_distribusi = [];
while($row = mysqli_fetch_assoc($query_distribusi)){
    $label_distribusi[] = $row['nama_klinik'];
    $data_distribusi[] = $row['jumlah'];
}


// 5. Panggil Komponen Layout
include 'layout/header.php';
include 'layout/navbar.php';
include 'layout/sidebar.php';
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark m-0">Dashboard</h3>
            <p class="text-muted small m-0">Ringkasan data operasional klinik</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-2 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1" style="font-size: 0.85rem;">Total Klinik</span>
                        <h2 class="fw-bold text-dark m-0"><?= number_format($total_klinik, 0, ',', '.'); ?></h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-building fs-3 line-height-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-2 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1" style="font-size: 0.85rem;">Total Dokter</span>
                        <h2 class="fw-bold text-dark m-0"><?= number_format($total_dokter, 0, ',', '.'); ?></h2>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-person-badge fs-3 line-height-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-2 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1" style="font-size: 0.85rem;">Total Pasien</span>
                        <h2 class="fw-bold text-dark m-0"><?= number_format($total_pasien, 0, ',', '.'); ?></h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="bi bi-people fs-3 line-height-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-2 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1" style="font-size: 0.85rem;">Pendaftaran Hari Ini</span>
                        <h2 class="fw-bold text-dark m-0"><?= number_format($total_pendaftaran, 0, ',', '.'); ?></h2>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                        <i class="bi bi-clipboard2-pulse fs-3 line-height-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-12 col-xl-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-hospital me-2 text-primary"></i>Daftar Klinik / Poli</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted sticky-top" style="font-size: 0.85rem;">
                                <tr>
                                    <th class="text-center ps-4" width="10%">No</th>
                                    <th>Nama Klinik</th>
                                    <th class="text-center pe-4" width="20%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if(mysqli_num_rows($query_list_klinik) > 0): 
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($query_list_klinik)): 
                                ?>
                                    <tr>
                                        <td class="text-center text-muted ps-4"><?= $no++; ?></td>
                                        <td class="text-dark fw-medium"><?= $row['nama_klinik']; ?></td>
                                        <td class="text-center pe-4">
                                            <?php 
                                                if(strcasecmp(trim($row['status']), 'Active') === 0) {
                                                    echo '<span class="badge bg-success bg-opacity-10 text-success rounded-pill small px-3">Active</span>';
                                                } else {
                                                    echo '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill small px-3">Inactive</span>';
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile; 
                                else: 
                                ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Belum ada data klinik</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-heart-pulse me-2 text-success"></i>Daftar Dokter</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted sticky-top" style="font-size: 0.85rem;">
                                <tr>
                                    <th class="text-center ps-4" width="10%">No</th>
                                    <th>Nama Dokter</th>
                                    <th>Spesialisasi</th>
                                    <th class="text-center pe-4" width="20%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if(mysqli_num_rows($query_list_dokter) > 0): 
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($query_list_dokter)): 
                                ?>
                                    <tr>
                                        <td class="text-center text-muted ps-4"><?= $no++; ?></td>
                                        <td class="text-dark fw-medium"><?= $row['nama_dokter']; ?></td>
                                        <td class="text-muted small"><?= $row['spesialisasi']; ?></td>
                                        <td class="text-center pe-4">
                                            <?php 
                                                if(strcasecmp(trim($row['status']), 'Active') === 0) {
                                                    echo '<span class="badge bg-success bg-opacity-10 text-success rounded-pill small px-3">Active</span>';
                                                } else {
                                                    echo '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill small px-3">Inactive</span>';
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile; 
                                else: 
                                ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data dokter</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mt-2 mb-5">
        
        <div class="col-12 col-xl-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-graph-up me-2 text-primary"></i>Tren Kunjungan Pasien (7 Hari Terakhir)</h6>
                </div>
                <div class="card-body">
                    <canvas id="kunjunganChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-pie-chart me-2 text-warning"></i>Distribusi Pasien per Poli</h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="distribusiChart"></canvas>
                </div>
            </div>
        </div>

    </div>

</div> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labelKunjungan = <?php echo json_encode($label_kunjungan); ?>;
    const dataKunjungan = <?php echo json_encode($data_kunjungan); ?>;
    
    const labelDistribusi = <?php echo json_encode($label_distribusi); ?>;
    const dataDistribusi = <?php echo json_encode($data_distribusi); ?>;

    // 1. Konfigurasi Line Chart
    const ctxKunjungan = document.getElementById('kunjunganChart').getContext('2d');
    new Chart(ctxKunjungan, {
        type: 'line',
        data: {
            labels: labelKunjungan,
            datasets: [{
                label: 'Jumlah Pasien',
                data: dataKunjungan,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // 2. Konfigurasi Doughnut Chart (Distribusi per Poli)
    const ctxDistribusi = document.getElementById('distribusiChart').getContext('2d');
    new Chart(ctxDistribusi, {
        type: 'doughnut',
        data: {
            labels: labelDistribusi,
            datasets: [{
                data: dataDistribusi,
                backgroundColor: [
                    '#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6610f2',
                    '#fd7e14', '#20c997', '#e83e8c', '#6f42c1', '#17a2b8', '#28a745' 
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                // UBAH BAGIAN INI: Mengubah display menjadi false untuk menyembunyikan legend
                legend: { 
                    display: false 
                }
            }
        }
    });
</script>

<?php include 'layout/footer.php'; ?>