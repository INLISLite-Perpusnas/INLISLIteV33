<?php

$request = service('request');

$db = db_connect($DBGroup = 'data');
$builder = $db->table('members')
    ->select('members.ID, Fullname, MemberNo, NoHp, Email, JenisAnggota_id, EndDate, Address, Fakultas_id, Jurusan_id')
    ->select('jenis_anggota.jenisanggota as JenisAnggota_label')
    ->select('master_fakultas.Nama as Fakultas_label')
    ->select('master_jurusan.Nama as Jurusan_label')
    ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
    ->join('master_fakultas', 'master_fakultas.id = members.Fakultas_id', 'left')
    ->join('master_jurusan', 'master_jurusan.id = members.Jurusan_id', 'left');
$anggotas = $builder->where('members.Branch_id', branch_id())->get()->getResult();

$fakultas = get_ref_table('master_fakultas', 'id,Nama', null, 'data');
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
                <div>Anggota
                    <div class="page-title-subheading">Perpanjangan semua Anggota
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i
                                    class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Keanggotaan</li>
                        <li class="breadcrumb-item active">Perpanjangan Anggota</li>
                    </ol>
                </nav>
            </div>
        </div>

    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form
            Perpanjangan Anggota
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <form id="frm_create" class="col-md-12 mx-auto" method="post"
                action="<?= base_url('perpanjangan-anggota/create'); ?>">
                <div class="form-row">



                </div>

                <div class="form-row">
                    <div class="col-md-4">
                        <div id="section_single_profile">
                            <?= $this->include('PerpanjanganAnggota\Views\section\member_profile') ?>
                        </div>
                        <div id="section_multiple_summary" style="display:none;">
                            <div class="card-shadow-dark card-border mb-3 card">
                                <div class="card-header bg-corporate-primary2 text-white">
                                    <i class="header-icon lnr-users icon-gradient bg-plum-plate"> </i>
                                    Ringkasan Pilihan (<span id="summary_count">0</span>)
                                </div>
                                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush" id="summary_list">
                                        <!-- Selected members will be listed here -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <strong><label for="sort">Anggota*</label></strong>
                        <div class="mb-2 p-2 border rounded bg-light">
                            <small class="text-muted d-block mb-1">Pilih Berdasarkan Filter:</small>
                            <div class="form-row">
                                <div class="col-md-5">
                                    <select class="form-control form-control-sm" id="filter_fakultas">
                                        <option value="">Fakultas</option>
                                        <?php foreach ($fakultas as $row): ?>
                                            <option value="<?= $row->id ?>"><?= $row->Nama ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control form-control-sm" id="filter_jurusan">
                                        <option value="">Jurusan</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-primary btn-block" id="btn-apply-filter" title="Pilih Semua yang Cocok">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-info" id="btn-select-expired">
                                <i class="fa fa-clock"></i> Pilih Semua Anggota Expired
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="btn-clear-selection">
                                <i class="fa fa-times"></i> Clear Semua Pilihan
                            </button>
                        </div>
                        <div class="-group ">

                            <select class="custom-select js-example-basic-multiple" id="package"
                                name="Member_id[]" multiple="multiple">
                                <?php foreach ($anggotas as $row): ?>
                                    <?php 
                                        $is_expired = false;
                                        if ($row->EndDate) {
                                            $today = date('Y-m-d');
                                            $is_expired = ($row->EndDate < $today);
                                        }
                                    ?>
                                    <option data-no_anggota="<?= _spec($row->MemberNo); ?>" data-name="<?= $row->Fullname ?>"
                                        data-email="<?= _spec($row->Email); ?>" data-address="<?= $row->Address ?>"
                                        data-date="<?= $row->EndDate ?>" data-id="<?= $row->ID ?>"
                                        data-nomor="<?= $row->NoHp ?>" data-jenis="<?= $row->JenisAnggota_id ?>"
                                        data-fakultas="<?= $row->Fakultas_id ?>" data-jurusan="<?= $row->Jurusan_id ?>"
                                        data-jenis_label="<?= $row->JenisAnggota_label ?>"
                                        data-fakultas_label="<?= $row->Fakultas_label ?>"
                                        data-jurusan_label="<?= $row->Jurusan_label ?>"
                                        data-expired="<?= $is_expired ? '1' : '0' ?>"
                                        value="<?= $row->ID ?>">
                                        <?= $row->MemberNo ?>-<?= $row->Fullname ?> <?= $is_expired ? '(Expired)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>


                        <div>
                            <strong> <label for="name">Update Tanggal berahir</label></strong>
                            <div>
                                <input type="text" class="form-control datepicker" id="date2" name="EndDate"
                                    placeholder="Tanggal Berahir " value="<?= set_value('EndDate') ?>" />
                            </div>
                        </div>

                        <div>
                            <strong> <label for="name">Update Jenis Anggota</label></strong>
                            <select class="form-control" name="Jenisanggota_id" id="jenisanggota_id" tabindex="-1"
                                aria-hidden="true">
                                <option id="jenisanggota_id" value="" disabled selected>
                                    <?= set_value('JenisAnggota_id') ?>
                                </option>
                                <?php foreach (get_ref_table('jenis_anggota', 'id, jenisanggota', null, 'data') as $row): ?>
                                    <option value="<?= $row->id ?>" <?= ($row->id == set_value('JenisAnggota_id')) ? 'selected' : '' ?>>
                                        <?= $row->jenisanggota ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="section_student_data" style="display:none;" class="p-2 border rounded mt-2 bg-light">
                            <strong><small>Data Mahasiswa</small></strong>
                            <div class="form-group mb-1">
                                <label class="small mb-0">Fakultas</label>
                                <select class="form-control form-control-sm" name="Fakultas_id" id="update_fakultas">
                                    <option value="">Pilih Fakultas</option>
                                    <?php foreach ($fakultas as $row): ?>
                                        <option value="<?= $row->id ?>"><?= $row->Nama ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small mb-0">Jurusan</label>
                                <select class="form-control form-control-sm" name="Jurusan_id" id="update_jurusan">
                                    <option value="">Pilih Jurusan</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <strong> <label for="sort">Biaya</label></strong>
                            <div>
                                <input type="number" class="form-control" id="frm_create_sort" name="biaya"
                                    value="<?= set_value('biaya') ?>" />

                            </div>
                        </div>

                        <!-- <div>
                          <strong>  <label for="sort">Tes</label></strong>
                            <div>
                            <input placeholder="masukkan tanggal Akhir" id="date2" type="text" class="form-control datepicker" name="tgl_akhir">

                            </div>
                        </div> -->

                        <div class="form-check form-group mt-1">
                            <div>
                                <input type="hidden" class="iCheck-square" name="is_lunas" id="is_lunas" value="0">
                                <input type="checkbox" class="iCheck-square" name="is_lunas" id="is_lunas" value="1">
                                <label class="  control-label">Sudah Lunas</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Keterangan </label>
                            <div>
                                <textarea id="frm_create_description" name="Keterangan" rows="2"
                                    class="form-control autosize-input"
                                    style="min-height: 38px;"><?= set_value('Keterangan') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<script type="text/javascript">
    var _confirmed = false;

    $(function () {
        $('#frm_create').on('submit', function(e) {
            if (_confirmed) {
                _confirmed = false;
                return true;
            }

            e.preventDefault();

            var members = $('#package').val();
            var endDate = $('[name=EndDate]').val();

            if (!members || members.length === 0) {
                Swal.fire('Peringatan', 'Silakan pilih minimal satu anggota', 'warning');
                return false;
            }

            if (!endDate) {
                Swal.fire('Peringatan', 'Silakan isi Tanggal Berakhir', 'warning');
                return false;
            }

            var jumlah = members.length;
            var confirmText = jumlah > 1
                ? 'Anda akan memperpanjang ' + jumlah + ' anggota sekaligus. Lanjutkan?'
                : 'Anda akan memperpanjang 1 anggota. Lanjutkan?';

            // SweetAlert v8: gunakan "type" bukan "icon", dan cek result.value bukan result.value
            Swal.fire({
                title: 'Konfirmasi Perpanjangan',
                text: confirmText,
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                cancelButtonColor: '#d33',
            }).then(function(result) {
                if (result.value) {
                    _confirmed = true;
                    $('#frm_create').submit();
                }
            });
        });

        $('[name=submit]').on('click', function() {
            console.log('[DEBUG] Tombol Simpan diklik');
        });
    });

    function formatDateIndo(dateStr) {
        if (!dateStr || dateStr === '-') return '-';
        var months = ['Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'];
        var date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
    }

    $(function () {
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });
    });
