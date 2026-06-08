<script>
    $(document).ready(function() {
        $('.pengarangUtamaSelect').on('change', function() {
            var formGroup = $(this).closest('.pengarangUtamaWrapper');
            var ind1 = formGroup.find('input[name^="pengarangUtamaRadio"]:checked').val();
            var value = $(this).val();

            // Log or use the values as needed
            console.log('Selected ind1:', ind1);
            console.log('Selected value:', value);

            // Assuming the form control is a direct child of the form-group
            var formControl = formGroup.find('.form-control');
            formControl.attr('name', 'pengarangUtama[' + value + '-' + ind1 + '][]');
        });

        $('.pengarangUtamaRadio').on('change', function() {
            var formGroup = $(this).closest('.pengarangUtamaWrapper');
            var ind1 = $(this).val();
            var target = $(this).data('id');
            var value = $('#' + target).val();

            // Log or use the values as needed
            console.log('Selected ind1:', ind1);
            console.log('Selected value:', value);

            // Assuming the form control is a direct child of the form-group
            var formControl = formGroup.find('.form-control');
            formControl.attr('name', 'pengarangUtama[' + value + '-' + ind1 + '][]');
        });

        var idxPengarangTambahan = 0;
        $('.add-pengarangTambahan').click(function() {
            idxPengarangTambahan++
            var html = [];
            html.push('<div class="form-group pengarangTambahanWrapper mb-2" id="pengarangTambahanWrapper' + idxPengarangTambahan + '">');
            html.push('<div class="form input-group">');
            html.push('<div class="input-group-prepend" style="width: 20%;">');
            html.push('<select class="custom-select pengarangTambahanSelect" id="pengarangTambahanSelect-' + idxPengarangTambahan + '" data-input="pengarangTambahanInput-' + idxPengarangTambahan + '" >');
            html.push('<option value="700" selected>Nama Orang</option>');
            html.push('<option value="710">Nama Badan</option>');
            html.push('<option value="711">Nama Pertemuan</option>');
            html.push('</select>');
            html.push('</div>');
            html.push('<input type="text" class="form-control" id="pengarangTambahanInput-' + idxPengarangTambahan + '" name="pengarangTambahan[700-0][]" placeholder="" value="" />');
            html.push('<div class="input-group-append">');
            html.push('<span data-id="' + idxPengarangTambahan + '" class="remove-pengarangTambahan btn btn-outline-secondary" ><i class="fa fa-minus"></i></span>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">');
            html.push('<fieldset class="form-group">');
            html.push('<div class="row" style="margin: 4px;">');
            html.push('<div class="col-sm-4">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect-' + idxPengarangTambahan + '" data-input="pengarangTambahanInput-' + idxPengarangTambahan + '" name="pengarangTambahanRadio[' + idxPengarangTambahan + '][]" value="0" checked>Nama Depan');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-4">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect-' + idxPengarangTambahan + '" data-input="pengarangTambahanInput-' + idxPengarangTambahan + '" name="pengarangTambahanRadio[' + idxPengarangTambahan + '][]" value="1" >Nama Belakang');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-4">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect-' + idxPengarangTambahan + '" data-input="pengarangTambahanInput-' + idxPengarangTambahan + '" name="pengarangTambahanRadio[' + idxPengarangTambahan + '][]" value="2" >Nama Keluarga');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('</div>');
            html.push('</fieldset>');
            html.push('</div>');
            html.push('</div>');

            $("#pengarangTambahanGroup").append(html.join(''));
        });

        $(document).on('click', '.remove-pengarangTambahan', function() {
            var id = $(this).data('id');
            $('#pengarangTambahanWrapper' + id).remove();
        });

        $(document).on('change', '.pengarangTambahanSelect', function() {
            var formGroup = $(this).closest('.pengarangTambahanWrapper');
            var ind1 = formGroup.find('input[name^="pengarangTambahanRadio"]:checked').val();
            var value = $(this).val();
            var input = $(this).data('input');

            // Log or use the values as needed
            console.log('Selected ind1:', ind1);
            console.log('Selected value:', value);

            // Assuming the form control is a direct child of the form-group
            var formInput = $('#' + input);
            formInput.attr('name', 'pengarangTambahan[' + value + '-' + ind1 + '][]');
        });

        $(document).on('change', '.pengarangTambahanRadio', function() {
            var formGroup = $(this).closest('.pengarangTambahanWrapper');
            var ind1 = $(this).val();
            var select = $(this).data('select');
            var value = $('#' + select).val();
            var input = $(this).data('input');

            // Log or use the values as needed
            console.log('Selected ind1:', ind1);
            console.log('Selected value:', value);

            // Assuming the form control is a direct child of the form-group
            var formInput = $('#' + input);
            formInput.attr('name', 'pengarangTambahan[' + value + '-' + ind1 + '][]');
        });
    });

    $(document).ready(function() {
        var idxSubject = 0;
        $('.add-subject').click(function() {
            idxSubject++
            var html = [];
            html.push('<div class="form-group subjectWrapper" id="subjectWrapper' + idxSubject + '">');
            html.push('<div class="form input-group">');
            html.push('<div class="input-group-prepend" style="width: 20%;">');
            html.push('<select class="custom-select subjectSelect" id="subjectSelect-' + idxSubject + '" data-input="subjectInput-' + idxSubject + '" >');
            html.push('<option value="600" selected>Nama Orang</option>');
            html.push('<option value="650">Topikal</option>');
            html.push('<option value="651">Nama Geografis</option>');
            html.push('</select>');
            html.push('</div>');
            html.push('<input type="text" class="form-control" id="subjectInput-' + idxSubject + '" name="subject[600-0][]" placeholder="" value="" />');
            html.push('<div class="input-group-append">');
            html.push('<span data-id="' + idxSubject + '" class="remove-subject btn btn-outline-secondary" ><i class="fa fa-minus"></i></span>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="form input-group" title=""></div>');
            html.push('</div>');

            $("#subjectGroup").append(html.join(''));
        });

        $(document).on('click', '.remove-subject', function() {
            var id = $(this).data('id');
            $('#subjectWrapper' + id).remove();
        });

        $(document).on('change', '.subjectSelect', function() {
            var formGroup = $(this).closest('.subjectWrapper');
            var ind1 = 0;
            var value = $(this).val();
            var input = $(this).data('input');

            // Log or use the values as needed
            console.log('Selected ind1:', ind1);
            console.log('Selected value:', value);

            // Assuming the form control is a direct child of the form-group
            var formInput = $('#' + input);
            formInput.attr('name', 'subject[' + value + '-' + ind1 + '][]');
        });
    });

    $(document).ready(function() {
        var idxVariasiBentukJudul = 1;
        $('.add-variasiBentukJudul').click(function() {
            idxVariasiBentukJudul++
            $('#variasiBentukJudul').append('<div id = "variasiBentukJudul' + idxVariasiBentukJudul + '" class="form input-group mb-2" title=""> <input type="text" class="form-control" name="variasiBentukJudul[]" placeholder="" value="" /> <div class="input-group-append"> <span data-id = "' + idxVariasiBentukJudul + '" class="remove-variasiBentukJudul btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
        });

        $(document).on('click', '.remove-variasiBentukJudul', function() {
            var id = $(this).data('id');
            $('#variasiBentukJudul' + id).remove();
        });
    });

    $(document).ready(function() {
        var idxJudulAsli = 1;
        $('.add-judulAsli').click(function() {
            idxJudulAsli++
            $('#judulAsli').append('<div id = "judulAsli' + idxJudulAsli + '" class="form input-group mb-2" title=""> <input type="text" class="form-control" name="judulAsli[]" placeholder="" value="" /> <div class="input-group-append"> <span data-id = "' + idxJudulAsli + '" class="remove-judulAsli btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
        });

        $(document).on('click', '.remove-judulAsli', function() {
            var id = $(this).data('id');
            $('#judulAsli' + id).remove();
        });
    });

    $(document).ready(function() {
        var idxFrekuensiSebelumnya = 1;
        $('.add-frekuensiSebelumnya').click(function() {
            idxFrekuensiSebelumnya++
            $('#frekuensiSebelumnya').append('<div id = "frekuensiSebelumnya' + idxFrekuensiSebelumnya + '" class="form input-group mb-2" title=""> <input type="text" class="form-control" name="frekuensiSebelumnya[]" placeholder="" value="" /> <div class="input-group-append"> <span data-id = "' + idxFrekuensiSebelumnya + '" class="remove-frekuensiSebelumnya btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
        });

        $(document).on('click', '.remove-frekuensiSebelumnya', function() {
            var id = $(this).data('id');
            $('#frekuensiSebelumnya' + id).remove();
        });
    });

    $(document).ready(function() {
        var i = 1;
        $('.add-CallNumber').click(function() {
            i++
            $('#CallNumber').append('<div id = "CallNumber' + i + '" class="form input-group mb-2" title=""> <input type="text" class="form-control" name="CallNumber[]" placeholder="" value="" /> <div class="input-group-append"> <span data-id = "' + i + '" class="remove-CallNumber btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');

        });

        $(document).on('click', '.remove-CallNumber', function() {
            var id = $(this).data('id');
            $('#CallNumber' + id).remove();
        });
    });

    $(document).ready(function() {
        var idxISBN = 1;
        $('.add-ISBN').click(function() {
            idxISBN++
            $('#ISBN').append('<div id = "ISBN' + idxISBN + '" class="form input-group mb-2" title=""> <input type="text" class="form-control" name="ISBN[]" placeholder="" value="" /> <div class="input-group-append"> <span data-id = "' + idxISBN + '" class="remove-ISBN btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');

        });

        $(document).on('click', '.remove-ISBN', function() {
            var id = $(this).data('id');
            $('#ISBN' + id).remove();
        });
    });

    $(document).ready(function() {
        var idxCatatan = 0;
        $('.add-catatan').click(function() {
            idxCatatan++
            var html = [];
            html.push('<div class="form-group catatanWrapper" id="catatanWrapper' + idxCatatan + '">');
            html.push('<div class="form input-group">');
            html.push('<textarea class="form-control" id="catatanInput-' + idxCatatan + '" name="catatan[520-0][]" placeholder="" rows="1"></textarea>');
            html.push('<div class="input-group-append">');
            html.push('<span data-id="' + idxCatatan + '" class="remove-catatan btn btn-outline-secondary" ><i class="fa fa-minus"></i></span>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">');
            html.push('<fieldset class="form-group">');
            html.push('<div class="row" style="margin: 4px;">');
            html.push('<div class="col-sm-2">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="520" checked>Abstrak / Anotasi');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-2">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="502" >Catatan Disertasi');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-2">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="504" >Catatan Bibliografi');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-2">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="505" >Catatan Isi');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-2">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-' + idxCatatan + '" name="catatanRadio[' + idxCatatan + ']" value="500" >Catatan Umum');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('</div>');
            html.push('</fieldset>');
            html.push('</div>');
            html.push('</div>');

            $("#catatanGroup").append(html.join(''));
        });

        $(document).on('click', '.remove-catatan', function() {
            var id = $(this).data('id');
            $('#catatanWrapper' + id).remove();
        });

        $(document).on('change', '.catatanRadio', function() {
            var formGroup = $(this).closest('.catatanWrapper');
            var ind1 = '0';
            var value = $(this).val();
            var input = $(this).data('input');

            // Log or use the values as needed
            console.log('Selected ind1:', ind1);
            console.log('Selected value:', value);

            // Assuming the form control is a direct child of the form-group
            var formInput = $('#' + input);
            formInput.attr('name', 'catatan[' + value + '-' + ind1 + '][]');
        });

        $(document).on('change', "#worksheet_id", function() {
            var selected = $(this).val();
            if (selected == '4') {
                $('.terbitanBerkala').show();
            } else {
                $('.terbitanBerkala').hide();
            }
        });
    });

    $(document).ready(function() {
        var idxLocCollDaring = 1;
        $('.add-LocCollDaring').click(function() {
            idxLocCollDaring++
            $('#LocCollDaring').append('<div id = "LocCollDaring' + idxLocCollDaring + '" class="form input-group mb-2" title=""> <input type="text" class="form-control" name="LocCollDaring[]" placeholder="" value="" /> <div class="input-group-append"> <span data-id = "' + idxLocCollDaring + '" class="remove-LocCollDaring btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');

        });

        $(document).on('click', '.remove-LocCollDaring', function() {
            var id = $(this).data('id');
            $('#LocCollDaring' + id).remove();
        });
    });

    $(document).ready(function() {
        $('#checkAll').click(function() {
            if ($(this).is(":checked")) {
                $('input:checkbox').not(this).prop('checked', this.checked);
            } else if ($(this).is(":not(:checked)")) {
                $('input:checkbox').not(this).prop('checked', this.checked);
            }
        });
    });
</script>