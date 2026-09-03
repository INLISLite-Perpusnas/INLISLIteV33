<?php
$regionModel = new \Region\Models\RegionModel();
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
                <div>Anggota
                    <div class="page-title-subheading">Daftar semua Anggota
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Keanggotaan</li>
                        <li class="breadcrumb-item ">Daftar Anggota</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Anggota
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= base_url('anggota/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
                    Tambah Anggota
                </a>
            </div>
        </div>

        <div class="card-body">
            <form name="form_items" id="form_items">
                
                <div class="d-block mb-3 pb-3 border-bottom">
                    <button type="button" id="proses_keranjang" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Semua anggota yang terpilih"><i class="fa fa-shopping-cart"></i> Pindahkan ke Keranjang</button>
                    <button type="button" id="print_kartu" class="btn btn-primary ml-2" data-toggle="tooltip" data-placement="top" title="Print kartu anggota yang terpilih"><i class="fa fa-print"></i> Print Kartu</button>
                    <button type="button" id="aktifkan_online" class="btn btn-success ml-2" data-toggle="tooltip" data-placement="top" title="Aktifkan akses online untuk anggota yang terpilih"><i class="fa fa-globe"></i> Aktifkan Online</button>
                    <?php if ($is_sekolah) : ?>
                        <button type="button" id="update_kelas" class="btn btn-info ml-2" data-toggle="tooltip" data-placement="top" title="Update kelas anggota yang terpilih secara batch"><i class="fa fa-graduation-cap"></i> Update Batch Kelas</button>
                    <?php endif; ?>
                </div>

                <?php if ($is_sekolah) : ?>
                    <!-- ── Filter: Kelas (khusus perpustakaan sekolah) ── -->
                    <div class="d-flex align-items-center flex-wrap mb-3" style="gap:6px;">
                        <div class="input-group" style="width:280px; flex-shrink:0;">
                            <div class="input-group-prepend">
                                <span class="btn btn-secondary"><i class="fa fa-filter"></i> Kelas</span>
                            </div>
                            <select class="form-control" id="filter_kelas_id">
                                <option value="">-- Semua Kelas --</option>
                                <?php foreach ($kelas_list as $row) : ?>
                                    <option value="<?= $row->id ?>"><?= esc($row->namakelassiswa) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button class="btn btn-outline-secondary" id="btnResetFilterKelas" type="button" style="height:38px; flex-shrink:0;">
                            <i class="fa fa-undo"></i> Reset Filter
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($is_perguruan_tinggi) : ?>
                    <!-- ── Filter: Fakultas & Jurusan (khusus perguruan tinggi) ── -->
                    <div class="d-flex align-items-center flex-wrap mb-3" style="gap:6px;">
                        <div class="input-group" style="width:280px; flex-shrink:0;">
                            <div class="input-group-prepend">
                                <span class="btn btn-secondary"><i class="fa fa-filter"></i> Fakultas</span>
                            </div>
                            <select class="form-control" id="filter_fakultas_id">
                                <option value="">-- Semua Fakultas --</option>
                                <?php foreach ($fakultas_list as $row) : ?>
                                    <option value="<?= $row->id ?>"><?= esc($row->Nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input-group" style="width:280px; flex-shrink:0;">
                            <div class="input-group-prepend">
                                <span class="btn btn-secondary"><i class="fa fa-filter"></i> Jurusan</span>
                            </div>
                            <select class="form-control" id="filter_jurusan_id">
                                <option value="">-- Semua Jurusan --</option>
                                <?php foreach ($jurusan_list as $row) : ?>
                                    <option value="<?= $row->id ?>"><?= esc($row->Nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button class="btn btn-outline-secondary" id="btnResetFilterFakultasJurusan" type="button" style="height:38px; flex-shrink:0;">
                            <i class="fa fa-undo"></i> Reset Filter
                        </button>
                    </div>
                <?php endif; ?>

                <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">No</th>
                            <th class="text-center" width="35">
                                <input type="checkbox" class="check_data" title="Pilih Semua">
                            </th>
                            <th>Nama Anggota</th>
                            <th>No. Anggota</th>
                            <th width="100">Tgl. Register</th>
                            <th width="100">Tgl. Berakhir</th>
                            <th width="130">Status Anggota</th>
                            <?php if ($is_sekolah) : ?>
                                <th width="120">Kelas</th>
                            <?php endif; ?>
                            <?php if ($is_perguruan_tinggi) : ?>
                                <th width="150">Fakultas</th>
                                <th width="150">Jurusan</th>
                            <?php endif; ?>
                            <th style="min-width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </form>
        </div>
        </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('Anggota\Views\modal_upload'); ?>
<?= $this->include('Anggota\Views\modal_camera'); ?>
<?php if ($is_sekolah) : ?>
    <?= $this->include('Anggota\Views\modal_update_kelas'); ?>
<?php endif; ?>
<script>
      $(document).ready(function() {
        <?php if (session()->getFlashdata('swal_icon')) : ?>
            Swal.fire({
                icon: '<?= session()->getFlashdata('swal_icon') ?>', // gunakan 'icon' jika SweetAlert2 versi terbaru
                title: '<?= session()->getFlashdata('swal_title') ?>',
                html: '<?= session()->getFlashdata('swal_html') ? session()->getFlashdata('swal_html') : session()->getFlashdata('swal_text') ?>',
                showConfirmButton: false,
                timer: 3000
            });
        <?php endif; ?>
    });
</script>
<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "scrollCollapse": true,
            "ajax": {
                "url": '<?php echo site_url('api/anggota/datatable'); ?>',
                "data": function(d) {
                    d.kelas_id = $('#filter_kelas_id').val();
                    d.fakultas_id = $('#filter_fakultas_id').val();
                    d.jurusan_id = $('#filter_jurusan_id').val();
                }
            },
            
            // KONFIGURASI DOM BARU
            // l = length menu (atas kiri), f = filter/search (atas kanan)
            // t = table (tengah)
            // i = info (bawah kiri), p = pagination (bawah kanan)
          "dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
       "<'row'<'col-md-12'tr>>" +
       "<'row mt-2'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",
            
            "pagingType": "full_numbers",
            "oLanguage": {
                "sSearch": "<i class='fa fa-search'></i> _INPUT_",
                "sLengthMenu": "_MENU_",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sLast": "<i class='fa fa-chevron-double-right'></i>",
                    "sFirst": "<i class='fa fa-chevron-double-left'></i>",
                }
            },
            "columns": [{
                    data: 'no',
                    className: 'text-center'
                }, {
                    data: 'cid',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'FullName'
                },
                {
                    data: 'MemberNo'
                },
                {
                    data: 'RegisterDate',
                    className: 'text-center'
                },
                {
                    data: 'EndDate',
                    className: 'text-center'
                },
                {
                    data: 'StatusAnggota',
                    className: 'text-center'
                },
                <?php if ($is_sekolah) : ?>
                {
                    data: 'KelasName',
                    className: 'text-center',
                    searchable: false,
                    orderable: false
                },
                <?php endif; ?>
                <?php if ($is_perguruan_tinggi) : ?>
                {
                    data: 'FakultasName',
                    className: 'text-center',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'JurusanName',
                    className: 'text-center',
                    searchable: false,
                    orderable: false
                },
                <?php endif; ?>
                {
                    data: 'action',
                    className: 'text-center',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'CreateDate',
                    visible: false,
                }

            ],
            "order": [
                [<?= 8 + ($is_sekolah ? 1 : 0) + ($is_perguruan_tinggi ? 2 : 0) ?>, "desc"]
            ],
            "drawCallback": function(data, type, full, meta) {
                var api = this.api();
                var data = api.rows().data();

                $('.apply-status').bootstrapToggle();

                $(".apply-status").on('change', function() {
                    var url = $(this).attr('data-href');
                    var field = $(this).attr('data-field');
                    var value = $(this).is(':checked') == true ? 1 : 0;
                    var data_post = 'field=' + field + '&value=' + value;

                    ajax_post(url, data_post);
                });

                $(".apply-select").on('change', function() {
                    var url = $(this).attr('data-href');
                    var field = $(this).attr('data-field');
                    var value = $(this).val();
                    var data_post = 'field=' + field + '&value=' + value;

                    ajax_post(url, data_post);
                });

                $.each(data, function(i, row) {
                    $("#lazy" + row.id).Lazy();
                });

                $('.image-link').magnificPopup({
                    type: 'image'
                });
            },
            "initComplete": function(settings, json) {

            }
        });
    });

    $(".check_data").click(function() {
        $('#tbl_data input:checkbox').not(this).prop('checked', this.checked);
    });

    // ── Filter: Kelas (khusus perpustakaan sekolah) ─────────────────────────
    $('#filter_kelas_id').on('change', function() {
        t.ajax.reload();
    });

    $('#btnResetFilterKelas').on('click', function() {
        $('#filter_kelas_id').val('');
        t.ajax.reload();
    });

    // ── Filter: Fakultas & Jurusan (khusus perguruan tinggi) ────────────────
    $('#filter_fakultas_id, #filter_jurusan_id').on('change', function() {
        t.ajax.reload();
    });

    $('#btnResetFilterFakultasJurusan').on('click', function() {
        $('#filter_fakultas_id').val('');
        $('#filter_jurusan_id').val('');
        t.ajax.reload();
    });

    // ── Update Batch Kelas (khusus perpustakaan sekolah) ────────────────────
    $('#update_kelas').click(function() {
        var checkedBoxes = $('#tbl_data input[type="checkbox"]:checked').not('.check_data');

        if (checkedBoxes.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan pilih minimal satu anggota untuk diupdate kelasnya',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return false;
        }

        $('#jml_anggota_terpilih').text(checkedBoxes.length);
        $('#target_kelas_id').val('');
        $('#modal_update_kelas').modal('show');

        return false;
    });

    $('#btn_simpan_update_kelas').click(function() {
        var checkedBoxes = $('#tbl_data input[type="checkbox"]:checked').not('.check_data');
        var selectedIds = [];
        checkedBoxes.each(function() {
            var checkboxValue = $(this).val();
            if (checkboxValue && checkboxValue !== 'on') {
                selectedIds.push(checkboxValue);
            }
        });

        var targetKelasId = $('#target_kelas_id').val();

        if (!targetKelasId) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan pilih kelas tujuan terlebih dahulu',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return false;
        }

        $('#modal_update_kelas').modal('hide');

        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang memperbarui kelas anggota',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
                url: '<?= base_url('anggota/update_batch_kelas') ?>',
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                    member_ids: selectedIds,
                    Kelas_id: targetKelasId
                },
                dataType: 'json'
            })
            .done(function(response) {
                if (response.error === false) {
                    Swal.fire({
                        title: 'Berhasil!',
                        html: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        t.ajax.reload(null, false);
                        $('#tbl_data input:checkbox').prop('checked', false);
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        html: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Terjadi kesalahan saat memproses data. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });

        return false;
    });

    $('#proses_keranjang').click(function() {
        var form = $('#form_items');
        var serialize_bulk = form.serialize();
        var url = "<?= base_url('anggota/proses_keranjang') ?>" + '?' + serialize_bulk;
        console.log(serialize_bulk);
        console.log(url);

        Swal.fire({
            title: 'Anda yakin?',
            html: "Semua anggota yang terpilih akan dipindahkan ke keranjang",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
        return false;
    });

    // Event handler untuk tombol Print Kartu
    $('#print_kartu').click(function() {
        // Debug: cek semua checkbox yang ada
        console.log('All checkboxes in table:', $('#tbl_data input[type="checkbox"]'));

        // Coba beberapa selector berbeda untuk menangkap checkbox
        var checkedBoxes = $('#tbl_data input[type="checkbox"]:checked').not('.check_data');

        console.log('Checked boxes found:', checkedBoxes.length);
        console.log('Checked boxes:', checkedBoxes);

        if (checkedBoxes.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan pilih minimal satu anggota untuk dicetak kartunya',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Collect all selected IDs
        var selectedIds = [];
        checkedBoxes.each(function() {
            var checkboxValue = $(this).val();
            var checkboxName = $(this).attr('name');
            var checkboxId = $(this).attr('id');

            console.log('Checkbox - Value:', checkboxValue, 'Name:', checkboxName, 'ID:', checkboxId);

            if (checkboxValue && checkboxValue !== 'on') {
                selectedIds.push(checkboxValue);
            }
        });

        console.log('Selected IDs:', selectedIds);

        // Konfirmasi sebelum print
        Swal.fire({
            title: 'Konfirmasi Print Kartu',
            html: `Akan mencetak kartu untuk <strong>${selectedIds.length}</strong> anggota yang dipilih.<br><br>
                <div style="text-align:left;display:inline-block">
                  <label style="margin-right:18px;cursor:pointer">
                    <input type="radio" name="swal_orientation" value="landscape" checked> Landscape
                  </label>
                  <label style="cursor:pointer">
                    <input type="radio" name="swal_orientation" value="portrait"> Portrait
                  </label>
                </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: 'Ya, Print Kartu',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const sel = document.querySelector('input[name="swal_orientation"]:checked');
                return sel ? sel.value : 'landscape';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim data ke controller
                printKartuAnggota(selectedIds, result.value || 'landscape');
            }
        });

        return false;
    });

    $('#hapus_permanen').click(function() {
        var form = $('#form_items');
        var serialize_bulk = form.serialize();
        var url = "<?= base_url('anggota/hapus_permanen') ?>" + '?' + serialize_bulk;
        console.log(serialize_bulk);
        console.log(url);

        Swal.fire({
            title: 'Anda yakin?',
            html: "Semua anggota yang terpilih akan dihapus secara permanen",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
        return false;
    });

    // Fungsi untuk mengirim data ke controller print kartu
    function printKartuAnggota(ids, orientation) {
        // Method 1: Kirim via POST dengan form hidden
        var form = $('<form></form>');
        form.attr('method', 'post');
        form.attr('action', '<?= base_url('anggota/multipleprint') ?>');
        form.attr('target', '_blank'); // Buka di tab baru

        // Tambahkan CSRF token jika ada
        <?php if (csrf_token()): ?>
            form.append('<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />');
        <?php endif; ?>

        // Orientasi kartu (landscape / portrait)
        form.append('<input type="hidden" name="orientation" value="' + (orientation === 'portrait' ? 'portrait' : 'landscape') + '" />');

        // Tambahkan IDs sebagai array
        $.each(ids, function(index, value) {
            form.append('<input type="hidden" name="member_ids[]" value="' + value + '" />');
        });

        // Append form ke body dan submit
        $('body').append(form);
        form.submit();
        form.remove();
    }

    $("body").on("click", ".remove-data", function() {
        var url = $(this).attr('data-href');
        console.log(url);
        Swal.fire({
            title: '<?= lang('App.swal.are_you_sure') ?>',
            text: "<?= lang('App.swal.can_not_be_restored') ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
        return false;
    });

    function ajax_post(url, data_post) {
        $.ajax({
                url: url,
                type: 'POST',
                data: data_post,
            })
            .done(function(res) {
                console.log(res)

                if (res.error == false) {
                    Swal.fire({
                        title: 'Berhasil',
                        html: res.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 5000,
                    }).then(() => {});
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: res.message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 5000
                    }).then(() => {});
                }
            })
            .fail(function(res) {
                console.log(res);

                Swal.fire({
                    title: 'Oups',
                    text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 5000
                }).then(() => {});
            });
    }

    // Event handler untuk tombol Aktifkan Online
    $('#aktifkan_online').click(function() {
        var checkedBoxes = $('#tbl_data input[type="checkbox"]:checked').not('.check_data');
        
        console.log('Checked boxes found:', checkedBoxes.length);
        
        if (checkedBoxes.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan pilih minimal satu anggota untuk diaktifkan secara online',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Collect all selected IDs
        var selectedIds = [];
        checkedBoxes.each(function() {
            var checkboxValue = $(this).val();
            if (checkboxValue && checkboxValue !== 'on') {
                selectedIds.push(checkboxValue);
            }
        });
        
        console.log('Selected IDs for online activation:', selectedIds);
        
        // Konfirmasi sebelum aktifkan
        Swal.fire({
            title: 'Konfirmasi Aktivasi Online',
            html: `Akan mengaktifkan akses online untuk <strong>${selectedIds.length}</strong> anggota yang dipilih.<br><br>
                   <small class="text-muted">Username akan sama dengan No. Anggota, dan password default sama dengan username.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: 'Ya, Aktifkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim data ke controller
                aktifkanAnggotaOnline(selectedIds);
            }
        });
        
        return false;
    });

    // Fungsi untuk mengirim data aktivasi online ke controller
    function aktifkanAnggotaOnline(ids) {
        // Tampilkan loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang mengaktifkan anggota secara online',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '<?= base_url('anggota/aktifkan_online') ?>',
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                member_ids: ids
            },
            dataType: 'json'
        })
        .done(function(response) {
            console.log('Response:', response);
            
            if (response.error === false) {
                Swal.fire({
                    title: 'Berhasil!',
                    html: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload datatable
                    t.ajax.reload(null, false);
                    // Uncheck all checkboxes
                    $('#tbl_data input:checkbox').prop('checked', false);
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    html: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.log('Error:', textStatus, errorThrown);
            console.log('Response:', jqXHR.responseText);
            
            Swal.fire({
                title: 'Oops!',
                text: 'Terjadi kesalahan saat memproses data. Silakan coba lagi.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }

    // Modal Upload Foto - Initialize on button click
    $(document).on('click', '.btn-upload-foto', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var field = $(this).attr('data-field');
        var title = $(this).attr('data-title');
        var format = $(this).attr('data-format');
        var formatTitle = $(this).attr('data-format-title');
        
        $('#upload_id').val(id);
        $('#upload_field').val(field);
        $('#upload_title').val(title);
        $('#upload_title_span').html(title);
        $('#upload_data_format_title').html(formatTitle || 'Format (JPG|PNG). Max 1 Files @ 2MB');
        
        // Dropzone has been replaced by a standard file input in modal_upload.php
        
        $('#modal_upload').modal('show');
    });

    // Modal Camera - Initialize on button click
    $(document).on('click', '.btn-ambil-foto', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $('#capture_id').val(id);
        $('#modal_camera').modal('show');
    });

    // Modal upload hidden handler is in modal_upload.php

    // Handle modal camera shown
    $(document).on('shown.bs.modal','#modal_camera', function (e) {
        // Startup camera when modal is shown
        var width = 350;
        var height = 0;
        var streaming = false;
        
        var video = document.getElementById('video');
        var canvas = document.getElementById('canvas');
        var photo = document.getElementById('photo');
        var camera_image = document.getElementById('camera_image');
        var startbutton = document.getElementById('startbutton');

        function startup() {
            navigator.mediaDevices.getUserMedia({video: true, audio: false})
            .then(function(stream) {
                video.srcObject = stream;
                video.play();
            })
            .catch(function(err) {
                console.log("An error occurred: " + err);
                Swal.fire({
                    title: 'Error',
                    text: 'Tidak dapat mengakses kamera: ' + err,
                    icon: 'error'
                });
            });

            video.addEventListener('canplay', function(ev){
                if (!streaming) {
                    height = video.videoHeight / (video.videoWidth/width);
                
                    if (isNaN(height)) {
                        height = width / (4/3);
                    }
                
                    video.setAttribute('width', width);
                    video.setAttribute('height', height);
                    canvas.setAttribute('width', width);
                    canvas.setAttribute('height', height);
                    streaming = true;
                }
            }, false);

            startbutton.addEventListener('click', function(ev){
                takepicture();
                ev.preventDefault();
            }, false);
        }

        function takepicture() {
            var context = canvas.getContext('2d');
            if (width && height) {
                canvas.width = width;
                canvas.height = height;
                context.drawImage(video, 0, 0, width, height);
                
                var data = canvas.toDataURL('image/png');
                photo.setAttribute('src', data);
                camera_image.setAttribute('value', data);
            }
        }

        startup();
    });

    // Handle modal camera hidden
    $(document).on('hidden.bs.modal','#modal_camera', function (e) {
        var video = document.getElementById('video');
        if (video && video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
    });

    $(document).on('click', '.btn-edit-anggota', function(e) {
    e.preventDefault();
    var encId = $(this).attr('data-id');

    var form = $('<form></form>');
    form.attr('method', 'post');
    form.attr('action', '<?= base_url('anggota/edit') ?>');

    // HTTP Method Spoofing: dikirim POST, diperlakukan CI4 sebagai PUT
    form.append('<input type="hidden" name="_method" value="PUT">');

    <?php if (csrf_token()): ?>
        form.append('<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">');
    <?php endif; ?>

    form.append('<input type="hidden" name="ID" value="' + encId + '">');

    $('body').append(form);
    form.submit();
});
</script>

<?= $this->endSection('script'); ?>