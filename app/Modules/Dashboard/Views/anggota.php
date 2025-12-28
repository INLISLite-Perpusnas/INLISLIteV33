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

            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card info">
                <div class="stat-header">
                    <div class="stat-content">
                        <h3>Jumlah Peminjaman</h3>
                        <div class="stat-number" data-target="<?= $total_peminjaman ?? 0 ?>">0</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card primary">
                <div class="stat-header">
                    <div class="stat-content">
                        <h3>Jumlah Pelanggaran</h3>
                        <div class="stat-number" data-target="<?= $total_pelanggaran ?? 0 ?>">0</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
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
            }, {
                threshold: 0.5
            });

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
                    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
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