<?php
$orientation = ($orientation ?? 'landscape') === 'portrait' ? 'portrait' : 'landscape';
$code_type = ($code_type ?? 'qr') === 'barcode' ? 'barcode' : 'qr';
$show_logo = $show_logo ?? true;
$show_name = $show_name ?? true;
$template = ($template ?? '1') === '2' ? '2' : '1';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota Perpustakaan - PDF Generator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
    <style>
        /* --- Print Styles untuk PDF --- */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                margin: 0;
                padding: 0;
                background-color: white !important;
                width: 100vw;
                height: 100vh;
                overflow: hidden;
            }

            .controls,
            .upload-section {
                display: none !important;
            }

            .status {
                display: none !important;
            }

            .card-container {
                margin: 0 !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                page-break-before: avoid !important;
                page-break-after: avoid !important;
                width: 1004px !important;
                height: 618px !important;
                transform: scale(0.8);
                transform-origin: center center;
                position: absolute;
                top: 50%;
                left: 50%;
                margin-left: -402px;
                margin-top: -247px;
            }

            @page {
                margin: 0;
            }
        }

        /* =====================================================
           PORTRAIT LAYOUT — kartu ditata ulang vertikal
           (bukan sekadar kartu landscape yang diputar)
           ===================================================== */
        body.orient-portrait .card-container {
            width: 620px;
            height: auto;
            min-height: 1004px;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            text-align: center;
            padding: 40px 34px 36px;
            box-sizing: border-box;
            border-radius: 28px;
        }

        /* Isi panel dijadikan anak langsung agar bisa diurutkan */
        body.orient-portrait .left-panel,
        body.orient-portrait .right-panel,
        body.orient-portrait .expiry-photo-section {
            display: contents;
        }

        /* Semua elemen di atas overlay warna */
        body.orient-portrait .header-section,
        body.orient-portrait .fullname,
        body.orient-portrait .member-photo,
        body.orient-portrait .member-type,
        body.orient-portrait .member-no,
        body.orient-portrait .expiry-date,
        body.orient-portrait .qrcode-placeholder {
            position: relative;
            z-index: 5;
        }

        /* Header */
        body.orient-portrait .header-section {
            order: 1;
            top: auto;
            left: auto;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            width: 100%;
            margin-bottom: 4px;
        }

        body.orient-portrait #logoImage {
            width: 110px !important;
            height: 110px !important;
            margin-bottom: 0 !important;
        }

        body.orient-portrait .library-name h1 {
            font-size: 20px;
            max-width: 100%;
        }

        /* Nama lengkap */
        body.orient-portrait .fullname {
            order: 2;
            font-size: 30px;
            margin: 14px 0 12px;
            width: 100%;
        }

        /* Foto anggota */
        body.orient-portrait .member-photo {
            order: 3;
            width: 200px;
            height: 240px;
            margin-bottom: 14px;
        }

        /* Badge jenis anggota */
        body.orient-portrait .member-type {
            order: 4;
            font-size: 22px;
            padding: 7px 32px;
            margin-bottom: 12px;
        }

        /* Nomor anggota */
        body.orient-portrait .member-no {
            order: 5;
            font-size: 24px;
            margin-bottom: 12px;
        }

        /* Masa berlaku */
        body.orient-portrait .expiry-date {
            order: 6;
            font-size: 19px;
            margin-bottom: 14px;
        }

        /* QR code */
        body.orient-portrait .qrcode-placeholder {
            order: 7;
            padding: 12px;
        }

        body.orient-portrait .qrcode-placeholder img {
            width: 165px;
            height: 165px;
        }

        /* Preview di layar */
        @media screen {
            body.orient-portrait {
                justify-content: flex-start;
            }

            body.orient-portrait .card-container {
                transform: scale(0.82);
                transform-origin: top center;
                margin-bottom: -170px;
            }
        }

        /* html2canvas harus menangkap ukuran asli kartu, bukan versi preview
           yang diperkecil untuk layar. */
        body.orient-portrait .card-container.pdf-capture {
            transform: none !important;
            transform-origin: top left !important;
            margin-bottom: 0 !important;
        }

        /* Saat dicetak */
        @media print {
            body.orient-portrait {
                display: block !important;
                overflow: visible !important;
                width: 620px !important;
                height: 1004px !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body.orient-portrait .card-container {
                position: static !important;
                width: 620px !important;
                height: 1004px !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 40px 34px 36px !important;
                transform: none !important;
                transform-origin: top left !important;
                border-radius: 0 !important;
            }
        }

        /* --- Toggle Logo / Nama Perpustakaan --- */
        body.hide-logo #logoImage {
            display: none;
        }

        body.hide-libname .library-name {
            display: none;
        }

        body.hide-header .header-section {
            display: none;
        }

        /* --- QR Code vs Barcode --- */
        .qrcode-placeholder img.code-barcode {
            display: block;
            width: 300px;
            height: 90px;
        }

        body.orient-portrait .qrcode-placeholder img.code-barcode {
            width: 340px;
            height: 90px;
        }

        body.mode-qr .qrcode-placeholder img.code-barcode {
            display: none;
        }

        body.mode-barcode .qrcode-placeholder img.code-qr {
            display: none;
        }

        /* --- Layout: pengaturan di kiri, preview di kanan --- */
