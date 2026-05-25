<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RKM SPI Dashboard')</title>

    <!-- Panggil Vite buat nge-load Bootstrap & SweetAlert2 -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Tambahin Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        /* Global & Font */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            /* Abu-abu super kalem ala Tailwind */
            color: #334155;
        }

        /* Layouting */
        .sidebar-desktop {
            width: 260px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
        }

        .main-content {
            flex: 1;
            overflow-x: hidden;
        }

        /* Sidebar Menu Modern */
        .nav-link {
            color: #64748b;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: #f1f5f9;
            color: #4f46e5;
        }

        .nav-link.active {
            background-color: #e0e7ff;
            /* Latar biru soft */
            color: #4338ca;
            /* Teks indigo gelap */
            font-weight: 600;
        }

        /* Card Modern (Border hilang, shadow super soft) */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Input Form Elegan */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: #334155;
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #ffffff;
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        /* Button Modern */
        .btn {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #4f46e5;
            /* Indigo */
            border-color: #4f46e5;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        /* Tabel Minimalis */
        .table> :not(caption)>*>* {
            padding: 1rem 1.5rem;
            border-bottom-color: #f1f5f9;
            color: #475569;
        }

        .table-light {
            background-color: #f8fafc;
        }

        .table-light th {
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }

        /* Badge modern */
        .badge {
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 6px;
        }
    </style>
    <style>
        /* Custom styling biar sidebar lebih clean */
        body {
            background-color: #f4f7f6;
        }

        .sidebar-desktop {
            width: 250px;
            min-height: 100vh;
            background: #ffffff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        }

        .main-content {
            flex: 1;
            overflow-x: hidden;
        }

        .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #e9ecef;
            color: #0d6efd;
        }
    </style>
</head>

<body class="d-flex">

    <!-- 1. SIDEBAR DESKTOP (Sembunyi kalau di layar HP/md-down) -->
    <nav class="sidebar-desktop d-none d-md-flex flex-column p-3">
        <h4 class="fw-bold mb-4 text-center mt-2">RKM SPI</h4>
        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house me-2"></i> Dashboard
                </a>
            </li>

            @if(Auth::user()->role && Auth::user()->role->role_level != 4)
            <li class="nav-item">
                <a href="{{ route('jalur.index') }}" class="nav-link {{ request()->routeIs('jalur.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-map-location-dot me-2"></i> Rencana Jalur RKM
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users me-2"></i> Data Member
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear me-2"></i> Kelola Pengguna
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield me-2"></i> Kelola Role
                </a>
            </li>
            @endif
            <!-- Nanti menu lain ditambah di sini -->
        </ul>
    </nav>

    <!-- 2. MAIN CONTENT AREA -->
    <div class="main-content d-flex flex-column">

        <!-- Navbar Atas -->
        <header class="p-3 bg-white shadow-sm d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <!-- Tombol Hamburger khusus muncul di HP buat manggil Sidebar Mobile -->
                <button class="btn btn-light d-md-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    ☰
                </button>
                <h5 class="mb-0 fw-bold">@yield('title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center">
                <span class="me-3 fw-medium d-none d-sm-block">
                    Halo, {{ Auth::user()->user_username }}
                </span>
                <!-- Tombol Logout di Sidebar/Header -->
                <a href="javascript:void(0)" onclick="confirmLogout()" class="nav-link text-danger fw-bold">
                    Logout
                </a>

                <!-- Form Logout Tersembunyi -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </header>

        <!-- Area Konten Utama (Berubah-ubah tiap halaman) -->
        <main class="p-4">
            @yield('content')
        </main>
    </div>

    <!-- 3. SIDEBAR MOBILE (Offcanvas menu geser) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary fw-bold">RKM SPI</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house me-2"></i> Dashboard
                    </a>
                </li>

                @if(Auth::user()->role && Auth::user()->role->role_level != 4)
                <li class="nav-item">
                    <a href="{{ route('jalur.index') }}" class="nav-link {{ request()->routeIs('jalur.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-map-location-dot me-2"></i> Rencana Jalur RKM
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users me-2"></i> Data Member
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear me-2"></i> Kelola Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-shield me-2"></i> Kelola Role
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Buat nyimpen script spesifik per halaman (misal Sweetalert) -->
    @stack('scripts')
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Selesai Bekerja?',
                text: "Anda akan keluar dari sesi manajemen RKM SPI.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e293b', // Warna gelap ala Jepang
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'rounded-0', // Biar siku tegas
                    cancelButton: 'rounded-0 text-dark'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jalankan form logout tersembunyi
                    document.getElementById('logout-form').submit();
                }
            })
        }
    </script>
</body>

</html>