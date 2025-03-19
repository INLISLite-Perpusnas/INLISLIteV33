<?php
$request = service('request');

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QROutputInterface;

$options = new QROptions([
    'version'      => 5,
    // 'outputType'       => QROutputInterface::FPDF,
    // 'eccLevel'     => EccLevel::L,
    'cssClass'     => 'qrcode',

]);

?>
<style>
    .container {
        /*padding-bottom: 20px;*/
    }

    .container-card {

        width: 1004px;
        height: 618px;
    }

    .image {
        float: left;
        margin-left: 20px;
        margin-top: 200px;
    }

    .image2 {
        float: left;
    }
</style>






<div class="container-card " margin="0" style="background-image: url('<?= $background ?>'); background-repeat: no-repeat; height:100% ">

    <div float="center">
        <br><br><br><br><br>

        <div class="image">
            <img style="margin-left:140%" src="<?php echo $picture ?>" width="150px" height="150px" />





        </div>

        <div style="margin-top: 35%; margin-left:20%">
            <h2><?php echo $anggota->Fullname; ?></h2>
            <h2><?php echo $anggota->MemberNo; ?></h2>
            <h2><?php echo $nama_anggota->jenisanggota; ?></h2>
        </div>
    </div>
    <div>
        <img style="margin-left:20%" src="<?php echo (new QRCode($options))->render($anggota->MemberNo); ?>" alt="QR Code" />
    </div>

    <?= $this->section('script'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js">
    </script>
    <script>
        const qrcode = new QRCode("qrcode",
            "<?php echo $anggota->MemberNo ?>");
    </script>
    <?= $this->endSection('script'); ?>