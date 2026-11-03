<div class="modal fade" id="modal_fixed" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title_fixed"></h5>
				<button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="card-body">
				<input type="hidden" id="dom_id_fixed" value="">
				<div id="ruasfixed-grid" class="row"></div>
			</div>
			<div class="card-footer">
				<button type="button" class="btn btn-primary copy-fixed">OK</button>
				<button type="button" class="btn btn-warning ml-2" data-bs-dismiss="modal" data-dismiss="modal">Batal</button>
			</div>
		</div>
	</div>
</div>

<script>
	/**
	 * Form isian ruas tetap (006/007/008) per posisi karakter, setara _setRuasFixed INLISLite 3.
	 * Bila jenis bahan tidak punya definisi unsur untuk tag tsb, onEmpty() dipanggil (modal subruas biasa).
	 */
	function openRuasFixed(fieldId, domId, tag, name, worksheetId, catalogId, onEmpty) {
		var url = `<?= base_url('api/katalog/get_field_fixed') ?>/${fieldId}`;
		$.get(url, {
			worksheet_id: worksheetId,
			catalog_id: catalogId || ''
		}, function(res) {
			if (!res.items || res.items.length === 0) {
				onEmpty();
				return;
			}

			// Nilai lama di input (awalan "$a" dari placeholder/data lama diabaikan)
			var current = String($('#' + domId).val() || '').replace(/^\$a\s*/, '');
			var esc = function(s) {
				return $('<div>').text(s == null ? '' : String(s)).html();
			};
			var html = '';

			$.each(res.items, function(i, item) {
				var start = parseInt(item.StartPosition, 10) || 0;
				var length = parseInt(item.Length, 10) || 1;
				var value = current.substr(start, length);
				value = value !== '' ? value.replace(/[ *]/g, '#') : '';
				if (value === '') value = item.DefaultValue || '';

				// Bawaan 008: tanggal entri, jenis tahun terbit & tahun 1 dari data katalog
				if (tag == '008' && value === item.DefaultValue) {
					if (start === 0) value = res.createdate;
					else if (start === 6 && res.is_catalog) value = 's';
					else if (start === 7 && res.is_catalog && res.publishyear) value = res.publishyear;
				}

				var hidden = item.Name === 'Belum ditetapkan';
				html += '<div class="col-md-4 mb-3"' + (hidden ? ' style="visibility:hidden"' : '') + '>';
				html += '<div style="border:1px solid #CCCCCC; padding:10px; background-color:#faffe1; height:100%">';
				html += '<b>' + esc(hidden ? '' : item.Name) + '</b> :';
				html += '<input type="text" class="form-control ruasfixed-input mt-1" maxlength="' + length + '" data-length="' + length + '" value="' + esc(value) + '">';

				if (item.RefferenceMode === 'Dropdown' && item.options.length > 0) {
					html += '<select class="form-control ruasfixed-select mt-2">';
					var matched = false;
					$.each(item.options, function(j, opt) {
						var selected = !matched && String(opt.Code).replace(/#/g, '') === value.replace(/#/g, '');
						if (selected) matched = true;
						html += '<option value="' + esc(opt.Code) + '"' + (selected ? ' selected' : '') + '>' + esc(opt.Code) + ' - ' + esc(opt.Name) + '</option>';
					});
					html += '</select>';
				}
				if (item.IdemTag) {
					html += '<br><i>Sama dengan Tag ' + esc(item.IdemTag) + ' Posisi ' + esc(item.IdemStartPosition) + '. Panjang : ' + length + '</i>';
				}
				html += '</div></div>';
			});

			$('#title_fixed').html(`Tag ${tag} - ${esc(name)}`);
			$('#dom_id_fixed').val(domId);
			$('#ruasfixed-grid').html(html);
			$('#modal_fixed').modal('show');
		}).fail(function() {
			onEmpty();
		});
	}

	$(document).on('change', '.ruasfixed-select', function() {
		$(this).prevAll('.ruasfixed-input').first().val($(this).val());
	});

	// Gabungkan semua unsur, tiap unsur dipadatkan dengan '#' sesuai panjangnya
	$('body').on('click', '.copy-fixed', function() {
		var result = '';
		$('#ruasfixed-grid .ruasfixed-input').each(function() {
			var length = parseInt($(this).data('length'), 10) || 1;
			var item = String($(this).val() || '').substr(0, length);
			while (item.length < length) item += '#';
			result += item;
		});

		$('#' + $('#dom_id_fixed').val()).val(result);
		$('#modal_fixed').modal('hide');
	});
</script>