@media screen {
    .layout {
        display: flex;
        gap: 24px;
        align-items: flex-start;
        justify-content: center;
        width: 100%;
    }

    .sidebar {
        width: 360px;
        flex-shrink: 0;
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
    }

    .sidebar .upload-section {
        max-width: none;
        margin-bottom: 16px;
    }

    .sidebar .upload-grid,
    .sidebar .color-section {
        grid-template-columns: 1fr;
    }

    .sidebar .controls button {
        margin: 4px;
    }

    .preview {
        flex: 0 0 auto;
    }
}

/* Layar sempit: kembali ke susunan atas-bawah */
@media screen and (max-width: 1450px) {
    .layout {
        flex-direction: column;
        align-items: center;
    }
    .sidebar {
        position: static;
        width: 100%;
        max-width: 800px;
        max-height: none;
        overflow: visible;
    }
}

/* Saat dicetak, hanya kartu yang tampil */
@media print {
    .layout { display: block !important; }
    .sidebar { display: none !important; }
}

/* =====================================================
   TEMPLATE 2
   Kiri : no anggota, kode, berlaku hingga
   Kanan: jenis anggota, foto, nama
   ===================================================== */

/* Elemen yang dipindah ke panel kiri */
body.tpl-2 .left-panel .member-no {
    margin: 0 0 16px;
    text-align: center;
}
body.tpl-2 .left-panel .expiry-date {
    margin: 16px 0 0;
    text-align: center;
}

/* Landscape: kedua panel mulai dari garis atas yang sama */
body.tpl-2:not(.orient-portrait) .left-panel,
body.tpl-2:not(.orient-portrait) .right-panel {
    box-sizing: border-box;
    justify-content: flex-start;
    padding-top: 190px;
}
body.tpl-2.hide-header:not(.orient-portrait) .left-panel,
body.tpl-2.hide-header:not(.orient-portrait) .right-panel {
    padding-top: 50px;
}

/* Panel kanan lebih padat, jadi foto dan nama dikecilkan */
body.tpl-2:not(.orient-portrait) .right-panel .member-type {
    margin-bottom: 14px;
}
body.tpl-2:not(.orient-portrait) .member-photo {
    width: 200px;
    height: 225px;
}
body.tpl-2:not(.orient-portrait) .right-panel .fullname {
    font-size: 26px;
    line-height: 1.2;
    margin: 14px 0 0;
    max-width: 340px;
}

/* Portrait: urutan susunan vertikal untuk template 2 */
body.orient-portrait.tpl-2 .member-photo   { order: 2; }
body.orient-portrait.tpl-2 .fullname       { order: 3; margin: 0 0 14px; }
body.orient-portrait.tpl-2 .member-type    { order: 4; }
body.orient-portrait.tpl-2 .member-no      { order: 5; margin-bottom: 12px; }
body.orient-portrait.tpl-2 .qrcode-placeholder { order: 6; }
body.orient-portrait.tpl-2 .expiry-date    { order: 7; margin: 14px 0 0; }

