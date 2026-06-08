<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= base_url('assets/vendors'); ?>/pflip/css/pdfflip.css">
</head>

<body>
    <div class="PDFFlip" id="PDFF" source="<?= $file; ?>"></div>
    <script src="<?= base_url('assets/vendors'); ?>/pflip/js/libs/jquery.min.js" type="text/javascript"></script>
    <script src="<?= base_url('assets/vendors'); ?>/pflip/js/pdfflip.js"></script>
    <script>
        var background = '<?= base_url('assets/vendors/pflip/background.jpg'); ?>';
        var option_PDFF = {
            openPage: 1,
            height: '100%',
            enableSound: true,
            downloadEnable: true,
            direction: pdfflip.DIRECTION.LTR, //RTL
            autoPlay: true,
            autoPlayStart: false,
            autoPlayDuration: 3000,
            autoEnableOutline: false,
            autoEnableThumbnail: false,
            /* TRANSLATE INTERFACE */
            text: {
                toggleSound: "Suara",
                toggleThumbnails: "Gambar Kecil",
                toggleOutline: "Isi",
                previousPage: "Sebelumnya",
                nextPage: "Selanjutnya",
                toggleFullscreen: "Layar Penuh",
                zoomIn: "Perbesar",
                zoomOut: "Perkecil",
                downloadPDFFile: "Download PDF",
                gotoFirstPage: "Halaman Awal",
                gotoLastPage: "Halaman Akhir",
                play: "AutoPlay On",
                pause: "AutoPlay Off",
                share: "Bagikan"
            },
            /* ADVANCED SETTINGS */
            hard: "none",
            overwritePDFOutline: true,
            duration: 1000,
            pageMode: pdfflip.PAGE_MODE.DOUBLE,
            singlePageMode: pdfflip.SINGLE_PAGE_MODE.BOOKLET,
            transparent: false,
            scrollWheel: true,
            zoomRatio: 1.5,
            maxTextureSize: 1600,
            // backgroundImage: background,
            backgroundColor: "#000",
            controlsPosition: pdfflip.CONTROLSPOSITION.BOTTOM,
            allControls: "thumbnail,play,altPrev,pageNumber,altNext,zoomIn,zoomOut,fullScreen,download,sound,share",
            hideControls: "outline,startPage,endPage",
        };
    </script>
</body>

</html>