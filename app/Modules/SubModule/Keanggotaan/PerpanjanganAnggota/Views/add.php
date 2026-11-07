<?php

$fakultas = get_ref_table('master_fakultas', 'id,Nama', null, 'data');
$kelas = get_ref_table('kelas_siswa', 'id,namakelassiswa', null, 'data');
$pendidikan = get_ref_table('master_pendidikan', 'id,Nama', null, 'data');
$pekerjaan = get_ref_table('master_pekerjaan', 'id,Pekerjaan', null, 'data');
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-perpanjangan-anggota.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-perpanjangan-anggota.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner ina-renew">
    <div class="app-page-title ina-renew__heading">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div>
                    <p class="ina-renew__eyebrow">LAYANAN KEANGGOTAAN</p>
                    <h1>Perpanjangan anggota</h1>
                    <p class="page-title-subheading">Perbarui masa aktif satu atau beberapa anggota dalam satu proses.</p>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('perpanjangan-anggota') ?>">Perpanjangan anggota</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div id="infoMessage"><?= $message ?? ''; ?></div>
    <?= get_message('message'); ?>

    <form id="frm_create" method="post" action="<?= base_url('perpanjangan-anggota/create'); ?>">
        <div class="ina-renew__layout">
            <div class="ina-renew__main">
                <section class="card ina-renew__panel" aria-labelledby="renew-members-title">
                    <div class="card-body">
                        <div class="ina-renew__section-title">
                            <span class="ina-renew__step">01</span>
                            <div>
                                <h2 id="renew-members-title">Pilih anggota</h2>
                                <p>Gunakan filter atau pilih anggota langsung dari daftar.</p>
                            </div>
                        </div>
                        <div class="ina-renew__filters">
                            <div class="ina-renew__field">
                                <label for="filter_fakultas">Fakultas</label>
                                <select class="form-control" id="filter_fakultas">
                                    <option value="">Semua fakultas</option>
                                    <?php foreach ($fakultas as $row): ?>
                                        <option value="<?= esc($row->id, 'attr') ?>"><?= esc($row->Nama) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ina-renew__field">
                                <label for="filter_jurusan">Jurusan</label>
                                <select class="form-control" id="filter_jurusan">
                                    <option value="">Semua jurusan</option>
                                </select>
                            </div>
                            <div class="ina-renew__field">
                                <label for="filter_kelas">Kelas</label>
                                <select class="form-control" id="filter_kelas">
                                    <option value="">Semua kelas</option>
                                    <?php foreach ($kelas as $row): ?>
                                        <option value="<?= esc($row->id, 'attr') ?>"><?= esc($row->namakelassiswa) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ina-renew__field">
                                <label for="filter_pendidikan">Pendidikan</label>
                                <select class="form-control" id="filter_pendidikan">
                                    <option value="">Semua pendidikan</option>
                                    <?php foreach ($pendidikan as $row): ?>
                                        <option value="<?= esc($row->id, 'attr') ?>"><?= esc($row->Nama) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ina-renew__field">
                                <label for="filter_pekerjaan">Pekerjaan</label>
                                <select class="form-control" id="filter_pekerjaan">
                                    <option value="">Semua pekerjaan</option>
                                    <?php foreach ($pekerjaan as $row): ?>
                                        <option value="<?= esc($row->id, 'attr') ?>"><?= esc($row->Pekerjaan) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="btn-apply-filter" title="Pilih maksimal 50 anggota pertama yang cocok">
                                <i class="fa fa-check" aria-hidden="true"></i> Pilih 50 hasil pertama
                            </button>
                        </div>
                        <div class="ina-renew__field">
                            <label for="package">Anggota <span class="ina-renew__required">*</span></label>
                            <p class="ina-renew__hint" id="renew-member-hint">Buka daftar untuk 50 anggota terbaru. Ketik nomor atau awal nama untuk mencari lebih banyak.</p>
                            <select class="custom-select js-example-basic-multiple" id="package" name="Member_id[]" multiple="multiple" aria-describedby="renew-member-hint" required>
                            </select>
                        </div>
                        <div class="ina-renew__quick-actions">
                            <button type="button" class="btn btn-outline-primary" id="btn-select-expired"><i class="fa fa-clock" aria-hidden="true"></i> Pilih 50 masa aktif berakhir</button>
                            <button type="button" class="btn btn-outline-secondary" id="btn-clear-selection"><i class="fa fa-times" aria-hidden="true"></i> Hapus pilihan</button>
                        </div>
                    </div>
                </section>

                <section class="card ina-renew__panel" aria-labelledby="renew-details-title">
                    <div class="card-body">
                        <div class="ina-renew__section-title">
                            <span class="ina-renew__step">02</span>
                            <div>
                                <h2 id="renew-details-title">Detail perpanjangan</h2>
                                <p>Atur masa aktif dan jenis keanggotaan baru.</p>
                            </div>
                        </div>
                        <div class="ina-renew__fields">
                            <div class="ina-renew__field">
                                <label for="date2">Tanggal berakhir baru <span class="ina-renew__required">*</span></label>
                                <input type="text" class="form-control datepicker" id="date2" name="EndDate" placeholder="YYYY-MM-DD" value="<?= esc(set_value('EndDate'), 'attr') ?>" autocomplete="off" required>
                            </div>
                            <div class="ina-renew__field">
                                <label for="jenisanggota_id">Jenis anggota <span class="ina-renew__required">*</span></label>
                                <select class="form-control" name="Jenisanggota_id" id="jenisanggota_id" required>
                                    <option value="">Pilih jenis anggota</option>
                                    <?php foreach (get_ref_table('jenis_anggota', 'id, jenisanggota', null, 'data') as $row): ?>
                                        <option value="<?= esc($row->id, 'attr') ?>" <?= ($row->id == set_value('Jenisanggota_id')) ? 'selected' : '' ?>><?= esc($row->jenisanggota) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div id="section_student_data" class="ina-renew__student" style="display:none;">
                            <h3>Data mahasiswa</h3>
                            <div class="ina-renew__fields">
                                <div class="ina-renew__field">
                                    <label for="update_fakultas">Fakultas</label>
                                    <select class="form-control" name="Fakultas_id" id="update_fakultas">
                                        <option value="">Pilih fakultas</option>
                                        <?php foreach ($fakultas as $row): ?>
                                            <option value="<?= esc($row->id, 'attr') ?>"><?= esc($row->Nama) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="ina-renew__field">
                                    <label for="update_jurusan">Jurusan</label>
                                    <select class="form-control" name="Jurusan_id" id="update_jurusan"><option value="">Pilih jurusan</option></select>
                                </div>
                            </div>
                        </div>
                        <div class="ina-renew__field ina-renew__cost">
                            <label for="frm_create_sort">Biaya (Rp)</label>
                            <input type="number" class="form-control" id="frm_create_sort" name="biaya" min="0" value="<?= esc(set_value('biaya'), 'attr') ?>">
                        </div>
                        <div class="ina-renew__field">
                            <label for="frm_create_description">Keterangan</label>
                            <textarea id="frm_create_description" name="Keterangan" rows="3" class="form-control" placeholder="Tambahkan keterangan jika diperlukan"><?= esc(set_value('Keterangan')) ?></textarea>
                        </div>
                        <div class="ina-renew__checkbox">
                            <input type="hidden" name="is_lunas" value="0">
                            <input type="checkbox" name="is_lunas" id="is_lunas" value="1" <?= set_value('is_lunas') ? 'checked' : '' ?>>
                            <label for="is_lunas">Pembayaran sudah lunas</label>
                        </div>
                    </div>
                </section>
                <div class="ina-renew__footer">
                    <a class="btn btn-outline-secondary" href="<?= base_url('perpanjangan-anggota') ?>">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save" aria-hidden="true"></i> Simpan perpanjangan</button>
                </div>
            </div>

            <aside class="ina-renew__aside" aria-label="Ringkasan anggota">
                <div id="section_single_profile">
                    <?= $this->include('PerpanjanganAnggota\Views\section\member_profile') ?>
                </div>
                <div id="section_multiple_summary" style="display:none;">
                    <div class="card ina-renew__panel">
                        <div class="card-body">
                            <h2>Anggota terpilih (<span id="summary_count">0</span>)</h2>
                            <ul class="list-group list-group-flush" id="summary_list"></ul>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </form>
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
    const memberCache = {};
    const memberSearchUrl = '<?= base_url('perpanjangan-anggota/search-members') ?>';

    function memberFilters() {
        return {
            fakultas: $('#filter_fakultas').val(),
            jurusan: $('#filter_jurusan').val(),
            kelas: $('#filter_kelas').val(),
            pendidikan: $('#filter_pendidikan').val(),
            pekerjaan: $('#filter_pekerjaan').val()
        };
    }

    function selectedMemberData(option) {
        return memberCache[option.value] || $(option).data();
    }

    $('#package').on('change', function () {
        const selectedOptions = $('#package option:selected');
        const summaryList = $('#summary_list');
        const summaryCount = $('#summary_count');

        if (selectedOptions.length === 1) {
            $('#section_single_profile').show();
            $('#section_multiple_summary').hide();

            const opt = selectedMemberData(selectedOptions[0]);
            const no_anggota = opt.no_anggota;
            const name = opt.name;
            const email = opt.email;
            const address = opt.address;
            const nomor = opt.nomor;
            const date = opt.date;
            const id = opt.id;
            const jenis = opt.jenis;

            $('[name=no_anggota]').text(no_anggota);
            $('#name').text(name);
            $('#date').text(formatDateIndo(date));
            $('#jenisanggota_id').val(jenis);
            $('#date2').val(date);
            $('#nomor').text(nomor);
            $('[name=email]').text(email);
            $('[name=address]').text(address);
        } else if (selectedOptions.length > 1) {
            $('#section_single_profile').hide();
            $('#section_multiple_summary').show();

            summaryCount.text(selectedOptions.length);
            let html = '';
            const safe = value => $('<div>').text(value == null ? '' : String(value)).html();
            selectedOptions.each(function() {
                const opt = selectedMemberData(this);
                const name = opt.name;
                const no = opt.no_anggota;
                const date = opt.date || '-';
                const jenis = opt.jenis_label || '-';
                const fakultas = opt.fakultas_label;
                const jurusan = opt.jurusan_label;

                let detail = `<span class="badge badge-info mr-1">${safe(jenis)}</span>`;
                detail += `<span class="badge badge-secondary mr-1">End : ${safe(formatDateIndo(date))}</span>`;
                if (fakultas) detail += `<small class="d-block text-muted">${safe(fakultas)} ${jurusan ? '/ ' + safe(jurusan) : ''}</small>`;

                html += `<li class="list-group-item p-2">
                    <div class="widget-content p-0">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left flex2">
                                <div class="widget-heading" style="font-size: 0.85rem; font-weight: bold;">${safe(name)}</div>
                                <div class="widget-subheading opacity-7" style="font-size: 0.75rem;">
                                    ${safe(no)} <br>
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

            $('[name=no_anggota]').text('');
            $('#name').text('');
            $('#date').text('');
            $('#nomor').text('');
            $('[name=email]').text('');
            $('[name=address]').text('');
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

    function chooseMembers(expired) {
        const filters = memberFilters();
        if (!expired && !Object.values(filters).some(Boolean)) {
            Swal.fire('Info', 'Pilih minimal satu filter terlebih dahulu', 'info');
            return;
        }

        const $button = $(expired ? '#btn-select-expired' : '#btn-apply-filter');
        $button.prop('disabled', true);
        $.getJSON(memberSearchUrl, Object.assign({page: 1, expired: expired ? '1' : '0'}, filters))
            .done(function(data) {
                if (!data.results.length) {
                    Swal.fire('Info', 'Tidak ada anggota yang cocok dengan filter tersebut', 'info');
                    return;
                }

                resetRenewalDetails();
                const $members = $('#package').empty();
                data.results.forEach(function(member) {
                    memberCache[member.id] = member;
                    $members.append(new Option(member.text, member.id, true, true));
                });
                $members.trigger('change');

                if (filters.fakultas) {
                    $('#jenisanggota_id').val('12').trigger('change');
                    $('#update_fakultas').val(filters.fakultas).trigger('change');
                    if (filters.jurusan) {
                        const selectedJurusan = filters.jurusan;
                        const checkInterval = setInterval(function() {
                            if ($('#update_jurusan option[value="' + selectedJurusan + '"]').length) {
                                $('#update_jurusan').val(selectedJurusan);
                                clearInterval(checkInterval);
                            }
                        }, 100);
                        setTimeout(function() { clearInterval(checkInterval); }, 3000);
                    }
                }
                if (data.pagination.more) {
                    Swal.fire('Info', 'Hanya 50 anggota pertama dipilih. Cari anggota lainnya lewat kolom Anggota.', 'info');
                }
            })
            .fail(function() { Swal.fire('Gagal', 'Anggota tidak dapat dimuat. Coba lagi.', 'error'); })
            .always(function() { $button.prop('disabled', false); });
    }

    $('#btn-select-expired').on('click', function() { chooseMembers(true); });
    $('#btn-apply-filter').on('click', function() { chooseMembers(false); });
    $('#btn-clear-selection').on('click', function() {
        resetRenewalDetails();
        $('#package').empty().trigger('change');
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
            placeholder: 'Cari nomor atau nama anggota',
            allowClear: true,
            ajax: {
                url: memberSearchUrl,
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return Object.assign({q: params.term || '', page: params.page || 1}, memberFilters());
                },
                processResults: function(data) {
                    data.results.forEach(function(member) { memberCache[member.id] = member; });
                    return data;
                }
            }
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