body.tpl-2.mode-barcode .qrcode-placeholder {
    margin-bottom: 90px; /* 210 - 120 */
}
body.tpl-2.mode-barcode.orient-portrait .qrcode-placeholder {
    margin-bottom: 75px; /* 189 - 114 */
}

        /* --- Base Styles --- */
        body {
            background-color: #e9eef2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
            padding: 20px;
        }

        /* --- Upload Section --- */
        .upload-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
            max-width: 800px;
        }

        .upload-section h3 {
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .upload-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .upload-item {
            padding: 15px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            background: #f9f9f9;
            transition: all 0.3s ease;
        }

        .upload-item:hover {
            border-color: #4CAF50;
            background: #f0fff0;
        }

        .upload-item label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .upload-item input[type="file"] {
            width: 100%;
            padding: 8px;
            border: none;
            background: transparent;
        }

        /* Color Picker Section */
        .color-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        .color-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .color-item label {
            font-weight: 600;
            color: #333;
        }

        .color-item input[type="color"] {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Preset Colors */
        .preset-colors {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }

        .preset-color {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .preset-color:hover {
            transform: scale(1.1);
        }

        /* --- Controls Section --- */
        .controls {
            margin-bottom: 20px;
            text-align: center;
        }

        .controls button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin: 0 10px;
            transition: background-color 0.3s;
        }

        .controls button:hover {
            background-color: #45a049;
        }

        .download-btn {
            background-color: #2196F3 !important;
        }

        .download-btn:hover {
            background-color: #1976D2 !important;
        }

        /* --- Card Container --- */
        .card-container {
            width: 1004px;
            height: 618px;
            /* Default background */
            background: linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            overflow: hidden;
            position: relative;
            display: flex;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        /* Color overlay for background tinting */
        .card-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--overlay-color, rgba(255, 224, 97, 0.3));
            z-index: 1;
            transition: background 0.3s ease;
        }

        /* --- Header Section (Logo & Library Name) --- */
        .header-section {
            position: absolute;
            top: 30px;
            left: 40px;
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 10;
        }

        .logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            object-fit: cover;
        }

        .library-name {
            font-weight: 900;
            color: #4a442a;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8);
        }

        .library-name h1 {
            font-size: 24px;
            margin: 0;
            line-height: 1.2;
            max-width: 400px;
        }

        /* --- Main Layout Panels (Flexbox) --- */
        .left-panel,
        .right-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            height: 100%;
            z-index: 5;
        }

        .left-panel {
            flex-basis: 60%;
            align-items: center;
        }

        .right-panel {
            flex-basis: 40%;
            background-color: var(--panel-bg, rgba(255, 255, 255, 0.4));
            -webkit-backdrop-filter: blur(var(--panel-blur, 10px));
            backdrop-filter: blur(var(--panel-blur, 10px));
        }

        /* --- Left Panel Content --- */
        .fullname {
            font-size: 40px;
            font-weight: 900;
            margin-bottom: 25px;
            color: #4a442a;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8);
        }

        .qrcode-placeholder {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .qrcode-placeholder img {
            width: 180px;
            height: 180px;
            display: block;
        }

        /* --- Right Panel Content --- */
        .member-type {
            background-color: black;
            color: white;
            font-size: 32px;
            font-weight: 700;
            padding: 12px 40px;
            border-radius: 50px;
            margin-bottom: 20px;
        }

        .member-no {
            font-size: 32px;
            font-weight: 700;
            color: #4a442a;
            margin-bottom: 25px;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8);
        }

        .expiry-photo-section {
            text-align: center;
        }

        .expiry-date {
            font-size: 24px;
            margin-bottom: 15px;
            color: #4a442a;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8);
        }

        .member-photo {
            width: 250px;
            height: 280px;
            border: 8px solid white;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* --- Status Messages --- */
        .status {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
        }

        .status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body class="orient-<?= $orientation ?> mode-<?= $code_type ?> tpl-<?= $template ?><?= $show_logo ? '' : ' hide-logo' ?><?= $show_name ? '' : ' hide-libname' ?><?= (!$show_logo && !$show_name) ? ' hide-header' : '' ?>">
    <div class="layout">

        <aside class="sidebar">
    <div class="upload-section">
        <h3>🎨 Kustomisasi Background & Warna</h3>

        <div class="upload-grid">
            <div class="upload-item">
                <label for="bgImage">📷 Upload Background Image:</label>
                <input type="file" id="bgImage" accept="image/*" onchange="handleBackgroundUpload(event)">
                <small style="color: #666; display: block; margin-top: 5px;">Format: JPG, PNG, GIF</small>
            </div>

            <div class="upload-item">
                <label>🎨 Atau Pilih Warna Background:</label>
                <input type="color" id="bgColorPicker" value="#ffe061" onchange="changeBackgroundColor(this.value)"
                    style="width: 100%; height: 50px; border: none; border-radius: 8px; cursor: pointer;">
            </div>
            <div class="upload-item">
    <label> Template Kartu:</label>
    <label style="font-weight:500;">
        <input type="radio" name="cardTemplate" value="1" <?= $template === '1' ? 'checked' : '' ?> onchange="applyTemplate(this.value)"> Template 1
    </label>
    <label style="font-weight:500;">
        <input type="radio" name="cardTemplate" value="2" <?= $template === '2' ? 'checked' : '' ?> onchange="applyTemplate(this.value)"> Template 2
    </label>
</div>
            <div class="upload-item">
                <label> Tampilan Header:</label>
                <label style="font-weight:500;">
                    <input type="checkbox" id="toggleLogo" <?= $show_logo ? 'checked' : '' ?>
                        onchange="applyCardOptions()"> Tampilkan Logo
                </label>
                <label style="font-weight:500;">
                    <input type="checkbox" id="toggleName" <?= $show_name ? 'checked' : '' ?>
                        onchange="applyCardOptions()"> Tampilkan Nama Perpustakaan
                </label>
            </div>

            <div class="upload-item">
                <label> Jenis Kode:</label>
                <label style="font-weight:500;">
                    <input type="radio" name="codeType" value="qr" <?= $code_type === 'qr' ? 'checked' : '' ?>
                        onchange="applyCardOptions()"> QR Code
                </label>
                <label style="font-weight:500;">
                    <input type="radio" name="codeType" value="barcode" <?= $code_type === 'barcode' ? 'checked' : '' ?>
                        onchange="applyCardOptions()"> Barcode
                </label>
            </div>

            <div class="upload-item">
                <label> Panel Kanan:</label>

                <label style="font-weight:500;">
                    <input type="checkbox" id="togglePanel" checked onchange="updatePanel()"> Tampilkan panel
                </label>

                <label style="font-weight:500; margin-top:8px;">Warna:</label>
                <input type="color" id="panelColor" value="#ffffff" onchange="updatePanel()"
                    style="width:100%; height:40px; border:none; border-radius:6px; cursor:pointer;">

                <label style="font-weight:500; margin-top:8px;">
                    Opacity: <span id="panelOpacityVal">40%</span>
                </label>
                <input type="range" id="panelOpacity" min="0" max="100" value="40" oninput="updatePanel()"
                    style="width:100%;">

                <label style="font-weight:500; margin-top:8px;">
                    Blur: <span id="panelBlurVal">10px</span>
                </label>
                <input type="range" id="panelBlur" min="0" max="20" value="10" oninput="updatePanel()"
                    style="width:100%;">
            </div>
        </div>

        <div class="color-section">
            <div class="color-item">
                <label>Warna Overlay:</label>
                <input type="color" id="overlayColor" value="#ffe061" onchange="changeOverlayColor(this.value)">
            </div>
            <div class="color-item">
                <label>Opacity Overlay:</label>
                <input type="range" id="overlayOpacity" min="0" max="100" value="30"
                    onchange="changeOverlayOpacity(this.value)" style="width: 100%;">
                <span id="opacityValue">30%</span>
            </div>
        </div>

        <div class="preset-colors">
            <div class="preset-color" style="background: linear-gradient(135deg, #ffe061, #f4c430);"
                onclick="applyPresetGradient('linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%)', '#ffe061')"
                title="Kuning Emas"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #4CAF50, #2E7D32);"
                onclick="applyPresetGradient('linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%)', '#4CAF50')"
                title="Hijau"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #2196F3, #1565C0);"
                onclick="applyPresetGradient('linear-gradient(135deg, #2196F3 0%, #1565C0 100%)', '#2196F3')"
                title="Biru"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #FF5722, #D32F2F);"
                onclick="applyPresetGradient('linear-gradient(135deg, #FF5722 0%, #D32F2F 100%)', '#FF5722')"
                title="Merah"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #9C27B0, #6A1B9A);"
                onclick="applyPresetGradient('linear-gradient(135deg, #9C27B0 0%, #6A1B9A 100%)', '#9C27B0')"
                title="Ungu"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #607D8B, #37474F);"
                onclick="applyPresetGradient('linear-gradient(135deg, #607D8B 0%, #37474F 100%)', '#607D8B')"
                title="Abu-abu"></div>
        </div>

        <div style="margin-top: 15px;">
            <button onclick="removeBackground()"
                style="background: #FF5722; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🗑️
                Hapus Background</button>
            <button onclick="resetToDefault()"
                style="background: #FF9800; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔄
                Reset Default</button>
        </div>
    </div>
    </aside>

    <main class="preview">
    <!-- Card dengan data PHP (simulasi) -->
    <div class="card-container" id="libraryCard" style="<?= $backgroundStyle ?>">
        <!-- Header dengan Logo dan Nama Perpustakaan -->
        <div class="header-section">

            <img style="width: 150px; height: 150px; object-fit: contain; border-radius: 16px; margin-bottom: 20px;"
                src=" <?php echo $logo_base64; ?> " alt="Logo Perpustakaan" id="logoImage">

            <div class="library-name">
                <!-- Simulasi PHP: <?php echo $perpus_name; ?> -->
                <h1> <?php echo $perpus_name; ?></h1>
            </div>
        </div>

        <!-- Panel Kiri -->
        <div class="left-panel">
            <div class="fullname">
                <?php echo $anggota->Fullname; ?>

            </div>
            <div class="qrcode-placeholder">
                <img class="code-qr" src="<?php echo $qr_image; ?>" alt="QR Code" id="qrImage">
                <img class="code-barcode" data-value="<?= esc($anggota->MemberNo) ?>" alt="Barcode" id="barcodeImage">
            </div>
        </div>

        <!-- Panel Kanan -->
        <div class="right-panel">
            <div class="member-type">
                <?php echo $jenis_anggota_nama; ?>

            </div>
            <div class="member-no">
                <?php echo $anggota->MemberNo; ?>

            </div>
            <div class="expiry-photo-section">
                <div class="expiry-date">
                    Berlaku Hingga<br>
                    <?php echo $end_date; ?>
                </div>
                <img src="<?php echo $photo_base64; ?>" alt="Foto Anggota" class="member-photo" id="memberPhoto">
            </div>
        </div>
    </div>
    <div class="controls">
        <div style="margin-bottom: 15px; margin-top: 24px;">
            <strong style="margin-right: 10px;">Orientasi:</strong>
            <button type="button" id="btnLandscape" onclick="setOrientation('landscape')">🖼️ Landscape</button>
            <button type="button" id="btnPortrait" onclick="setOrientation('portrait')">📄 Portrait</button>
        </div>
        <button onclick="printCard()">🖨️ Print sebagai PDF</button>
        <button onclick="downloadAsHTML()" class="download-btn">💾 Download HTML</button>
        <button onclick="generatePDFWithLibrary()" class="download-btn">📄 Generate PDF (Advanced)</button>
    </div>
    </main>

    <div id="status"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
        integrity="sha384-JcnsjUPPylna1s1fvi1u12X5qjY5OL56iySh75FdtrwhO/SWXgMjoVqcKyIIWOLk"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        integrity="sha384-ZZ1pncU3bQe8y31yfZdMFdSpttDoPmOZg2wguVK9almUodir1PghgT0eY7Mrty8H"
        crossorigin="anonymous"></script>

    <script>
        // Pindahkan elemen sesuai template (markup tetap satu)
function applyTemplate(tpl) {
    tpl = tpl === '2' ? '2' : '1';

    const left    = document.querySelector('#libraryCard .left-panel');
    const right   = document.querySelector('#libraryCard .right-panel');
    const section = document.querySelector('#libraryCard .expiry-photo-section');
    const name    = document.querySelector('#libraryCard .fullname');
    const code    = document.querySelector('#libraryCard .qrcode-placeholder');
    const type    = document.querySelector('#libraryCard .member-type');
    const no      = document.querySelector('#libraryCard .member-no');
    const expiry  = document.querySelector('#libraryCard .expiry-date');
    const photo   = document.getElementById('memberPhoto');

    if (tpl === '2') {
        left.append(no, code, expiry);
        right.append(type, section);
        section.append(photo, name);
    } else {
        left.append(name, code);
        right.append(type, no, section);
        section.append(expiry, photo);
    }

    document.body.classList.toggle('tpl-1', tpl === '1');
    document.body.classList.toggle('tpl-2', tpl === '2');

    const radio = document.querySelector(`input[name="cardTemplate"][value="${tpl}"]`);
    if (radio) radio.checked = true;
}
        // Terapkan toggle logo / nama / jenis kode
        function applyCardOptions() {
            const showLogo = document.getElementById('toggleLogo').checked;
            const showName = document.getElementById('toggleName').checked;
            const codeType = document.querySelector('input[name="codeType"]:checked').value;
            const cl = document.body.classList;

            cl.toggle('hide-logo', !showLogo);
            cl.toggle('hide-libname', !showName);
            cl.toggle('hide-header', !showLogo && !showName);
            cl.toggle('mode-qr', codeType === 'qr');
            cl.toggle('mode-barcode', codeType === 'barcode');
        }

        // Render barcode CODE128 dari MemberNo (hasilnya data URL PNG di <img>)
        function renderBarcode() {
            const img = document.getElementById('barcodeImage');
            if (!img) return;
            if (typeof JsBarcode === 'undefined') {
                console.warn('JsBarcode gagal dimuat (cek koneksi internet).');
                return;
            }
            try {
                JsBarcode(img, img.dataset.value, {
                    format: 'CODE128',
                    displayValue: false,
                    margin: 0,
                    width: 4,
                    height: 100
                });
            } catch (e) {
                console.error('Barcode error:', img.dataset.value, e);
            }
        }

        function updatePanel() {
            const card = document.getElementById('libraryCard');
            const on = document.getElementById('togglePanel').checked;
            const color = document.getElementById('panelColor').value;
            const opacity = document.getElementById('panelOpacity').value;
            const blur = document.getElementById('panelBlur').value;

            document.getElementById('panelOpacityVal').textContent = opacity + '%';
            document.getElementById('panelBlurVal').textContent = blur + 'px';

            const r = parseInt(color.substr(1, 2), 16);
            const g = parseInt(color.substr(3, 2), 16);
            const b = parseInt(color.substr(5, 2), 16);

            if (on) {
                card.style.setProperty('--panel-bg', `rgba(${r}, ${g}, ${b}, ${opacity / 100})`);
                card.style.setProperty('--panel-blur', blur + 'px');
            } else {
                card.style.setProperty('--panel-bg', 'transparent');
                card.style.setProperty('--panel-blur', '0px');
            }
        }

        function showStatus(message, type = 'success') {
            const statusDiv = document.getElementById('status');
            statusDiv.className = `status ${type}`;
            statusDiv.textContent = message;

            setTimeout(() => {
                statusDiv.textContent = '';
                statusDiv.className = '';
            }, 3000);
        }

        // Background Image Handler
        // Ganti fungsi lama dengan yang ini
        async function handleBackgroundUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // 1. Tampilkan preview lokal secara instan
            const reader = new FileReader();
            reader.onload = function (e) {
                const cardContainer = document.getElementById('libraryCard');
                cardContainer.style.background = `url('${e.target.result}')`;
                cardContainer.style.backgroundSize = 'cover';
                cardContainer.style.backgroundPosition = 'center';
                cardContainer.style.backgroundRepeat = 'no-repeat';
            };
            reader.readAsDataURL(file);

            // 2. Siapkan dan kirim file ke server
            const formData = new FormData();
            formData.append('bgImage', file);

            // Wajib disertakan agar tidak diblokir Config\Security CI4 (status 303)
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            try {
                showStatus('Sedang mengupload background...', 'success');

                // redirect: 'manual' supaya kita bisa deteksi eksplisit kalau request di-redirect
                // (misalnya karena CSRF gagal) alih-alih otomatis diikuti browser secara diam-diam
                const response = await fetch('<?= site_url("anggota/uploadBackground") ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    credentials: 'same-origin',
                    redirect: 'manual',
                });

                console.log('[uploadBackground] status:', response.status, '| type:', response.type);

                if (response.type === 'opaqueredirect' || response.status === 0) {
                    // Request di-redirect sebelum sampai ke controller — hampir pasti CSRF ditolak
                    console.error('[uploadBackground] Request di-redirect. Kemungkinan besar CSRF token ditolak server.');
                    showStatus('Upload gagal: request di-redirect oleh server (kemungkinan CSRF). Cek console.', 'error');
                    return;
                }

                const rawText = await response.text();
                console.log('[uploadBackground] raw response:', rawText.substring(0, 500));

                let result;
                try {
                    result = JSON.parse(rawText);
                } catch (parseErr) {
                    showStatus('Server tidak mengembalikan JSON. Cek console untuk detail response.', 'error');
                    return;
                }

                if (result.success) {
                    showStatus(result.message, 'success');
                    // CI4 me-regenerate token CSRF tiap request (jika regenerate=true di Config\Security),
                    // jadi token lama pada halaman ini sudah tidak valid. Refresh agar token baru ter-load
                    // sebelum melakukan upload berikutnya.
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    const errorMessage = result.errors ? Object.values(result.errors).join(', ') : result.message;
                    showStatus(`Error: ${errorMessage}`, 'error');
                }
            } catch (error) {
                showStatus('Terjadi kesalahan koneksi saat mengupload file.', 'error');
                console.error('Upload failed:', error);
            }
        }

        // Background Color Handler
        function changeBackgroundColor(color) {
            const cardContainer = document.getElementById('libraryCard');
            cardContainer.style.background = color;
            document.getElementById('overlayColor').value = color;
            updateOverlay();
            showStatus('✅ Warna background berhasil diubah!');
        }

        // Overlay Color Handler
        function changeOverlayColor(color) {
            updateOverlay();
            showStatus('✅ Warna overlay berhasil diubah!');
        }

        // Overlay Opacity Handler
        function changeOverlayOpacity(value) {
            document.getElementById('opacityValue').textContent = value + '%';
            updateOverlay();
        }

        // Update Overlay
        function updateOverlay() {
            const color = document.getElementById('overlayColor').value;
            const opacity = document.getElementById('overlayOpacity').value / 100;
            const cardContainer = document.getElementById('libraryCard');

            // Convert hex to rgba
            const r = parseInt(color.substr(1, 2), 16);
            const g = parseInt(color.substr(3, 2), 16);
            const b = parseInt(color.substr(5, 2), 16);

            cardContainer.style.setProperty('--overlay-color', `rgba(${r}, ${g}, ${b}, ${opacity})`);
        }

        // Apply Preset Gradient
        function applyPresetGradient(gradient, overlayColor) {
            const cardContainer = document.getElementById('libraryCard');
            cardContainer.style.background = gradient;
            document.getElementById('overlayColor').value = overlayColor;
            updateOverlay();
            showStatus('✅ Preset warna berhasil diterapkan!');
        }

        // Remove Background
        function removeBackground() {
            const cardContainer = document.getElementById('libraryCard');
            cardContainer.style.background = 'linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%)';
            cardContainer.style.setProperty('--overlay-color', 'rgba(255, 224, 97, 0.3)');
            document.getElementById('overlayColor').value = '#ffe061';
            document.getElementById('overlayOpacity').value = 30;
            document.getElementById('opacityValue').textContent = '30%';
            showStatus('✅ Background berhasil dihapus!');
        }

        // Reset to Default
        function resetToDefault() {
            removeBackground();
            document.getElementById('bgColorPicker').value = '#ffe061';
            // Clear file input
            document.getElementById('bgImage').value = '';
            document.getElementById('toggleLogo').checked = true;
            document.getElementById('toggleName').checked = true;
            document.querySelector('input[name="codeType"][value="qr"]').checked = true;
            applyCardOptions();
            document.getElementById('togglePanel').checked = true;
            document.getElementById('panelColor').value = '#ffffff';
            document.getElementById('panelOpacity').value = 40;
            document.getElementById('panelBlur').value = 10;
            updatePanel();
            applyTemplate('1');
            showStatus('✅ Kartu berhasil direset ke default!');
        }

        // Orientasi kartu (landscape / portrait)
        let currentOrientation = <?= json_encode($orientation) ?>;

        function applyOrientationStyle() {
            let styleEl = document.getElementById('dynamicPageStyle');
            if (!styleEl) {
                styleEl = document.createElement('style');
                styleEl.id = 'dynamicPageStyle';
                document.head.appendChild(styleEl);
            }
            // Pada mode portrait, halaman PDF harus sama dengan kartu. Sebelumnya
            // halaman A4/Letter membuat area putih karena kartu diperkecil.
            styleEl.textContent = currentOrientation === 'portrait'
                ? '@page { size: 620px 1004px; margin: 0; }'
                : '@page { size: landscape; margin: 0; }';
        }

        function setOrientation(orientation) {
            currentOrientation = orientation === 'portrait' ? 'portrait' : 'landscape';
            document.body.classList.toggle('orient-portrait', currentOrientation === 'portrait');
            document.body.classList.toggle('orient-landscape', currentOrientation === 'landscape');

            const btnP = document.getElementById('btnPortrait');
            const btnL = document.getElementById('btnLandscape');
            if (btnP && btnL) {
                const active = 'background-color:#2196F3;';
                btnP.style.cssText = currentOrientation === 'portrait' ? active : '';
                btnL.style.cssText = currentOrientation === 'landscape' ? active : '';
            }

            applyOrientationStyle();
            showStatus('Orientasi kartu: ' + (currentOrientation === 'portrait' ? 'Portrait' : 'Landscape'));
        }

        // Print Function
        function printCard() {
            try {
                const controls = document.querySelector('.controls');
                const status = document.querySelector('#status');
                const uploadSections = document.querySelectorAll('.upload-section');

                controls.style.display = 'none';
                status.style.display = 'none';
                uploadSections.forEach(section => section.style.display = 'none');

                applyOrientationStyle();

                window.print();

                setTimeout(() => {
                    controls.style.display = 'block';
                    status.style.display = 'block';
                    uploadSections.forEach(section => section.style.display = 'block');
                }, 1000);

                showStatus('Dialog print telah dibuka. Pilih "Save as PDF" sebagai printer.');
            } catch (error) {
                showStatus('Error saat membuka dialog print: ' + error.message, 'error');
            }
        }

        // Download HTML Function
        function downloadAsHTML() {
            try {
                const htmlContent = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota Perpustakaan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        ${document.querySelector('style').textContent}
        
        body {
            background-color: white;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .controls, .upload-section, .status { display: none; }
    </style>
</head>
<body class="${document.body.className}">
    ${document.getElementById('libraryCard').outerHTML}
</body>
</html>`;

                const blob = new Blob([htmlContent], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'kartu-anggota-perpustakaan.html';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                showStatus('File HTML berhasil didownload!');
            } catch (error) {
                showStatus('Error saat mendownload HTML: ' + error.message, 'error');
            }
        }

        // Generate PDF Function
        async function generatePDFWithLibrary() {
            try {
                showStatus('Sedang menggenerate PDF...', 'success');

                const element = document.getElementById('libraryCard');

                // Hide other elements
                const controls = document.querySelector('.controls');
                const status = document.querySelector('#status');
                const uploadSections = document.querySelectorAll('.upload-section');
                const isPortrait = currentOrientation === 'portrait';

                controls.style.display = 'none';
                status.style.display = 'none';
                uploadSections.forEach(section => section.style.display = 'none');

                // Preview portrait diperkecil di layar. Lepaskan transform tersebut
                // sebelum diraster agar background mengisi seluruh halaman PDF.
                if (isPortrait) {
                    element.classList.add('pdf-capture');
                    await new Promise(resolve => requestAnimationFrame(resolve));
                }

                await new Promise(resolve => setTimeout(resolve, 100));

                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: null,
                    width: isPortrait ? 620 : 1004,
                    height: isPortrait ? 1004 : 618,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: 1200,
                    windowHeight: isPortrait ? 1400 : 800
                });

                element.classList.remove('pdf-capture');

                // Restore elements
                controls.style.display = 'block';
                status.style.display = 'block';
                uploadSections.forEach(section => section.style.display = 'block');

                const imgData = canvas.toDataURL('image/png', 1.0);

                const { jsPDF } = window.jspdf;

                const pdfW = isPortrait ? 162 : 254;
                const pdfH = isPortrait ? (pdfW * canvas.height / canvas.width) : 157;

                const pdf = new jsPDF({
                    orientation: isPortrait ? 'portrait' : 'landscape',
                    unit: 'mm',
                    format: [pdfW, pdfH]
                });

                // Gunakan dimensi halaman aktual dari jsPDF agar gambar tidak
                // menyisakan area kosong bila jsPDF menyesuaikan orientasi.
                pdf.addImage(
                    imgData,
                    'PNG',
                    0,
                    0,
                    pdf.internal.pageSize.getWidth(),
                    pdf.internal.pageSize.getHeight(),
                    '',
                    'FAST'
                );

                pdf.save('kartu-anggota-perpustakaan.pdf');

                showStatus('PDF berhasil digenerate dan didownload!');

            } catch (error) {
                const element = document.getElementById('libraryCard');
                if (element) element.classList.remove('pdf-capture');
                const controls = document.querySelector('.controls');
                const status = document.querySelector('#status');
                const uploadSections = document.querySelectorAll('.upload-section');
                if (controls) controls.style.display = 'block';
                if (status) status.style.display = 'block';
                uploadSections.forEach(section => section.style.display = 'block');
                showStatus('Error saat menggenerate PDF: ' + error.message, 'error');
                console.error('PDF generation error:', error);
            }
        }

        // Initialize
        window.addEventListener('load', function () {
            console.log('Card with PHP data and background customization loaded successfully');
            updateOverlay(); // Set initial overlay
            renderBarcode();       // baru
            applyCardOptions();    // baru
            applyTemplate(document.querySelector('input[name="cardTemplate"]:checked').value);
            setOrientation(currentOrientation); // Set initial orientation + tombol aktif
        });
    </script>
</body>

</html>