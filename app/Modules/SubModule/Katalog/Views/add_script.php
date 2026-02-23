<script>
    $(document).ready(function() {

        // ========== PENGARANG UTAMA ==========
        $('.pengarangUtamaSelect').on('change', function() {
            var formGroup = $(this).closest('.pengarangUtamaWrapper');
            var ind1 = formGroup.find('input[name^="pengarangUtamaRadio"]:checked').val() ?? '0';
            var value = $(this).val();
            var formControl = formGroup.find('.form-control');
            formControl.attr('name', 'pengarangUtama[' + value + '-' + ind1 + '][]');
        });

        $('.pengarangUtamaRadio').on('change', function() {
            var formGroup = $(this).closest('.pengarangUtamaWrapper');
            var ind1 = $(this).val();
            var target = $(this).data('id');
            var value = $('#' + target).val();
            var formControl = formGroup.find('.form-control');
            formControl.attr('name', 'pengarangUtama[' + value + '-' + ind1 + '][]');
        });

        // ========== PENGARANG TAMBAHAN - TAMBAH BARIS ==========
        var idxPengarangTambahan = 0;

       $('.add-pengarangTambahan').click(function() {
            idxPengarangTambahan++;

            // Baca dari URL, bukan dari hidden input
            var urlParams = new URLSearchParams(window.location.search);
            var isRDA = urlParams.get('rda') == '1';

            var html = [];
            html.push('<div class="form-group pengarangTambahanWrapper mb-2" id="pengarangTambahanWrapper' + idxPengarangTambahan + '">');
            html.push('<div class="form input-group">');
            html.push('<div class="input-group-prepend" style="width: 20%;">');

            if (isRDA) {
                html.push('<select class="custom-select pengarangTambahanSelect" id="pengarangTambahanSelect-' + idxPengarangTambahan + '" data-input="pengarangTambahanInput-' + idxPengarangTambahan + '" data-relator-input="pengarangTambahanRelator-' + idxPengarangTambahan + '">');
                html.push('<option value="700" data-relator="">Nama Orang</option>');
                html.push('<option value="710" data-relator="">Nama Badan</option>');
                html.push('<option value="711" data-relator="">Nama Pertemuan</option>');
                html.push('<option value="700" data-relator="penerjemah">Penerjemah</option>');
                html.push('<option value="700" data-relator="ilustrator">Ilustrator</option>');
                html.push('<option value="700" data-relator="penyunting">Penyunting</option>');
                html.push('<option value="700" data-relator="komposer">Komposer</option>');
                html.push('</select>');
            } else {
                html.push('<select class="custom-select pengarangTambahanSelect" id="pengarangTambahanSelect-' + idxPengarangTambahan + '" data-input="pengarangTambahanInput-' + idxPengarangTambahan + '" data-relator-input="pengarangTambahanRelator-' + idxPengarangTambahan + '">');
                html.push('<option value="700" data-relator="">Nama Orang</option>');
                html.push('<option value="710" data-relator="">Nama Badan</option>');
                html.push('<option value="711" data-relator="">Nama Pertemuan</option>');
                html.push('</select>');
            }

            html.push('</div>');
            // ← KUNCI: name pakai idxPengarangTambahan sebagai key unik, BUKAN 700-0 semua
            html.push('<input type="text" class="form-control" id="pengarangTambahanInput-' + idxPengarangTambahan + '" name="pengarangTambahan[700-' + idxPengarangTambahan + '][]" placeholder="" value="" />');
            html.push('<input type="hidden" id="pengarangTambahanRelator-' + idxPengarangTambahan + '" name="pengarangTambahanRelator[' + idxPengarangTambahan + ']" value="" />');
            html.push('<div class="input-group-append">');
            html.push('<span data-id="' + idxPengarangTambahan + '" class="remove-pengarangTambahan btn btn-outline-secondary"><i class="fa fa-minus"></i></span>');
            html.push('</div>');
            html.push('</div>');
            // radio buttons
            html.push('<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">');
            html.push('<fieldset class="form-group"><div class="row" style="margin: 4px;">');
            html.push('<div class="col-sm-4"><div class="form-check"><label class="form-check-label">');
            html.push('<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect-' + idxPengarangTambahan + '" data-input="pengarangTambahanInput-' + idxPengarangTambahan + '" name="pengarangTambahanRadio[' + idxPengarangTambahan + '][]" value="0" checked>Nama Depan');
            html.push('</label></div></div>');
            html.push('<div class="col-sm-4"><div class="form-check"><label class="form-check-label">');
            html.push('<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect-' + idxPengarangTambahan + '" data-input="pengarangTambahanInput-' + idxPengarangTambahan + '" name="pengarangTambahanRadio[' + idxPengarangTambahan + '][]" value="1">Nama Belakang');
            html.push('</label></div></div>');
            html.push('<div class="col-sm-4"><div class="form-check"><label class="form-check-label">');
            html.push('<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect-' + idxPengarangTambahan + '" data-input="pengarangTambahanInput-' + idxPengarangTambahan + '" name="pengarangTambahanRadio[' + idxPengarangTambahan + '][]" value="2">Nama Keluarga');
            html.push('</label></div></div>');
            html.push('</div></fieldset></div>');
            html.push('</div>');

            $("#pengarangTambahanGroup").append(html.join(''));
        });

        $(document).on('click', '.remove-pengarangTambahan', function() {
            var id = $(this).data('id');
            $('#pengarangTambahanWrapper' + id).remove();
        });

        // ← event change select pengarang tambahan (semua baris termasuk index 0)
        $(document).on('change', '.pengarangTambahanSelect', function() {
            var formGroup = $(this).closest('.pengarangTambahanWrapper');
            var ind1 = formGroup.find('input[name^="pengarangTambahanRadio"]:checked').val() ?? '0';
            var value = $(this).val();
            var input = $(this).data('input');
            var relatorInput = $(this).data('relator-input');
            var relator = $(this).find('option:selected').data('relator') ?? '';

            console.log('ind1:', ind1, 'value:', value, 'relator:', relator);

            $('#' + input).attr('name', 'pengarangTambahan[' + value + '-' + ind1 + '][]');

            // ← simpan relator ke hidden input
            if (relatorInput) {
                $('#' + relatorInput).val(relator);
            }
        });

        $(document).on('change', '.pengarangTambahanRadio', function() {
            var formGroup = $(this).closest('.pengarangTambahanWrapper');
            var ind1 = $(this).val();
            var select = $(this).data('select');
            var value = $('#' + select).val();
            var input = $(this).data('input');

            $('#' + input).attr('name', 'pengarangTambahan[' + value + '-' + ind1 + '][]');
        });

        // ========== SUBJECT ==========
        var idxSubject = 0;
        $('.add-subject').click(function() {
            idxSubject++;
            var html = [];
            html.push('<div class="form-group subjectWrapper" id="subjectWrapper' + idxSubject + '">');
            html.push('<div class="form input-group">');
            html.push('<div class="input-group-prepend" style="width: 20%;">');
            html.push('<select class="custom-select subjectSelect" id="subjectSelect-' + idxSubject + '" data-input="subjectInput-' + idxSubject + '">');
            html.push('<option value="600" selected>Nama Orang</option>');
            html.push('<option value="650">Topikal</option>');
            html.push('<option value="651">Nama Geografis</option>');
            html.push('</select>');
            html.push('</div>');
            html.push('<input type="text" class="form-control" id="subjectInput-' + idxSubject + '" name="subject[600-0][]" placeholder="" value="" />');
            html.push('<div class="input-group-append">');
            html.push('<span data-id="' + idxSubject + '" class="remove-subject btn btn-outline-secondary"><i class="fa fa-minus"></i></span>');
            html.push('</div>');
            html.push('</div>');
            html.push('</div>');
            $("#subjectGroup").append(html.join(''));
        });

        $(document).on('click', '.remove-subject', function() {
            $('#subjectWrapper' + $(this).data('id')).remove();
        });

        $(document).on('change', '.subjectSelect', function() {
            var ind1 = 0;
            var value = $(this).val();
            var input = $(this).data('input');
            $('#' + input).attr('name', 'subject[' + value + '-' + ind1 + '][]');
        });

        // ========== VARIASI BENTUK JUDUL ==========
        var idxVariasiBentukJudul = 1;
        $('.add-variasiBentukJudul').click(function() {
            idxVariasiBentukJudul++;
            $('#variasiBentukJudul').append('<div id="variasiBentukJudul' + idxVariasiBentukJudul + '" class="form input-group mb-2"><input type="text" class="form-control" name="variasiBentukJudul[]" placeholder="" value="" /><div class="input-group-append"><span data-id="' + idxVariasiBentukJudul + '" class="remove-variasiBentukJudul btn btn-outline-secondary"><i class="fa fa-minus"></i></span></div></div>');
        });
        $(document).on('click', '.remove-variasiBentukJudul', function() {
            $('#variasiBentukJudul' + $(this).data('id')).remove();
        });

        // ========== JUDUL ASLI ==========
        var idxJudulAsli = 1;
        $('.add-judulAsli').click(function() {
            idxJudulAsli++;
            $('#judulAsli').append('<div id="judulAsli' + idxJudulAsli + '" class="form input-group mb-2"><input type="text" class="form-control" name="judulAsli[]" placeholder="" value="" /><div class="input-group-append"><span data-id="' + idxJudulAsli + '" class="remove-judulAsli btn btn-outline-secondary"><i class="fa fa-minus"></i></span></div></div>');
        });
        $(document).on('click', '.remove-judulAsli', function() {
            $('#judulAsli' + $(this).data('id')).remove();
        });

        // ========== FREKUENSI SEBELUMNYA ==========
        var idxFrekuensiSebelumnya = 1;
        $('.add-frekuensiSebelumnya').click(function() {
            idxFrekuensiSebelumnya++;
            $('#frekuensiSebelumnya').append('<div id="frekuensiSebelumnya' + idxFrekuensiSebelumnya + '" class="form input-group mb-2"><input type="text" class="form-control" name="frekuensiSebelumnya[]" placeholder="" value="" /><div class="input-group-append"><span data-id="' + idxFrekuensiSebelumnya + '" class="remove-frekuensiSebelumnya btn btn-outline-secondary"><i class="fa fa-minus"></i></span></div></div>');
        });
        $(document).on('click', '.remove-frekuensiSebelumnya', function() {
            $('#frekuensiSebelumnya' + $(this).data('id')).remove();
        });

        // ========== CALL NUMBER ==========
        var i = 1;
        $('.add-CallNumber').click(function() {
            i++;
            $('#CallNumber').append('<div id="CallNumber' + i + '" class="form input-group mb-2"><input type="text" class="form-control" name="CallNumber[]" placeholder="" value="" /><div class="input-group-append"><span data-id="' + i + '" class="remove-CallNumber btn btn-outline-secondary"><i class="fa fa-minus"></i></span></div></div>');
        });
        $(document).on('click', '.remove-CallNumber', function() {
            $('#CallNumber' + $(this).data('id')).remove();
        });

        // ========== ISBN ==========
        var idxISBN = 1;
        $('.add-ISBN').click(function() {
            idxISBN++;
            $('#ISBN').append('<div id="ISBN' + idxISBN + '" class="form input-group mb-2"><input type="text" class="form-control" name="ISBN[]" placeholder="" value="" /><div class="input-group-append"><span data-id="' + idxISBN + '" class="remove-ISBN btn btn-outline-secondary"><i class="fa fa-minus"></i></span></div></div>');
        });
        $(document).on('click', '.remove-ISBN', function() {
            $('#ISBN' + $(this).data('id')).remove();
        });

        // ========== CATATAN ==========
        var idxCatatan = 0;
        $('.add-catatan').click(function() {
            idxCatatan++;
            var html = [];
            html.push('<div class="form-group catatanWrapper" id="catatanWrapper' + idxCatatan + '">');
            html.push('<div class="form input-group">');
            html.push('<textarea class="form-control" id="catatanInput-' + idxCatatan + '" name="catatan[520-0][]" placeholder="" rows="1"></textarea>');
            html.push('<div class="input-group-append">');
            html.push('<span data-id="' + idxCatatan + '" class="remove-catatan btn btn-outline-secondary"><i class="fa fa-minus"></i></span>');
            html.push('</div></div>');
            html.push('<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">');
            html.push('<fieldset class="form-group"><div class="row" style="margin: 4px;">');
            html.push('<div class="col-sm-2"><div class="form-check"><label class="form-check-label"><input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="520" checked>Abstrak / Anotasi</label></div></div>');
            html.push('<div class="col-sm-2"><div class="form-check"><label class="form-check-label"><input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="502">Catatan Disertasi</label></div></div>');
            html.push('<div class="col-sm-2"><div class="form-check"><label class="form-check-label"><input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="504">Catatan Bibliografi</label></div></div>');
            html.push('<div class="col-sm-2"><div class="form-check"><label class="form-check-label"><input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="505">Catatan Isi</label></div></div>');
            html.push('<div class="col-sm-2"><div class="form-check"><label class="form-check-label"><input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="500">Catatan Umum</label></div></div>');
            html.push('</div></fieldset></div></div>');
            $("#catatanGroup").append(html.join(''));
        });

        $(document).on('click', '.remove-catatan', function() {
            $('#catatanWrapper' + $(this).data('id')).remove();
        });

        $(document).on('change', '.catatanRadio', function() {
            var value = $(this).val();
            var input = $(this).data('input');
            $('#' + input).attr('name', 'catatan[' + value + '-0][]');
        });

        // ========== WORKSHEET ==========
        $(document).on('change', "#worksheet_id", function() {
            if ($(this).val() == '4') {
                $('.terbitanBerkala').show();
            } else {
                $('.terbitanBerkala').hide();
            }
        });

        // ========== LOC COLL DARING ==========
        var idxLocCollDaring = 1;
        $('.add-LocCollDaring').click(function() {
            idxLocCollDaring++;
            $('#LocCollDaring').append('<div id="LocCollDaring' + idxLocCollDaring + '" class="form input-group mb-2"><input type="text" class="form-control" name="LocCollDaring[]" placeholder="" value="" /><div class="input-group-append"><span data-id="' + idxLocCollDaring + '" class="remove-LocCollDaring btn btn-outline-secondary"><i class="fa fa-minus"></i></span></div></div>');
        });
        $(document).on('click', '.remove-LocCollDaring', function() {
            $('#LocCollDaring' + $(this).data('id')).remove();
        });

        // ========== CHECK ALL ==========
        $('#checkAll').click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    });
$(document).ready(function() {
    // Fungsi untuk mengupdate Relator saat dropdown berubah
    $(document).on('change', '.pengarangTambahanSelect', function() {
        // 1. Ambil ID target input hidden dari atribut data-relator-input
        var targetInputId = $(this).data('relator-input');
        
        // 2. Ambil value data-relator dari option yang sedang dipilih
        var selectedRelator = $('option:selected', this).data('relator');
        
        // 3. Masukkan value tersebut ke input hidden yang sesuai
        $('#' + targetInputId).val(selectedRelator);
        
        // Debugging di console (tekan F12 untuk melihat)
        console.log("Target ID: " + targetInputId + " | Relator Value: " + selectedRelator);
    });

    // PENTING: Trigger change saat halaman pertama kali dimuat 
    // agar input hidden terisi sesuai default dropdown
    $('.pengarangTambahanSelect').trigger('change');
});
    
</script>