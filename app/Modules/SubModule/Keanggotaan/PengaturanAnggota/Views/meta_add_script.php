<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<script>
	$(document).on('click', '.item-btn-remove', function() {
		var url = $(this).data('href');
		var row = $(this).closest('tr');

		row.remove();
		return false;
	});

	function addIndicator(key, value) {
		var index = get_unique_id(6);
		$('#item-tbody').append(`
			<tr class="rm-row">
				<td width="300">
					<input type="text" class="form-control" name="key[` + index + `]" placeholder="key" value="` + key + `" />
				</td>
				<td>
					<input type="hidden" name="index[]" value="` + index + `">
					<input type="text" class="form-control" name="value[` + index + `]" placeholder="value" value="` + value + `" />
				</td>
				<td width="100" class="text-left">
					<button type="button" class="btn btn-danger item-btn-remove" data-href=""><i class="fa fa-trash"></i></button>
				</td>
			</tr>
		`);

	}

	$(".item-btn-add").click(function() {
		addIndicator('', '');
	});
</script>