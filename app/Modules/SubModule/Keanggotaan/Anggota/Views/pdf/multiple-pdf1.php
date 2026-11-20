<?php
$orientation = ($orientation ?? 'landscape') === 'portrait' ? 'portrait' : 'landscape';
// Landscape: 4 anggota per halaman A4. Portrait: 3 anggota per halaman A4.
$cards_per_page = $orientation === 'portrait' ? 3 : 4;
$code_type = ($code_type ?? 'qr') === 'barcode' ? 'barcode' : 'qr';
$show_logo = $show_logo ?? true;
$show_name = $show_name ?? true;
$template = ($template ?? '1') === '2' ? '2' : '1';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
    <style>
        /* =====================================================
           BASE
           ===================================================== */
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

        /* =====================================================
           A4 & BARIS KARTU
           ===================================================== */
        .a4-container {
            width: 210mm;
            height: 297mm;
            background-color: white;
            box-shadow: 0 5mm 15mm rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            padding: 12mm;
            gap: 2mm;
            box-sizing: border-box;
            position: relative;
        }

        .card-row {
            display: flex;
            gap: 5mm;
            height: 60mm;
            margin-bottom: 2mm;
        }

        /* =====================================================
           KARTU (acuan: pdf1)
           Semua ukuran di dalam kartu ditulis dalam satuan --u,
           yaitu 1 "px" pada desain pdf1 (1004 x 618) yang
           diskalakan ke kartu 85mm x 54mm. Jadi proporsinya
           sama persis dengan pdf1.
           ===================================================== */
        .card-front,
        .card-back {
            --u: 0.08466mm;          /* landscape: 85mm / 1004px */
            width: 85mm;
            height: 54mm;
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
            border-radius: calc(24 * var(--u));   /* ganti ke 0 kalau kartu dipotong kotak */
            box-shadow: 0 1mm 3mm rgba(0, 0, 0, 0.1);
        }

        /* ---------- Depan ---------- */
        .card-front {
            --ts: 0.085mm 0.085mm 0.17mm rgba(255, 255, 255, 0.8);
            background: var(--front-bg, linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%));
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
        }

        .card-front::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--front-overlay, rgba(255, 224, 97, 0.3));
            z-index: 1;
        }

        /* Header: logo & nama perpustakaan */
        .front-header {
            position: absolute;
            top: calc(30 * var(--u));
            left: calc(40 * var(--u));
            display: flex;
            align-items: center;
            gap: calc(20 * var(--u));
            z-index: 10;
        }

        .front-logo {
            width: calc(150 * var(--u));
            height: calc(150 * var(--u));
            object-fit: contain;
            border-radius: calc(16 * var(--u));
            margin-bottom: calc(20 * var(--u));
        }

        .front-library-name {
            font-weight: 900;
            color: #4a442a;
            font-size: calc(24 * var(--u));
            line-height: 1.2;
            max-width: calc(400 * var(--u));
            text-shadow: var(--ts);
        }

        /* Panel kiri & kanan */
        .front-left,
        .front-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: calc(20 * var(--u));
            height: 100%;
            z-index: 5;
        }

        .front-left {
            flex-basis: 60%;
        }

        .front-right {
            flex-basis: 40%;
            background-color: var(--panel-bg, rgba(255, 255, 255, 0.4));
            -webkit-backdrop-filter: blur(var(--panel-blur, 10px));
            backdrop-filter: blur(var(--panel-blur, 10px));
        }

        .front-name {
            font-size: calc(40 * var(--u));
            font-weight: 900;
            margin-bottom: calc(25 * var(--u));
            color: #4a442a;
            text-align: center;
            text-shadow: var(--ts);
        }

        .front-qr {
            background-color: #ffffff;
            padding: calc(15 * var(--u));
            border-radius: calc(12 * var(--u));
            box-shadow: 0 0.3mm 0.7mm rgba(0, 0, 0, 0.1);
        }

        .front-qr img.code-qr {
            width: calc(180 * var(--u));
            height: calc(180 * var(--u));
            display: block;
        }

        .front-member-type {
            background-color: black;
            color: white;
            font-size: calc(32 * var(--u));
            font-weight: 700;
            padding: calc(12 * var(--u)) calc(40 * var(--u));
            border-radius: calc(50 * var(--u));
            margin-bottom: calc(20 * var(--u));
        }

        .front-member-no {
            font-size: calc(32 * var(--u));
            font-weight: 700;
            color: #4a442a;
            margin-bottom: calc(25 * var(--u));
            text-shadow: var(--ts);
        }

        .front-section {
            text-align: center;
        }

        .front-expiry {
            font-size: calc(24 * var(--u));
            margin-bottom: calc(15 * var(--u));
            color: #4a442a;
            font-weight: 700;
            text-shadow: var(--ts);
        }

        .front-photo {
            width: calc(250 * var(--u));
            height: calc(280 * var(--u));
            border: calc(8 * var(--u)) solid white;
            object-fit: cover;
            border-radius: calc(16 * var(--u));
            box-shadow: 0 0.3mm 1.2mm rgba(0, 0, 0, 0.2);
        }

        /* ---------- QR Code vs Barcode ---------- */
        .front-qr img.code-barcode {
            display: block;
            width: calc(300 * var(--u));
            height: calc(90 * var(--u));
        }

        body.mode-qr .front-qr img.code-barcode {
            display: none;
        }

        body.mode-barcode .front-qr img.code-qr {
            display: none;
        }

        /* ---------- Belakang ---------- */
        .card-back {
            background: var(--back-bg, linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%));
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4mm;
        }

        .card-back::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--back-overlay, rgba(244, 196, 48, 0.3));
            z-index: 1;
        }

        .back-content {
            z-index: 10;
            position: relative;
            padding: 4mm;
        }

        .back-terms-title,
        .back-terms-list li,
        .back-terms-list li::before,
        .back-office-name,
        .back-office-address {
            color: var(--back-text-color, #2c5530);
        }

        .back-terms-title {
            font-size: 4.5mm;
            font-weight: 700;
            margin: 0 0 4mm;
            text-align: center;
            text-shadow: 0.3mm 0.3mm 0.7mm rgba(255, 255, 255, 0.5);
        }

        .back-terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
            counter-reset: item;
        }

        .back-terms-list li {
            font-size: 3.2mm;
            font-weight: 500;
            margin-bottom: 2.5mm;
            line-height: 1.3;
            display: flex;
            align-items: flex-start;
            text-shadow: 0.2mm 0.2mm 0.5mm rgba(255, 255, 255, 0.3);
        }

        .back-terms-list li::before {
            content: counter(item) ". ";
            counter-increment: item;
            font-weight: 700;
            margin-right: 2mm;
            min-width: 4mm;
        }

        .back-separator {
            width: 100%;
            height: 1mm;
            background: linear-gradient(90deg, transparent 0%, var(--back-text-color, #2c5530) 20%, var(--back-text-color, #2c5530) 80%, transparent 100%);
            margin: 4mm 0;
            z-index: 10;
            position: relative;
        }

        .back-footer {
            text-align: center;
            z-index: 10;
            position: relative;
            padding: 0 4mm 4mm 4mm;
        }

        .back-office-name {
            font-size: 3.8mm;
            font-weight: 700;
            margin-bottom: 2mm;
            line-height: 1.2;
            text-shadow: 0.3mm 0.3mm 0.7mm rgba(255, 255, 255, 0.5);
        }

        .back-office-address {
            font-size: 3.2mm;
            font-weight: 500;
            opacity: 0.9;
            text-shadow: 0.2mm 0.2mm 0.5mm rgba(255, 255, 255, 0.3);
        }

        /* =====================================================
           PORTRAIT (acuan: pdf1, 620 x 1004 -> 55mm x 84mm)
           ===================================================== */
        body.orient-portrait .a4-container {
            padding: 8mm 12mm;
        }

        body.orient-portrait .card-row {
            height: 86mm;
        }

        body.orient-portrait .card-front,
        body.orient-portrait .card-back {
            --u: 0.0837mm;           /* portrait: 84mm / 1004px */
            width: 55mm;
            height: 84mm;
        }

        body.orient-portrait .card-front {
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            text-align: center;
            padding: calc(40 * var(--u)) calc(34 * var(--u)) calc(36 * var(--u));
            border-radius: calc(28 * var(--u));
        }

        /* Isi panel dijadikan anak langsung agar bisa diurutkan */
        body.orient-portrait .front-left,
        body.orient-portrait .front-right,
        body.orient-portrait .front-section {
            display: contents;
        }

        body.orient-portrait .front-header,
        body.orient-portrait .front-name,
        body.orient-portrait .front-photo,
        body.orient-portrait .front-member-type,
        body.orient-portrait .front-member-no,
        body.orient-portrait .front-expiry,
        body.orient-portrait .front-qr {
            position: relative;
            z-index: 5;
        }

        body.orient-portrait .front-header {
            order: 1;
            top: auto;
            left: auto;
            flex-direction: column;
            align-items: center;
            gap: calc(8 * var(--u));
            width: 100%;
            margin-bottom: calc(4 * var(--u));
        }

        body.orient-portrait .front-logo {
            width: calc(110 * var(--u));
            height: calc(110 * var(--u));
            margin-bottom: 0;
        }

        /* Nama perpustakaan dibatasi 2 baris supaya ukuran kartu tetap */
        body.orient-portrait .front-library-name {
            font-size: calc(20 * var(--u));
            max-width: 100%;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        body.orient-portrait .front-name {
            order: 2;
            font-size: calc(30 * var(--u));
            margin: calc(14 * var(--u)) 0 calc(12 * var(--u));
            width: 100%;
        }

        body.orient-portrait .front-photo {
            order: 3;
            width: calc(200 * var(--u));
            height: calc(240 * var(--u));
            margin-bottom: calc(14 * var(--u));
        }

        body.orient-portrait .front-member-type {
            order: 4;
            font-size: calc(22 * var(--u));
            padding: calc(7 * var(--u)) calc(32 * var(--u));
            margin-bottom: calc(12 * var(--u));
        }

        body.orient-portrait .front-member-no {
            order: 5;
            font-size: calc(24 * var(--u));
            margin-bottom: calc(12 * var(--u));
        }

        body.orient-portrait .front-expiry {
            order: 6;
            font-size: calc(19 * var(--u));
            margin-bottom: calc(14 * var(--u));
        }

        body.orient-portrait .front-qr {
            order: 7;
            padding: calc(12 * var(--u));
        }

        body.orient-portrait .front-qr img.code-qr {
            width: calc(165 * var(--u));
            height: calc(165 * var(--u));
        }

        body.orient-portrait .front-qr img.code-barcode {
            width: calc(340 * var(--u));
            height: calc(90 * var(--u));
        }

        /* Kartu belakang portrait */
        body.orient-portrait .card-back {
            padding: 3mm;
        }

        body.orient-portrait .back-content {
            padding: 2mm;
        }

        body.orient-portrait .back-terms-title {
            font-size: 3.2mm;
            margin-bottom: 2.5mm;
        }

        body.orient-portrait .back-terms-list li {
            font-size: 2.3mm;
            margin-bottom: 1.5mm;
        }

        body.orient-portrait .back-terms-list li::before {
            min-width: 3mm;
            margin-right: 1mm;
        }

        body.orient-portrait .back-separator {
            margin: 2.5mm 0;
        }

        body.orient-portrait .back-footer {
            padding: 0 2mm 2mm 2mm;
        }

        body.orient-portrait .back-office-name {
            font-size: 2.9mm;
        }

        body.orient-portrait .back-office-address {
            font-size: 2.5mm;
        }

        /* =====================================================
           TEMPLATE 2 (acuan: pdf1)
           Kiri : no anggota, kode, berlaku hingga
           Kanan: jenis anggota, foto, nama
           ===================================================== */
        body.tpl-2 .front-left .front-member-no {
            margin: 0 0 calc(16 * var(--u));
            text-align: center;
        }

        body.tpl-2 .front-left .front-expiry {
            margin: calc(16 * var(--u)) 0 0;
            text-align: center;
        }

        /* Landscape: kedua panel mulai dari garis atas yang sama */
        body.tpl-2:not(.orient-portrait) .front-left,
        body.tpl-2:not(.orient-portrait) .front-right {
            box-sizing: border-box;
            justify-content: flex-start;
            padding-top: calc(190 * var(--u));
        }

        body.tpl-2.hide-header:not(.orient-portrait) .front-left,
        body.tpl-2.hide-header:not(.orient-portrait) .front-right {
            padding-top: calc(50 * var(--u));
        }

        body.tpl-2:not(.orient-portrait) .front-right .front-member-type {
            margin-bottom: calc(14 * var(--u));
        }

        body.tpl-2:not(.orient-portrait) .front-photo {
            width: calc(200 * var(--u));
            height: calc(225 * var(--u));
        }

        body.tpl-2:not(.orient-portrait) .front-right .front-name {
            font-size: calc(26 * var(--u));
            line-height: 1.2;
            margin: calc(14 * var(--u)) 0 0;
            max-width: calc(340 * var(--u));
        }

        /* Portrait: urutan susunan vertikal untuk template 2 */
        body.orient-portrait.tpl-2 .front-photo       { order: 2; }
        body.orient-portrait.tpl-2 .front-name        { order: 3; margin: 0 0 calc(14 * var(--u)); }
        body.orient-portrait.tpl-2 .front-member-type { order: 4; }
        body.orient-portrait.tpl-2 .front-member-no   { order: 5; margin-bottom: calc(12 * var(--u)); }
        body.orient-portrait.tpl-2 .front-qr          { order: 6; }
        body.orient-portrait.tpl-2 .front-expiry      { order: 7; margin: calc(14 * var(--u)) 0 0; }

        /* Template 2 + barcode: ruang tetap sebesar kotak QR supaya "Berlaku Hingga" tidak naik */
        body.tpl-2.mode-barcode .front-qr {
            margin-bottom: calc(90 * var(--u));
        }

        body.tpl-2.mode-barcode.orient-portrait .front-qr {
            margin-bottom: calc(75 * var(--u));
        }

        /* =====================================================
           TOGGLE LOGO / NAMA PERPUSTAKAAN (sama seperti pdf1)
           Taruh paling bawah + !important supaya tidak kalah
           oleh aturan display lain.
           ===================================================== */
        body.hide-logo .front-logo {
            display: none !important;
        }

        body.hide-libname .front-library-name {
            display: none !important;
        }

        body.hide-header .front-header {
            display: none !important;
        }

        /* =====================================================
           LAYOUT: pengaturan di kiri, halaman A4 di kanan
           ===================================================== */
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

            .sidebar .upload-grid {
                grid-template-columns: 1fr;
            }

            .sidebar .controls button {
                margin: 4px;
            }

            .preview {
                flex: 0 0 auto;
                display: flex;
                flex-direction: column;
                gap: 16px;
            }
        }

        /* Layar sempit: kembali ke susunan atas-bawah */
        @media screen and (max-width: 1280px) {
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

        /* =====================================================
           PANEL PENGATURAN
           ===================================================== */
        .controls {
            margin-bottom: 16px;
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
            margin-top: 0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .upload-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .upload-item {
            padding: 15px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            background: #f9f9f9;
            transition: all 0.3s ease;
            text-align: left;
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
            font-size: 14px;
        }

        .upload-item input[type="file"] {
            width: 100%;
            padding: 6px;
            border: none;
            background: transparent;
            font-size: 12px;
        }

        .upload-item input[type="color"] {
            width: 100%;
            height: 40px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        /* Status: tampil sebagai notifikasi di pojok kanan bawah */
        #status {
            position: fixed;
            right: 20px;
            bottom: 20px;
            max-width: 320px;
            z-index: 1000;
        }

        .status {
            padding: 10px 14px;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

        /* =====================================================
           PRINT
           ===================================================== */
        @page {
            margin: 0;
            size: A4 portrait;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                background-color: white !important;
                display: block;
                width: auto;
                height: auto;
                min-height: 0;
                overflow: visible;
            }

            .sidebar,
            #status {
                display: none !important;
            }

            .layout,
            .preview {
                display: block !important;
                width: auto;
                gap: 0;
            }

            .a4-container {
                margin: 0 !important;
                box-shadow: none !important;
                width: 210mm !important;
                height: 296mm !important;
                break-inside: avoid;
            }

            .a4-container + .a4-container {
                break-before: page;
            }

            .card-front,
            .card-back {
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body class="orient-<?= $orientation ?> mode-<?= $code_type ?> tpl-<?= $template ?><?= $show_logo ? '' : ' hide-logo' ?><?= $show_name ? '' : ' hide-libname' ?><?= (!$show_logo && !$show_name) ? ' hide-header' : '' ?>">
    <div class="layout">

        <aside class="sidebar">
            <div class="controls">
                <button onclick="window.print()">🖨️ Print PDF</button>
                <button onclick="window.close()">❌ Tutup</button>
            </div>

            <div class="upload-section">
                <h3>🎨 Kustomisasi Kartu</h3>

                <div class="upload-grid">
                    <div class="upload-item">
                        <label for="frontBgImage">📷 Background Depan:</label>
                        <input type="file" id="frontBgImage" accept="image/*" onchange="loadFrontBackground(event)">
                    </div>

                    <div class="upload-item">
                        <label for="backBgImage">📷 Background Belakang:</label>
                        <input type="file" id="backBgImage" accept="image/*" onchange="loadBackBackground(event)">
                    </div>

                    <div class="upload-item">
                        <label>🎨 Warna Depan:</label>
                        <input type="color" id="frontColorPicker" value="#ffe061" onchange="changeFrontColor(this.value)">
                    </div>

                    <div class="upload-item">
                        <label>🎨 Warna Belakang:</label>
                        <input type="color" id="backColorPicker" value="#f4c430" onchange="changeBackColor(this.value)">
                    </div>

                    <div class="upload-item">
                        <label>📝 Warna Teks Belakang:</label>
                        <input type="color" id="backTextColor" value="#2c5530" onchange="changeBackTextColor(this.value)">
                    </div>

                    <div class="upload-item">
                        <label>🧩 Template Kartu:</label>
                        <label style="font-weight:500;">
                            <input type="radio" name="cardTemplate" value="1" <?= $template === '1' ? 'checked' : '' ?> onchange="applyTemplate(this.value)"> Template 1
                        </label>
                        <label style="font-weight:500;">
                            <input type="radio" name="cardTemplate" value="2" <?= $template === '2' ? 'checked' : '' ?> onchange="applyTemplate(this.value)"> Template 2
                        </label>
                    </div>

                    <div class="upload-item">
                        <label>🏷️ Tampilan Header:</label>
                        <label style="font-weight:500;">
                            <input type="checkbox" id="toggleLogo" <?= $show_logo ? 'checked' : '' ?> onchange="applyCardOptions()"> Tampilkan Logo
                        </label>
                        <label style="font-weight:500;">
                            <input type="checkbox" id="toggleName" <?= $show_name ? 'checked' : '' ?> onchange="applyCardOptions()"> Tampilkan Nama Perpustakaan
                        </label>
                    </div>

                    <div class="upload-item">
                        <label>🔳 Jenis Kode:</label>
                        <label style="font-weight:500;">
                            <input type="radio" name="codeType" value="qr" <?= $code_type === 'qr' ? 'checked' : '' ?> onchange="applyCardOptions()"> QR Code
                        </label>
                        <label style="font-weight:500;">
                            <input type="radio" name="codeType" value="barcode" <?= $code_type === 'barcode' ? 'checked' : '' ?> onchange="applyCardOptions()"> Barcode
                        </label>
                    </div>

                    <div class="upload-item">
                        <label>🪟 Panel Kanan (Landscape):</label>

                        <label style="font-weight:500;">
                            <input type="checkbox" id="togglePanel" checked onchange="updatePanel()"> Tampilkan panel
                        </label>

                        <label style="font-weight:500; margin-top:8px;">Warna:</label>
                        <input type="color" id="panelColor" value="#ffffff" onchange="updatePanel()">

                        <label style="font-weight:500; margin-top:8px;">
                            Opacity: <span id="panelOpacityVal">40%</span>
                        </label>
                        <input type="range" id="panelOpacity" min="0" max="100" value="40" oninput="updatePanel()" style="width:100%;">

                        <label style="font-weight:500; margin-top:8px;">
                            Blur: <span id="panelBlurVal">10px</span>
                        </label>
                        <input type="range" id="panelBlur" min="0" max="20" value="10" oninput="updatePanel()" style="width:100%;">
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <button onclick="resetToDefault()"
                        style="background: #FF9800; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔄
                        Reset Default</button>
                    <button onclick="applyPreset('green')"
                        style="background: #4CAF50; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🟢
                        Hijau</button>
                    <button onclick="applyPreset('blue')"
                        style="background: #2196F3; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔵
                        Biru</button>
                    <button onclick="applyPreset('red')"
                        style="background: #F44336; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔴
                        Merah</button>
                </div>
            </div>
        </aside>

        <main class="preview">
            <?php
            // Bagi anggota per halaman ($cards_per_page anggota per halaman, 1 anggota per baris)
            $pages = array_chunk($members_data, $cards_per_page);

            foreach ($pages as $page_index => $page_data):
                ?>
                <div class="a4-container">
                    <?php foreach ($page_data as $member): ?>
                        <div class="card-row">
                            <!-- Kartu depan -->
                            <div class="card-front">
                                <div class="front-header">
                                    <img src="<?= $logo_base64 ?>" alt="Logo" class="front-logo">
                                    <div class="front-library-name"><?= esc(strtoupper($perpus_name)) ?></div>
                                </div>

                                <div class="front-left">
                                    <div class="front-name"><?= esc(strtoupper($member['anggota']->Fullname)) ?></div>
                                    <div class="front-qr">
                                        <img class="code-qr" src="<?= $member['qr_image'] ?>" alt="QR Code">
                                        <img class="code-barcode" data-value="<?= esc($member['anggota']->MemberNo) ?>" alt="Barcode">
                                    </div>
                                </div>

                                <div class="front-right">
                                    <div class="front-member-type"><?= esc(strtoupper($member['jenis_anggota_nama'])) ?></div>
                                    <div class="front-member-no"><?= esc($member['anggota']->MemberNo) ?></div>
                                    <div class="front-section">
                                        <div class="front-expiry">Berlaku Hingga<br><?= esc($member['end_date']) ?></div>
                                        <img src="<?= $member['photo_base64'] ?>" alt="Foto" class="front-photo">
                                    </div>
                                </div>
                            </div>

                            <!-- Kartu belakang -->
                            <div class="card-back">
                                <div class="back-content">
                                    <h2 class="back-terms-title">SYARAT DAN KETENTUAN</h2>
                                    <ol class="back-terms-list">
                                        <li>Kartu ini tidak dapat dipindahtangankan</li>
                                        <li>Berlaku sesuai masa aktif yang tertera</li>
                                        <li>Wajib membawa kartu saat berkunjung</li>
                                        <li>Kehilangan kartu harap segera melapor</li>
                                    </ol>
                                </div>
                                <div class="back-separator"></div>
                                <div class="back-footer">
                                    <div class="back-office-name"><?= esc($perpus_name) ?></div>
                                    <div class="back-office-address">Alamat Perpustakaan</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php
                    // Isi baris kosong supaya halaman terakhir tetap sejajar
                    $empty_rows = $cards_per_page - count($page_data);
                    for ($i = 0; $i < $empty_rows; $i++):
                        ?>
                        <div class="card-row">
                            <div class="card-front" style="opacity: 0.1;"></div>
                            <div class="card-back" style="opacity: 0.1;"></div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endforeach; ?>
        </main>
    </div>

    <div id="status"></div>

    <script>
        function showStatus(message, type = 'success') {
            const statusDiv = document.getElementById('status');
            statusDiv.className = `status ${type}`;
            statusDiv.textContent = message;

            setTimeout(() => {
                statusDiv.textContent = '';
                statusDiv.className = '';
            }, 3000);
        }

        function hexToRgb(hex) {
            return {
                r: parseInt(hex.substr(1, 2), 16),
                g: parseInt(hex.substr(3, 2), 16),
                b: parseInt(hex.substr(5, 2), 16)
            };
        }

        // Pindahkan elemen di semua kartu sesuai template (markup tetap satu)
        function applyTemplate(tpl) {
            tpl = tpl === '2' ? '2' : '1';

            document.querySelectorAll('.card-front').forEach(card => {
                const left = card.querySelector('.front-left');
                const right = card.querySelector('.front-right');
                if (!left || !right) return; // slot kosong

                const section = card.querySelector('.front-section');
                const name = card.querySelector('.front-name');
                const code = card.querySelector('.front-qr');
                const type = card.querySelector('.front-member-type');
                const no = card.querySelector('.front-member-no');
                const expiry = card.querySelector('.front-expiry');
                const photo = card.querySelector('.front-photo');

                if (tpl === '2') {
                    left.append(no, code, expiry);
                    right.append(type, section);
                    section.append(photo, name);
                } else {
                    left.append(name, code);
                    right.append(type, no, section);
                    section.append(expiry, photo);
                }
            });

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

        // Render semua barcode CODE128 dari MemberNo (hasilnya gambar PNG di <img>)
        function renderBarcodes() {
            if (typeof JsBarcode === 'undefined') {
                console.warn('JsBarcode gagal dimuat (cek koneksi internet).');
                return;
            }
            document.querySelectorAll('img.code-barcode').forEach(img => {
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
            });
        }

        // Panel kanan: warna, opacity, blur (berlaku untuk semua kartu)
        function updatePanel() {
            const root = document.documentElement;
            const on = document.getElementById('togglePanel').checked;
            const color = document.getElementById('panelColor').value;
            const opacity = document.getElementById('panelOpacity').value;
            const blur = document.getElementById('panelBlur').value;

            document.getElementById('panelOpacityVal').textContent = opacity + '%';
            document.getElementById('panelBlurVal').textContent = blur + 'px';

            const { r, g, b } = hexToRgb(color);

            if (on) {
                root.style.setProperty('--panel-bg', `rgba(${r}, ${g}, ${b}, ${opacity / 100})`);
                root.style.setProperty('--panel-blur', blur + 'px');
            } else {
                root.style.setProperty('--panel-bg', 'transparent');
                root.style.setProperty('--panel-blur', '0px');
            }
        }

        // Background depan (preview + simpan ke server)
        async function loadFrontBackground(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                document.documentElement.style.setProperty('--front-bg', `url('${e.target.result}')`);
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('bgImage', file);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            try {
                showStatus('Mengupload background...', 'success');
                const response = await fetch('<?= site_url('anggota/uploadBackground') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    credentials: 'same-origin',
                });
                const result = await response.json();
                if (result.success) {
                    showStatus('✅ Background depan berhasil disimpan!', 'success');
                } else {
                    const msg = result.errors ? Object.values(result.errors).join(', ') : result.message;
                    showStatus('Error: ' + msg, 'error');
                }
            } catch (error) {
                showStatus('Terjadi kesalahan saat mengupload.', 'error');
            }
        }

        // Background belakang (hanya preview)
        function loadBackBackground(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.documentElement.style.setProperty('--back-bg', `url('${e.target.result}')`);
                    showStatus('✅ Background belakang berhasil diupload!');
                };
                reader.readAsDataURL(file);
            }
        }

        function changeFrontColor(color) {
            const { r, g, b } = hexToRgb(color);
            document.documentElement.style.setProperty('--front-bg', color);
            document.documentElement.style.setProperty('--front-overlay', `rgba(${r}, ${g}, ${b}, 0.3)`);
            showStatus('✅ Warna depan berhasil diubah!');
        }

        function changeBackColor(color) {
            const { r, g, b } = hexToRgb(color);
            document.documentElement.style.setProperty('--back-bg', color);
            document.documentElement.style.setProperty('--back-overlay', `rgba(${r}, ${g}, ${b}, 0.3)`);
            showStatus('✅ Warna belakang berhasil diubah!');
        }

        function changeBackTextColor(color) {
            document.documentElement.style.setProperty('--back-text-color', color);
            showStatus('✅ Warna teks belakang berhasil diubah!');
        }

        function applyPreset(preset) {
            const presets = {
                green: { front: '#4CAF50', back: '#2E7D32', text: '#1B5E20' },
                blue:  { front: '#2196F3', back: '#1565C0', text: '#0D47A1' },
                red:   { front: '#F44336', back: '#D32F2F', text: '#B71C1C' }
            };

            const colors = presets[preset];
            if (colors) {
                document.getElementById('frontColorPicker').value = colors.front;
                document.getElementById('backColorPicker').value = colors.back;
                document.getElementById('backTextColor').value = colors.text;

                changeFrontColor(colors.front);
                changeBackColor(colors.back);
                changeBackTextColor(colors.text);

                showStatus(`✅ Preset ${preset} berhasil diterapkan!`);
            }
        }

        function resetToDefault() {
            // Warna
            document.getElementById('frontColorPicker').value = '#ffe061';
            document.getElementById('backColorPicker').value = '#f4c430';
            document.getElementById('backTextColor').value = '#2c5530';

            const root = document.documentElement;
            root.style.setProperty('--front-bg', 'linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%)');
            root.style.setProperty('--back-bg', 'linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%)');
            root.style.setProperty('--front-overlay', 'rgba(255, 224, 97, 0.3)');
            root.style.setProperty('--back-overlay', 'rgba(244, 196, 48, 0.3)');
            root.style.setProperty('--back-text-color', '#2c5530');

            // File input
            document.getElementById('frontBgImage').value = '';
            document.getElementById('backBgImage').value = '';

            // Opsi kartu
            document.getElementById('toggleLogo').checked = true;
            document.getElementById('toggleName').checked = true;
            document.querySelector('input[name="codeType"][value="qr"]').checked = true;
            applyCardOptions();

            // Panel kanan
            document.getElementById('togglePanel').checked = true;
            document.getElementById('panelColor').value = '#ffffff';
            document.getElementById('panelOpacity').value = 40;
            document.getElementById('panelBlur').value = 10;
            updatePanel();

            applyTemplate('1');
            showStatus('✅ Semua pengaturan berhasil direset ke default!');
        }

        // Inisialisasi (script ada di akhir body, jadi DOM sudah siap)
        (function init() {
            const savedBg = <?= !empty($bg_b64) ? json_encode("url('" . $bg_b64 . "')") : 'null' ?>;
            const root = document.documentElement;
            root.style.setProperty('--front-bg', savedBg || 'linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%)');
            root.style.setProperty('--back-bg', 'linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%)');
            root.style.setProperty('--front-overlay', 'rgba(255, 224, 97, 0.3)');
            root.style.setProperty('--back-overlay', 'rgba(244, 196, 48, 0.3)');
            root.style.setProperty('--back-text-color', '#2c5530');

            renderBarcodes();
            applyCardOptions();
            updatePanel();
            applyTemplate(document.querySelector('input[name="cardTemplate"]:checked').value);
        })();
    </script>
</body>

</html>