<?= $this->extend('App\Views\layout\main');

?>
<?= $this->section('style') ?>
<style><?= file_get_contents(FCPATH . 'assets/css/dashboard.css') ?></style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner" aria-labelledby="dashboard-title">
   <div class="dashboard-container">
       <!-- Page Header -->
    <div class="page-header">
           <div class="page-title" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
               
               <div style="display: flex; align-items: center; gap: 15px;">
                   <div class="page-icon">
                       <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                   </div>
                   <div>
                       <h1 id="dashboard-title">Dashboard</h1>
                       <p class="page-subtitle">Sistem Manajemen Perpustakaan Digital</p>
                   </div>
               </div>
               
               <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                 <button type="button" class="btn-send-api" id="btnKirimLaporan">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i> Kirim Laporan
                </button>
                   
                   <small style="color: #dc2626; font-weight: 600; font-size: 11px; background: #fee2e2; padding: 4px 8px; border-radius: 6px;">
                       <i class="fas fa-exclamation-circle" aria-hidden="true"></i> Setiap Perpustakaan Wajib mengirimkan laporan total data setiap bulan
                   </small>
               </div>

           </div>
       </div>

       <!-- Statistics Cards -->
       <section class="stats-grid" aria-label="Statistik pengguna dan kunjungan">
           <a class="stat-card info" href="<?= base_url('anggota') ?>" aria-label="Jumlah anggota: <?= number_format((int) ($total_anggota ?? 0)) ?>">
               <div class="stat-header">
                   <div class="stat-content">
                       <h2>Jumlah Anggota</h2>
                       <div class="stat-number"><?= number_format((int) ($total_anggota ?? 0)) ?></div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-users" aria-hidden="true"></i>
                   </div>
               </div>
           </a>

           <a class="stat-card success" href="<?= base_url('user') ?>" aria-label="User aktif: <?= number_format((int) ($total_user_active ?? 0)) ?>">
               <div class="stat-header">
                   <div class="stat-content">
                       <h2>User Aktif</h2>
                       <div class="stat-number"><?= number_format((int) ($total_user_active ?? 0)) ?></div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-user-check" aria-hidden="true"></i>
                   </div>
               </div>
           </a>

           <a class="stat-card warning" href="<?= base_url('bukutamu') ?>" aria-label="Kunjungan anggota: <?= number_format((int) ($total_anggota_guest ?? 0)) ?>">
               <div class="stat-header">
                   <div class="stat-content">
                       <h2>Kunjungan Anggota</h2>
                       <div class="stat-number"><?= number_format((int) ($total_anggota_guest ?? 0)) ?></div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-door-open" aria-hidden="true"></i>
                   </div>
               </div>
           </a>

           <a class="stat-card danger" href="<?= base_url('bukutamu/non_anggota') ?>" aria-label="Kunjungan non anggota: <?= number_format((int) ($total_nonanggota_guest ?? 0)) ?>">
               <div class="stat-header">
                   <div class="stat-content">
                       <h2>Kunjungan Non Anggota</h2>
                       <div class="stat-number"><?= number_format((int) ($total_nonanggota_guest ?? 0)) ?></div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-user-times" aria-hidden="true"></i>
                   </div>
               </div>
           </a>

           <div class="stat-card dark" role="group" aria-label="Anggota bebas pustaka: <?= number_format((int) ($total_anggota_bebas_pustaka ?? 0)) ?>">
               <div class="stat-header">
                   <div class="stat-content">
                       <h2>Anggota Bebas Pustaka</h2>
                       <div class="stat-number"><?= number_format((int) ($total_anggota_bebas_pustaka ?? 0)) ?></div>
                   </div>
                   <div class="stat-icon">
                       <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                   </div>
               </div>
           </div>
       </section>

       <!-- Chart Cards -->
       <section class="chart-grid" aria-label="Statistik koleksi dan sirkulasi">
           <a class="chart-card" href="<?= base_url('katalog') ?>" aria-label="Total katalog: <?= number_format((int) ($total_katalog ?? 0)) ?>">
               <div class="chart-header">
                   <div class="chart-icon">
                       <i class="fas fa-book" aria-hidden="true"></i>
                   </div>
                   <h2 class="chart-title">Total Katalog</h2>
               </div>
               <div class="chart-value"><?= number_format((int) ($total_katalog ?? 0)) ?></div>
           </a>

           <a class="chart-card" href="<?= base_url('eksemplar') ?>" aria-label="Total koleksi: <?= number_format((int) ($total_koleksi ?? 0)) ?>">
               <div class="chart-header">
                   <div class="chart-icon">
                       <i class="fas fa-layer-group" aria-hidden="true"></i>
                   </div>
                   <h2 class="chart-title">Total Koleksi</h2>
               </div>
               <div class="chart-value"><?= number_format((int) ($total_koleksi ?? 0)) ?></div>
           </a>

           <a class="chart-card" href="<?= base_url('sirkulasi-peminjaman') ?>" aria-label="Total peminjaman: <?= number_format((int) ($total_peminjaman ?? 0)) ?>">
               <div class="chart-header">
                   <div class="chart-icon">
                       <i class="fas fa-handshake" aria-hidden="true"></i>
                   </div>
                   <h2 class="chart-title">Total Peminjaman</h2>
               </div>
               <div class="chart-value"><?= number_format((int) ($total_peminjaman ?? 0)) ?></div>
           </a>
       </section>
   </div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Definisikan URL dan Tombol
        const CONTROLLER_URL = <?= json_encode(base_url('dashboard/kirimlaporan'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const btn = document.getElementById('btnKirimLaporan');
        
        if (!btn) {
            return;
        }

        // 2. Pasang Event Listener
        btn.addEventListener('click', async function(e) {
            e.preventDefault();

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
                return;
            }

            // --- TAHAP 2: UI LOADING ---
            try {
                btn.disabled = true;
                btn.textContent = 'Memproses...';

                // --- TAHAP 3: FETCH DATA ---
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
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Gagal!', error.message || 'Koneksi Gagal.', 'error');
                } else {
                    alert("Gagal: " + error.message);
                }
            } finally {
                // --- TAHAP 5: RESET TOMBOL ---
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    });
</script>
<?= $this->endSection('script') ?>
