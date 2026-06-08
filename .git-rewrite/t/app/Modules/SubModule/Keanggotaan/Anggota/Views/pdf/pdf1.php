<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota Perpustakaan - PDF Generator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
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
                size: landscape;
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
        }

        .preset-color {
            width: 30px;
            height: 30px;
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            object-fit: cover;
        }
        
        .library-name {
            font-weight: 900;
            color: #4a442a;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
        }
        
        .library-name h1 {
            font-size: 24px;
            margin: 0;
            line-height: 1.2;
            max-width: 400px;
        }
        
        /* --- Main Layout Panels (Flexbox) --- */
        .left-panel, .right-panel {
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
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
        }
        
        /* --- Left Panel Content --- */
        .fullname {
            font-size: 40px;
            font-weight: 900;
            margin-bottom: 25px;
            color: #4a442a;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
        }
        
        .qrcode-placeholder {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
            text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
        }
        
        .expiry-photo-section {
            text-align: center;
        }
        
        .expiry-date {
            font-size: 24px;
            margin-bottom: 15px;
            color: #4a442a;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
        }
        
        .member-photo {
            width: 250px;
            height: 280px;
            border: 8px solid white;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
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
<body>
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
                <input type="color" id="bgColorPicker" value="#ffe061" onchange="changeBackgroundColor(this.value)" style="width: 100%; height: 50px; border: none; border-radius: 8px; cursor: pointer;">
            </div>
        </div>

        <div class="color-section">
            <div class="color-item">
                <label>Warna Overlay:</label>
                <input type="color" id="overlayColor" value="#ffe061" onchange="changeOverlayColor(this.value)">
            </div>
            <div class="color-item">
                <label>Opacity Overlay:</label>
                <input type="range" id="overlayOpacity" min="0" max="100" value="30" onchange="changeOverlayOpacity(this.value)" style="width: 100%;">
                <span id="opacityValue">30%</span>
            </div>
        </div>

        <div class="preset-colors">
            <div class="preset-color" style="background: linear-gradient(135deg, #ffe061, #f4c430);" onclick="applyPresetGradient('linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%)', '#ffe061')" title="Kuning Emas"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #4CAF50, #2E7D32);" onclick="applyPresetGradient('linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%)', '#4CAF50')" title="Hijau"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #2196F3, #1565C0);" onclick="applyPresetGradient('linear-gradient(135deg, #2196F3 0%, #1565C0 100%)', '#2196F3')" title="Biru"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #FF5722, #D32F2F);" onclick="applyPresetGradient('linear-gradient(135deg, #FF5722 0%, #D32F2F 100%)', '#FF5722')" title="Merah"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #9C27B0, #6A1B9A);" onclick="applyPresetGradient('linear-gradient(135deg, #9C27B0 0%, #6A1B9A 100%)', '#9C27B0')" title="Ungu"></div>
            <div class="preset-color" style="background: linear-gradient(135deg, #607D8B, #37474F);" onclick="applyPresetGradient('linear-gradient(135deg, #607D8B 0%, #37474F 100%)', '#607D8B')" title="Abu-abu"></div>
        </div>

        <div style="margin-top: 15px;">
            <button onclick="removeBackground()" style="background: #FF5722; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🗑️ Hapus Background</button>
            <button onclick="resetToDefault()" style="background: #FF9800; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔄 Reset Default</button>
        </div>
    </div>

    <div class="controls">
        <button onclick="printCard()">🖨️ Print sebagai PDF</button>
        <button onclick="downloadAsHTML()" class="download-btn">💾 Download HTML</button>
        <button onclick="generatePDFWithLibrary()" class="download-btn">📄 Generate PDF (Advanced)</button>
    </div>
<!-- Card dengan data PHP (simulasi) -->
   <div class="card-container" id="libraryCard" style="<?= $backgroundStyle ?>">
        <!-- Header dengan Logo dan Nama Perpustakaan -->
        <div class="header-section">
         
            <img style="width: 150px; height: 150px; object-fit: contain; border-radius: 16px; margin-bottom: 20px;" src=" <?php echo $logo_base64; ?> " alt="Logo Perpustakaan" id="logoImage">
            
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
                <!-- Simulasi PHP: <?php echo $qr_image; ?> -->
                <img src=" <?php echo $qr_image; ?>" alt="QR Code" id="qrImage">
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

    <div id="status"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

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

        // Background Image Handler
        // Ganti fungsi lama dengan yang ini
async function handleBackgroundUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    // 1. Tampilkan preview lokal secara instan
    const reader = new FileReader();
    reader.onload = function(e) {
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

    // Jika Anda mengaktifkan proteksi CSRF di CI4, uncomment baris ini
    // formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    try {
        showStatus('Sedang mengupload background...', 'success');
        
        // Gunakan URL yang sudah didaftarkan di routes.php
        const response = await fetch('<?= site_url("anggota/uploadBackground") ?>', {
            method: 'POST',
            body: formData,
        });
        
        const result = await response.json();

        if (result.success) {
            showStatus(result.message, 'success');
        } else {
            const errorMessage = result.errors ? Object.values(result.errors).join(', ') : result.message;
            showStatus(`Error: ${errorMessage}`, 'error');
        }
    } catch (error) {
        showStatus('Terjadi kesalahan koneksi saat mengupload file.', 'error');
        console.error('Upload failed:', error);
    }
}

