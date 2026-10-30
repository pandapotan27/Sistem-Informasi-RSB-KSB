<style>
    /* Efek hover khusus untuk link email di footer */
    .footer-contact {
        transition: color 0.2s ease-in-out;
    }
    .footer-contact:hover {
        color: #0d6efd !important;
    }
</style>

<footer class="bg-white border-top border-secondary border-opacity-10 pt-4 pb-4 mt-auto">
    <div class="container-fluid px-4">
        
        <div class="row align-items-center gy-3">
            <div class="col-12 col-lg-8">
                <h5 class="fw-bold text-primary mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-hospital"></i> RSB Summarecon
                </h5>
            </div>

            <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="d-block text-dark fw-bold small text-uppercase mb-1" style="letter-spacing: 0.5px;">Hubungi Kami</span>
                <a href="mailto:sisfoRsbSummarecon@gmail.com" class="text-muted text-decoration-none small footer-contact d-inline-flex align-items-center gap-2">
                    <i class="bi bi-envelope-at"></i> sisfoRsbSummarecon@gmail.com
                </a>
            </div>
        </div>

        <hr class="border-secondary border-opacity-10 mt-4 mb-3">

        <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted small mb-0">
                &copy; <?= date('Y'); ?> Sistem Pendaftaran Pasien Rawat Jalan. All rights reserved.
            </p>
            <div class="text-muted small">
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-normal">
                    Versi 1.0
                </span>
            </div>
        </div>

    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('btn-hamburger').addEventListener('click', function () {
    document.body.classList.toggle('sidebar-toggled');
});

const currentUrl = window.location.href;
const sidebarLinks = document.querySelectorAll('.sidebar a');

sidebarLinks.forEach(link => {
    if (currentUrl.includes(link.getAttribute('href'))) {
        link.classList.add('active');
    }
});
</script>
</body>
</html>