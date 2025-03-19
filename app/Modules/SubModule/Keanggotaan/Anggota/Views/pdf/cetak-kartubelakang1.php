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






<div class="container-card " style="background-image: url('<?= $background ?>'); background-repeat: no-repeat; ">

    <div float="left" margin-top="70px" margin-left="100px" font-size="50px" width="900px">

        <h2><?php echo $keterangan ?></h2>
        <br><br>




    </div>
</div>

<?= $this->section('script'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js">
</script>
<script>
    const qrcode = new QRCode("qrcode",
        "<?php echo $anggota->MemberNo ?>");
</script>
<?= $this->endSection('script'); ?>