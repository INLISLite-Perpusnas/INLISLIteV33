<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
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
            
            .controls {
                display: none !important;
            }
            
            .a4-container {
                margin: 0 !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                page-break-before: avoid !important;
                page-break-after: avoid !important;
                width: 210mm !important;
                height: 297mm !important;
                transform: none !important;
                position: relative !important;
                top: auto !important;
                left: auto !important;
            }
            
            @page {
                margin: 0;
                size: A4 portrait;
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

        /* --- A4 Container --- */
        .a4-container {
            width: 210mm;
            height: 297mm;
            background-color: white;
            box-shadow: 0 5mm 15mm rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            padding: 15mm 12mm;
            gap: 2mm;
            box-sizing: border-box;
            position: relative;
        }

        /* --- Card Row --- */
        .card-row {
            display: flex;
            gap: 5mm;
            height: 60mm;
            margin-bottom: 2mm;
        }

        /* --- Individual Card Styles --- */
        .card-front, .card-back {
            width: 85mm;
            height: 54mm;
            border-radius: 0;
            overflow: hidden;
            position: relative;
            box-shadow: 0 1mm 3mm rgba(0, 0, 0, 0.1);
        }

        /* Card Front Styles */
        .card-front {
            background: var(--front-bg, linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%));
            background-size: cover;
            background-position: center;
            display: flex;
        }

        .card-front::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--front-overlay, rgba(255, 224, 97, 0.3));
            z-index: 1;
        }

        /* Card Back Styles */
        .card-back {
            background: var(--back-bg, linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%));
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4mm;
            box-sizing: border-box;
        }

        .card-back::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--back-overlay, rgba(244, 196, 48, 0.3));
            z-index: 1;
        }

        /* Front Card Content */
        .front-header {
            position: absolute;
            top: 3mm;
            left: 4mm;
            right: 4mm;
            display: flex;
            align-items: center;
            gap: 3mm;
            z-index: 10;
        }

        .front-logo {
            width: 12mm;
            height: 12mm;
            border-radius: 0;
            object-fit: cover;
            background-color: #4a442a;
        }

        .front-library-name {
            font-weight: 900;
            color: #4a442a;
            font-size: 3.2mm;
            line-height: 1.1;
            text-shadow: 0.3mm 0.3mm 0.7mm rgba(255,255,255,0.8);
        }

        .front-left, .front-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            z-index: 5;
            padding: 4mm;
        }

        .front-left {
            flex-basis: 50%;
        }

        .front-right {
            flex-basis: 50%;
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(2px);
        }

        .front-name {
            font-size: 3mm;
            font-weight: 900;
            color: #4a442a;
            text-align: center;
            margin-bottom: 3mm;
            text-shadow: 0.3mm 0.3mm 0.7mm rgba(255,255,255,0.8);
            line-height: 1.1;
        }

        .front-qr {
            background-color: white;
            padding: 2mm;
            border-radius: 0;
            box-shadow: 0 1mm 2mm rgba(0,0,0,0.1);
        }

        .front-qr img {
            width: 16mm;
            height: 16mm;
            display: block;
        }

        .front-member-type {
            background-color: black;
            color: white;
            margin-top: 5mm;
            font-size: 3.2mm;
            font-weight: 700;
            padding: 2mm 4mm;
            border-radius: 0;
            margin-bottom: 2mm;
        }

        .front-member-no {
            font-size: 3.8mm;
            font-weight: 700;
            color: #4a442a;
            margin-bottom: 3mm;
            text-shadow: 0.3mm 0.3mm 0.7mm rgba(255,255,255,0.8);
        }

        .front-expiry {
            font-size: 2.8mm;
            color: #4a442a;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2mm;
            text-shadow: 0.3mm 0.3mm 0.7mm rgba(255,255,255,0.8);
            line-height: 1.2;
        }

        .front-photo {
            width: 15mm;
            height: 17mm;
            /* border: 2mm solid white; */
            object-fit: cover;
            border-radius: 0;
            box-shadow: 0 1mm 2mm rgba(0,0,0,0.1);
        }

        /* Back Card Content */
        .back-content {
            z-index: 10;
            position: relative;
            padding: 4mm;
        }

        .back-terms-title {
            font-size: 4.5mm;
            font-weight: 700;
            color: #2c5530;
            margin-bottom: 4mm;
            text-align: center;
            text-shadow: 0.3mm 0.3mm 0.7mm rgba(255,255,255,0.5);
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
            color: #2c5530;
            margin-bottom: 2.5mm;
            line-height: 1.3;
            display: flex;
            align-items: flex-start;
            text-shadow: 0.2mm 0.2mm 0.5mm rgba(255,255,255,0.3);
        }

        .back-terms-list li::before {
            content: counter(item) ". ";
            counter-increment: item;
            font-weight: 700;
            color: #2c5530;
            margin-right: 2mm;
            min-width: 4mm;
        }

        .back-separator {
            width: 100%;
            height: 1mm;
            background: linear-gradient(90deg, transparent 0%, #2c5530 20%, #2c5530 80%, transparent 100%);
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
            color: #2c5530;
            margin-bottom: 2mm;
            line-height: 1.2;
            text-shadow: 0.3mm 0.3mm 0.7mm rgba(255,255,255,0.5);
        }

        .back-office-address {
            font-size: 3.2mm;
            font-weight: 500;
            color: #2c5530;
            opacity: 0.9;
            text-shadow: 0.2mm 0.2mm 0.5mm rgba(255,255,255,0.3);
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

        /* Status Messages */
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
        <h3>🎨 Kustomisasi Background Kartu</h3>
        
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
        </div>

        <div style="margin-top: 15px;">
            <button onclick="resetToDefault()" style="background: #FF9800; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔄 Reset Default</button>
            <button onclick="applyPreset('green')" style="background: #4CAF50; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🟢 Hijau</button>
            <button onclick="applyPreset('blue')" style="background: #2196F3; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔵 Biru</button>
            <button onclick="applyPreset('red')" style="background: #F44336; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔴 Merah</button>
        </div>
    </div>

    <div class="controls">
        <button onclick="printCards()">🖨️ Print PDF</button>
        <button onclick="window.close()">❌ Tutup</button>
    </div>

    <?php
    // Split members into pages (2 rows per page, 1 member per row = 2 members per page)
    $cards_per_page = 2;
    $pages = array_chunk($members_data, $cards_per_page);
    
    foreach ($pages as $page_index => $page_data): 
    ?>
        <div class="a4-container" style="<?= $page_index > 0 ? 'page-break-before: always;' : '' ?>">
            <?php foreach ($page_data as $member): ?>
                <div class="card-row">
                    <!-- Card Front -->
                    <div class="card-front">
                        <div class="front-header">
                            <img src="<?= $logo_base64 ?>" alt="Logo" class="front-logo">
                            <div class="front-library-name"><?= strtoupper($perpus_name) ?></div>
                        </div>
                        <div class="front-left">
                            <div class="front-name"><?= strtoupper($member['anggota']->Fullname) ?></div>
                            <div class="front-qr">
                                <img src="<?= $member['qr_image'] ?>" alt="QR Code">
                            </div>
                        </div>
                        <div class="front-right">
                            <div class="front-member-type"><?= strtoupper($member['jenis_anggota_nama']) ?></div>
                            <div class="front-member-no"><?= $member['anggota']->MemberNo ?></div>
                            <!-- <div class="front-expiry">Berlaku Hingga<br><?= $member['end_date'] ?></div> -->
                            <img src="<?= $member['photo_base64'] ?>" alt="Foto" class="front-photo">
                        </div>
                    </div>
                    
                    <!-- Card Back -->
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
                            <div class="back-office-name"><?= $perpus_name ?></div>
                            <div class="back-office-address">Alamat Perpustakaan</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php 
            // Fill empty rows if page has only 1 member
            $empty_rows = 2 - count($page_data);
            for ($i = 0; $i < $empty_rows; $i++): 
            ?>
                <div class="card-row">
                    <!-- Empty slots for alignment -->
                    <div class="card-front" style="opacity: 0.1;"></div>
                    <div class="card-back" style="opacity: 0.1;"></div>
                </div>
            <?php endfor; ?>
        </div>
    <?php endforeach; ?>

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

        // Load Front Background
        function loadFrontBackground(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.documentElement.style.setProperty('--front-bg', `url('${e.target.result}')`);
                    showStatus('✅ Background depan berhasil diupload!');
                };
                reader.readAsDataURL(file);
            }
        }

        // Load Back Background
        function loadBackBackground(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.documentElement.style.setProperty('--back-bg', `url('${e.target.result}')`);
                    showStatus('✅ Background belakang berhasil diupload!');
                };
                reader.readAsDataURL(file);
            }
        }

        // Change Front Color
        function changeFrontColor(color) {
            document.documentElement.style.setProperty('--front-bg', color);
            
            // Convert hex to rgba for overlay
            const r = parseInt(color.substr(1, 2), 16);
            const g = parseInt(color.substr(3, 2), 16);
            const b = parseInt(color.substr(5, 2), 16);
            document.documentElement.style.setProperty('--front-overlay', `rgba(${r}, ${g}, ${b}, 0.3)`);
            
            showStatus('✅ Warna depan berhasil diubah!');
        }

        // Change Back Color
        function changeBackColor(color) {
            document.documentElement.style.setProperty('--back-bg', color);
            
            // Convert hex to rgba for overlay
            const r = parseInt(color.substr(1, 2), 16);
            const g = parseInt(color.substr(3, 2), 16);
            const b = parseInt(color.substr(5, 2), 16);
            document.documentElement.style.setProperty('--back-overlay', `rgba(${r}, ${g}, ${b}, 0.3)`);
            
            showStatus('✅ Warna belakang berhasil diubah!');
        }

        // Change Back Text Color
        function changeBackTextColor(color) {
            document.documentElement.style.setProperty('--back-text-color', color);
            showStatus('✅ Warna teks belakang berhasil diubah!');
        }

        // Apply Preset
        function applyPreset(preset) {
            const presets = {
                green: {
                    front: '#4CAF50',
                    back: '#2E7D32',
                    text: '#1B5E20'
                },
                blue: {
                    front: '#2196F3',
                    back: '#1565C0',
                    text: '#0D47A1'
                },
                red: {
                    front: '#F44336',
                    back: '#D32F2F',
                    text: '#B71C1C'
                }
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

        // Reset to Default
        function resetToDefault() {
            // Reset colors
            document.getElementById('frontColorPicker').value = '#ffe061';
            document.getElementById('backColorPicker').value = '#f4c430';
            document.getElementById('backTextColor').value = '#2c5530';
            
            // Reset CSS variables
            document.documentElement.style.setProperty('--front-bg', 'linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%)');
            document.documentElement.style.setProperty('--back-bg', 'linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%)');
            document.documentElement.style.setProperty('--front-overlay', 'rgba(255, 224, 97, 0.3)');
            document.documentElement.style.setProperty('--back-overlay', 'rgba(244, 196, 48, 0.3)');
            document.documentElement.style.setProperty('--back-text-color', '#2c5530');
            
            // Clear file inputs
            document.getElementById('frontBgImage').value = '';
            document.getElementById('backBgImage').value = '';
            
            showStatus('✅ Semua pengaturan berhasil direset ke default!');
        }

        function printCards() {
            try {
                const controls = document.querySelector('.controls');
                const status = document.querySelector('#status');
                const uploadSections = document.querySelectorAll('.upload-section');
                
                controls.style.display = 'none';
                status.style.display = 'none';
                uploadSections.forEach(section => section.style.display = 'none');
                
                window.print();
                
                setTimeout(() => {
                    controls.style.display = 'block';
                    status.style.display = 'block';
                    uploadSections.forEach(section => section.style.display = 'block');
                }, 1000);
                
            } catch (error) {
                console.error('Error saat print:', error);
                alert('Error saat membuka dialog print: ' + error.message);
            }
        }

        // Initialize CSS variables on page load
        window.addEventListener('load', function() {
            document.documentElement.style.setProperty('--front-bg', 'linear-gradient(135deg, #ffe061 0%, #f4c430 50%, #e0a818 100%)');
            document.documentElement.style.setProperty('--back-bg', 'linear-gradient(135deg, #f4c430 0%, #f8c43a 50%, #e0a818 100%)');
            document.documentElement.style.setProperty('--front-overlay', 'rgba(255, 224, 97, 0.3)');
            document.documentElement.style.setProperty('--back-overlay', 'rgba(244, 196, 48, 0.3)');
            document.documentElement.style.setProperty('--back-text-color', '#2c5530');
        });

        // Auto print when page loads (optional)
        // window.addEventListener('load', function() {
        //     setTimeout(printCards, 1000);
        // });
    </script>
</body>
</html>