<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KTU Shipyard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-ktu.jpg') }}">

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        /* Background container untuk kontrol yang lebih baik */
        .background-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            /* Coba berbagai path alternatif */
            background-image: url("assets/image/backgroud-login.png");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 100% 100%; /* Fit exact ke setiap layar tanpa mempertahankan aspect ratio */
            background-attachment: fixed;
            background-color: #f8f9fa; /* Fallback color untuk area kosong */
        }

        /* Alternative: gunakan body sebagai backup */
        body {
            background-image: url("/assets/image/backgroud-login.png");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 100% 100%; /* Fit exact ke setiap layar */
            background-attachment: fixed;
        }
        
        /* Override body background untuk mobile */
        @media (max-width: 768px) {
            body {
                background-image: none !important;
                background-color: rgba(6, 41, 99, 0.75);
            }
        }

        /* Overlay untuk memastikan readability */
        .background-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(248, 249, 250, 0.1);
        }

        .login-container {
            position: relative;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            min-height: 100vh;
            padding: 20px 80px 20px 20px; /* Tambah padding kanan untuk geser ke kiri */
        }

        /* Auto center untuk mobile dari awal */
        @media (max-width: 991px) {
            .login-container {
                justify-content: center;
                padding: 20px;
            }
            
            .login-card {
                transform: translateX(0); /* Reset posisi untuk mobile */
            }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 6px;
            box-shadow: 0px 5px 30px rgba(0, 0, 0, 0.15);
            padding: 30px;
            width: 100%;
            max-width: 360px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateX(-50px); /* Geser card 50px ke kiri */
        }

        .login-card .login-logo {
            display: block;
            width: 110px;
            margin-bottom: 15px;
        }

        .login-card h3 {
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            color: #333;
        }

        .login-card p {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        .btn-custom {
            background-color: #7F56D8 !important;
            color: #fff !important;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 1px solid #7F56D8 !important;
        }

        .btn-custom:hover {
            background-color: #fff !important;
            color: #7F56D8 !important;
            border: 1px solid #7F56D8 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(127, 86, 216, 0.3);
        }

        /* Remember me checkbox styling */
        .form-check {
            margin-bottom: 1.5rem;
        }

        .form-check-input:checked {
            background-color: #7F56D8;
            border-color: #7F56D8;
        }

        .form-check-input:focus {
            border-color: #7F56D8;
            box-shadow: 0 0 0 0.25rem rgba(127, 86, 216, 0.25);
        }

        .form-check-label {
            color: #495057;
            font-size: 0.9rem;
        }

        /* Loading state for login button */
        .btn-custom:disabled {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            cursor: not-allowed;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Tablet responsive */
        @media (max-width: 1024px) {
            .background-wrapper {
                background-attachment: scroll;
                background-size: 100% 100%; /* Fit exact untuk tablet */
            }
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .background-wrapper {
                background-image: none !important; /* Hilangkan background image di mobile */
                background-color: rgba(6, 41, 99, 0.75); /* Ganti dengan warna biru solid */
            }
            
            /* Hapus overlay karena sudah menggunakan background solid */

            .login-container {
                justify-content: center;
                padding: 15px;
                min-height: 100vh;
                align-items: center;
                position: relative;
                z-index: 2;
            }

            .login-card {
                max-width: 320px;
                padding: 25px;
                margin: auto;
                background: rgba(255, 255, 255, 0.98);
                transform: translateX(0); /* Pastikan tidak ada transform di mobile */
                position: relative;
                z-index: 3;
            }

            .login-card .login-logo {
                width: 90px;
                margin: 0 auto 15px auto;
            }

            .login-card h3 {
                font-size: 1.3rem;
            }
        }

        /* Small mobile responsive */
        @media (max-width: 480px) {
            .background-wrapper {
                background-image: none !important; /* Hilangkan background image di small mobile */
                background-color: rgba(6, 41, 99, 0.75); /* Ganti dengan warna biru solid */
                background-attachment: scroll;
            }
            
            /* Hapus overlay karena sudah menggunakan background solid */

            .login-container {
                justify-content: center;
                padding: 10px;
                align-items: center;
                position: relative;
                z-index: 2;
            }

            .login-card {
                max-width: 280px;
                padding: 20px;
                border-radius: 8px;
                margin: auto;
                position: relative;
                z-index: 3;
            }

            .login-card .login-logo {
                width: 80px;
                margin: 0 auto 15px auto;
            }

            .login-card h3 {
                font-size: 1.1rem;
                margin-bottom: 8px;
            }

            .login-card p {
                font-size: 0.9rem;
                margin-bottom: 15px;
            }
        }

        /* Very small screens */
        @media (max-width: 360px) {
            .background-wrapper {
                background-image: none !important; /* Hilangkan background image di very small screen */
                background-color: rgba(6, 41, 99, 0.75); /* Ganti dengan warna biru solid */
            }
            
            .login-container {
                justify-content: center;
                align-items: center;
                padding: 5px;
                position: relative;
                z-index: 2;
            }

            .login-card {
                max-width: 260px;
                padding: 15px;
                margin: auto;
                position: relative;
                z-index: 3;
            }

            .login-card .login-logo {
                margin: 0 auto 10px auto;
            }
        }

        /* Landscape mobile */
        @media (max-height: 600px) and (orientation: landscape) {
            .background-wrapper {
                background-image: none !important; /* Hilangkan background image di landscape mobile */
                background-color: rgba(6, 41, 99, 0.75); /* Ganti dengan warna biru solid */
                background-attachment: scroll;
            }

            .login-container {
                min-height: 100vh;
                padding: 10px;
                position: relative;
                z-index: 2;
            }

            .login-card {
                padding: 20px;
                position: relative;
                z-index: 3;
            }

            .login-card h3 {
                font-size: 1.2rem;
                margin-bottom: 8px;
            }

            .login-card p {
                margin-bottom: 15px;
            }
        }

        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .background-wrapper {
                background-size: 100% 100%; /* Fit exact untuk high DPI */
            }
        }

        /* Print styles */
        @media print {
            .background-wrapper {
                display: none;
            }
            
            body {
                background: white;
            }
        }
    </style>
</head>

<body>
    <div class="background-wrapper">
        <div class="background-overlay"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <img src="{{ asset('assets/image/logo-ktu-shipyard.png') }}" alt="KTU Logo" class="login-logo">
            <h3 style="font-size:1.35rem; font-weight:700; color:rgb(var(--NN800, 64, 68, 78)); margin:0 0 6px 0;">
                IT Support Ticketing System
            </h3>
            <p style="margin:0;">KTU SHIPYARD</p>

            {{-- Tampilkan error jika ada --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tampilkan pesan sukses jika ada --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tampilkan pesan info jika ada --}}
            @if (session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf
    <div class="mb-3">
        <label for="id_staff" class="form-label">Username</label>
        <input type="text" id="id_staff" name="id_staff" class="form-control"
               value="{{ old('id_staff') }}" required autofocus>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>

    {{-- Remember Me checkbox --}}
    <div class="mb-3 form-check">
        <input type="checkbox" name="remember" id="remember" value="1"
               class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">
            Remember me.
        </label>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-custom w-50" id="loginBtn">
            <span class="btn-text">Login</span>
            <span class="btn-loading d-none">
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                Logging in...
            </span>
        </button>
    </div>
</form>


            {{-- Optional: Forgot Password Link --}}
            <div class="text-center mt-3">
                <small>
                    <a href="#" class="text-muted text-decoration-none" onclick="showForgotPasswordInfo()">
                        Forgot your password?
                    </a>
                </small>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = loginBtn.querySelector('.btn-text');
            const btnLoading = loginBtn.querySelector('.btn-loading');

            // Handle form submission with loading state
            loginForm.addEventListener('submit', function(e) {
                // Show loading state
                loginBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');

                // If there's a validation error, the page will reload and reset the button
                // If login is successful, user will be redirected
                
                // Optional: Add timeout to re-enable button in case of network issues
                setTimeout(function() {
                    loginBtn.disabled = false;
                    btnText.classList.remove('d-none');
                    btnLoading.classList.add('d-none');
                }, 10000); // 10 seconds timeout
            });

            // Remember me tooltip or info
            const rememberCheckbox = document.getElementById('remember');
            rememberCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    console.log('Remember me enabled - user will stay logged in for 30 days');
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });

        // Forgot password info function
        function showForgotPasswordInfo() {
            alert('Please contact IT administrator to reset your password.\n\nEmail: it@ktushipyard.com\nPhone: +62-XXX-XXXX-XXXX');
        }

        // Prevent multiple form submissions
        let isSubmitting = false;
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
        });

        // Enhanced security: Clear form data on page unload
        window.addEventListener('beforeunload', function() {
            const passwordField = document.querySelector('input[name="password"]');
            if (passwordField) {
                passwordField.value = '';
            }
        });
    </script>
</body>
</html>