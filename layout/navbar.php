<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <button class="btn btn-primary me-2" id="btn-hamburger" type="button">
            <i class="bi bi-list fs-4"></i>
        </button>

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
            <i class="bi bi-hospital fs-4"></i>
            <span>RSB Summarecon</span>
        </a>

        <div class="ms-auto d-flex align-items-center">
            <span class="text-white opacity-90 me-3 d-none d-sm-inline">
                Halo, <span class="fw-semibold"><?= htmlspecialchars($_SESSION['nama']); ?></span>
            </span>
            <a href="<?= $base_url ?>logout.php" class="btn btn-light btn-sm fw-medium px-3 rounded-pill shadow-sm">
                Logout
            </a>
        </div>
    </div>
</nav>