// Terakhir, ubah atribut onchange di tag input file Anda
// <input type="file" id="bgImage" accept="image/*" onchange="loadBackgroundImage(event)">
// menjadi:
// <input type="file" id="bgImage" accept="image/*" onchange="handleBackgroundUpload(event)">

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
            showStatus('✅ Kartu berhasil direset ke default!');
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
                
                const style = document.createElement('style');
                style.textContent = `
                    @page {
                        size: landscape;
                        margin: 0;
                    }
                    @media print {
                        body {
                            margin: 0 !important;
                            padding: 0 !important;
                            overflow: hidden !important;
                        }
                        .card-container {
                            transform: scale(0.8) !important;
                            transform-origin: center center !important;
                            position: absolute !important;
                            top: 50% !important;
                            left: 50% !important;
                            margin-left: -402px !important;
                            margin-top: -247px !important;
                        }
                    }
                `;
                document.head.appendChild(style);
                
                window.print();
                
                setTimeout(() => {
                    controls.style.display = 'block';
                    status.style.display = 'block';
                    uploadSections.forEach(section => section.style.display = 'block');
                    document.head.removeChild(style);
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
<body>
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
                
                controls.style.display = 'none';
                status.style.display = 'none';
                uploadSections.forEach(section => section.style.display = 'none');
                
                await new Promise(resolve => setTimeout(resolve, 100));
                
                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: null,
                    width: 1004,
                    height: 618,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: 1200,
                    windowHeight: 800
                });
                
                // Restore elements
                controls.style.display = 'block';
                status.style.display = 'block';
                uploadSections.forEach(section => section.style.display = 'block');
                
                const imgData = canvas.toDataURL('image/png', 1.0);
                
                const { jsPDF } = window.jspdf;
                
                const cardWidthMM = 254;
                const cardHeightMM = 157;
                
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: [cardWidthMM, cardHeightMM]
                });
                
                pdf.addImage(imgData, 'PNG', 0, 0, cardWidthMM, cardHeightMM, '', 'FAST');
                
                pdf.save('kartu-anggota-perpustakaan.pdf');
                
                showStatus('PDF berhasil digenerate dan didownload!');
                
            } catch (error) {
                showStatus('Error saat menggenerate PDF: ' + error.message, 'error');
                console.error('PDF generation error:', error);
            }
        }

        // Initialize
        window.addEventListener('load', function() {
            console.log('Card with PHP data and background customization loaded successfully');
            updateOverlay(); // Set initial overlay
        });
    </script>
</body>
</html>