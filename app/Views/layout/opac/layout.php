
<?php
$db=db_connect('data');
$nama_perpustakaan=$db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value;
$alamat=$db->table('settingparameters')->where('Name', 'NamaLokasiPerpustakaan')->get()->getRow()->Value;
$logo=$db->table('branchs')->where('Name', $nama_perpustakaan)->get()->getRow()->Logo;



?>
<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPAC - Online Public Access Catalog</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #28a745;
            --primary-dark: #1e7e34;
            --accent-yellow: #ffc107;
            --text-white: #ffffff;
            --text-dark: #333333;
        }

        /* Header Styles */
        .main-header {
            background-color: var(--primary-green);
            color: var(--text-white);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

   .logo-section {
            display: flex;
            align-items: center;
            gap: 20px; /* Menambahkan jarak antara logo dan teks */
        }

        .logo-section img {
            height: 100px;
            width: 100px;
            border-radius: 8px; /* Opsional: memberikan sudut melengkung pada logo */
            object-fit: cover; /* Memastikan proporsi logo tetap baik */
        }

        .logo-text {
            flex: 1; /* Membuat teks mengisi ruang yang tersisa */
        }

        .logo-text h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 5px 0; /* Memberikan sedikit jarak antara nama dan alamat */
            line-height: 1.2;
        }

        .logo-text p {
            font-size: 14px;
            margin: 0;
            opacity: 0.9;
            font-weight: 400;
            line-height: 1.3;
        }

        .main-nav {
            display: flex;
            align-items: center;
        }

        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 30px;
        }

        .nav-links a {
            color: var(--text-white);
            text-decoration: none;
            font-weight: 500;
            padding: 10px 0;
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--accent-yellow);
        }

        .nav-links a.active {
            color: var(--accent-yellow);
            border-bottom: 2px solid var(--accent-yellow);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--accent-yellow);
            bottom: 0;
            left: 0;
            transition: width 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #e6f4ee 0%, #e6f4ee 100%);
            color: white;
            padding: 80px 0;
        }
        
        .search-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .catalog-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .catalog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stats-card {
            background: linear-gradient(45deg, #e6f4ee 0%, #e6f4ee 100%);
            color: #028548;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .browse-letter {
            display: inline-block;
            padding: 8px 12px;
            margin: 2px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s ease;
        }
        
        .browse-letter:hover, .browse-letter.active {
            background: #007bff;
            color: white;
            text-decoration: none;
        }

        /* Footer Styles */
        .main-footer {
            background-color: var(--primary-green);
            color: var(--text-white);
            padding: 50px 0 20px;
            margin-top: 50px;
        }

        .footer-section h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-section h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: var(--accent-yellow);
        }

        .footer-section p {
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: var(--text-white);
            text-decoration: none;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .footer-section ul li a:hover {
            opacity: 1;
            padding-left: 5px;
            color: var(--accent-yellow);
        }

        .footer-contact p {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .footer-contact i {
            margin-right: 10px;
            width: 20px;
            color: var(--accent-yellow);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 30px;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .logo-section {
                margin-bottom: 15px;
            }

            .nav-links {
                flex-direction: column;
                gap: 15px;
            }

            .main-nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-section {
                padding: 40px 0;
            }
        }
        
        .badge-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="logo-section">
                           <img style="height: 100px;width: 100px" src="<?= base_url('uploads/branch/' . $logo) ?>" alt="Logo">
                     
                        <div class="logo-text">
                            <h1><?=$nama_perpustakaan?></h1>
                            <p><?=$alamat?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <nav class="main-nav justify-content-end">
                        <ul class="nav-links">
                            <li><a href="<?= base_url('home') ?>"><i class="fas fa-home me-2"></i>Beranda</a></li>
                            <li><a href="<?= base_url('buku-tamu') ?>"><i class="fas fa-address-book"></i>Buku Tamu</a></li>
                            <li><a href="<?= base_url('opac/browse') ?>"><i class="fas fa-list me-2"></i>Browse</a></li>
                            <li><a href="<?= base_url('opac/statistics') ?>"><i class="fas fa-chart-bar me-2"></i>Statistik</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

  

    <!-- Main Content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="footer-section">
                        <h4>Tentang Kami</h4>
                        <p>Dinas Perpustakaan dan Arsip Provinsi Sumatera Utara berkomitmen untuk menyediakan layanan perpustakaan berkualitas bagi masyarakat.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="footer-section">
                        <h4>Layanan</h4>
                        <ul>
                            <li><a href="#">Keanggotaan Online</a></li>
                            <li><a href="#">Peminjaman Mandiri</a></li>
                            <li><a href="#">Pengembalian Mandiri</a></li>
                            <li><a href="#">OPAC</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="footer-section footer-contact">
                        <h4>Kontak</h4>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Perpustakaan No.1, Medan, Sumatera Utara</p>
                        <p><i class="fas fa-phone"></i> (061) 1234567</p>
                        <p><i class="fas fa-envelope"></i> info@perpustakaan-sumut.go.id</p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Dinas Perpustakaan dan Arsip Provinsi Sumatera Utara. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   
    </script>
</body>
</html>