</script>
<script>
    $('#package').on('change', function () {
        const selectedOptions = $('#package option:selected');
        const summaryList = $('#summary_list');
        const summaryCount = $('#summary_count');
        
        if (selectedOptions.length === 1) {
            $('#section_single_profile').show();
            $('#section_multiple_summary').hide();

            const opt = $(selectedOptions[0]);
            const no_anggota = opt.data('no_anggota');
            const name = opt.data('name');
            const email = opt.data('email');
            const address = opt.data('address');
            const nomor = opt.data('nomor');
            const date = opt.data('date');
            const id = opt.data('id');
            const jenis = opt.data('jenis');

            $('[name=no_anggota]').html(no_anggota);
            $('#name').html(name);
            $('#date').html(formatDateIndo(date));
            $('#jenisanggota_id').val(jenis);
            $('#date2').val(date);
            $('#nomor').html(nomor);
            $('[name=email]').html(email);
            $('[name=address]').html(address);
        } else if (selectedOptions.length > 1) {
            $('#section_single_profile').hide();
            $('#section_multiple_summary').show();
            
            summaryCount.text(selectedOptions.length);
            let html = '';
            selectedOptions.each(function() {
                const opt = $(this);
                const name = opt.data('name');
                const no = opt.data('no_anggota');
                const date = opt.data('date') || '-';
                const jenis = opt.data('jenis_label') || '-';
                const fakultas = opt.data('fakultas_label');
                const jurusan = opt.data('jurusan_label');
                
                let detail = `<span class="badge badge-info mr-1">${jenis}</span>`;
                detail += `<span class="badge badge-secondary mr-1">End : ${formatDateIndo(date)}</span>`;
                if (fakultas) detail += `<small class="d-block text-muted">${fakultas} ${jurusan ? '/ ' + jurusan : ''}</small>`;

                html += `<li class="list-group-item p-2">
                    <div class="widget-content p-0">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left flex2">
                                <div class="widget-heading" style="font-size: 0.85rem; font-weight: bold;">${name}</div>
                                <div class="widget-subheading opacity-7" style="font-size: 0.75rem;">
                                    ${no} <br>
                                    ${detail}
                                </div>
                            </div>
                        </div>
                    </div>
                </li>`;
            });
            summaryList.html(html);
        } else {
            $('#section_single_profile').show();
            $('#section_multiple_summary').hide();
            
            $('[name=no_anggota]').html('');
            $('#name').html('');
            $('#date').html('');
            $('#nomor').html('');
            $('[name=email]').html('');
            $('[name=address]').html('');
        }
    });

    function resetStudentFields() {
        $('#update_fakultas').val('').trigger('change');
        $('#update_jurusan').html('<option value="">Pilih Jurusan</option>');
    }

    function resetRenewalDetails() {
        $('#jenisanggota_id').val('').trigger('change');
        resetStudentFields();
    }

    $('#btn-select-expired').on('click', function() {
        resetRenewalDetails();
        const expiredIds = [];
        $('#package option').each(function() {
            if ($(this).data('expired') == '1') {
                expiredIds.push($(this).val());
            }
        });
        $('#package').val(expiredIds).trigger('change');
    });

    $('#btn-clear-selection').on('click', function() {
        resetRenewalDetails();
        $('#package').val([]).trigger('change');
    });

    // Handle Faculty/Department filtering
    $('#filter_fakultas, #update_fakultas').on('change', function() {
        const fakultas_id = $(this).val();
        const target_jurusan = ($(this).attr('id') === 'filter_fakultas') ? $('#filter_jurusan') : $('#update_jurusan');
        
        target_jurusan.html('<option value="">Loading...</option>');
        
        if (fakultas_id) {
            $.ajax({
                url: `<?= base_url('api/jurusan/getjurusan') ?>/${fakultas_id}`,
                type: 'GET',
                success: function(res) {
                    let html = '<option value="">Pilih Jurusan</option>';
                    if (res.data) {
                        res.data.forEach(function(item) {
                            html += `<option value="${item.id}">${item.Nama}</option>`;
                        });
                    }
                    target_jurusan.html(html);
                }
            });
        } else {
            target_jurusan.html('<option value="">Jurusan</option>');
        }
    });

    $('#btn-apply-filter').on('click', function() {
        const f_id = $('#filter_fakultas').val();
        const j_id = $('#filter_jurusan').val();
        const selectedIds = [];

        if (!f_id && !j_id) {
            Swal.fire('Info', 'Pilih Fakultas atau Jurusan terlebih dahulu', 'info');
            return;
        }

        // Clear previous selection and reset renewal details
        $('#package').val([]).trigger('change');
        resetRenewalDetails();

        $('#package option').each(function() {
            const opt_f = $(this).data('fakultas');
            const opt_j = $(this).data('jurusan');
            
            let match = true;
            if (f_id && opt_f != f_id) match = false;
            if (j_id && opt_j != j_id) match = false;

            if (match) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            $('#package').val(selectedIds).trigger('change');

            // Auto-Sync: Set Jenis Anggota to Mahasiswa and sync Faculty/Dept
            if (f_id) {
                $('#jenisanggota_id').val('12').trigger('change');
                $('#update_fakultas').val(f_id).trigger('change');
                
                // Wait for jurusan to be loaded via AJAX before setting value
                if (j_id) {
                    const checkInterval = setInterval(function() {
                        if ($('#update_jurusan option[value="' + j_id + '"]').length > 0) {
                            $('#update_jurusan').val(j_id);
                            clearInterval(checkInterval);
                        }
                    }, 100);
                    // Timeout after 3 seconds just in case
                    setTimeout(() => clearInterval(checkInterval), 3000);
                }
            }
        } else {
            Swal.fire('Info', 'Tidak ada anggota yang cocok dengan filter tersebut', 'info');
        }
    });

    // Conditional update fields for Mahasiswa
    $('#jenisanggota_id').on('change', function() {
        if ($(this).val() == '12') {
            $('#section_student_data').slideDown();
        } else {
            $('#section_student_data').slideUp();
            resetStudentFields();
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('.js-example-basic-multiple').select2({
            placeholder: "Pilih Anggota",
            allowClear: true
        });
    });


    $(".btn-pilih").click(function () {
        var id = $(this).data('id');
        
        // Append to Select2 if not already there
        var currentValues = $('#package').val() || [];
        if (currentValues.indexOf(id.toString()) === -1) {
            currentValues.push(id.toString());
            $('#package').val(currentValues).trigger('change');
        }

        $('#modal_create').modal('hide');
    });
</script>
<?= $this->endSection('script'); ?>