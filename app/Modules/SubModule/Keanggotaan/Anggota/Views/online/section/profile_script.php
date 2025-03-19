<script>
$(document).ready(function() {
    $('#Provincy').change(function() {
        var propinsi_id = $(this).val();
        var uriParam = '?propinsi_id=' + propinsi_id;
        getDropdown('City', uriParam, 'Pilih', false, false);
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

<script>
var checkboxes = $('#is_similar');
checkboxes.on('ifChanged', function(event) {
    if (checkboxes.filter(':checked').length == checkboxes.length) {
        $('#AddressNow').val($('#Address').val());
        $('#KecamatanNow').val($('#Kecamatan').val());
        $('#KelurahanNow').val($('#Kelurahan').val());
        $('#RTNow').val($('#RT').val());
        $('#RWNow').val($('#RW').val());

		var provincy = $('#Provincy').val();
		var city = $('#City').val();

		$("#ProvincyNow").val(provincy).trigger('change');
		$("#CityNow").val(city).trigger('change');
    } 
});
</script>