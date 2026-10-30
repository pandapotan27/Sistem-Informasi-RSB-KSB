<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Pendaftaran Klinik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-image: url('assets/img/rsb.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            z-index: 1;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            overflow: hidden; /* Mencegah konten keluar dari border radius */
        }

        .btn-login {
            border-radius: 8px;
            font-weight: 500;
        }

        .form-control {
            border-radius: 8px;
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        /* Garis pemisah khusus mode desktop */
        @media (min-width: 768px) {
            .divider-vertical {
                border-right: 1px solid rgba(0,0,0,0.1);
            }
        }
    </style>
</head>

<body>

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-11 col-md-10 col-lg-8 col-xl-7">
            <div class="card login-card">
                <div class="card-body p-0"> <div class="row g-0 align-items-center">
                        
                        <div class="col-md-6 p-4 p-md-5 text-center divider-vertical">
                            <i class="bi bi-hospital" style="font-size: 3.5rem; color: #0d6efd;"></i>
                            <h4 class="mt-3 fw-bold text-primary" style="line-height: 1.4;">
                                Sistem Informasi<br>RSB Summarecon
                            </h4>
                            <p class="text-muted small mt-2 mb-0">
                                Pendaftaran Pasien Rawat Jalan
                            </p>
                        </div>

                        <div class="col-md-6 p-4 p-md-5">
                            
                            <h5 class="fw-semibold mb-4 text-center text-md-start">Silakan Login</h5>

                            <?php
                            if(isset($_GET['pesan'])){
                                if($_GET['pesan'] == "gagal"){
                                    echo '<div class="alert alert-danger py-2 small text-center rounded-3 mb-3">Username atau Password salah.</div>';
                                } else if($_GET['pesan'] == "berhasil_register"){
                                    echo '<div class="alert alert-success py-2 small text-center rounded-3 mb-3">Registrasi berhasil! Silakan login.</div>';
                                }
                            }
                            ?>

                            <form action="proses_login.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                                </div>

                                <div class="d-grid mb-3">
                                    <button class="btn btn-primary btn-login py-2">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center text-md-start mt-3">
                                <small class="text-muted">
                                    Belum punya akun? <a href="register.php" class="text-decoration-none fw-semibold">Daftar</a>
                                </small>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>