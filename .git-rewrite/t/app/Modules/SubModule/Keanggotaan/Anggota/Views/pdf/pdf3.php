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






<div class="container-card " style="background-image: url('<?= $background ?> '); background-repeat: no-repeat; ">

    <div float="center">
        <br><br>
        <div class="image">
            <img src="<?php echo $picture ?>" width="150px" height="150px" style="float:left" />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            <h2 style="margin-left: 200px;">Nomor &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; : <?= $anggota->MemberNo ?></h2>
            <h2 style="margin-left: 200px;">Nama &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; : <?php echo $anggota->Fullname; ?> </h2>
            <h2 style="margin-left: 200px;">Jenis Anggota &nbsp; : <?php echo $nama_anggota->jenisanggota; ?> </h2>

        </div>


        <div>
            <h1></h1>
        </div>
    </div><br><br><br><br><br><br><br>
    <img src="<?php echo (new QRCode($options))->render($anggota->MemberNo); ?>" alt="QR Code" />
    <?= $this->section('script'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js">
    </script>
    <script>
        const qrcode = new QRCode("qrcode",
            "<?php echo $anggota->MemberNo ?>");
    </script>
    <?= $this->endSection('script'); ?>