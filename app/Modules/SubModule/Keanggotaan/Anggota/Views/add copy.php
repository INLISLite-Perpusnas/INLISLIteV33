<?php
$request = service('request');

?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>


<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-id icon-gradient bg-strong-bliss"></i>
                </div>
                <div><?= lang('Anggota.action.update') ?> <?= lang('Anggota.module') ?>
                    <div class="page-title-subheading"><?= lang('Anggota.form.complete_the_data') ?>.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                <?= lang('Anggota.label.home') ?></a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('anggota') ?>"><?= lang('Anggota.module') ?></a>
                        </li>
                        <li class="active breadcrumb-item" aria-current="page"><?= lang('Anggota.action.update') ?>
                            <?= lang('Anggota.module') ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header-tab card-header">
            <div class="card-header-title">
                <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i>
                Tambah Anggota
            </div>
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <?= $this->include("Anggota\Views\section\personal"); ?>

        </div>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<!-- mengambil data provinsi -->



<!-- <script>
$('#Province').on('change', function() {
    // ambil data dari elemen option yang dipilih
    const code = $('#Province option:selected').data('code');
    alert(code);
   

   

    $('[name=no_anggota]').html(no_anggota);
    $('#anggota_id').val(id);
    $('#name').html(name);
    $('#date').html(date);
    $('#jenisanggota_id').val(jenis);
    $('#date2').val(date);
    $('#nomor').html(nomor);
    $('[name=email]').html(email);
    $('[name=address]').html(address);
    //   $('[name=discount]').val(totalDiscount);

    //   $('#total').text(`Rp ${total}`);
});
</script> -->

<!-- end mengambil data provinsi -->
<script type="text/javascript">
    $(function() {
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
    });
</script>

<script>
    $("body").on("submit", "#myform", function() {
        // e.preventDefault();
        let form = $(this).parents('form');
        let submit = form.submit();
        Swal.fire({
            // title: '<?= lang('App.swal.are_you_sure') ?>',
            // showConfirmButton: false,
            text: "Anggota berhasil disimpan",
            type: 'success',
            // showCancelButton: true,
            timer: 6000,
            // confirmButtonColor: '#3085d6',
            // cancelButtonColor: '#dd6b55',
            // confirmButtonText: '<?= lang('App.btn.yes') ?>',
            // cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.value) {
                submit;
            }


        });
        // return false;

    });
</script>

<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });

    $('.select2').select2();
</script>
<script>
    Dropzone.autoDiscover = false;
    var checkboxes = $('#is_similar');

    checkboxes.on('ifChanged', function(event) {
        if (checkboxes.filter(':checked').length == checkboxes.length) {
            $('#AddressNow').val($('#Address').val());
            $('#KecamatanNow').val($('#Kecamatan').val());
            $('#KelurahanNow').val($('#Kelurahan').val());
            $('#RTNow').val($('#RT').val());
            $('#RWNow').val($('#RW').val());
            $('#ProvincyNow').val($('#Provincy').val());
            $('#CityNow').val($('#City').val());

            // $('#KecamatanNow').val($('#Kecamatan').val());
            // $('#ProvincyNow').val($('#Provincy').val());
            // $('#KecamatanNow').val($('#Kecamatan').val());    } else {
            alert('uncheck');
        }
    });

    var file_image = setDropzone('file_image', 'anggota', '.jpg,.jpeg,.png', 1, 10);
</script>

<script>
    $('#package').on('change', function() {
        // ambil data dari elemen option yang dipilih
        const date = $('#package option:selected').data('date');
        // const url= $('#package option:selected').data('url');
        var today = moment().format('YYYY-MM-DD');
        var startdate = '26-12-2020'

        var newDate = moment().add(date, 'days').format('YYYY-MM-DD');
        const id = $('#package option:selected').data('id');
        $('#anggota_id').val(id);
        $('#EndDate').val(newDate);


        //   $('#total').text(`Rp ${total}`);
    });
</script>
<script>
    $(document).ready(function() {
        $('#Province').change(function() {
            // var propinsi_id = $(this).val();
            const code = $('#Province option:selected').data('code');
            alert(code);
            var uriParam = '?propinsi_id=' + propinsi_id;
            getData(`<?= base_url('api/region/city') ?>/${code}.`, `#City`);
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#ProvincyNow').change(function() {
            var propinsi_id = $(this).val();
            var uriParam = '?propinsi_id=' + propinsi_id;
            getDropdown('CityNow', uriParam, 'Pilih', false, false);
        });
    });
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<!-- Kode untuk snapshot dan menampilkan picture -->
<script language="JavaScript">
    // Konfigurasi dan pengaturan kamera
    function configure() {
        Webcam.set({
            width: 320,
            height: 240,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
        Webcam.attach('#my_camera');
    }
    // Tombol untuk menangkap

    // preload shutter audio clip
    var shutter = new Audio();
    shutter.autoplay = false;
    shutter.src = navigator.userAgent.match(/Firefox/) ? 'shutter.ogg' : 'shutter.mp3';

    function take_snapshot() {
        // play sound effect
        shutter.play();

        //  snapshot dan mendapatkan data gambar
        Webcam.snap(function(data_uri) {
            // display results in page
            document.getElementById('results').innerHTML = '<img id="imageprev" src="' + data_uri + '"/>';
            $('#camera_image').val(data_uri);
        });

        Webcam.reset();
    }

    function saveSnap() {
        // Get base64 value from <img id='imageprev'> source
        var base64image = document.getElementById("imageprev").src;

        Webcam.upload(base64image, '<?= base_url('anggota/camera') ?>', function(code, text) {
            console.log('Save successfully');
            //console.log(text);
        });

    }
</script>
<?= $this->endSection('script'); ?>