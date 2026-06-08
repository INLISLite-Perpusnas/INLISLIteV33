<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keanggotaan Online - Perpustakaan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .email-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }
        
        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #00b08c 0%, #00d4aa 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 15px;
            filter: brightness(0) invert(1);
        }
        
        .header-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            margin-bottom: 8px;
        }
        
        .header-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
            font-weight: 400;
        }
        
        .main-content {
            padding: 40px 30px;
        }
        
        .welcome-section {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .welcome-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #00b08c, #00d4aa);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0, 176, 140, 0.3);
        }
        
        .welcome-title {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 10px 0;
        }
        
        .welcome-text {
            font-size: 16px;
            color: #718096;
            margin: 0;
        }
        
        .credentials-card {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 25px;
            margin: 30px 0;
            position: relative;
        }
        
        .credentials-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #00b08c, #00d4aa);
            border-radius: 16px 16px 0 0;
        }
        
        .credentials-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
        }
        
        .credentials-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .credentials-item:last-child {
            border-bottom: none;
        }
        
        .credentials-label {
            font-size: 14px;
            color: #718096;
            font-weight: 500;
        }
        
        .credentials-value {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
            background: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .action-section {
            text-align: center;
            margin: 35px 0;
        }
        
        .action-text {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 25px;
        }
        
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #00b08c 0%, #00d4aa 100%);
            color: white !important;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 8px 20px rgba(0, 176, 140, 0.3);
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 176, 140, 0.4);
        }
        
        .btn-secondary {
            display: inline-block;
            background: transparent;
            color: #00b08c !important;
            text-decoration: none;
            padding: 12px 24px;
            border: 2px solid #00b08c;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 15px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #00b08c;
            color: white !important;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 35px 0;
        }
        
        .footer-section {
            text-align: center;
            color: #718096;
        }
        
        .footer-signature {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .footer-admin {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }
        
        .footer-site {
            font-size: 14px;
            color: #a0aec0;
        }
        
        .url-display {
            background: #f1f5f9;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            padding: 12px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #2d3748;
            word-break: break-all;
            margin: 15px 0;
        }
        
        @media (max-width: 600px) {
            .email-container {
                padding: 20px 10px;
            }
            
            .main-content {
                padding: 30px 20px;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .header-title {
                font-size: 24px;
            }
            
            .credentials-card {
                padding: 20px;
            }
            
            .credentials-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .credentials-value {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-content">
            <!-- Header -->
            <div class="header">
                <img src="<?=$logo_url?>" alt="Perpusnas" class="logo">
                <h1 class="header-title">Keanggotaan Online</h1>
                <p class="header-subtitle">Perpustakaan Digital Indonesia</p>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <div class="welcome-icon">
                        <svg width="40" height="40" fill="white" viewBox="0 0 24 24">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 3.5C14.1 3.1 13 3.4 12.6 4.3L10.5 9.3C10.1 10.2 10.4 11.3 11.3 11.7L13 12.4V19C13 20.1 13.9 21 15 21S17 20.1 17 19V14L19 13V11C19 9.9 18.1 9 17 9S15 9.9 15 11V13L13 14.2V12.4L15.3 11.3C16.2 10.9 16.5 9.8 16.1 8.9L14 3.8C13.6 2.9 12.5 2.6 11.6 3L6 5.5V7C6 8.1 6.9 9 8 9S10 8.1 10 7V6L12 5.5V9H10V11H14V9H12Z"/>
                        </svg>
                    </div>
                    <h2 class="welcome-title">Selamat Datang!</h2>
                    <p class="welcome-text">Terima kasih telah bergabung sebagai anggota perpustakaan kami</p>
                </div>

                <!-- Credentials Card -->
                <div class="credentials-card">
                    <h3 class="credentials-title">
                        <svg width="20" height="20" fill="#00b08c" viewBox="0 0 24 24" style="margin-right: 10px;">
                            <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                        </svg>
                        Informasi Akun Anda
                    </h3>
                    <div class="credentials-item">
                        <span class="credentials-label">Nomor Anggota</span>
                        <span class="credentials-value"><?=$username?></span>
                    </div>
                    <div class="credentials-item">
                        <span class="credentials-label">Password Sementara</span>
                        <span class="credentials-value"><?=$password?></span>
                    </div>
                </div>

                <!-- Action Section -->
                <div class="action-section">
                    <p class="action-text">
                        Klik tombol di bawah untuk mengaktifkan akun Anda dan mulai menjelajahi koleksi perpustakaan digital kami.
                    </p>
                    
                    <a href="<?=$action_url?>" class="btn-primary" target="_blank">
                        <svg width="20" height="20" fill="white" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 8px;">
                            <path d="M9,20.42L2.79,14.21L5.62,11.38L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z"/>
                        </svg>
                        Verifikasi Pendaftaran
                    </a>
                    
                    <div class="url-display">
                        <?=$action_url?>
                    </div>
                    
                    <p style="font-size: 14px; color: #718096; margin-top: 20px;">
                        Sudah memiliki akun?
                        <a href="<?=$login_url?>" class="btn-secondary" target="_blank">Masuk di sini</a>
                    </p>
                </div>

                <!-- Divider -->
                <div class="divider"></div>

                <!-- Footer -->
                <div class="footer-section">
                    <p class="footer-signature">Salam Literasi,</p>
                    <p class="footer-admin">Tim Administrator</p>
                    <p class="footer-site"><?=$site_name?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>