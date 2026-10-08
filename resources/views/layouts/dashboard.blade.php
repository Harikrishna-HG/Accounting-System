<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <style>
        @include('layouts._type-scale')
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html { font-size: 78.125%; }

        body {
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size:var(--fs-1-6);
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        .dashboard-body {
            display: flex;
            min-height: 100vh;
        }

        .main-content-area {
            flex: 1;
            /* min-width:auto pins this column to its content's min-content width,
               which defeats .table-responsive and widens the whole page (on a
               phone that widens the layout viewport itself, shrinking all type). */
            min-width: 0;
            margin-left: 80px;
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .main-content-area.expanded {
            margin-left: 340px;
        }

        .page-content {
            flex: 1;
            padding: 0;
        }

        @media (max-width: 768px) {
            .main-content-area {
                margin-left: 0;
            }
            .main-content-area.expanded {
                margin-left: 0;
            }
        }

        /* Toast notification for session messages */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            padding: 10px 16px;
            border-radius: 8px;
            color: white;
            font-size:var(--fs-1-3);
            font-weight: 500;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .toast-success { background: #28a745; }
        .toast-error { background: #CD2737; }
        .toast-info { background: #17a2b8; }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #a1a1a1; }

        .custom-pagination { display: flex; flex-wrap: wrap; list-style: none; gap: 4px; align-items: center; margin: 0; padding: 0; }
        .custom-pagination .page-item.disabled .page-link { color: #adb5bd; cursor: not-allowed; background: #f8f9fa; }
        .custom-pagination .page-item.active .page-link { background: #CD2737; color: white; border-color: #CD2737; }
        .custom-pagination .page-link {
            display: flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px;
            padding: 0 12px;
            border: 1px solid #dee2e6; border-radius: 8px;
            background: white; color: #333;
            font-size:var(--fs-1-3); font-weight: 500;
            text-decoration: none; transition: all 0.2s;
        }
        .custom-pagination .page-link:hover { background: #f0f0f0; border-color: #CD2737; color: #CD2737; }
        .custom-pagination .page-item.disabled .page-link:hover { background: #f8f9fa; border-color: #dee2e6; color: #adb5bd; }
        .custom-pagination .page-item:first-child .page-link,
        .custom-pagination .page-item:last-child .page-link {
            font-size:var(--fs-1-5); font-weight: 700; padding: 0 8px;
        }
    </style>

    <style>
        /* ===== Dashboard Page Layout ===== */
        .dashboard-content { padding: 30px; }
        .page-header { margin-bottom: 30px; }
        .header-top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .header-actions { display: flex; gap: 12px; align-items: center; }
        .page-title { font-size:var(--fs-2-2); font-weight: 700; color: #1a1a2e; margin: 0; }
        .page-subtitle { color: #6c757d; margin: 5px 0 0 0; font-size:var(--fs-1-3); }

        /* ===== Cards ===== */
        .form-card, .list-card { background: white; border-radius: 16px; padding: 40px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .list-card { padding: 0; overflow: hidden; }

        /* ===== Form Elements ===== */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .form-group.full-width { grid-column: 1 / -1; }
        /* Single owner for .detail-grid. It used to be redeclared inside each
           show view's body-level <style>, which lands after this stylesheet and
           therefore won the cascade, making the mobile override below a no-op. */
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .detail-row.full-width { grid-column: 1 / -1; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-weight: 600; color: #1a1a2e; font-size:var(--fs-1-3); }
        .form-group .required { color: #CD2737; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 2px solid #e9ecef; border-radius: 8px; font-size:var(--fs-1-4); font-family: 'Noto Sans Devanagari', sans-serif; transition: border-color 0.2s, box-shadow 0.2s; background: #f8f9fa; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #CD2737; background: white; box-shadow: 0 0 0 3px rgba(205, 39, 55, 0.1); }
        .form-actions { display: flex; gap: 16px; padding-top: 8px; }
        .hint { font-size:var(--fs-1-1); color: #6c757d; }
        .field-error { font-size:var(--fs-1-2); color: #CD2737; }

        /* ===== Buttons ===== */
        .btn { padding: 10px 22px; border: none; border-radius: 8px; font-size:var(--fs-1-4); font-weight: 600; cursor: pointer; transition: all 0.3s; font-family: 'Noto Sans Devanagari', sans-serif; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-primary { background: #CD2737; color: white; box-shadow: 0 4px 15px rgba(205, 39, 55, 0.3); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(205, 39, 55, 0.4); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; transform: translateY(-2px); }
        .btn-sm { padding: 6px 12px; font-size:var(--fs-1-2); }

        /* ===== Alerts ===== */
        .alert { padding: 12px 16px; border-radius: 8px; font-size:var(--fs-1-3); font-weight: 500; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* ===== Tables ===== */
        .table-responsive { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background: #f8f9fa; color: #1a1a2e; font-weight: 600; font-size:var(--fs-1-2); text-transform: uppercase; letter-spacing: 0.5px; padding: 12px; text-align: left; border-bottom: 2px solid #e9ecef; }
        .data-table td { padding: 10px 12px; border-bottom: 1px solid #e9ecef; font-size:var(--fs-1-3); }
        .data-table tr:hover td { background: #f8f9fa; }
        .data-table .actions { display: flex; gap: 8px; }

        /* ===== Badges ===== */
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size:var(--fs-1-1); font-weight: 600; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info { background: #d1ecf1; color: #0c5460; }

        /* ===== Status Toggle ===== */
        .status-toggle { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .status-toggle input[type="checkbox"] { width: 18px; height: 18px; accent-color: #CD2737; cursor: pointer; }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .dashboard-content { padding: 20px; }
            .form-card, .list-card { padding: 24px; }
            .form-grid { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
            .form-actions { flex-direction: column; }
            .header-top { flex-direction: column; align-items: flex-start; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="dashboard-body">
        <x-dashboard.sidebar />

        <div class="main-content-area" id="mainContentArea">
            <x-dashboard.header />

            @if(session('success'))
                <div class="toast-container">
                    <div class="toast toast-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                </div>
                <script>
                    setTimeout(() => {
                        document.querySelector('.toast-container')?.remove();
                    }, 4000);
                </script>
            @endif

            @if(session('error'))
                <div class="toast-container">
                    <div class="toast toast-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                </div>
                <script>
                    setTimeout(() => {
                        document.querySelector('.toast-container')?.remove();
                    }, 4000);
                </script>
            @endif

            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        const mainContent = document.getElementById('mainContentArea');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function handleResponsive() {
            const isMobile = window.innerWidth <= 768;
            if (isMobile) {
                toggleBtn.style.display = 'none';
                mobileMenuBtn.style.display = 'flex';
                if (!sidebar.classList.contains('active')) {
                    mobileMenuBtn.style.display = 'flex';
                }
                sidebar.classList.remove('active');
                mainContent.classList.remove('expanded');
                if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            } else {
                toggleBtn.style.display = 'flex';
                mobileMenuBtn.style.display = 'none';
                const saved = localStorage.getItem('sidebarActive');
                if (saved === 'true') {
                    sidebar.classList.add('active');
                    mainContent.classList.add('expanded');
                }
            }
        }

        handleResponsive();
        window.addEventListener('resize', () => {
            clearTimeout(window._resizeTimer);
            window._resizeTimer = setTimeout(handleResponsive, 100);
        });

        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('expanded');
                localStorage.setItem('sidebarActive', sidebar.classList.contains('active'));
            });
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.add('active');
                mainContent.classList.add('expanded');
                if (sidebarOverlay) sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                mobileMenuBtn.style.display = 'none';
            });
        }

        function closeMobileSidebar() {
            sidebar.classList.remove('active');
            mainContent.classList.remove('expanded');
            if (sidebarOverlay) sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
            if (window.innerWidth <= 768) {
                mobileMenuBtn.style.display = 'flex';
            }
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeMobileSidebar);
        }
    </script>
    @stack('scripts')
</body>
</html>

