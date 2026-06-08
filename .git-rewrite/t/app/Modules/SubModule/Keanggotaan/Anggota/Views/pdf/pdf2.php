<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota Perpustakaan - Portrait</title>
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
            
            .controls {
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
                background-color: #ffe061 !important;
                width: 618px !important;
                height: 1004px !important;
                transform: scale(0.7);
                transform-origin: center center;
                position: absolute;
                top: 50%;
                left: 50%;
                margin-left: -309px;
                margin-top: -502px;
            }
            
            @page {
                margin: 0;
                size: portrait;
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

        .download-btn {
            background-color: #2196F3 !important;
        }

        .download-btn:hover {
            background-color: #1976D2 !important;
        }

        /* --- Card Container (Portrait) --- */
        .card-container {
            width: 618px;
            height: 1004px;
            background-color: #ffe061;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            min-height: 1004px;
            max-height: 1004px;
            min-width: 618px;
            max-width: 618px;
        }

        /* --- Header Section (Logo & Library Name) --- */
        .header-section {
            padding: 30px 40px 20px 40px;
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            background-color: #4a442a;
            flex-shrink: 0;
        }
        
        .library-name {
            font-weight: 900;
            color: #4a442a;
            flex: 1;
        }
        
        .library-name h1 {
            font-size: 18px;
            margin: 0;
            line-height: 1.3;
        }

        /* --- Main Content Section --- */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 40px;
            gap: 35px;
            align-items: center;
            text-align: center;
        }
        
        /* --- Name Section --- */
        .name-section {
            text-align: center;
        }

        .fullname {
            font-size: 32px;
            font-weight: 900;
            color: #4a442a;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 0;
        }

        /* --- Member Type Section --- */
        .member-type-section {
            display: flex;
            justify-content: center;
        }

        .member-type {
            background-color: black;
            color: white;
            font-size: 24px;
            font-weight: 700;
            padding: 12px 40px;
            border-radius: 50px;
            text-align: center;
        }

        /* --- Member Number Section --- */
        .member-no-section {
            display: flex;
            justify-content: center;
        }

        .member-no {
            font-size: 28px;
            font-weight: 900;
            color: #4a442a;
            text-align: center;
        }

        /* --- QR Code Section --- */
        .qrcode-section {
            display: flex;
            justify-content: center;
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

        /* --- Expiry Date Section --- */
        .expiry-section {
            display: flex;
            justify-content: center;
        }
        
        .expiry-date {
            font-size: 20px;
            color: #4a442a;
            font-weight: 700;
            text-align: center;
        }

        /* --- Photo Section --- */
        .photo-section {
            display: flex;
            justify-content: center;
        }
        
        .member-photo {
            width: 220px;
            height: 260px;
            border: 8px solid white;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        /* --- Decorative Elements --- */
        .shape-decoration {
            position: absolute;
            z-index: 1;
            opacity: 0.1;
        }
        
        .shape-1 {
            top: 15%;
            left: -5%;
            width: 120px;
            height: 120px;
            background-color: #4a442a;
            border-radius: 50%;
        }
        
        .shape-2 {
            bottom: 10%;
            right: -5%;
            width: 150px;
            height: 150px;
            background-color: #e0a818;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
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
        @media (max-width: 768px) {
            .card-container {
                transform: scale(0.6);
            }
        }

        @media (max-width: 480px) {
            .card-container {
                transform: scale(0.4);
            }
        }
    </style>
</head>
<body>
    <div class="controls">
        <button onclick="printCard()">🖨️ Print sebagai PDF</button>
        <button onclick="downloadAsHTML()" class="download-btn">💾 Download HTML</button>
        <button onclick="generatePDFWithLibrary()" class="download-btn">📄 Generate PDF (Advanced)</button>
        <button onclick="generateSimplePDF()" style="background-color: #FF5722;">🎯 Generate PDF (Simple)</button>
    </div>

    <div class="card-container" id="libraryCardPortrait">
        <!-- Shape decorations -->
        <div class="shape-decoration shape-1"></div>
        <div class="shape-decoration shape-2"></div>

        <!-- Header dengan Logo dan Nama Perpustakaan -->
        <div class="header-section">
            <div class="logo"></div>
            <div class="library-name">
                <h1>DINAS PERPUSTAKAAN DAN KEARSIPAN DAERAH PROVINSI JAWA BARAT</h1>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Name Section -->
            <div class="name-section">
                <div class="fullname">
                    SITI NURHALIZA BINTI ABDULLAH
                </div>
            </div>

            <!-- Member Type Section -->
            <div class="member-type-section">
                <div class="member-type">
                    PELAJAR
                </div>
            </div>

            <!-- Member Number Section -->
            <div class="member-no-section">
                <div class="member-no">
                    S002345678
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="qrcode-section">
                <div class="qrcode-placeholder">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=S002345678" alt="QR Code">
                </div>
            </div>

            <!-- Expiry Date Section -->
            <div class="expiry-section">
                <div class="expiry-date">
                    Berlaku Hingga<br>
                    30 Juni 2026
                </div>
            </div>

            <!-- Photo Section -->
            <div class="photo-section">
                <img src="https://placehold.co/220x260/ffcccc/ff6666?text=FOTO" alt="Foto Anggota" class="member-photo">
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

        // Metode 1: Print menggunakan browser
        function printCard() {
            try {
                const controls = document.querySelector('.controls');
                const status = document.querySelector('#status');
                
                controls.style.display = 'none';
                status.style.display = 'none';
                
                const style = document.createElement('style');
                style.textContent = `
                    @page {
                        size: portrait;
                        margin: 0;
                    }
                    @media print {
                        body {
                            margin: 0 !important;
                            padding: 0 !important;
                            overflow: hidden !important;
                        }
                        .card-container {
                            transform: scale(0.7) !important;
                            transform-origin: center center !important;
                            position: absolute !important;
                            top: 50% !important;
                            left: 50% !important;
                            margin-left: -309px !important;
                            margin-top: -502px !important;
                        }
                    }
                `;
                document.head.appendChild(style);
                
                window.print();
                
                setTimeout(() => {
                    controls.style.display = 'block';
                    status.style.display = 'block';
                    document.head.removeChild(style);
                }, 1000);
                
                showStatus('Dialog print telah dibuka. Pilih "Save as PDF" sebagai printer dan pastikan orientasi portrait.');
            } catch (error) {
                showStatus('Error saat membuka dialog print: ' + error.message, 'error');
            }
        }

        // Metode 2: Download sebagai file HTML
        function downloadAsHTML() {
            try {
                const htmlContent = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota Perpustakaan - Portrait</title>
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
        
        .controls { display: none; }
        .status { display: none; }
    </style>
</head>
<body>
    ${document.getElementById('libraryCardPortrait').outerHTML}
</body>
</html>`;

                const blob = new Blob([htmlContent], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'kartu-anggota-perpustakaan-portrait.html';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                showStatus('File HTML portrait berhasil didownload!');
            } catch (error) {
                showStatus('Error saat mendownload HTML: ' + error.message, 'error');
            }
        }

        // Metode 3: Generate PDF menggunakan jsPDF dan html2canvas
        async function generatePDFWithLibrary() {
            try {
                showStatus('Sedang menggenerate PDF portrait...', 'success');
                
                const element = document.getElementById('libraryCardPortrait');
                
                const controls = document.querySelector('.controls');
                const status = document.querySelector('#status');
                const originalControlsDisplay = controls.style.display;
                const originalStatusDisplay = status.style.display;
                
                controls.style.display = 'none';
                status.style.display = 'none';
                
                await new Promise(resolve => setTimeout(resolve, 100));
                
                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: null,
                    width: 618,
                    height: 1004,
                    scrollX: 0,
                    scrollY: 0,
                    x: 0,
                    y: 0,
                    windowWidth: 1000,
                    windowHeight: 1200,
                    removeContainer: false,
                    foreignObjectRendering: false,
                    imageTimeout: 5000,
                    logging: false
                });
                
                controls.style.display = originalControlsDisplay;
                status.style.display = originalStatusDisplay;
                
                const imgData = canvas.toDataURL('image/png', 1.0);
                
                const { jsPDF } = window.jspdf;
                
                const originalWidth = 618;
                const originalHeight = 1004;
                const ratio = originalWidth / originalHeight;
                
                const cardHeightMM = 297; // A4 portrait height
                const cardWidthMM = Math.round(cardHeightMM * ratio);
                
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [cardWidthMM, cardHeightMM]
                });
                
                pdf.addImage(imgData, 'PNG', 0, 0, cardWidthMM, cardHeightMM, '', 'FAST');
                
                pdf.save('kartu-anggota-perpustakaan-portrait.pdf');
                
                showStatus('PDF portrait berhasil digenerate dan didownload!');
                
            } catch (error) {
                showStatus('Error saat menggenerate PDF: ' + error.message, 'error');
                console.error('PDF generation error:', error);
            }
        }

        // Metode 4: Generate PDF Simple
        function generateSimplePDF() {
            try {
                showStatus('Sedang menggenerate PDF simple portrait...', 'success');
                
                const { jsPDF } = window.jspdf;
                
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });
                
                const pageWidth = 210;
                const pageHeight = 297;
                
                // Background
                pdf.setFillColor(255, 224, 97); // #ffe061
                pdf.rect(0, 0, pageWidth, pageHeight, 'F');
                
                // Header background
                pdf.setFillColor(255, 255, 255, 0.1);
                pdf.rect(0, 0, pageWidth, 50, 'F');
                
                // Logo placeholder
                pdf.setFillColor(74, 68, 42); // #4a442a
                pdf.circle(30, 25, 15, 'F');
                
                // Library name
                pdf.setFont('helvetica', 'bold');
                pdf.setFontSize(12);
                pdf.setTextColor(74, 68, 42);
                const libraryText = ['DINAS PERPUSTAKAAN DAN', 'KEARSIPAN DAERAH PROVINSI', 'JAWA BARAT'];
                libraryText.forEach((line, index) => {
                    pdf.text(line, 50, 20 + (index * 6));
                });
                
                // Name - centered
                pdf.setFontSize(18);
                pdf.setFont('helvetica', 'bold');
                pdf.text('SITI NURHALIZA BINTI', pageWidth/2, 80, { align: 'center' });
                pdf.text('ABDULLAH', pageWidth/2, 95, { align: 'center' });
                
                // Member type - centered
                pdf.setFillColor(0, 0, 0);
                pdf.roundedRect(pageWidth/2 - 25, 105, 50, 15, 7, 7, 'F');
                pdf.setTextColor(255, 255, 255);
                pdf.setFontSize(12);
                pdf.text('PELAJAR', pageWidth/2, 115, { align: 'center' });
                
                // Member number - centered
                pdf.setTextColor(74, 68, 42);
                pdf.setFontSize(16);
                pdf.setFont('helvetica', 'bold');
                pdf.text('S002345678', pageWidth/2, 140, { align: 'center' });
                
                // QR Code placeholder - centered
                pdf.setFillColor(255, 255, 255);
                const qrSize = 45;
                pdf.rect(pageWidth/2 - qrSize/2, 150, qrSize, qrSize, 'F');
                pdf.setTextColor(0, 0, 0);
                pdf.setFontSize(8);
                pdf.text('QR CODE', pageWidth/2, 175, { align: 'center' });
                
                // Expiry date - centered
                pdf.setTextColor(74, 68, 42);
                pdf.setFontSize(12);
                pdf.setFont('helvetica', 'bold');
                pdf.text('Berlaku Hingga', pageWidth/2, 215, { align: 'center' });
                pdf.text('30 Juni 2026', pageWidth/2, 225, { align: 'center' });
                
                // Photo placeholder - centered
                pdf.setFillColor(255, 204, 204);
                const photoWidth = 44;
                const photoHeight = 52;
                pdf.rect(pageWidth/2 - photoWidth/2, 235, photoWidth, photoHeight, 'F');
                pdf.setTextColor(255, 102, 102);
                pdf.setFontSize(10);
                pdf.text('FOTO', pageWidth/2, 263, { align: 'center' });
                
                pdf.save('kartu-anggota-perpustakaan-portrait-simple.pdf');
                
                showStatus('PDF simple portrait berhasil digenerate dan didownload!');
                
            } catch (error) {
                showStatus('Error saat menggenerate PDF simple: ' + error.message, 'error');
                console.error('PDF simple generation error:', error);
            }
        }

        window.addEventListener('load', function() {
            console.log('Portrait card loaded successfully');
        });
    </script>
</body>
</html>