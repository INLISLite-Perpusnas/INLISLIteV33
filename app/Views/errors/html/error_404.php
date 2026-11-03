<?php
// Halaman 404 - pola "404 Section" INA Digital (design.inadigital.go.id/pattern/404-section/guide/)
// Desktop: horizontal (ilustrasi kiri, teks + CTA kanan). Mobile: vertikal, rata tengah.
$homeUrl = function_exists('base_url') ? base_url() : '/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>404: Halaman Tidak Ditemukan - INLISLite</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        :root {
            --ina-primary: #2d659e;
            --ina-primary-hover: #24527f;
            --ina-primary-soft: #eef4fa;
            --ina-ink: #1f2937;
            --ina-ink-subtle: #374151;
            --ina-muted: #6b7280;
            --ina-border: #e5e7eb;
            --ina-bg: #f7f7f8;
            --ina-focus: 0 0 0 3px rgba(45, 101, 158, .25);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            background: var(--ina-bg);
            color: var(--ina-ink);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .nf {
            width: 100%;
            max-width: 1040px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            align-items: center;
            gap: 64px;
        }

        /* Ilustrasi: proporsional, tidak mendominasi */
        .nf-illustration { display: flex; justify-content: center; }
        .nf-illustration svg { width: 100%; max-width: 420px; height: auto; }

        .nf-brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            line-height: 20px;
            font-weight: 600;
            color: var(--ina-muted);
        }
        .nf-brand span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--ina-primary);
        }

        /* Judul (H3 32/40) + deskripsi (Body 18/26) */
        .nf-title { margin-bottom: 12px; font-size: 32px; line-height: 40px; font-weight: 700; color: var(--ina-ink); }
        .nf-desc { margin-bottom: 32px; font-size: 18px; line-height: 26px; color: var(--ina-ink-subtle); }

        .nf-actions { display: flex; flex-wrap: wrap; gap: 12px; }
        .nf-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
            padding: 12px 24px;
            font-size: 16px;
            line-height: 24px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            border: 1px solid var(--ina-primary);
            cursor: pointer;
            font-family: inherit;
        }
        .nf-btn-primary { color: #fff; background: var(--ina-primary); }
        .nf-btn-primary:hover { background: var(--ina-primary-hover); border-color: var(--ina-primary-hover); }
        .nf-btn-secondary { color: var(--ina-primary); background: #fff; }
        .nf-btn-secondary:hover { background: var(--ina-primary-soft); }
        .nf-btn:focus-visible { outline: none; box-shadow: var(--ina-focus); }
        .nf-btn svg { width: 20px; height: 20px; flex-shrink: 0; }

        .nf-help { margin-top: 24px; font-size: 14px; line-height: 20px; color: var(--ina-muted); }

        .nf-debug {
            margin-top: 24px;
            padding: 12px 16px;
            font-family: Consolas, 'Courier New', monospace;
            font-size: 13px;
            line-height: 20px;
            color: var(--ina-ink-subtle);
            background: #fff;
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            word-break: break-word;
        }
        .nf-debug strong { display: block; margin-bottom: 4px; font-family: 'Inter', sans-serif; color: #c62828; }

        /* Mobile: vertikal, rata tengah */
        @media (max-width: 767px) {
            .nf { grid-template-columns: 1fr; gap: 24px; text-align: center; }
            .nf-illustration svg { max-width: 260px; }
            .nf-title { font-size: 24px; line-height: 32px; }
            .nf-desc { font-size: 16px; line-height: 24px; margin-bottom: 24px; }
            .nf-actions { flex-direction: column; }
            .nf-btn { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="nf" aria-labelledby="nf-title">
        <div class="nf-illustration" aria-hidden="true">
            <!-- Ilustrasi: rak buku dengan satu buku hilang + kaca pembesar -->
            <svg viewBox="0 0 420 320" xmlns="http://www.w3.org/2000/svg" role="img">
                <ellipse cx="210" cy="292" rx="170" ry="14" fill="#e5e7eb"/>
                <rect x="60" y="60" width="300" height="220" rx="12" fill="#ffffff" stroke="#d1d5db" stroke-width="2"/>
                <rect x="60" y="164" width="300" height="10" fill="#e5e7eb"/>
                <rect x="60" y="268" width="300" height="12" rx="4" fill="#e5e7eb"/>
                <!-- rak atas -->
                <rect x="84" y="92" width="26" height="72" rx="3" fill="#2d659e"/>
                <rect x="114" y="104" width="22" height="60" rx="3" fill="#9fbad6"/>
                <rect x="140" y="86" width="28" height="78" rx="3" fill="#cfdeee"/>
                <rect x="172" y="98" width="20" height="66" rx="3" fill="#2d659e"/>
                <!-- slot kosong -->
                <rect x="198" y="92" width="26" height="72" rx="3" fill="none" stroke="#9ca3af" stroke-width="2" stroke-dasharray="6 5"/>
                <rect x="230" y="100" width="24" height="64" rx="3" fill="#9fbad6"/>
                <rect x="258" y="90" width="30" height="74" rx="3" fill="#2d659e"/>
                <rect x="292" y="108" width="20" height="56" rx="3" fill="#cfdeee"/>
                <rect x="316" y="96" width="22" height="68" rx="3" fill="#9fbad6"/>
                <!-- rak bawah -->
                <rect x="84" y="196" width="24" height="72" rx="3" fill="#cfdeee"/>
                <rect x="112" y="206" width="30" height="62" rx="3" fill="#2d659e"/>
                <rect x="146" y="192" width="22" height="76" rx="3" fill="#9fbad6"/>
                <rect x="250" y="200" width="26" height="68" rx="3" fill="#cfdeee"/>
                <rect x="280" y="190" width="22" height="78" rx="3" fill="#2d659e"/>
                <rect x="306" y="204" width="30" height="64" rx="3" fill="#9fbad6"/>
                <!-- angka 404 -->
                <text x="210" y="248" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-size="44" font-weight="700" fill="#2d659e">404</text>
                <!-- kaca pembesar -->
                <circle cx="300" cy="48" r="30" fill="#eef4fa" stroke="#2d659e" stroke-width="6"/>
                <line x1="321" y1="69" x2="348" y2="96" stroke="#2d659e" stroke-width="10" stroke-linecap="round"/>
                <text x="300" y="58" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-size="28" font-weight="700" fill="#2d659e">?</text>
            </svg>
        </div>

        <div class="nf-content">
            <div class="nf-brand"><span></span>INLISLite</div>
            <h1 class="nf-title" id="nf-title">404: Halaman Tidak Ditemukan</h1>
            <p class="nf-desc">
                Maaf, halaman yang Anda cari tidak tersedia. Halaman tersebut mungkin telah dipindahkan
                atau tidak lagi dapat diakses.
            </p>

            <div class="nf-actions">
                <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nf-btn nf-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                    Kembali ke Beranda
                </a>
                <button type="button" class="nf-btn nf-btn-secondary" onclick="history.length > 1 ? history.back() : location.assign('<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                    Halaman Sebelumnya
                </button>
            </div>

            <p class="nf-help">Jika Anda yakin alamat ini benar, silakan hubungi administrator perpustakaan.</p>

            <?php if (ENVIRONMENT !== 'production') : ?>
                <div class="nf-debug">
                    <strong>Informasi debug (tidak tampil di production)</strong>
                    <?= nl2br(esc($message ?? 'Page not found')) ?>
                </div>
            <?php endif ?>
        </div>
    </main>
</body>
</html>
