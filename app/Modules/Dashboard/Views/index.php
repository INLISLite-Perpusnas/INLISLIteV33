<?= $this->extend('App\Views\layout\main');

$userModel = new \Auth\Models\UserModel();

$total_user_active = $userModel
    ->where('users.active', 1)
    ->countAllResults(false);
$total_user_inactive = $userModel
    ->where('users.active', 0)
    ->countAllResults(false);

$anggotaModel = new \Member\Models\MemberModel();
$total_anggota = $anggotaModel
    ->countAllResults(false);

$total_anggota_baru = $anggotaModel
    ->where('StatusAnggota_id', 1)
    ->countAllResults(false);

$memberguestModel = new \BukuTamu\Models\MemberGuestModel();

$total_anggota_guest = $memberguestModel
    ->where('NoAnggota !=', null)
    ->countAllResults(false);

$total_nonanggota_guest = $memberguestModel
    ->where('NoAnggota', null)
    ->countAllResults(false);

$total_anggota_bebas_pustaka = $anggotaModel
    ->where('StatusAnggota_id', 5)
    ->countAllResults(false);

$katalogModel = new \Katalog\Models\KatalogModel();
$total_katalog = $katalogModel
    ->countAllResults(false);

$koleksiModel = new \Peminjaman\Models\CollectionModel();
$total_koleksi = $koleksiModel
    ->countAllResults(false);

$peminjamanModel = new \Peminjaman\Models\CollectionLoanItemModel();
$total_peminjaman = $peminjamanModel
    ->countAllResults(false);

