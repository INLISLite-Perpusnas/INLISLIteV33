<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - INLISLite</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #039550;
            --primary-dark: #027a42;
            --primary-light: #e6f4ee;
            --accent: #f8c43a;
            --light-gray: #f5f5f5;
            --text: #333333;
            --white: #ffffff;
            --error-red: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            height: 100vh;
            background: linear-gradient(135deg, var(--primary-light), #f8f9fa);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        /* Animated background elements */
        .floating-books {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .book-icon {
            position: absolute;
            font-size: 24px;
            color: var(--primary);
            opacity: 0.1;
            animation: float 15s infinite;
        }
        
        .book-icon:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
        .book-icon:nth-child(2) { top: 20%; right: 15%; animation-delay: 2s; }
        .book-icon:nth-child(3) { bottom: 20%; left: 20%; animation-delay: 4s; }
        .book-icon:nth-child(4) { bottom: 30%; right: 10%; animation-delay: 6s; }
        .book-icon:nth-child(5) { top: 50%; left: 5%; animation-delay: 8s; }
        .book-icon:nth-child(6) { top: 60%; right: 25%; animation-delay: 10s; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(5deg); }
            50% { transform: translateY(-10px) rotate(-3deg); }
            75% { transform: translateY(-15px) rotate(2deg); }
        }
        
        .error-container {
            max-width: 600px;
            padding: 3rem;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            z-index: 10;
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-section {
            margin-bottom: 2rem;
        }
        
        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }
        
        .logo i {
            font-size: 36px;
            color: var(--white);
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .app-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
        }
        
        .app-subtitle {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2rem;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: transparent;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            background-clip: text;
            -webkit-background-clip: text;
            margin-bottom: 1rem;
            line-height: 1;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .book-illustration {
            font-size: 4rem;
            color: var(--primary);
            margin: 1.5rem 0;
            animation: bookFlip 3s ease-in-out infinite;
        }
        
        @keyframes bookFlip {
            0%, 100% { transform: rotateY(0deg); }
            50% { transform: rotateY(180deg); }
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 12px 24px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(3, 149, 80, 0.3);
        }
        
        .btn-secondary {
            background: var(--white);
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-secondary:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
        }
        
        .search-suggestion {
            background: var(--light-gray);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .search-suggestion h4 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .suggestion-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .suggestion-item {
            background: var(--white);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid var(--primary-light);
        }
        
        .suggestion-item:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-1px);
        }
        
        .footer-info {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
            font-size: 0.85rem;
            color: #999;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .error-container {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }
            
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 250px;
            }
        }
        
        /* Environment-specific styles */
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
            text-align: left;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: #495057;
        }
        
        .debug-info h5 {
            color: var(--error-red);
            margin-bottom: 0.5rem;
            font-family: 'Segoe UI', sans-serif;
        }
    </style>
</head>
<body>
    <!-- Floating background books -->
    <div class="floating-books">
        <i class="fas fa-book book-icon"></i>
        <i class="fas fa-book-open book-icon"></i>
        <i class="fas fa-bookmark book-icon"></i>
        <i class="fas fa-graduation-cap book-icon"></i>
        <i class="fas fa-scroll book-icon"></i>
        <i class="fas fa-feather-alt book-icon"></i>
    </div>
    
    <div class="error-container">
        <!-- Logo and App Info -->
        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-book-reader"></i>
            </div>
            <div class="app-name">INLISLite</div>
            <div class="app-subtitle">Integrated Library Information System</div>
        </div>
        
        <!-- Error Code -->
        <div class="error-code">404</div>
        
        <!-- Book Illustration -->
        <div class="book-illustration">
            <i class="fas fa-book-dead"></i>
        </div>
        
        <!-- Error Message -->
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <div class="error-message">
            Maaf, halaman yang Anda cari seperti buku yang terselip di rak yang salah. 
            Halaman ini mungkin telah dipindahkan, dihapus, atau URL yang Anda masukkan tidak tepat.
        </div>
        
        <!-- Environment-specific content -->
        <?php if (ENVIRONMENT !== 'production') : ?>
            <div class="debug-info">
                <h5><i class="fas fa-bug"></i> Debug Information</h5>
                <div><?= nl2br(esc($message ?? 'Page not found')) ?></div>
            </div>
        <?php endif ?>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Halaman Sebelumnya
            </a>
        </div>
        
       
        
        <!-- Footer Info -->
        <div class="footer-info">
            <i class="fas fa-info-circle"></i>
            Jika masalah berlanjut, silakan hubungi administrator sistem
        </div>
    </div>
    
    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add click effect to buttons
            const buttons = document.querySelectorAll('.btn, .suggestion-item');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.5);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s ease-out;
                        pointer-events: none;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                });
            });
            
            // Add style for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>