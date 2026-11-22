<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota Perpustakaan - Bagian Belakang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
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
            
            .controls, .upload-section {
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
           PORTRAIT LAYOUT — kartu belakang ditata vertikal
           ===================================================== */
        body.orient-portrait #libraryCardBack {
            width: 620px;
            min-width: 620px;
            max-width: 620px;
            height: 1004px;
            min-height: 1004px;
            max-height: 1004px;
            padding: 55px 48px;
            justify-content: flex-start;
        }
        body.orient-portrait .terms-title {
            font-size: 27px;
            margin-bottom: 30px;
        }
        body.orient-portrait .terms-list li {
            font-size: 20px;
            margin-bottom: 18px;
        }
        body.orient-portrait .separator {
            margin: 30px 0 0;
        }
        body.orient-portrait .footer-section {
            margin-top: auto;
        }
        body.orient-portrait .office-name {
            font-size: 25px;
        }
        body.orient-portrait .office-address {
            font-size: 18px;
        }

        /* Preview di layar */
        @media screen {
            body.orient-portrait {
                justify-content: flex-start;
            }
            body.orient-portrait #libraryCardBack {
                transform: scale(0.82);
                transform-origin: top center;
                margin-bottom: -170px;
            }
        }

        /* html2canvas harus menangkap ukuran asli kartu, bukan versi preview yang diperkecil */
        body.orient-portrait #libraryCardBack.pdf-capture {
            transform: none !important;
            transform-origin: top left !important;
            margin-bottom: 0 !important;
        }

        /* Saat dicetak */
        @media print {
            body.orient-portrait {
                display: block !important;
                overflow: visible !important;
                height: auto !important;
            }
            body.orient-portrait #libraryCardBack {
                position: static !important;
                width: 620px !important;
                height: 1004px !important;
                margin: 0 auto !important;
                transform: scale(0.92) !important;
                transform-origin: top center !important;
            }
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Preset Colors */
        .preset-colors {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .preset-color {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
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
            background: linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            min-height: 618px;
            max-height: 618px;
            min-width: 1004px;
            max-width: 1004px;
        }

        /* Color overlay for background tinting */
        .card-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--overlay-color, rgba(244, 196, 48, 0.3));
            z-index: 1;
            transition: background 0.3s ease;
        }

        /* --- Decorative Elements --- */
        .decorative-shape {
            position: absolute;
            z-index: 2;
            opacity: 0.1;
        }

        .shape-1 {
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .shape-2 {
            bottom: -30px;
            left: -30px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: rotate 15s linear infinite;
        }

        .shape-3 {
            top: 40%;
            left: -20px;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.08);
            transform: rotate(45deg);
            animation: pulse 6s ease-in-out infinite;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.08; transform: rotate(45deg) scale(1); }
            50% { opacity: 0.15; transform: rotate(45deg) scale(1.1); }
        }

        /* --- Content Sections --- */
        .terms-section {
            z-index: 10;
            position: relative;
        }

        .terms-title {
            font-size: 32px;
            font-weight: 700;
            color: #2c5530;
            margin-bottom: 40px;
            text-align: left;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
        }

        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
            counter-reset: item;
        }

        .terms-list li {
            font-size: 24px;
            font-weight: 500;
            color: #2c5530;
            margin-bottom: 20px;
            line-height: 1.4;
            display: flex;
            align-items: flex-start;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.3);
        }

        .terms-list li::before {
            content: counter(item) ". ";
            counter-increment: item;
            font-weight: 700;
            color: #2c5530;
            margin-right: 8px;
            min-width: 30px;
        }

        /* --- Separator Line --- */
        .separator {
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, #2c5530 20%, #2c5530 80%, transparent 100%);
            margin: 30px 0;
            z-index: 10;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* --- Footer Section --- */
        .footer-section {
            text-align: center;
            z-index: 10;
            position: relative;
        }

        .office-name {
            font-size: 28px;
            font-weight: 700;
            color: #2c5530;
            margin-bottom: 8px;
            line-height: 1.3;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
        }

        .office-address {
            font-size: 20px;
            font-weight: 500;
            color: #2c5530;
            opacity: 0.9;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.3);
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

        /* --- Responsive Design --- */
        @media (max-width: 1200px) {
            .card-container {
                transform: scale(0.7);
            }
        }

        @media (max-width: 900px) {
            .card-container {
                transform: scale(0.5);
            }
        }
    </style>
</head>
<?php $orientation = ($orientation ?? 'landscape') === 'portrait' ? 'portrait' : 'landscape'; ?>
<body class="orient-<?= $orientation ?>">
    <div class="upload-section">
        <h3>🎨 Kustomisasi Background Bagian Belakang</h3>
        <?php if (!empty($can_edit_layout)): ?>
            <p style="margin: 0 0 12px; font-size: 13px; color: #555;">Warna background, overlay, dan teks tersimpan otomatis dan dipakai lagi saat halaman dibuka. Gambar background yang diunggah juga tersimpan; memilih warna/preset atau Hapus Background akan menghapus gambar tersimpan.</p>
        <?php endif; ?>
        
        <div class="upload-grid">
            <div class="upload-item">
                <label for="bgImage">📷 Upload Background Image:</label>
                <input type="file" id="bgImage" accept="image/*" onchange="loadBackgroundImage(event)">
                <small style="color: #666; display: block; margin-top: 5px;">Format: JPG, PNG, GIF</small>
            </div>
            
            <div class="upload-item">
                <label>🎨 Atau Pilih Warna Background:</label>
                <input type="color" id="bgColorPicker" value="#f4c430" onchange="changeBackgroundColor(this.value)" style="width: 100%; height: 50px; border: none; border-radius: 8px; cursor: pointer;">
            </div>
        </div>

        <div class="color-section">
            <div class="color-item">
                <label>Warna Overlay:</label>
                <input type="color" id="overlayColor" value="#f4c430" onchange="changeOverlayColor(this.value)">
            </div>
            <div class="color-item">
                <label>Opacity Overlay:</label>
                <input type="range" id="overlayOpacity" min="0" max="100" value="30" onchange="changeOverlayOpacity(this.value)" style="width: 100%;">
                <span id="opacityValue">30%</span>
            </div>
            <div class="color-item">
                <label>Warna Teks:</label>
                <input type="color" id="textColor" value="#2c5530" onchange="changeTextColor(this.value)">
            </div>
        </div>

        <div class="preset-colors">
            <div class="preset-color" style="background: linear-gradient(135deg, #f4c430, #f8c43a);" onclick="applyPresetGradient('linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%)', '#f4c430')" title="Kuning Emas (Default)"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #4CAF50, #2E7D32);" onclick="applyPresetGradient('linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%)', '#4CAF50')" title="Hijau"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #2196F3, #1565C0);" onclick="applyPresetGradient('linear-gradient(135deg, #2196F3 0%, #1565C0 100%)', '#2196F3')" title="Biru"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #FF5722, #D32F2F);" onclick="applyPresetGradient('linear-gradient(135deg, #FF5722 0%, #D32F2F 100%)', '#FF5722')" title="Merah"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #9C27B0, #6A1B9A);" onclick="applyPresetGradient('linear-gradient(135deg, #9C27B0 0%, #6A1B9A 100%)', '#9C27B0')" title="Ungu"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #607D8B, #37474F);" onclick="applyPresetGradient('linear-gradient(135deg, #607D8B 0%, #37474F 100%)', '#607D8B')" title="Abu-abu"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #FF9800, #F57C00);" onclick="applyPresetGradient('linear-gradient(135deg, #FF9800 0%, #F57C00 100%)', '#FF9800')" title="Orange"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #795548, #5D4037);" onclick="applyPresetGradient('linear-gradient(135deg, #795548 0%, #5D4037 100%)', '#795548')" title="Cokelat"></div>
        </div>

        <div style="margin-top: 15px;">
            <button onclick="removeBackground()" style="background: #FF5722; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🗑️ Hapus Background</button>
            <button onclick="resetToDefault()" style="background: #FF9800; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔄 Reset Default</button>
            <button onclick="toggleAnimations()" id="animBtn" style="background: #673AB7; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🎭 Matikan Animasi</button>
        </div>
    </div>

    <div class="controls">
        <div style="margin-bottom: 15px;">
            <strong style="margin-right: 10px;">Orientasi:</strong>
            <button type="button" id="btnLandscape" onclick="setOrientation('landscape')">🖼️ Landscape</button>
            <button type="button" id="btnPortrait" onclick="setOrientation('portrait')">📄 Portrait</button>
        </div>
        <button onclick="printCard()">🖨️ Print sebagai PDF</button>
        <button onclick="downloadAsHTML()" class="download-btn">💾 Download HTML</button>
        <button onclick="generatePDFWithLibrary()" class="download-btn">📄 Generate PDF (Advanced)</button>
    </div>

    <div class="card-container" id="libraryCardBack">
        <!-- Decorative Shapes -->
        <div class="decorative-shape shape-1"></div>
        <div class="decorative-shape shape-2"></div>
        <div class="decorative-shape shape-3"></div>

        <!-- Terms and Conditions Section -->
        <div class="terms-section">
            <h2 class="terms-title">SYARAT DAN KETENTUAN</h2>
            <ol class="terms-list">
                <li>Kartu ini tidak dapat dipindahtangankan</li>
                <li>Berlaku sesuai masa aktif yang tertera</li>
                <li>Wajib membawa kartu saat berkunjung</li>
                <li>Kehilangan kartu harap segera melapor</li>
            </ol>
        </div>

        <!-- Separator Line -->
        <div class="separator"></div>

        <!-- Footer Section -->
        <div class="footer-section">
            <div class="office-name">
               <?= $perpus_name?>
            </div>
            <div class="office-address">
               <?=$lokasi_perpustakaan ?>
            </div>
        </div>
    </div>

    <div id="status"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" integrity="sha384-JcnsjUPPylna1s1fvi1u12X5qjY5OL56iySh75FdtrwhO/SWXgMjoVqcKyIIWOLk" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha384-ZZ1pncU3bQe8y31yfZdMFdSpttDoPmOZg2wguVK9almUodir1PghgT0eY7Mrty8H" crossorigin="anonymous"></script>
    <?= view('Anggota\Views\pdf\_card_capture_js') ?>

    <script>
        let animationsEnabled = true;

        // ---- Warna kartu belakang tersimpan di settingparameters (KartuBelakangWarna) ----
        const DEFAULT_BACK_BACKGROUND = 'linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%)';
        const SAVED_BACK_COLORS = <?= json_encode((object) ($back_colors ?? []), JSON_HEX_TAG | JSON_HEX_AMP) ?>;
        const CAN_SAVE_BACK_COLORS = <?= !empty($can_edit_layout) ? 'true' : 'false' ?>;
        let currentBackground = DEFAULT_BACK_BACKGROUND;
        const SAVED_BACK_IMAGE = <?= json_encode($back_bg_b64 ?? '') ?>;
        let hasSavedBackImage = SAVED_BACK_IMAGE !== '';
        let saveColorsTimer = null;
        let backColorsReady = false; // jangan simpan selama halaman masih memasang nilai awal

        // Pasang warna tersimpan saat halaman dibuka (tanpa memicu simpan ulang)
        function applySavedBackColors() {
            const saved = SAVED_BACK_COLORS;
            if (!saved || !saved.background) return;
            const card = document.getElementById('libraryCardBack');
            card.style.background = saved.background;
            currentBackground = saved.background;
            const firstHex = (/#[0-9a-f]{6}/i.exec(saved.background) || [saved.overlayColor])[0];
            document.getElementById('bgColorPicker').value = firstHex;
            document.getElementById('overlayColor').value = saved.overlayColor;
            document.getElementById('overlayOpacity').value = saved.overlayOpacity;
            document.getElementById('opacityValue').textContent = saved.overlayOpacity + '%';
            document.getElementById('textColor').value = saved.textColor;
            applyTextColor(saved.textColor);
        }

        // Simpan otomatis (ditunda sebentar supaya geser slider tidak mengirim banyak request)
        function scheduleSaveColors() {
            if (!CAN_SAVE_BACK_COLORS || !backColorsReady) return;
            clearTimeout(saveColorsTimer);
            saveColorsTimer = setTimeout(saveBackColors, 700);
        }

        async function saveBackColors() {
            try {
                const response = await fetch('<?= base_url('anggota/savecardbackcolors') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({
                        background: currentBackground,
                        overlayColor: document.getElementById('overlayColor').value,
                        overlayOpacity: Number(document.getElementById('overlayOpacity').value),
                        textColor: document.getElementById('textColor').value,
                        orientation: currentOrientation
                    })
                });
                const result = await response.json().catch(() => ({ success: false, message: 'Respons server tidak valid.' }));
                showStatus(result.success ? '💾 Pengaturan kartu tersimpan' : ('Pengaturan gagal disimpan: ' + (result.message || '')), result.success ? 'success' : 'error');
            } catch (error) {
                showStatus('Warna gagal disimpan: ' + error.message, 'error');
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
        function loadBackgroundImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const cardContainer = document.getElementById('libraryCardBack');
                    cardContainer.style.background = `url('${e.target.result}')`;
                    cardContainer.style.backgroundSize = 'cover';
                    cardContainer.style.backgroundPosition = 'center';
                    cardContainer.style.backgroundRepeat = 'no-repeat';
                    showStatus('✅ Background image berhasil diupload!');
                };
                reader.readAsDataURL(file);
                saveBackImage(file);
            }
        }

        // Simpan gambar background ke server (settingparameters.FileKartuAnggotaBelakang)
        async function saveBackImage(file) {
            if (!CAN_SAVE_BACK_COLORS) return;
            const formData = new FormData();
            formData.append('bgImage', file);
            try {
                const response = await fetch('<?= base_url('anggota/uploadbackbackground') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await response.json().catch(() => ({ success: false, message: 'Respons server tidak valid.' }));
                if (result.success) hasSavedBackImage = true;
                showStatus(result.success ? '💾 Gambar background tersimpan' : ('Gambar gagal disimpan: ' + (result.message || '')), result.success ? 'success' : 'error');
            } catch (error) {
                showStatus('Gambar gagal disimpan: ' + error.message, 'error');
            }
        }

        // Hapus gambar tersimpan (dipanggil saat background diganti warna/preset atau dihapus)
        async function removeSavedBackImage() {
            if (!CAN_SAVE_BACK_COLORS || !hasSavedBackImage) return;
            hasSavedBackImage = false;
            try {
                await fetch('<?= base_url('anggota/removebackbackground') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
            } catch (error) {
                showStatus('Gambar background lama gagal dihapus: ' + error.message, 'error');
            }
        }

        // Background Color Handler
        function changeBackgroundColor(color) {
            const cardContainer = document.getElementById('libraryCardBack');
            cardContainer.style.background = color;
            currentBackground = color;
            removeSavedBackImage();
            document.getElementById('overlayColor').value = color;
            updateOverlay();
            showStatus('✅ Warna background berhasil diubah!');
            scheduleSaveColors();
        }

        // Overlay Color Handler
        function changeOverlayColor(color) {
            updateOverlay();
            showStatus('✅ Warna overlay berhasil diubah!');
            scheduleSaveColors();
        }

        // Overlay Opacity Handler
        function changeOverlayOpacity(value) {
            document.getElementById('opacityValue').textContent = value + '%';
            updateOverlay();
            scheduleSaveColors();
        }

        // Text Color Handler
        function changeTextColor(color) {
            applyTextColor(color);
            showStatus('✅ Warna teks berhasil diubah!');
            scheduleSaveColors();
        }

        function applyTextColor(color) {
            const elements = document.querySelectorAll('.terms-title, .terms-list li, .office-name, .office-address, .separator');
            elements.forEach(element => {
                if (element.classList.contains('separator')) {
                    element.style.background = `linear-gradient(90deg, transparent 0%, ${color} 20%, ${color} 80%, transparent 100%)`;
                } else {
                    element.style.color = color;
                }
            });
        }

        // Update Overlay
        function updateOverlay() {
            const color = document.getElementById('overlayColor').value;
            const opacity = document.getElementById('overlayOpacity').value / 100;
            const cardContainer = document.getElementById('libraryCardBack');
            
            // Convert hex to rgba
            const r = parseInt(color.substr(1, 2), 16);
            const g = parseInt(color.substr(3, 2), 16);
            const b = parseInt(color.substr(5, 2), 16);
            
            cardContainer.style.setProperty('--overlay-color', `rgba(${r}, ${g}, ${b}, ${opacity})`);
        }

        // Apply Preset Gradient
        function applyPresetGradient(gradient, overlayColor) {
            const cardContainer = document.getElementById('libraryCardBack');
            cardContainer.style.background = gradient;
            currentBackground = gradient;
            removeSavedBackImage();
            document.getElementById('overlayColor').value = overlayColor;
            document.getElementById('bgColorPicker').value = overlayColor;
            updateOverlay();
            showStatus('✅ Preset warna berhasil diterapkan!');
            scheduleSaveColors();
        }

        // Remove Background
        function removeBackground() {
            const cardContainer = document.getElementById('libraryCardBack');
            cardContainer.style.background = DEFAULT_BACK_BACKGROUND;
            currentBackground = DEFAULT_BACK_BACKGROUND;
            removeSavedBackImage();
            cardContainer.style.setProperty('--overlay-color', 'rgba(244, 196, 48, 0.3)');
            document.getElementById('overlayColor').value = '#f4c430';
            document.getElementById('bgColorPicker').value = '#f4c430';
            document.getElementById('overlayOpacity').value = 30;
            document.getElementById('opacityValue').textContent = '30%';
            showStatus('✅ Background berhasil dihapus!');
            scheduleSaveColors();
        }

        // Reset to Default
        function resetToDefault() {
            removeBackground();
            
            // Reset text color
            document.getElementById('textColor').value = '#2c5530';
            changeTextColor('#2c5530');
            
            // Clear file input
            document.getElementById('bgImage').value = '';
            
            // Enable animations
            if (!animationsEnabled) {
                toggleAnimations();
            }
            
            showStatus('✅ Kartu bagian belakang berhasil direset ke default!');
        }

        // Toggle Animations
        function toggleAnimations() {
            const shapes = document.querySelectorAll('.decorative-shape');
            const btn = document.getElementById('animBtn');
            
            if (animationsEnabled) {
                shapes.forEach(shape => {
                    shape.style.animation = 'none';
                });
                btn.textContent = '🎭 Nyalakan Animasi';
                animationsEnabled = false;
                showStatus('✅ Animasi dimatikan');
            } else {
                document.querySelector('.shape-1').style.animation = 'float 8s ease-in-out infinite';
                document.querySelector('.shape-2').style.animation = 'rotate 15s linear infinite';
                document.querySelector('.shape-3').style.animation = 'pulse 6s ease-in-out infinite';
                btn.textContent = '🎭 Matikan Animasi';
                animationsEnabled = true;
                showStatus('✅ Animasi dinyalakan');
            }
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
            // Ukuran halaman cetak; tata letak kartu diatur lewat class body.orient-*
            styleEl.textContent = '@page { size: ' + (currentOrientation === 'portrait' ? 'portrait' : 'landscape') + '; margin: 0; }';
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
            scheduleSaveColors();
        }

        // Tangkap kartu belakang persis seperti preview (dipakai PDF dan print)
        async function captureCardCanvas() {
            const element = document.getElementById('libraryCardBack');
            const isPortrait = currentOrientation === 'portrait';
            element.classList.add('pdf-capture');
            await new Promise(resolve => requestAnimationFrame(resolve));
            try {
                const canvas = await CardCapture.capture(element, {
                    scale: 3,
                    width: element.offsetWidth,
                    height: element.offsetHeight,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: 1400,
                    windowHeight: isPortrait ? 1400 : 900
                });
                // Ukuran halaman sama dengan kartu depan (pdf1) dan mengikuti rasio kartu
                const widthMm = isPortrait ? 162 : 254;
                const heightMm = +(widthMm * canvas.height / canvas.width).toFixed(2);
                return { canvas, isPortrait, widthMm, heightMm };
            } finally {
                element.classList.remove('pdf-capture');
            }
        }

        // Print: cetak gambar hasil capture yang sama dengan PDF, supaya warna dan
        // desain identik dengan preview (render print bawaan browser bisa berbeda).
        async function printCard() {
            try {
                showStatus('Menyiapkan kartu untuk dicetak...');
                const { canvas, widthMm, heightMm } = await captureCardCanvas();
                await CardCapture.printImages([canvas.toDataURL('image/png')], widthMm, heightMm);
            } catch (error) {
                showStatus('Error saat menyiapkan print: ' + error.message, 'error');
                console.error('Print error:', error);
            }
        }

        // Download HTML Function
        function downloadAsHTML() {
            try {
                const htmlContent = `<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota Perpustakaan - Bagian Belakang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
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
<body>
    ${document.getElementById('libraryCardBack').outerHTML}
</body>
</html>`;

                const blob = new Blob([htmlContent], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'kartu-anggota-perpustakaan-belakang-custom.html';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                showStatus('File HTML bagian belakang berhasil didownload!');
            } catch (error) {
                showStatus('Error saat mendownload HTML: ' + error.message, 'error');
            }
        }

        // Generate PDF Function
        async function generatePDFWithLibrary() {
            try {
                showStatus('Sedang menggenerate PDF bagian belakang...', 'success');
                const { canvas, isPortrait, widthMm, heightMm } = await captureCardCanvas();
                const pdf = new window.jspdf.jsPDF({
                    orientation: isPortrait ? 'portrait' : 'landscape',
                    unit: 'mm',
                    format: [widthMm, heightMm]
                });
                pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, 0,
                    pdf.internal.pageSize.getWidth(), pdf.internal.pageSize.getHeight(), '', 'FAST');
                pdf.save('kartu-anggota-perpustakaan-belakang-custom.pdf');
                showStatus('PDF bagian belakang berhasil digenerate dan didownload!');
            } catch (error) {
                showStatus('Error saat menggenerate PDF: ' + error.message, 'error');
                console.error('PDF generation error:', error);
            }
        }

        // Initialize
        window.addEventListener('load', function() {
            console.log('Card back with customization loaded successfully');
            applySavedBackColors();
            if (SAVED_BACK_IMAGE) {
                const card = document.getElementById('libraryCardBack');
                card.style.background = `url('${SAVED_BACK_IMAGE}') no-repeat center center / cover`;
            }
            updateOverlay();
            setOrientation(currentOrientation); // Set orientasi awal + tombol aktif
            backColorsReady = true;
        });
    </script>
</body>
</html>