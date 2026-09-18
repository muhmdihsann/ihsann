<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Operator | SPM Kebakaran</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f8fafc;
        }

        /* =========================
           MAIN LAYOUT
        ========================= */

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* =========================
           LEFT SIDE
        ========================= */

        .visual-side {
            width: 58%;
            min-height: 100vh;
            position: relative;

            background-image:
                linear-gradient(
                    120deg,
                    rgba(7, 18, 35, 0.94),
                    rgba(10, 31, 52, 0.82)
                ),
                url('https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=1600&auto=format&fit=crop');

            background-size: cover;
            background-position: center;

            display: flex;
            align-items: center;

            color: white;
        }

        .visual-side::after {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(220, 38, 38, 0.08);
            top: -200px;
            right: -180px;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            padding: 70px;
            max-width: 850px;
        }

        /* Logo */

        .government-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 55px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);

            border-radius: 12px;

            font-size: 20px;
        }

        .brand-text small {
            display: block;
            color: rgba(255, 255, 255, 0.6);
            font-size: 11px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .brand-text strong {
            display: block;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        /* Badge */

        .system-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 8px 14px;

            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(248, 113, 113, 0.35);

            border-radius: 50px;

            color: #fecaca;

            font-size: 12px;
            font-weight: 600;

            margin-bottom: 22px;
        }

        .system-title {
            font-size: clamp(38px, 4vw, 62px);
            line-height: 1.08;
            font-weight: 700;

            letter-spacing: -1.5px;

            margin-bottom: 22px;
        }

        .system-title span {
            color: #ef4444;
        }

        .system-description {
            max-width: 650px;

            color: rgba(255, 255, 255, 0.62);

            font-size: 16px;
            line-height: 1.8;

            margin-bottom: 35px;
        }

        /* Information */

        .system-info {
            display: flex;
            gap: 35px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;

            color: rgba(255, 255, 255, 0.55);

            font-size: 13px;
        }

        .info-item i {
            color: #ef4444;
        }

        /* =========================
           RIGHT SIDE
        ========================= */

        .form-side {
            width: 42%;
            min-height: 100vh;

            background: #f8fafc;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 40px;

            position: relative;
        }

        .form-container {
            width: 100%;
            max-width: 430px;
        }

        /* Header */

        .login-header {
            margin-bottom: 32px;
        }

        .login-icon {
            width: 56px;
            height: 56px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #fef2f2;
            color: #dc2626;

            border-radius: 14px;

            font-size: 21px;

            margin-bottom: 20px;
        }

        .login-header h2 {
            color: #111827;

            font-size: 29px;
            font-weight: 700;

            margin-bottom: 8px;
        }

        .login-header p {
            color: #6b7280;

            font-size: 14px;

            margin: 0;
        }

        /* Card */

        .login-card {
            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            padding: 32px;

            box-shadow:
                0 10px 30px rgba(15, 23, 42, 0.06);
        }

        /* Label */

        .form-label {
            color: #374151;

            font-size: 13px;
            font-weight: 600;

            margin-bottom: 8px;
        }

        /* Input */

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;

            left: 16px;
            top: 50%;

            transform: translateY(-50%);

            color: #9ca3af;

            font-size: 14px;

            z-index: 2;
        }

        .form-input {
            width: 100%;

            height: 50px;

            padding: 0 45px;

            background: #ffffff;

            border: 1px solid #d1d5db;

            border-radius: 10px;

            color: #111827;

            font-size: 14px;

            outline: none;

            transition: all 0.2s ease;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .form-input:focus {
            border-color: #dc2626;

            box-shadow:
                0 0 0 3px rgba(220, 38, 38, 0.08);
        }

        /* Password button */

        .password-toggle {
            position: absolute;

            right: 14px;
            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            color: #9ca3af;

            cursor: pointer;

            padding: 5px;
        }

        .password-toggle:hover {
            color: #dc2626;
        }

        /* Login button */

        .btn-login {
            width: 100%;

            height: 50px;

            border: none;

            border-radius: 10px;

            background: #b91c1c;

            color: white;

            font-size: 14px;

            font-weight: 600;

            transition: all 0.2s ease;

            box-shadow:
                0 5px 15px rgba(185, 28, 28, 0.18);
        }

        .btn-login:hover {
            background: #991b1b;

            transform: translateY(-1px);

            box-shadow:
                0 8px 20px rgba(185, 28, 28, 0.25);
        }

        /* Security */

        .security-info {
            display: flex;
            align-items: center;
            justify-content: center;

            gap: 7px;

            color: #9ca3af;

            font-size: 11px;

            margin-top: 20px;
        }

        .security-info i {
            color: #16a34a;
        }

        /* Back */

        .back-home {
            text-align: center;

            margin-top: 25px;
        }

        .back-home a {
            color: #6b7280;

            text-decoration: none;

            font-size: 13px;

            transition: 0.2s;
        }

        .back-home a:hover {
            color: #b91c1c;
        }

        /* Footer */

        .form-footer {
            text-align: center;

            margin-top: 30px;

            color: #9ca3af;

            font-size: 11px;
        }

        /* Alert */

        .custom-alert {
            border: none;

            border-radius: 10px;

            background: #fef2f2;

            color: #991b1b;

            font-size: 13px;

            padding: 12px 14px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 992px) {

            .login-wrapper {
                display: block;
                overflow: auto;
            }

            .visual-side {
                display: none;
            }

            .form-side {
                width: 100%;
                min-height: 100vh;

                padding: 30px 20px;
            }

            .form-container {
                max-width: 430px;
            }
        }

        @media (max-width: 480px) {

            .form-side {
                padding: 25px 16px;
            }

            .login-card {
                padding: 24px 20px;
            }

            .login-header h2 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <!-- =========================
             LEFT SIDE
        ========================= -->

        <section class="visual-side">

            <div class="visual-content">

                <!-- Government Brand -->

                <div class="government-brand">

                    <div class="brand-icon">
                        <i class="fas fa-shield-halved"></i>
                    </div>

                    <div class="brand-text">
                        <small>Portal Resmi</small>
                        <strong>Kementerian Dalam Negeri</strong>
                    </div>

                </div>


                <!-- System Badge -->

                <div class="system-badge">

                    <i class="fas fa-fire-flame-curved"></i>

                    Sistem Informasi Pelayanan Dasar

                </div>


                <!-- Title -->

                <h1 class="system-title">

                    Sistem Informasi
                    <br>

                    <span>SPM Kebakaran</span>

                </h1>


                <!-- Description -->

                <p class="system-description">

                    Platform pengelolaan dan pemantauan data Standar
                    Pelayanan Minimal bidang kebakaran untuk mendukung
                    penyajian informasi yang terintegrasi, akurat,
                    dan akuntabel.

                </p>


                <!-- Information -->

                <div class="system-info">

                    <div class="info-item">

                        <i class="fas fa-database"></i>

                        <span>Data Terintegrasi</span>

                    </div>

                    <div class="info-item">

                        <i class="fas fa-chart-line"></i>

                        <span>Monitoring Nasional</span>

                    </div>

                    <div class="info-item">

                        <i class="fas fa-lock"></i>

                        <span>Akses Terproteksi</span>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================
             RIGHT SIDE
        ========================= -->

        <section class="form-side">

            <div class="form-container">

                <!-- Login Header -->

                <div class="login-header">

                    <div class="login-icon">

                        <i class="fas fa-fire-extinguisher"></i>

                    </div>

                    <h2>Login Operator</h2>

                    <p>
                        Masukkan akun Anda untuk mengakses sistem.
                    </p>

                </div>


                <!-- Login Card -->

                <div class="login-card">

                    @if(session('error'))

                        <div class="custom-alert mb-4">

                            <i class="fas fa-circle-exclamation me-2"></i>

                            {{ session('error') }}

                        </div>

                    @endif


                    <form action="{{ url('/login') }}" method="POST">

                        @csrf


                        <!-- Email -->

                        <div class="mb-4">

                            <label class="form-label">
                                Email Operator
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-envelope input-icon"></i>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-input"
                                    placeholder="Masukkan email"
                                    autocomplete="email"
                                    required
                                    autofocus
                                >

                            </div>

                        </div>


                        <!-- Password -->

                        <div class="mb-4">

                            <label class="form-label">
                                Password
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-lock input-icon"></i>

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-input"
                                    placeholder="Masukkan password"
                                    autocomplete="current-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    onclick="togglePassword()"
                                    aria-label="Tampilkan password"
                                >
                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                </button>

                            </div>

                        </div>


                        <!-- Login Button -->

                        <button type="submit" class="btn-login">

                            <i class="fas fa-right-to-bracket me-2"></i>

                            Masuk ke Sistem

                        </button>

                    </form>


                    <!-- Security -->

                    <div class="security-info">

                        <i class="fas fa-circle-check"></i>

                        <span>Koneksi dan akses sistem terlindungi</span>

                    </div>

                </div>


                <!-- Back -->

                <div class="back-home">

                    <a href="{{ url('/') }}">

                        <i class="fas fa-arrow-left me-2"></i>

                        Kembali ke Dashboard

                    </a>

                </div>


                <!-- Footer -->

                <div class="form-footer">

                    SPM Kebakaran &nbsp;•&nbsp; Kementerian Dalam Negeri

                    <br>

                    © {{ date('Y') }} Sistem Informasi SPM Kebakaran

                </div>

            </div>

        </section>

    </div>


    <!-- Password Toggle -->

    <script>

        function togglePassword() {

            const password = document.getElementById('password');
            const icon = document.getElementById('passwordIcon');

            if (password.type === 'password') {

                password.type = 'text';

                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');

            } else {

                password.type = 'password';

                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');

            }

        }

    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
