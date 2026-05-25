    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - RKM SPI</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body {
                /* Gradasi biru laut yang tenang */
                background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
                font-family: 'Inter', sans-serif;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
                overflow: hidden;
                position: relative;
            }

            /* Eleman Gelombang di Background */
            .ocean-wave {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 150px;
                background: url('https://raw.githubusercontent.com/yusufshakeel/svg-wave/master/wave.svg');
                background-size: 1000px 150px;
                opacity: 0.1;
                animation: wave-move 20s linear infinite;
            }

            @keyframes wave-move {
                0% {
                    background-position-x: 0;
                }

                100% {
                    background-position-x: 1000px;
                }
            }

            .login-card {
                background: rgba(255, 255, 255, 0.9);
                /* Sedikit transparan */
                backdrop-filter: blur(10px);
                border: 1px solid rgba(186, 230, 253, 0.5);
                border-radius: 0px;
                box-shadow: 0 20px 50px rgba(7, 89, 133, 0.05);
                width: 100%;
                max-width: 400px;
                padding: 50px;
                position: relative;
                z-index: 10;
            }

            .vertical-text {
                position: absolute;
                left: -45px;
                top: 60px;
                writing-mode: vertical-rl;
                color: #0369a1;
                /* Biru laut dalam */
                font-weight: 400;
                letter-spacing: 6px;
                font-size: 0.75rem;
                opacity: 0.6;
            }

            .brand-title {
                font-weight: 700;
                letter-spacing: -1px;
                color: #0c4a6e;
                /* Navy Japanese */
                margin-bottom: 35px;
            }

            .form-control {
                border: none;
                border-bottom: 2px solid #e2e8f0;
                border-radius: 0;
                padding: 12px 0;
                background: transparent;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                color: #0c4a6e;
            }

            .form-control:focus {
                box-shadow: none;
                border-bottom-color: #0ea5e9;
                /* Biru cerah saat fokus */
            }

            .btn-login {
                background: #0c4a6e;
                /* Deep Ocean Blue */
                color: white;
                border-radius: 0;
                padding: 14px;
                width: 100%;
                margin-top: 30px;
                font-weight: 500;
                letter-spacing: 3px;
                transition: 0.3s;
                border: none;
            }

            .btn-login:hover {
                background: #0ea5e9;
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
            }

            .jp-decoration {
                font-size: 0.65rem;
                color: #7dd3fc;
                margin-top: 25px;
                text-align: center;
                letter-spacing: 4px;
                font-weight: 600;
            }
        </style>
    </head>

    <body>

        <div class="ocean-wave"></div>
        <div class="ocean-wave" style="bottom: 10px; opacity: 0.05; animation-duration: 15s; transform: scaleY(-1);"></div>

        <div class="login-card">
            <div class="vertical-text">ようこそ — OCEAN MIST</div>

            <h3 class="brand-title text-center">RKM SPI</h3>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="small text-muted fw-semibold">USERNAME</label>
                    <input type="text" name="user_username" class="form-control" placeholder="Entri Nama Pengguna" required autofocus>
                </div>

                <div class="mb-2 position-relative">
                    <label class="small text-muted fw-semibold">PASSWORD</label>
                    <input type="password" name="password" id="passInput" class="form-control" placeholder="Entri Kata Sandi" required>
                    <span id="toggleEye" style="position: absolute; right: 0; bottom: 12px; cursor: pointer; opacity: 0.5;">👁️</span>
                </div>

                <button type="submit" class="btn btn-login">MASUK</button>
            </form>

            <div class="jp-decoration">Digital Monitoring System</div>
        </div>

        <script>
            // Toggle Intip Password
            document.getElementById('toggleEye').addEventListener('click', function() {
                const pass = document.getElementById('passInput');
                pass.type = pass.type === 'password' ? 'text' : 'password';
                this.textContent = pass.type === 'password' ? '👁️' : '🙈';
            });

            // Alert Gagal
            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('
                error ') }}',
                confirmButtonColor: '#1e293b'
            });
            @endif
        </script>
    </body>

    </html>