?>
<?= $this->section('style') ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    .app-main__inner {
        background: #ffffff;
        min-height: 100vh;
        padding: 20px;
        margin: 0;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        background: rgba(255, 255, 255, 1);
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        animation: slideDown 0.8s ease-out;
    }

    .page-title {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-icon {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 22px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
    }

    .page-title h1 {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: 14px;
        margin-top: 2px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        border-radius: 16px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        cursor: pointer;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
        min-height: 140px;
        color: white;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    .stat-card:nth-child(5) { animation-delay: 0.5s; }
    .stat-card:nth-child(6) { animation-delay: 0.6s; }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%);
        border-radius: 16px;
    }

    .stat-card.info { 
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }
    .stat-card.primary { 
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }
    .stat-card.success { 
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .stat-card.warning { 
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .stat-card.danger { 
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    .stat-card.dark { 
        background: linear-gradient(135deg, #6366f1, #4f46e5);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
    }

    .stat-content h3 {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-number {
        font-size: 24px;
        font-weight: 700;
        color: white;
        line-height: 1;
    }

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .chart-card {
        border-radius: 16px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
        min-height: 140px;
        color: white;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .chart-card:nth-child(1) { 
        background: linear-gradient(135deg, #667eea, #764ba2);
        animation-delay: 0.7s; 
    }
    .chart-card:nth-child(2) { 
        background: linear-gradient(135deg, #f093fb, #f5576c);
        animation-delay: 0.8s; 
    }
    .chart-card:nth-child(3) { 
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        animation-delay: 0.9s; 
    }

    .chart-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
    }

    .chart-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .chart-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        color: white;
        font-size: 18px;
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
    }

    .chart-title {
        font-size: 16px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.9);
    }

    .chart-value {
        font-size: 32px;
        font-weight: 700;
        color: white;
        text-align: left;
        margin: 12px 0 0 0;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        
        .chart-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .page-header {
            padding: 20px;
        }
        
        .stat-card {
            padding: 16px;
            min-height: 100px;
        }
        
        .chart-card {
            padding: 20px;
        }
        
        .stat-number {
            font-size: 20px;
        }
        
        .chart-value {
            font-size: 28px;
        }
    }
    
    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
   <div class="dashboard-container">
       <!-- Page Header -->
       <div class="page-header">
           <div class="page-title">
               <div class="page-icon">
                   <i class="fas fa-tachometer-alt"></i>
               </div>
               <div>
                   <h1>Dashboard</h1>
                   <div class="page-subtitle">Sistem Manajemen Perpustakaan Digital</div>
               </div>
           </div>
       </div>

       <!-- Statistics Cards -->
       <div class="stats-grid">
           <div class="stat-card info">
               <div class="stat-header">
                   <div class="stat-content">
                       <h3>Jumlah Anggota</h3>
                       <div class="stat-number" data-target="<?= $total_anggota ?? 0 ?>">0</div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-users"></i>
                   </div>
               </div>
           </div>

           <div class="stat-card primary">
               <div class="stat-header">
                   <div class="stat-content">
                       <h3>Anggota Baru</h3>
                       <div class="stat-number" data-target="<?= $total_anggota_baru ?? 0 ?>">0</div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-user-plus"></i>
                   </div>
               </div>
           </div>

           <div class="stat-card success">
               <div class="stat-header">
                   <div class="stat-content">
                       <h3>User Aktif</h3>
                       <div class="stat-number" data-target="<?= $total_user_active ?? 0 ?>">0</div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-user-check"></i>
                   </div>
               </div>
           </div>

           <div class="stat-card warning">
               <div class="stat-header">
                   <div class="stat-content">
                       <h3>Kunjungan Anggota</h3>
                       <div class="stat-number" data-target="<?= $total_anggota_guest ?? 0 ?>">0</div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-door-open"></i>
                   </div>
               </div>
           </div>

           <div class="stat-card danger">
               <div class="stat-header">
                   <div class="stat-content">
                       <h3>Kunjungan Non Anggota</h3>
                       <div class="stat-number" data-target="<?= $total_nonanggota_guest ?? 0 ?>">0</div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-user-times"></i>
                   </div>
               </div>
           </div>

           <div class="stat-card dark">
               <div class="stat-header">
                   <div class="stat-content">
                       <h3>Anggota Bebas Pustaka</h3>
                       <div class="stat-number" data-target="<?= $total_anggota_bebas_pustaka ?? 0 ?>">0</div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-graduation-cap"></i>
                   </div>
               </div>
           </div>
       </div>

       <!-- Chart Cards -->
       <div class="chart-grid">
           <div class="chart-card">
               <div class="chart-header">
                   <div class="chart-icon">
                       <i class="fas fa-book"></i>
                   </div>
                   <div class="chart-title">Total Katalog</div>
               </div>
               <div class="chart-value" data-target="<?= $total_katalog ?? 0 ?>">0</div>
           </div>

           <div class="chart-card">
               <div class="chart-header">
                   <div class="chart-icon">
                       <i class="fas fa-layer-group"></i>
                   </div>
                   <div class="chart-title">Total Koleksi</div>
               </div>
               <div class="chart-value" data-target="<?= $total_koleksi ?? 0 ?>">0</div>
           </div>

           <div class="chart-card">
               <div class="chart-header">
                   <div class="chart-icon">
                       <i class="fas fa-handshake"></i>
                   </div>
                   <div class="chart-title">Total Peminjaman</div>
               </div>
               <div class="chart-value" data-target="<?= $total_peminjaman ?? 0 ?>">0</div>
           </div>
       </div>
   </div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<script>
   // Number counter animation
   function animateNumbers() {
       const counters = document.querySelectorAll('.stat-number, .chart-value');
       
       const animateCounter = (counter) => {
           const target = parseInt(counter.getAttribute('data-target'));
           if (target === 0) {
               counter.textContent = '0';
               return;
           }
           
           const increment = target / 100;
           let current = 0;
           
           const timer = setInterval(() => {
               current += increment;
               if (current >= target) {
                   current = target;
                   clearInterval(timer);
               }
               counter.textContent = Math.floor(current).toLocaleString();
           }, 20);
       };
       
       const observer = new IntersectionObserver((entries) => {
           entries.forEach(entry => {
               if (entry.isIntersecting) {
                   animateCounter(entry.target);
                   observer.unobserve(entry.target);
               }
           });
       }, { threshold: 0.5 });
       
       counters.forEach(counter => {
           observer.observe(counter);
       });
   }

   // Card hover effects
   function initCardEffects() {
       const cards = document.querySelectorAll('.stat-card, .chart-card');
       
       cards.forEach(card => {
           card.addEventListener('mouseenter', () => {
               card.style.transform = 'translateY(-10px) scale(1.02)';
           });
           
           card.addEventListener('mouseleave', () => {
               card.style.transform = 'translateY(0) scale(1)';
           });
       });
   }

   // Click ripple effect
   function initClickEffects() {
       document.querySelectorAll('.stat-card, .chart-card').forEach(card => {
           card.addEventListener('click', function(e) {
               const ripple = document.createElement('div');
               const rect = card.getBoundingClientRect();
               const size = 20;
               
               ripple.style.position = 'absolute';
               ripple.style.borderRadius = '50%';
               ripple.style.background = 'rgba(102, 126, 234, 0.3)';
               ripple.style.transform = 'scale(0)';
               ripple.style.animation = 'ripple 0.6s linear';
               ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
               ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
               ripple.style.width = ripple.style.height = size + 'px';
               ripple.style.pointerEvents = 'none';
               
               card.appendChild(ripple);
               
               setTimeout(() => {
                   ripple.remove();
               }, 600);
           });
       });
   }

   // Initialize animations
   document.addEventListener('DOMContentLoaded', () => {
       animateNumbers();
       initCardEffects();
       initClickEffects();
   });

   // Add ripple animation CSS
   const style = document.createElement('style');
   style.textContent = `
       @keyframes ripple {
           to {
               transform: scale(4);
               opacity: 0;
           }
       }
   `;
   document.head.appendChild(style);
</script>
<?= $this->endSection('script') ?>