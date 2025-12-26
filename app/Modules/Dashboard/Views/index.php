<?= $this->extend('App\Views\layout\main');

?>
<?= $this->section('style') ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets'); ?>/css/dashboard.css">
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
   <div class="dashboard-container">
       <!-- Page Header -->
    <div class="page-header">
           <div class="page-title" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
               
               <div style="display: flex; align-items: center; gap: 15px;">
                   <div class="page-icon">
                       <i class="fas fa-tachometer-alt"></i>
                   </div>
                   <div>
                       <h1>Dashboard</h1>
                       <div class="page-subtitle">Sistem Manajemen Perpustakaan Digital</div>
                   </div>
               </div>
               
               <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                 <button type="button" class="btn-send-api" id="btnKirimLaporan">
                    <i class="fas fa-paper-plane"></i> Kirim Laporan
                </button>
                   
                   <small style="color: #dc2626; font-weight: 600; font-size: 11px; background: #fee2e2; padding: 4px 8px; border-radius: 6px;">
                       <i class="fas fa-exclamation-circle"></i> Setiap Perpustakaan Wajib mengirimkan laporan total data setiap bulan
                   </small>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Definisikan URL dan Tombol
        const CONTROLLER_URL = "<?= base_url('dashboard/kirimlaporan') ?>";
        const btn = document.getElementById('btnKirimLaporan');

        console.log("Status Script: Ready (V8 Compatible)");
        
        if (!btn) {
            console.error("FATAL ERROR: Tombol dengan ID 'btnKirimLaporan' tidak ditemukan!");
            return;
        }

        // 2. Pasang Event Listener
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            console.log("Tombol diklik...");

            const originalText = btn.innerHTML;
            let isConfirmed = false;
            
            // --- TAHAP 1: KONFIRMASI (Syntax SweetAlert2 Versi 8) ---
            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: 'Kirim Laporan?',
                    text: "Sistem akan menghitung data dan mengirim ke Pusat.",
                    type: 'question', // PERBAIKAN 1: Ganti 'icon' menjadi 'type'
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'Ya, Kirim'
                });
                
                // PERBAIKAN 2: Versi 8 menggunakan .value untuk konfirmasi, bukan .isConfirmed
                isConfirmed = result.value; 
            } else {
                isConfirmed = confirm("Kirim laporan ke pusat?");
            }

            // Jika user klik batal atau klik di luar area
            if (!isConfirmed) {
                console.log("Aksi dibatalkan user.");
                return;
            }

            console.log("User mengonfirmasi. Memulai proses...");

            // --- TAHAP 2: UI LOADING ---
            try {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

                // --- TAHAP 3: FETCH DATA ---
                console.log("Melakukan Fetch ke:", CONTROLLER_URL);

                // Handling CSRF (Jika diperlukan)
                let headers = {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                };
                const csrfToken = document.querySelector('.txt_csrfname'); 
                if(csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken.value;
                }

                const response = await fetch(CONTROLLER_URL, {
                    method: 'POST',
                    headers: headers
                });

                const textResult = await response.text();
                let result;
                try {
                    result = JSON.parse(textResult);
                } catch (err) {
                    throw new Error("Server error (Bukan JSON): " + textResult.substring(0, 50));
                }

                // --- TAHAP 4: HASIL (Syntax V8) ---
                if (result.status === 'success') {
                    if (typeof Swal !== 'undefined') Swal.fire('Berhasil!', result.message, 'success'); // V8 otomatis detect type dari argumen ke-3
                    else alert(result.message);
                } else if (result.status === 'warning') {
                    if (typeof Swal !== 'undefined') Swal.fire('Info', result.message, 'info');
                    else alert(result.message);
                } else {
                    throw new Error(result.message || "Terjadi kesalahan tidak diketahui.");
                }

            } catch (error) {
                console.error('Error Occurred:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Gagal!', error.message || 'Koneksi Gagal.', 'error');
                } else {
                    alert("Gagal: " + error.message);
                }
            } finally {
                // --- TAHAP 5: RESET TOMBOL ---
                btn.disabled = false;
                btn.innerHTML = originalText;
                console.log("Proses selesai.");
            }
        });
    });
</script>
<?= $this->endSection('script') ?>