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

            <form id="myform" class="col-md-12 mx-auto" method="post" action="<?= base_url('anggota/create'); ?>">
                <?= csrf_field() ?>
          
                <!-- info personal -->
                <?= $this->include("Anggota\Views\section\component_add\info_personal"); ?>

                <!-- info anggota -->
                <?= $this->include("Anggota\Views\section\component_add\info_anggota"); ?>

                <!-- info alamat -->
                <?= $this->include("Anggota\Views\section\component_add\info_alamat"); ?>

                <!-- info tambahan -->
                <?= $this->include("Anggota\Views\section\component_add\info_tambahan"); ?>

                <!-- upload foto -->
                <?= $this->include("Anggota\Views\section\component_add\upload_foto"); ?>

                <div class="form-group mt-1">
                    <button type="submit" class="btn btn-lg btn-primary" id="btn-submit" name="submit">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

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
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });

    $('.select2').select2();
</script>
<!-- end scropt mengambil select2 -->
<script>
    Dropzone.autoDiscover = false;
    var file_image = setDropzone('file_image', 'anggota', '.jpg,.jpeg,.png', 1, 10);
</script>

<script>
    $('#package').on('change', function() {
        const date = $('#package option:selected').data('date');
        var today = moment().format('YYYY-MM-DD');
        var startdate = '26-12-2020'

        var newDate = moment().add(date, 'days').format('YYYY-MM-DD');
        const id = $('#package option:selected').data('id');
        $('#anggota_id').val(id);
        $('#EndDate').val(newDate);
    });
</script>
<script>
    $(document).ready(function() {
        getData(`<?= base_url('api/region/province') ?>`, `#Province`);
        $('#Province').change(function(e) {
            var code = $(this).val();
            getData(`<?= base_url('api/region/city') ?>/${code}.`, `#City`);
        });
        $('#City').change(function(e) {
            var code = $(this).val();
            getData(`<?= base_url('api/region/district') ?>/${code}`, `#District`);
        });
        $('#District').change(function(e) {
            var code = $(this).val();
            getData(`<?= base_url('api/region/sub_district') ?>/${code}`, `#SubDistrict`);
        });
    });

    $(document).ready(function() {
        getData(`<?= base_url('api/region/province') ?>`, `#ProvinceNow`);
        $('#ProvinceNow').change(function(e) {
            var code = $(this).val();
            getData(`<?= base_url('api/region/city') ?>/${code}.`, `#CityNow`, $('#City').val());
        });
        $('#CityNow').change(function(e) {
            var code = $(this).val();
            getData(`<?= base_url('api/region/district') ?>/${code}`, `#DistrictNow`, $('#District').val());
        });
        $('#DistrictNow').change(function(e) {
            var code = $(this).val();
            getData(`<?= base_url('api/region/sub_district') ?>/${code}`, `#SubDistrictNow`, $('#SubDistrict').val());
        });
    });

    $(document).ready(function() {
        $("#is_similar").click(function() {
            if ($(this).is(":checked")) {
                $('#AddressNow').val($('#Address').val());
                cloneOptions('#Province', '#ProvinceNow', $('#Province').val());
                cloneOptions('#City', '#CityNow', $('#City').val());
                cloneOptions('#District', '#DistrictNow', $('#District').val());
                cloneOptions('#SubDistrict', '#SubDistrictNow', $('#SubDistrict').val());
                $('#RTNow').val($('#RT').val());
                $('#RWNow').val($('#RW').val());
            } else {
                $('#AddressNow').val('');
                $('#ProvinceNow, #CityNow, #DistrictNow, #SubDistrictNow').empty();
                $('#RTNow, #RWNow').val('');
            }
        });
    });

    function cloneOptions(sourceSelector, targetSelector, selectedValue) {
        var sourceOptions = $(sourceSelector + ' option').clone();
        var targetSelect = $(targetSelector);
        targetSelect.empty();
        targetSelect.append(sourceOptions);
        targetSelect.val(selectedValue).trigger('change');
    }

    // Script untuk Fakultas dan Jurusan
    $(document).ready(function() {
        // Ketika Fakultas dipilih, load Jurusan yang sesuai
        $('#Fakultas_id').change(function() {
            var fakultasId = $(this).val();
            var jurusanSelect = $('#Jurusan_id');
            
            // Reset jurusan select
            jurusanSelect.empty();
            jurusanSelect.append('<option value="">Pilih Jurusan</option>');
            
            if (fakultasId) {
                // Ambil data jurusan berdasarkan fakultas_id
                $.ajax({
                    url: `<?= base_url('api/jurusan/getjurusan') ?>/${fakultasId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            $.each(response.data, function(key, jurusan) {
                                jurusanSelect.append(
                                    `<option value="${jurusan.id}">${jurusan.Nama}</option>`
                                );
                            });
                        }
                    },
                    error: function() {
                        console.log('Error loading jurusan data');
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection('script'); ?>