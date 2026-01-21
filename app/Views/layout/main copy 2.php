<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Argon Dashboard 2 PRO</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="stylesheet" href="<?= base_url('themes/uigniter'); ?>/css/base.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 280px;
            background: white;
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar.collapsed {
            width: 80px;
            padding: 30px 10px;
        }

        .sidebar.collapsed .logo-text,
        .sidebar.collapsed .nav-title,
        .sidebar.collapsed .nav-item span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar.collapsed .logo {
            justify-content: center;
        }

        .sidebar.collapsed .nav-item {
            justify-content: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            padding: 0 10px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .logo-text {
            font-weight: 700;
            font-size: 16px;
            color: #344767;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-title {
            font-size: 11px;
            font-weight: 600;
            color: #67748e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 10px;
            margin-bottom: 15px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            color: #67748e;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-item:hover {
            background: #f8f9fa;
        }

        .nav-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            transition: all 0.3s;
        }

        .toggle-btn {
            position: absolute;
            top: 25px;
            right: -15px;
            width: 30px;
            height: 30px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .toggle-btn:hover {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }

        .toggle-btn i {
            font-size: 12px;
            transition: transform 0.3s;
        }

        .sidebar.collapsed .toggle-btn i {
            transform: rotate(180deg);
        }

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            padding-bottom: 50px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            font-size: 14px;
        }

        .page-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
            margin-top: 5px;
        }

        .header-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-box {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 10px 20px;
            border-radius: 8px;
            color: white;
            width: 250px;
            backdrop-filter: blur(10px);
        }

        .search-box::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .sign-in-btn {
            background: white;
            color: #667eea;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .sign-in-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-info h4 {
            font-size: 12px;
            color: #67748e;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #344767;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 13px;
            font-weight: 600;
        }

        .stat-change.positive {
            color: #4ade80;
        }

        .stat-change.negative {
            color: #f87171;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .icon-blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .icon-red {
            background: linear-gradient(135deg, #f43f5e 0%, #dc2626 100%);
        }

        .icon-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .icon-orange {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #344767;
            margin-bottom: 5px;
        }

        .card-subtitle {
            font-size: 13px;
            color: #67748e;
        }

        .chart-container {
            height: 300px;
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            padding: 20px 0;
        }

        .chart-line {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .chart-area {
            fill: url(#gradient);
            opacity: 0.3;
        }

        .chart-path {
            fill: none;
            stroke: #667eea;
            stroke-width: 3;
        }

        .promo-card {
            background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .promo-overlay {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="%23a855f7"/><stop offset="100%" stop-color="%237c3aed"/></linearGradient></defs><circle cx="350" cy="150" r="200" fill="url(%23g)" opacity="0.3"/></svg>') no-repeat;
            background-size: contain;
            background-position: right top;
            opacity: 0.5;
        }

        .promo-content {
            position: relative;
            z-index: 1;
        }

        .play-btn {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .play-btn:hover {
            transform: scale(1.1);
        }

        .promo-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .promo-text {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .dashboard-footer {
            background: white;
            border-radius: 12px;
            padding: 20px 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .footer-links {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #67748e;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .footer-links a:hover {
            color: #667eea;
        }

        .footer-copyright {
            color: #67748e;
            font-size: 14px;
        }

        .footer-copyright i {
            color: #f43f5e;
            margin: 0 3px;
        }

        .list-item {
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .list-info h5 {
            font-size: 14px;
            font-weight: 600;
            color: #344767;
            margin-bottom: 3px;
        }

        .list-info p {
            font-size: 12px;
            color: #67748e;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-footer {
                flex-direction: column;
                text-align: center;
            }

            .footer-links {
                justify-content: center;
            }
        }
           <?= $this->include('App\Views\layout\partial\style'); ?>
    <?= $this->include('App\Views\layout\partial\style_custom'); ?>
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="toggle-btn" id="toggleBtn">
            <i class="fas fa-chevron-left"></i>
        </div>
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="logo-text">Argon Dashboard 2 PRO</div>
        </div>

        <nav>
            <div class="nav-item active">
                <i class="fas fa-th-large"></i>
                <span>Dashboards</span>
            </div>

            <div class="nav-section">
                <div class="nav-title">Pages</div>
                <div class="nav-item">
                    <i class="far fa-file"></i>
                    <span>Pages</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-cube"></i>
                    <span>Applications</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Ecommerce</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-lock"></i>
                    <span>Authentication</span>
                </div>
            </div>

            <div class="nav-section">
                <div class="nav-title">Docs</div>
                <div class="nav-item">
                    <i class="fas fa-book"></i>
                    <span>Basic</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-puzzle-piece"></i>
                    <span>Components</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-list"></i>
                    <span>Changelog</span>
                </div>
            </div>
        </nav>
    </div>

    <div class="main-content">
          <?= $this->renderSection('page'); ?>

        <!-- Footer -->
        <footer class="dashboard-footer">
            <div class="footer-copyright">
                © 2025, made with <i class="fas fa-heart"></i> by Creative Tim for a better web.
            </div>
            <div class="footer-links">
                <a href="#">Creative Tim</a>
                <a href="#">About Us</a>
                <a href="#">Blog</a>
                <a href="#">License</a>
            </div>
        </footer>
    </div>
  <?= $this->include('App\Views\layout\partial\script'); ?>
    <?= $this->include('App\Views\layout\partial\script_custom'); ?>
    <script>
        // Toggle sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });

        // Optional: Close sidebar on mobile when clicking outside
        if (window.innerWidth <= 768) {
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                if (!isClickInsideSidebar && !sidebar.classList.contains('collapsed')) {
                    sidebar.classList.add('collapsed');
                }
            });
        }
    </script>
      <?= $this->include('App\Views\layout\partial\script'); ?>
</body>
</html>