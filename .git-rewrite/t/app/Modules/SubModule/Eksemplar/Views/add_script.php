<script>
    $(document).ready(function() {

        $('.add-judul').click(function() {
            var i = $('.form-reapet').length;
            if (i >= 0) {
                i = parseInt(i) + 1;
            }
            $('#judul-sebelumnya').append('<div id = "judul-sebelumnya' + i + '" class="form-reapet input-group colorpicker-component" title="Using format option"> <input type="text" class="form-control" name="judul_sebelumnya[]" id="judul_sebelumnya[]" placeholder="Judul Sebelumnya" value="Judul Sebelumnya' + i + '" /> <div class="input-group-append"> <span id = "' + i + '" class="remove-judul btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
            // alert(i);
        });

        // var i = 0;
        // $('.add-judul').click(function() {
        //     var jum_field = $('.form-reapet-judul-sebelumnya').length;
        //     i++
        //     if(jum_field >= 0)
        //     {
        //         jum_field = parseInt(jum_field)+1;
        //     }        
        //     $('#judul-sebelumnya').append('<div alt = "var i: '+i+' var jum_field:'+jum_field+'" id = "judul-sebelumnya'+i+'" class="form-reapet input-group colorpicker-component" title="Using format option"> <input type="text" class="form-control" name="judul_sebelumnya[]" id="judul_sebelumnya[]" placeholder="Judul Sebelumnya" value="Judul Sebelumnya'+i+'" /> <div class="input-group-append"> <span id = "'+i+'" class="remove-judul btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
        //     alert(jum_field);
        // });

        $(document).on('click', '.remove-judul', function() {
            var button_id = $(this).attr('id');
            // alert("$('#tajuk-pengarang'"+button_id+").remove()");        
            $('#judul-sebelumnya' + button_id).remove();
        });
    });

    $(document).ready(function() {
        var i = 0;
        $('.add-tajuk').click(function() {
            i++
            var html = [];
            var numItems = $('#tajuk-pengarang').children('div').length;
            if (numItems >= 0) {
                numItems = parseInt(numItems) + 1;
            }
            // var x = i-1;
            // $('#tajuk-pengarang').append('<div id = "tajuk-pengarang'+i+'" class="form input-group colorpicker-component" title="Using format option"> <input type="text" class="form-control" name="pengarang_tambahan_'+i+'" id="pengarang_tambahan_'+i+'" placeholder="Tajuk Pengarang Tambahan" value="" /> <div class="input-group-append"> <span id = "'+i+'" class="remove btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
            html.push('<div id = "tajuk-pengarang-reapet' + numItems + '" >');
            html.push('<div class="form input-group colorpicker-component">');
            html.push('<div class="input-group-prepend" style="width: 20%;">');
            html.push('<select class="reapet-cust-sel custom-select" id="' + numItems + '">');
            html.push('<option value="700" selected>Nama Orang</option>');
            html.push('<option value="710">Nama Badan</option>');
            html.push('<option value="711">Nama Pertemuan</option>');
            html.push('</select>');
            html.push('</div>');
            html.push('<input type="text" class="form-control" style="height: 38px;" name="pengarang_tambahan[' + i + '][700]" id="pengarang_tambahan_' + numItems + '" placeholder="Tajuk Pengarang Tambahan" value="Tajuk Pengarang Tambahan' + numItems + '" />');
            html.push('<div class="input-group-append" style="height: 38px;">');
            html.push('<span id = "' + numItems + '" class="remove btn btn-outline-secondary" ><i class="fa fa-minus"></i></span>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="form-radio" style="padding-bottom:15px; background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">');
            html.push('<fieldset class="form-group-tajuk-reapet" id = "' + numItems + '">');
            html.push('<div class="row" style="margin: 4px;">');
            html.push('<div class="col-sm-4">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input" type="radio" id="reapet-Radio-tajuk' + numItems + '" name="pengarang_tambahan[' + i + '][ind1]" value="0" checked>Nama Depan');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-4">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input" type="radio" id="reapet-Radio-tajuk' + numItems + '" name="pengarang_tambahan[' + i + '][ind1]" value="1" >Nama Belakang');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-4">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input" type="radio" id="reapet-Radio-tajuk' + numItems + '" name="pengarang_tambahan[' + i + '][ind1]" value="2" >Nama Keluarga');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('</div>');
            html.push('</fieldset>');
            html.push('</div>');
            html.push('</div>');

            $("#tajuk-pengarang").append(html.join(''));
            // alert('hohoho');
        });

        $(document).on('click', '.remove', function() {
            var button_id = $(this).attr('id');
            // alert("$('#tajuk-pengarang'"+button_id+").remove()");        
            $('#tajuk-pengarang-reapet' + button_id).remove();
        });

        $('#inputGroupSelect01').on('change', function() {
            var ind1 = $('input[id="Radio-tajuk"]:checked').val();
            var value = $(this).find(":selected").val();
            // alert($('#Author').attr('name'));
            $('#Author').attr('name', 'Author[' + ind1 + '][' + value + ']');
        });

        $("input[id='Radio-tajuk']").change(function() {
            var ind1 = $('input[type="radio"]:checked').val();
            var tagAuthor = $('#inputGroupSelect01').find(":selected").val();

            $('#Author').attr('name', 'Author[' + ind1 + '][' + tagAuthor + ']');
            // alert($('input[type="radio"]:checked').val());
            // alert($('#inputGroupSelect01').find(":selected").val());
        });

        $('#inputGroupSelect02').on('change', function() {
            //var ind1 = $('input[id="reapet-Radio-tajuk"]:checked').val();
            var value = $(this).find(":selected").val();
            // alert(ind1);
            $('#pengarang_tambahan').attr('name', 'pengarang_tambahan[][' + value + ']');
        });

        $("input[id='reapet-Radio-tajuk']").change(function() {
            // var ind1 = $('input[id="reapet-Radio-tajuk"]:checked').val();
            var tagAuthor = $('#inputGroupSelect02').find(":selected").val();

            $('#pengarang_tambahan').attr('name', 'pengarang_tambahan[][' + tagAuthor + ']');
            // alert(ind1);
            // alert($('#inputGroupSelect01').find(":selected").val());
        });

        // $(document).on('change', ".form-group-tajuk-reapet", function() {
        //     var id = $(this).attr("id");
        //     var tag = $('input[name="Repet-Radio-Note['+id+']"]:checked').val()

        //     // $('#Note').attr('name','new_name')
        //     $('#Note_'+id+'').attr('name','Radio-Note[]['+tag+']');
        //     // alert($('input[name="Repet-Radio-Note['+id+']"]:checked').val());
        // });

        $(document).on('change', ".reapet-cust-sel", function() {
            var attr_id = $(this).attr("id");
            //var ind1 = $('input[id="reapet-Radio-tajuk'+attr_id+'"]:checked').val();
            var value = $(this).find(":selected").val();
            //alert('pengarang_tambahan['+ind1+']['+value+']');
            $('#pengarang_tambahan_' + attr_id).attr('name', 'pengarang_tambahan[][' + value + ']');
        });

        $(document).on('change', ".form-group-tajuk-reapet", function() {
            var id = $(this).attr("id");
            // var ind1 = $('input[name="Repet-tajuk-pengarang['+id+']"]:checked').val();
            var tagAuthor = $('#' + id).find(":selected").val();

            // alert(tagAuthor);
            $('#pengarang_tambahan_' + id + '').attr('name', 'pengarang_tambahan[][' + tagAuthor + ']');
            // $('#Note').attr('name','new_name')
            // $('#Note_'+id+'').attr('name','Radio-Note[]['+tag+']');
            // alert($('input[name="Repet-Radio-Note['+id+']"]:checked').val());
        });

        // $('select').on('change', function() {
        //     alert( this.value );
        //   });
    });

    $(document).ready(function() {
        var i = 1;
        $('.add-frekuensi').click(function() {
            i++
            $('#frekuensi_sebelumnya').append('<div id = "frekuensi_sebelumnya' + i + '" class="form input-group colorpicker-component" title="Using format option"> <input type="text" class="form-control" name="frekuensi_sebelumnya[]" id="frekuensi_sebelumnya[]" placeholder="Frekuensi Publikasi Sebelumnya" value="Frekuensi Publikasi Sebelumnya' + i + '" /> <div class="input-group-append"> <span id = "' + i + '" class="remove-frekuensi_sebelumnya btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
            // alert('hohoho');
        });

        $(document).on('click', '.remove-frekuensi_sebelumnya', function() {
            var button_id = $(this).attr('id');
            // alert("$('#tajuk-pengarang'"+button_id+").remove()");        
            $('#frekuensi_sebelumnya' + button_id).remove();
        });
    });

    $(document).ready(function() {
        var i = 1;
        $('.add-eksemplar').click(function() {
            var html = [];
            i++

            html.push('<div id = "eksemplar' + i + '" class="form input-group colorpicker-component mt-2" title="Using format option"> ');
            html.push('<div class="input-group-prepend"><span class="input-group-text">No. Induk: </span></div>');
            html.push('<input type="text" class="form-control" name="Collections[NoInduk][0]" id="repeat-NoInduk' + i + '" placeholder="" value="" />');
            html.push('<div class="input-group-prepend"><span class="input-group-text">No. Barcode: </span></div>');
            html.push('<input type="text" class="form-control" name="Collections[NomorBarcode][0]" id="repeat-NomorBarcode' + i + '" placeholder="" value="" />');
            html.push('<div class="input-group-prepend"><span class="input-group-text">No. RFID: </span></div>');
            html.push('<input type="text" class="form-control" name="Collections[RFID][0]" id="repeat-RFID' + i + '" placeholder="" value="" />');
            html.push('<div class="input-group-append"> <span id = "' + i + '" class="remove-eksemplar btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div>');
            html.push('</div>');
            $("#eksemplar").append(html.join(''));
        });

        $(document).on('click', '.remove-eksemplar', function() {
            var button_id = $(this).attr('id');
            // alert("$('#tajuk-pengarang'"+button_id+").remove()");        
            $('#eksemplar' + button_id).remove();
        });
    });

    $(document).ready(function() {
        var i = 1;
        $('.add-callNumber').click(function() {
            i++
            $('#CallNumber').append('<div id = "CallNumber' + i + '" class="form input-group colorpicker-component" title="Using format option"> <input type="text" class="form-control" name="repeat-CallNumber[]" id="repeat-CallNumber[]" placeholder="No. Panggil" value="No. Panggil' + i + '" /> <div class="input-group-append"> <span id = "' + i + '" class="remove-callNumber btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
            // alert('hohoho');
        });

        $(document).on('click', '.remove-callNumber', function() {
            var button_id = $(this).attr('id');
            // alert("$('#tajuk-pengarang'"+button_id+").remove()");        
            $('#CallNumber' + button_id).remove();
        });
    });

    $(document).ready(function() {
        var i = 1;
        $('.add-ISBN').click(function() {
            i++
            $('#ISBN').append('<div id = "ISBN' + i + '" class="form input-group colorpicker-component" title="Using format option"> <input type="text" class="form-control" name="ISBN[]" id="ISBN[]" placeholder="ISSN" value="ISSN' + i + '" /> <div class="input-group-append"> <span id = "' + i + '" class="remove-ISBN btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
            // alert('hohoho');
        });

        $(document).on('click', '.remove-ISBN', function() {
            var button_id = $(this).attr('id');
            // alert("$('#tajuk-pengarang'"+button_id+").remove()");        
            $('#ISBN' + button_id).remove();
        });
    });

    $(document).ready(function() {
        var i = 1;
        $('.add-Note').click(function() {
            var html = [];
            i++
            // $('#Note').append('<div class="form-group" id="Note-group"> <div id = "Note'+i+'" class="form-radio colorpicker-component"> <input type="text" class="form-control" name="Note_'+i+'" id="Note_'+i+'" placeholder="Catatan" value="" /> <div class="input-group-append"> <span id = "'+i+'" class="remove-Note btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div> </div>');
            html.push('<div id="Note' + i + '" class="form-radio-group colorpicker-component">');
            html.push('<div class="form-radio input-group colorpicker-component" title="Using format option">');
            html.push('<input type="text" class="form-control" name="Radio-Note[][520]" id="Note_' + i + '" placeholder="Catatan" value="Catatan' + i + '" />');
            html.push('<div class="input-group-append">');
            html.push('<span id = "' + i + '" class="remove-Note btn btn-outline-secondary"><i class="fa fa-minus"></i></span>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">');
            html.push('<fieldset class="form-group-reapet" id = "' + i + '">');
            html.push('<div class="row" style="margin: 4px;">');
            html.push('<div class="col-sm-3">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input" type="radio" name="Repet-Radio-Note[' + i + ']" value="520" checked>Abstrak / Anotasi');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-3">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input" type="radio" name="Repet-Radio-Note[' + i + ']" value="502" >Catatan Disertasi');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-3">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input" type="radio" name="Repet-Radio-Note[' + i + ']" value="504" >Catatan Bibliografi');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-3">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input" type="radio" name="Repet-Radio-Note[' + i + ']" value="505" >Rincian Isi');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('<div class="col-sm-3">');
            html.push('<div class="form-check">');
            html.push('<label class="form-check-label">');
            html.push('<input class="form-check-input" type="radio" name="Repet-Radio-Note[' + i + ']" value="500" >Catatan Umum');
            html.push('</label>');
            html.push('</div>');
            html.push('</div>');
            html.push('</div>');
            html.push('</fieldset>');
            html.push('</div>');
            $("#Note-group").append(html.join(''));
            // alert('hohoho');
        });

        $(document).on('click', '.remove-Note', function() {
            var button_id = $(this).attr('id');
            // alert("$('#tajuk-pengarang'"+button_id+").remove()");        
            $('#Note' + button_id).remove();
        });

        // $("input[name='gridRadios']").change(function(){
        //     $('#Note').attr('name','new_name')
        // });

        $("input[id='Radio-Note1']").change(function() {
            var tag = $('input[id="Radio-Note1"]:checked').val();

            $('#Note_fix').attr('name', 'Radio-Note[][' + tag + ']');
        });

        $(document).on('change', ".form-group-reapet", function() {
            var id = $(this).attr("id");
            var tag = $('input[name="Repet-Radio-Note[' + id + ']"]:checked').val()

            // $('#Note').attr('name','new_name')
            $('#Note_' + id + '').attr('name', 'Radio-Note[][' + tag + ']');
            // alert($('input[name="Repet-Radio-Note['+id+']"]:checked').val());
        });

        // $('fieldset').on('change', function() {
        //     alert('asdasdasd');
        //   });
    });

    $(document).ready(function() {
        var i = 1;
        $('.add-LocCollDaring').click(function() {
            i++
            $('#LocCollDaring').append('<div id = "LocCollDaring' + i + '" class="form input-group colorpicker-component" title="Using format option"> <input type="text" class="form-control" name="LocCollDaring[]" id="LocCollDaring_' + i + '" placeholder="Lokasi Koleksi Daring" value=" Lokasi Koleksi Daring' + i + '" /> <div class="input-group-append"> <span id = "' + i + '" class="remove-LocCollDaring btn btn-outline-secondary"><i class="fa fa-minus"></i></span> </div> </div>');
            // alert('hohoho');
        });

        $(document).on('click', '.remove-LocCollDaring', function() {
            var button_id = $(this).attr('id');
            // alert("$('#tajuk-pengarang'"+button_id+").remove()");        
            $('#LocCollDaring' + button_id).remove();
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

    $('#proses').on('submit', function() {
        // var ind1 = $('input[id="Radio-tajuk"]:checked').val();
        // var value = $(this).find(":selected").val();
        // alert('hohohoohoh');
        // $('#Author').attr('name','Author['+ind1+']['+value+']');
    });
</script>