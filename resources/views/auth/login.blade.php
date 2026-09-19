<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Operator | SPM Kebakaran</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f5f7fa;
        }

        body {
            overflow-x: hidden;
        }

        /* =====================================================
           MAIN LAYOUT
        ===================================================== */

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            background: #f5f7fa;
        }


        /* =====================================================
           LEFT / VISUAL SIDE
        ===================================================== */

        .visual-side {
            width: 58%;
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;

            /*
             * GANTI GAMBAR DI:
             * public/images/login-bg.jpg
             *
             * Tidak perlu mengubah kode ini lagi.
             */

            background-image:
                linear-gradient(
                    115deg,
                    rgba(3, 15, 30, 0.94) 0%,
                    rgba(4, 24, 44, 0.86) 45%,
                    rgba(8, 30, 52, 0.72) 100%
                ),
                url("{{ asset('images/login-bg.jpg') }}");

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            color: #ffffff;
        }

        /* Efek gelap tambahan di bagian bawah */
        .visual-side::before {
            content: "";
            position: absolute;
            inset: 0;

            background:
                linear-gradient(
                    to top,
                    rgba(2, 10, 20, 0.45),
                    transparent 50%
                );

            pointer-events: none;
        }

        /* Lingkaran dekoratif */
        .visual-side::after {
            content: "";
            position: absolute;

            width: 620px;
            height: 620px;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(220, 38, 38, 0.12) 0%,
                    rgba(220, 38, 38, 0.03) 50%,
                    transparent 70%
                );

            top: -260px;
            right: -240px;

            pointer-events: none;
        }


        /* =====================================================
           VISUAL CONTENT
        ===================================================== */

        .visual-content {
            position: relative;
            z-index: 5;

            width: 100%;
            max-width: 900px;

            padding: 70px 75px;
        }


        /* =====================================================
           GOVERNMENT BRAND
        ===================================================== */

        .government-brand {
            display: flex;
            align-items: center;
            gap: 15px;

            margin-bottom: 58px;
        }

        .brand-icon {
            width: 50px;
            height: 50px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 13px;

            background: rgba(255, 255, 255, 0.10);

            border: 1px solid rgba(255, 255, 255, 0.18);

            backdrop-filter: blur(10px);

            font-size: 20px;

            box-shadow:
                0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .brand-text small {
            display: block;

            color: rgba(255, 255, 255, 0.60);

            font-size: 10px;
            font-weight: 600;

            letter-spacing: 1.5px;

            text-transform: uppercase;

            margin-bottom: 3px;
        }

        .brand-text strong {
            display: block;

            color: #ffffff;

            font-size: 15px;
            font-weight: 650;

            letter-spacing: 0.2px;
        }


        /* =====================================================
           SYSTEM BADGE
        ===================================================== */

        .system-badge {
            display: inline-flex;
            align-items: center;
            gap: 9px;

            padding: 9px 15px;

            background: rgba(220, 38, 38, 0.13);

            border: 1px solid rgba(248, 113, 113, 0.30);

            border-radius: 50px;

            color: #fecaca;

            font-size: 12px;
            font-weight: 600;

            margin-bottom: 22px;

            backdrop-filter: blur(8px);
        }

        .system-badge i {
            color: #f87171;
        }


        /* =====================================================
           SYSTEM TITLE
        ===================================================== */

        .system-title {
            margin: 0 0 22px;

            font-size: clamp(42px, 4.2vw, 64px);

            line-height: 1.04;

            font-weight: 750;

            letter-spacing: -2.2px;

            color: #ffffff;
        }

        .system-title span {
            color: #ef4444;
        }


        /* =====================================================
           DESCRIPTION
        ===================================================== */

        .system-description {
            max-width: 680px;

            margin: 0 0 36px;

            color: rgba(255, 255, 255, 0.66);

            font-size: 15px;

            line-height: 1.8;
        }


        /* =====================================================
           INFORMATION ITEMS
        ===================================================== */

        .system-info {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 9px;

            color: rgba(255, 255, 255, 0.58);

            font-size: 12px;
            font-weight: 500;
        }

        .info-item i {
            color: #ef4444;
            font-size: 13px;
        }


        /* =====================================================
           RIGHT FORM SIDE
        ===================================================== */

        .form-side {
            width: 42%;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 50px 55px;

            position: relative;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(220, 38, 38, 0.035),
                    transparent 35%
                ),
                #f8fafc;
        }


        /* =====================================================
           FORM CONTAINER
        ===================================================== */

        .form-container {
            width: 100%;
            max-width: 440px;
        }


        /* =====================================================
           LOGIN HEADER
        ===================================================== */

        .login-header {
            margin-bottom: 27px;
        }

        .login-icon {
            width: 58px;
            height: 58px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 19px;

            border-radius: 15px;

            color: #dc2626;

            background:
                linear-gradient(
                    145deg,
                    #fff1f2,
                    #fef2f2
                );

            border: 1px solid #fee2e2;

            font-size: 21px;

            box-shadow:
                0 8px 20px rgba(220, 38, 38, 0.07);
        }

        .login-header h2 {
            margin: 0 0 7px;

            color: #111827;

            font-size: 30px;

            font-weight: 750;

            letter-spacing: -0.7px;
        }

        .login-header p {
            margin: 0;

            color: #6b7280;

            font-size: 14px;

            line-height: 1.6;
        }


        /* =====================================================
           LOGIN CARD
        ===================================================== */

        .login-card {
            padding: 31px;

            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            box-shadow:
                0 18px 45px rgba(15, 23, 42, 0.07),
                0 2px 6px rgba(15, 23, 42, 0.025);
        }


        /* =====================================================
           LABEL
        ===================================================== */

        .form-label {
            display: block;

            margin-bottom: 8px;

            color: #374151;

            font-size: 13px;

            font-weight: 650;
        }


        /* =====================================================
           INPUT
        ===================================================== */

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

            transition: color 0.2s ease;
        }

        .form-input {
            width: 100%;
            height: 52px;

            padding: 0 46px;

            border: 1px solid #d1d5db;

            border-radius: 11px;

            outline: none;

            background: #ffffff;

            color: #111827;

            font-size: 14px;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
        }

        .form-input:hover {
            border-color: #bfc4cc;
        }

        .form-input:focus {
            border-color: #dc2626;

            background: #ffffff;

            box-shadow:
                0 0 0 3px rgba(220, 38, 38, 0.08);
        }

        .form-input:focus + .password-toggle {
            color: #dc2626;
        }

        .form-input::placeholder {
            color: #a1a9b5;
        }


        /* =====================================================
           PASSWORD TOGGLE
        ===================================================== */

        .password-toggle {
            position: absolute;

            right: 13px;
            top: 50%;

            transform: translateY(-50%);

            width: 32px;
            height: 32px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;

            background: transparent;

            color: #9ca3af;

            border-radius: 7px;

            cursor: pointer;

            transition:
                background 0.2s ease,
                color 0.2s ease;
        }

        .password-toggle:hover {
            background: #fef2f2;
            color: #dc2626;
        }


        /* =====================================================
           LOGIN BUTTON
        ===================================================== */

        .btn-login {
            width: 100%;
            height: 52px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;

            border-radius: 11px;

            background:
                linear-gradient(
                    135deg,
                    #dc2626,
                    #b91c1c
                );

            color: #ffffff;

            font-size: 14px;
            font-weight: 650;

            cursor: pointer;

            box-shadow:
                0 8px 20px rgba(185, 28, 28, 0.18);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                filter 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);

            filter: brightness(0.97);

            box-shadow:
                0 11px 25px rgba(185, 28, 28, 0.25);
        }

        .btn-login:active {
            transform: translateY(0);
        }


        /* =====================================================
           SECURITY INFO
        ===================================================== */

        .security-info {
            display: flex;
            align-items: center;
            justify-content: center;

            gap: 7px;

            margin-top: 19px;

            color: #9ca3af;

            font-size: 11px;
        }

        .security-info i {
            color: #16a34a;
        }


        /* =====================================================
           ALERT
        ===================================================== */

        .custom-alert {
            display: flex;
            align-items: flex-start;

            gap: 3px;

            padding: 12px 14px;

            margin-bottom: 22px;

            border: 1px solid #fecaca;

            border-radius: 10px;

            background: #fef2f2;

            color: #991b1b;

            font-size: 12px;

            line-height: 1.5;
        }


        /* =====================================================
           BACK HOME
        ===================================================== */

        .back-home {
            text-align: center;

            margin-top: 23px;
        }

        .back-home a {
            display: inline-flex;
            align-items: center;

            color: #6b7280;

            text-decoration: none;

            font-size: 13px;

            transition:
                color 0.2s ease,
                transform 0.2s ease;
        }

        .back-home a:hover {
            color: #b91c1c;

            transform: translateX(-2px);
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .form-footer {
            text-align: center;

            margin-top: 28px;

            color: #9ca3af;

            font-size: 10.5px;

            line-height: 1.8;
        }

        .form-footer strong {
            color: #6b7280;
            font-weight: 600;
        }


        /* =====================================================
           RESPONSIVE TABLET
        ===================================================== */

        @media (max-width: 1100px) {

            .visual-content {
                padding: 55px;
            }

            .form-side {
                padding: 40px 35px;
            }

            .system-title {
                font-size: 48px;
            }

            .system-info {
                gap: 18px;
            }
        }


        /* =====================================================
           RESPONSIVE MOBILE
        ===================================================== */

        @media (max-width: 992px) {

            .login-wrapper {
                display: block;
                min-height: 100vh;
            }

            .visual-side {
                width: 100%;
                min-height: 230px;

                align-items: flex-end;
            }

            .visual-content {
                padding: 32px 28px;
            }

            .government-brand {
                margin-bottom: 20px;
            }

            .system-badge {
                margin-bottom: 12px;
            }

            .system-title {
                font-size: 34px;
                letter-spacing: -1px;
                margin-bottom: 12px;
            }

            .system-description,
            .system-info {
                display: none;
            }

            .form-side {
                width: 100%;
                min-height: calc(100vh - 230px);

                padding: 38px 22px;

                align-items: flex-start;
            }

            .form-container {
                max-width: 450px;
            }
        }


        /* =====================================================
           SMALL MOBILE
        ===================================================== */

        @media (max-width: 480px) {

            .visual-side {
                min-height: 205px;
            }

            .visual-content {
                padding: 25px 20px;
            }

            .government-brand {
                margin-bottom: 18px;
            }

            .brand-icon {
                width: 43px;
                height: 43px;
            }

            .brand-text strong {
                font-size: 13px;
            }

            .system-title {
                font-size: 29px;
            }

            .form-side {
                min-height: calc(100vh - 205px);

                padding: 30px 16px;
            }

            .login-header h2 {
                font-size: 26px;
            }

            .login-card {
                padding: 23px 19px;
                border-radius: 15px;
            }

            .login-icon {
                width: 52px;
                height: 52px;
            }

            .form-input,
            .btn-login {
                height: 50px;
            }
        }
    </style>
</head>


<body>

    <div class="login-wrapper">

        <!-- =====================================================
             LEFT SIDE
        ====================================================== -->

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

                    Platform pengelolaan dan pemantauan data
                    Standar Pelayanan Minimal bidang kebakaran
                    untuk mendukung penyajian informasi yang
                    terintegrasi, akurat, dan akuntabel.

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



        <!-- =====================================================
             RIGHT SIDE
        ====================================================== -->

        <section class="form-side">

            <div class="form-container">

                <!-- Login Header -->
                <div class="login-header">

                    <div class="login-icon">

                        <i class="fas fa-fire-extinguisher"></i>

                    </div>

                    <h2>Login Operator</h2>

                    <p>
                        Masukkan akun operator untuk mengakses
                        sistem informasi SPM Kebakaran.
                    </p>

                </div>



                <!-- Login Card -->
                <div class="login-card">

                    {{-- Error Session --}}
                    @if(session('error'))

                        <div class="custom-alert">

                            <i class="fas fa-circle-exclamation"></i>

                            <span>
                                {{ session('error') }}
                            </span>

                        </div>

                    @endif


                    {{-- Validation Error --}}
                    @if($errors->any())

                        <div class="custom-alert">

                            <i class="fas fa-circle-exclamation"></i>

                            <div>
                                {{ $errors->first() }}
                            </div>

                        </div>

                    @endif


                    <!-- Login Form -->
                    <form
                        action="{{ url('/login') }}"
                        method="POST"
                    >

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
                                    placeholder="Masukkan email operator"
                                    autocomplete="email"
                                    value="{{ old('email') }}"
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

                                    <i
                                        class="fas fa-eye"
                                        id="passwordIcon"
                                    ></i>

                                </button>

                            </div>

                        </div>


                        <!-- Login Button -->
                        <button
                            type="submit"
                            class="btn-login"
                        >

                            <i class="fas fa-right-to-bracket me-2"></i>

                            Masuk ke Sistem

                        </button>

                    </form>


                    <!-- Security -->
                    <div class="security-info">

                        <i class="fas fa-circle-check"></i>

                        <span>
                            Koneksi dan akses sistem terlindungi
                        </span>

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

                    <strong>SPM Kebakaran</strong>
                    &nbsp;•&nbsp;
                    Kementerian Dalam Negeri

                    <br>

                    © {{ date('Y') }}
                    Sistem Informasi SPM Kebakaran

                </div>

            </div>

        </section>

    </div>



    <!-- =====================================================
         PASSWORD TOGGLE
    ====================================================== -->

    <script>

        function togglePassword() {

            const password =
                document.getElementById('password');

            const icon =
                document.getElementById('passwordIcon');


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


    <!-- Bootstrap JS -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>
