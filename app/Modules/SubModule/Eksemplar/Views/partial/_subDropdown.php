<?php
switch ($processid) {

    default:
    case 'OPAC1':
    case 'OPAC0':
    case 'KERANJANG0':
    case 'KERANJANG1':
    case 'KARANTINA':
        # code...
        break;
    case '2':
        $select = '<table id="subTable" style="width:100%">';
        $select .= '<tbody>';
        $select .= '<tr>';
        $select .= '<td>Piih sumber No. Panggil</td>';
        $select .= '<td>';
        $select .= '<div class="input-group">';
        $select .= '<label class="radio-inline">';
        $select .= '<input type="radio" name="optradio" id="katalog" value="catalogs" checked>Katalog';
        $select .= '</label>';
        $select .= '<label class="radio-inline" style="margin-left: 10px;">';
        $select .= '<input type="radio" name="optradio" id="koleksi" value="collections">Koleksi';
        $select .= '</label>';
        $select .= '</div>';
        $select .= '</td>';
        $select .= '</tr>';
        $select .= '<tr>';
        $select .= '<td>Pilih ukuran kertas</td>';
        $select .= '<td>';
        $select .= '<select class="form-control" id="ukuranKertas" name="ukuranKertas">';
        // $select .= '<option value="label-roll">';
        // $select .= 'Kertas Label Roll';
        // $select .= '</option>';
        $select .= '<option value="a4">';
        $select .= 'Kertas A4';
        $select .= '</option>';
        $select .= '</select>';
        $select .= '</td>';
        $select .= '</tr>';
        $select .= '<tr>';
        $select .= '<td>Piih model</td>';
        $select .= '<td>';
        $select .= '<div id="label-model">';
        $select .= '<select class="form-control" id="model_kertas" name="model_kertas">';
        $select .= '<option value="a4-1">';
        $select .= 'Model A4-1 (No. Panggil + Barcode)';
        $select .= '</option>';
        $select .= '<option value="a4-2">';
        $select .= 'Model A4-2 (No. Panggil + Barcode)';
        $select .= '</option>';
        $select .= '<option value="a4-3">';
        $select .= 'Model A4-3 (No. Panggil + Barcode + 1 Warna)';
        $select .= '</option>';
        $select .= '<option value="a4-4">';
        $select .= 'Model A4-4 (No. Panggil + Barcode + 1 Warna)';
        $select .= '</option>';
        $select .= '<option value="a4-5">';
        $select .= 'Model A4-5 (No. Panggil + Barcode)';
        $select .= '</option>';
        // $select .= '<option value="a4-6">';
        // $select .= 'Model A4-6 (No. Panggil + Barcode)';
        // $select .= '</option>';
        // 'a4-7' => 'Model A4-7 (No. Panggil + Barcode + 1 Warna)',
        // 'a4-8' => 'Model A4-8 (No. Panggil + Barcode + 1 Warna)',
        // 'a4-9' => 'Model A4-9 (No. Panggil + Barcode + 5 Warna)',
        // 'a4-10' => 'Model A4-10 (No. Panggil Rata Kiri + Barcode)',
        // 'a4-11' => 'Model A4-11 (No. Panggil + 5 Warna)',
        // 'a4-12' => 'Model A4-12 (Barcode + Judul)'
        $select .= '</option>';
        $select .= '</select>';
        $select .= '</div>';
        $select .= '</td>';
        $select .= '<tr>';
        $select .= '<td>Piih format dokumen</td>';
        $select .= '<td>';
        $select .= '<select class="form-control" id="format_dokumen" name="format_dokumen">';
        $select .= '<option value="pdf">';
        $select .= 'Portable Document Format (Pdf)';
        $select .= '</option>';
        $select .= '</select>';
        $select .= '</td>';
        $select .= '</tr>';
        $select .= '</table>';
        echo $select;
        break;
}
?>
<script>
    $(document).ready(function() {
        $("#ukuranKertas").on("change", function() {
            var value = $(this).val();
            // alert(id);
            $.ajax({
                type: "POST",
                url: "collections/GetDropdownLabelmodel",
                data: {
                    value: value
                },
                success: function(data) {
                    $("#label-model").html(data);
                }
            });
        });
    });
</script>