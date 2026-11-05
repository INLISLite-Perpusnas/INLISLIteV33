<?= $this->extend('App\Views\layout\main');

?>
<?= $this->section('style') ?>
<style><?= file_get_contents(FCPATH . 'assets/css/dashboard.css') ?></style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<?php
// Card Metrics disusun berdasarkan prioritas: koleksi & layanan utama di atas, kunjungan & pengguna di bawah.
$utama = [
    ['label' => 'Total Katalog', 'value' => $total_katalog ?? 0, 'sub' => 'Judul terdaftar', 'icon' => 'fas fa-book', 'color' => 'blue', 'url' => 'katalog'],
    ['label' => 'Total Eksemplar', 'value' => $total_koleksi ?? 0, 'sub' => 'Eksemplar koleksi', 'icon' => 'fas fa-layer-group', 'color' => 'orange', 'url' => 'eksemplar'],
    ['label' => 'Total Peminjaman', 'value' => $total_peminjaman ?? 0, 'sub' => 'Seluruh transaksi pinjam', 'icon' => 'fas fa-handshake', 'color' => 'green', 'url' => 'sirkulasi-peminjaman'],
    ['label' => 'Jumlah Anggota', 'value' => $total_anggota ?? 0, 'sub' => 'Anggota terdaftar', 'icon' => 'fas fa-users', 'color' => 'teal', 'url' => 'anggota'],
];
$pendukung = [
    ['label' => 'Kunjungan Anggota', 'value' => $total_anggota_guest ?? 0, 'sub' => 'Tercatat di buku tamu', 'icon' => 'fas fa-door-open', 'color' => 'indigo', 'url' => 'bukutamu'],
    ['label' => 'Kunjungan Non Anggota', 'value' => $total_nonanggota_guest ?? 0, 'sub' => 'Tercatat di buku tamu', 'icon' => 'fas fa-user-times', 'color' => 'red', 'url' => 'bukutamu/non_anggota'],
    ['label' => 'User Aktif', 'value' => $total_user_active ?? 0, 'sub' => 'Akun pengguna aktif', 'icon' => 'fas fa-user-check', 'color' => 'blue', 'url' => 'user'],
];
?>
<div class="app-main__inner">
    <div class="dashboard-container">
        <header class="dash-header">
            <div>
                <h1 id="dashboard-title">Dashboard</h1>
                <p class="dash-subtitle">Ringkasan data koleksi, layanan, dan kunjungan perpustakaan.</p>
            </div>
            <div class="dash-actions">
                <div class="dash-tooltip">
                    <button type="button" class="btn-send-api" id="btnKirimLaporan" aria-describedby="tipKirimLaporan">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i> Kirim Laporan
                    </button>
                    <div class="dash-tooltip-box" id="tipKirimLaporan" role="tooltip">
                        Data dashboard ini akan dikirim ke database Perpusnas, agar Perpusnas mengetahui jumlah katalog, anggota, dan peminjaman dari setiap perpustakaan.
                    </div>
                </div>
                <small>Wajib dikirim setiap bulan</small>
            </div>
        </header>

        <section class="dash-section" aria-labelledby="dash-utama">
            <h2 class="dash-section-title" id="dash-utama">Koleksi &amp; Layanan</h2>
            <div class="metric-grid">
                <?php foreach ($utama as $m) : ?>
                    <?= view('Dashboard\Views\metric_card', ['m' => $m]) ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="dash-section" aria-labelledby="dash-pendukung">
            <h2 class="dash-section-title" id="dash-pendukung">Kunjungan &amp; Pengguna</h2>
            <div class="metric-grid">
                <?php foreach ($pendukung as $m) : ?>
                    <?= view('Dashboard\Views\metric_card', ['m' => $m, 'compact' => true]) ?>
                <?php endforeach; ?>
            </div>
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
