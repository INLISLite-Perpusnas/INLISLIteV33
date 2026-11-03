<script>
	/**
	 * Script bersama form MARC (tambah & edit). Aturan baris sama dengan slug/marc_row.php.
	 * catalogId: ID katalog saat edit ('' saat tambah) untuk nilai bawaan form ruas tetap.
	 */
	function initMarcForm(catalogId) {
		var esc = function(s) {
			return $('<div>').text(s == null ? '' : String(s)).html();
		};

		$('#field_id').select2({
			width: "resolve"
		});

		$(document).on('click', '.btn-value', function() {
			var id = $(this).data('id');
			var field_id = $(this).data('field_id');
			var tag = String($(this).data('tag'));
			var name = $(this).data('name');

			var showContent = function() {
				$('#title_content').html(`Tag ${tag} - ${name} `);
				$('#field_id_content').val(id);
				$('#dom_id_content').val(field_id);
				$('#modal_content').modal('show');
			};

			// Ruas tetap yang custom (006/007/008): isian per posisi karakter sesuai jenis bahan
			if ($(this).data('custom') == 1) {
				openRuasFixed(id, field_id, tag, name, $("#worksheet_id").val(), catalogId, showContent);
				return;
			}
			showContent();
		});

		// Baris baru dari tombol "Tambah Tag"
		var buildMarcRow = function(f) {
			var index = Date.now();
			var tag = esc(f.Tag);
			var name = esc(f.Name);
			var fixed = f.Fixed == 1;
			var enabled = f.Enabled == null || f.Enabled == 1;
			var custom = f.IsCustomable == 1;
			var showButton = (!fixed && !custom && enabled) || (fixed && custom);
			var maxlength = (fixed && parseInt(f.Length, 10) > 0) ? ` maxlength="${parseInt(f.Length, 10)}"` : '';
			var extra = { '336': '$2 rdacontent', '337': '$2 rdamedia', '338': '$2 rdacarrier' };
			var value = fixed ? '' : '$a ' + (extra[f.Tag] || '');

			var indicator = function(n) {
				if (fixed) return '<td></td>';
				return `<td>
					<div class="input-group">
						<input type="text" class="form-control" name="Indicator${n}[${tag}][]" id="Indicator${n}_${tag}_${index}" value="#" />
						<div class="input-group-append">
							<button class="btn btn bg-primary text-white btn-indicator${n}" data-id="${esc(f.ID)}" data-field_id="Indicator${n}_${tag}_${index}" data-tag="${tag}" data-name="${name}" type="button"><i class="fa fa-list"></i></button>
						</div>
					</div>
				</td>`;
			};

			return `<tr class="rm-row">
				<td class="text-center">
					<button type="button" class="btn btn-danger marc-btn-remove" data-field_id="${esc(f.ID)}"><i class="fa fa-trash"></i></button>
				</td>
				<td>
					<input type="hidden" name="Index[${tag}][]" value="Index_${tag}_${index}">
					${tag}
				</td>
				<td>${name}</td>
				${indicator(1)}
				${indicator(2)}
				<td>
					<div class="input-group">
						<input type="text" class="form-control" name="Value[${tag}][]" id="Value_${tag}_${index}" value="${esc(value)}"${enabled ? '' : ' readonly'}${maxlength} />
						${showButton ? `<div class="input-group-append">
							<button class="btn btn bg-primary text-white btn-value" data-id="${esc(f.ID)}" data-custom="${custom ? 1 : 0}" data-field_id="Value_${tag}_${index}" data-tag="${tag}" data-name="${name}" type="button"><i class="fa fa-list"></i></button>
						</div>` : ''}
					</div>
				</td>
			</tr>`;
		};

		$(document).on('click', '.marc-btn-remove', function() {
			var $this = $(this);
			$.ajax({
				url: "<?= base_url('api/katalog/remove_from_session') ?>/" + $.trim($this.data('field_id')),
				type: 'GET',
				dataType: 'json',
				success: function(response) {
					$this.closest('tr').remove();
					if ($('#field_id option[value="' + response.ID + '"]').length === 0) {
						$('#field_id').append($('<option>', {
							value: response.ID,
							text: '[' + response.Tag + '] ' + response.Name
						}));
					}
				},
				error: function(xhr, status, error) {
					console.error('API call error:', status, error);
				}
			});
		});

		$(".marc-btn-add").click(function() {
			var field_id = $('#field_id').val();
			if (!field_id) return;

			$.ajax({
				url: "<?= base_url('api/katalog/add_to_session') ?>/" + field_id,
				type: 'GET',
				dataType: 'json',
				success: function(response) {
					if (response.Repeatable < 1) {
						$('#field_id').find('option:selected').remove();
						$('#field_id').val('').trigger('change');
					}
					$('#marc-tbody').append(buildMarcRow(response));
					window.scrollTo({
						left: 0,
						top: document.body.scrollHeight,
						behavior: "smooth"
					});
				},
				error: function(xhr, status, error) {
					console.error('API call error:', status, error);
				}
			});
		});
	}
</script>
