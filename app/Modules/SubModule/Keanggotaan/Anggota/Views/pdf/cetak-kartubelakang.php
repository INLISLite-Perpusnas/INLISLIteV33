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
<body>
    <div class="upload-section">
        <h3>🎨 Kustomisasi Background Bagian Belakang</h3>
        
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        let animationsEnabled = true;

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
            }
        }

        // Background Color Handler
        function changeBackgroundColor(color) {
            const cardContainer = document.getElementById('libraryCardBack');
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

        // Text Color Handler
        function changeTextColor(color) {
            const elements = document.querySelectorAll('.terms-title, .terms-list li, .office-name, .office-address, .separator');
            elements.forEach(element => {
                if (element.classList.contains('separator')) {
                    element.style.background = `linear-gradient(90deg, transparent 0%, ${color} 20%, ${color} 80%, transparent 100%)`;
                } else {
                    element.style.color = color;
                }
            });
            showStatus('✅ Warna teks berhasil diubah!');
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
            document.getElementById('overlayColor').value = overlayColor;
            document.getElementById('bgColorPicker').value = overlayColor;
            updateOverlay();
            showStatus('✅ Preset warna berhasil diterapkan!');
        }

        // Remove Background
        function removeBackground() {
            const cardContainer = document.getElementById('libraryCardBack');
            cardContainer.style.background = 'linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%)';
            cardContainer.style.setProperty('--overlay-color', 'rgba(244, 196, 48, 0.3)');
            document.getElementById('overlayColor').value = '#f4c430';
            document.getElementById('bgColorPicker').value = '#f4c430';
            document.getElementById('overlayOpacity').value = 30;
            document.getElementById('opacityValue').textContent = '30%';
            showStatus('✅ Background berhasil dihapus!');
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
                
                const element = document.getElementById('libraryCardBack');
                
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
                    windowWidth: 1400,
                    windowHeight: 900
                });
                
                controls.style.display = 'block';
                status.style.display = 'block';
                uploadSections.forEach(section => section.style.display = 'block');
                
                const imgData = canvas.toDataURL('image/png', 1.0);
                
                const { jsPDF } = window.jspdf;
                
                const originalWidth = 1004;
                const originalHeight = 618;
                const ratio = originalWidth / originalHeight;
                
                const cardWidthMM = 297;
                const cardHeightMM = Math.round(cardWidthMM / ratio);
                
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: [cardWidthMM, cardHeightMM]
                });
                
                pdf.addImage(imgData, 'PNG', 0, 0, cardWidthMM, cardHeightMM, '', 'FAST');
                
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
            updateOverlay();
        });
    </script>
</body>
</html>