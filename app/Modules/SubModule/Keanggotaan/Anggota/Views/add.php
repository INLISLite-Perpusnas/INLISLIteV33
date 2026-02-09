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

      <form id="myform" class="col-md-12 mx-auto" method="post" action="<?= base_url('anggota/create'); ?>" novalidate>
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
  $('#frm_create_NoHp').on('input', function() {
    this.value = this.value.replace(/[^0-9.]/g, ''); 
  });
  $(document).ready(function() {
    getData(`<?= base_url('api/region/province') ?>`, `#Province`, $("#Province")[0].dataset.value ?? "");
    if ($("#Province")[0].dataset.value) {
      if ($("#City")[0].dataset.value) {
        getData(`<?= base_url('api/region/city') ?>/${$("#Province")[0].dataset.value}.`, `#City`, $("#City")[0].dataset.value);
        if ($("#District")[0].dataset.value) {
          getData(`<?= base_url('api/region/district') ?>/${$("#City")[0].dataset.value}`, `#District`, $("#District")[0].dataset.value);
          if ($("#SubDistrict")[0].dataset.value) {
            getData(`<?= base_url('api/region/sub_district') ?>/${$("#District")[0].dataset.value}`, `#SubDistrict`, $("#SubDistrict")[0].dataset.value);
          }
        }
      }
    }
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
    getData(`<?= base_url('api/region/province') ?>`, `#ProvinceNow`, $("#ProvinceNow")[0].dataset.value ?? "");
    if ($("#ProvinceNow")[0].dataset.value) {
      if ($("#CityNow")[0].dataset.value) {
        getData(`<?= base_url('api/region/city') ?>/${$("#ProvinceNow")[0].dataset.value}.`, `#CityNow`, $("#CityNow")[0].dataset.value);
        if ($("#DistrictNow")[0].dataset.value) {
          getData(`<?= base_url('api/region/district') ?>/${$("#CityNow")[0].dataset.value}`, `#DistrictNow`, $("#DistrictNow")[0].dataset.value);
          if ($("#SubDistrictNow")[0].dataset.value) {
            getData(`<?= base_url('api/region/sub_district') ?>/${$("#DistrictNow")[0].dataset.value}`, `#SubDistrictNow`, $("#SubDistrictNow")[0].dataset.value);
          }
        }
      }
    }
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
      console.log(fakultasId)

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
                  `<option value="${jurusan.id}" ${$("#Jurusan_id")[0].dataset.value === jurusan.id ? "selected" : ""}>${jurusan.Nama}</option>`
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

<script>
  $('#package').on('change', function() {
    // --- Logika Lama (Hitung Tanggal) ---
    const date = $('#package option:selected').data('date');
    var today = moment().format('YYYY-MM-DD');
    
    // Pastikan library moment.js sudah di-load, jika error ganti logika date native JS
    var newDate = moment().add(date, 'days').format('YYYY-MM-DD');
    
    // Ambil ID Jenis Anggota
    const id = $(this).val(); // Mengambil value dari select itu sendiri
    
    $('#anggota_id').val(id); // Jika ada hidden input anggota_id
    $('#EndDate').val(newDate);

    // --- Logika Baru (Ambil Default Koleksi & Lokasi) ---
    if(id) {
        $.ajax({
            url: '<?= base_url('anggota/get_defaults') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                // Opsional: Tampilkan loading atau disable input sementara
                $('select[name="CategoryLoan_id[]"]').prop('disabled', true);
                $('select[name="LocationLoan_id[]"]').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    // 1. Update Select2 Koleksi
                    // .val() untuk set nilai, .trigger('change') agar Select2 me-render ulang UI-nya
                    $('select[name="CategoryLoan_id[]"]').val(response.collections).trigger('change');

                    // 2. Update Select2 Lokasi
                    $('select[name="LocationLoan_id[]"]').val(response.locations).trigger('change');
                }
            },
            error: function(xhr, status, error) {
                console.error("Gagal mengambil data default: " + error);
            },
            complete: function() {
                // Aktifkan kembali input
                $('select[name="CategoryLoan_id[]"]').prop('disabled', false);
                $('select[name="LocationLoan_id[]"]').prop('disabled', false);
            }
        });
    } else {
        // Jika user memilih opsi kosong/reset, kosongkan juga select2 nya
        $('select[name="CategoryLoan_id[]"]').val(null).trigger('change');
        $('select[name="LocationLoan_id[]"]').val(null).trigger('change');
    }
  });
</script>
<?= $this->endSection('script'); ?>