<script>
	var ROOTPATH = '/';
	var WRITEPATH = '/writable/';
	var BASEURL = '<?= base_url() ?>';

	$('.select2').select2({
		theme: "bootstrap4",
		width: "100%",
	});
	$('.tags').select2({
		theme: "bootstrap4",
		width: "100%",
		allowClear: true,
		tags: true,
		tokenSeparators: [',']
	});

	/* Sidebar */
	$('.close-sidebar-btn').click(function() {
		var classToSwitch = $(this).attr('data-class');
		var containerElement = '.app-container';
		$(containerElement).toggleClass(classToSwitch);

		var closeBtn = $(this);

		if (closeBtn.hasClass('is-active')) {
			closeBtn.removeClass('is-active');
			$('.user-profile').show();
		} else {
			closeBtn.addClass('is-active');
			$('.user-profile').hide();
		}
	});

	function toggleUserProfile() {
		var closeBtn = $('.app-container');

		if (closeBtn.hasClass('closed-sidebar')) {
			closeBtn.removeClass('closed-sidebar');
			$('.user-profile').hide();
		} else {
			closeBtn.addClass('closed-sidebar');
			$('.user-profile').show();
		}
	}

	// $(document).ready(function() {
	// 	toggleUserProfile();
	// });

	/* SweetAlert - Flash global (swal_icon / swal_title / swal_text) */
	<?php $swal_icon = session()->getFlashdata('swal_icon'); ?>
	<?php if ($swal_icon): ?>
	Swal.fire({
		icon:  '<?= esc($swal_icon) ?>',
		title: '<?= esc(session()->getFlashdata('swal_title') ?? '') ?>',
		text:  '<?= esc(session()->getFlashdata('swal_text')  ?? '') ?>',
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
	});
	<?php endif; ?>

	/* SweetAlert - Permission Denied */
	<?php $swal_permission = session()->getFlashdata('swal_permission'); ?>
	<?php if (is_array($swal_permission)): ?>
	Swal.fire({
		icon: '<?= esc((string)($swal_permission['icon']  ?? '')) ?>',
		title: '<?= esc((string)($swal_permission['title'] ?? '')) ?>',
		text: '<?= esc((string)($swal_permission['text']  ?? '')) ?>',
		confirmButtonColor: '#d33',
		timer: 4000,
		timerProgressBar: true,
	});
	<?php endif; ?>

	/* Toastr */
	var toastr_msg = '<?= get_message('toastr_msg'); ?>';
	var toastr_type = '<?= get_message('toastr_type'); ?>';

	if (toastr_msg.length > 0) {
		toastr.options = {
			"closeButton": true,
			"debug": false,
			"newestOnTop": true,
			"progressBar": true,
			"positionClass": "toast-top-full-width",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "3500",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};
		toastr[toastr_type](toastr_msg, "Information");
	}

	/* Magnific Popup */
	$('.ajax-popup-link').magnificPopup({
		type: 'iframe',
		iframe: {
			markup: '<style>.mfp-iframe-holder .mfp-content {max-width: 95%;height:95%}</style>' +
				'<div class="mfp-iframe-scaler" >' +
				'<div class="mfp-close"></div>' +
				'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
				'</div></div>'
		},
	});

	$('.image-link').magnificPopup({
		type: 'image'
	});

	$('.youtube-link').magnificPopup({
		type: 'iframe',
		iframe: {
			markup: `
				<style>.mfp-iframe-holder .mfp-content {max-width: 95%;height:95%}</style>
				<div class="mfp-iframe-scaler" >
					<div class="mfp-close"></div>
						<iframe class="mfp-iframe" frameborder="5" allow="autoplay;" allowfullscreen></iframe>' +
					</div>
				</div>`,
		},
	});

	function youtube_parser(url) {
		var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
		var match = url.match(regExp);
		return (match && match[7].length == 11) ? match[7] : false;
	}

	function image_url(path) {
		return 'https://www.perpusnas.go.id/' + path;
	}

	/* Data Table*/
	function setDataTable(dom, disableOrderCols = [4, 6, 7], defaultOrderCols = [0, 'asc'], autoNumber = false) {
		var t = $(dom).DataTable({
			"dom": "<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'f><'col-md-6 col-sm-4 col-xs-12 d-none d-sm-block text-right'p>>" +
				"<'row'<'col-md-12'tr>>" +
				"<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12 text-right'i>>",
			"pagingType": "full",
			"oLanguage": {
				"sSearch": "<i class='fa fa-search'></i> _INPUT_",
				"sLengthMenu": "_MENU_",
				// "sInfo": "_START_ - _END_ of _TOTAL_",
				"oPaginate": {
					"sNext": "<i class='fa fa-chevron-right'></i>",
					"sPrevious": "<i class='fa fa-chevron-left'></i>",
					"sLast": "<i class='fa fa-chevron-double-right'></i>",
					"sFirst": "<i class='fa fa-chevron-double-left'></i>",
				}
			},
			"drawCallback": function(settings) {
				$('.apply-status').bootstrapToggle();

				$(".apply-status").on('change', function() {
					var href = $(this).attr('data-href');
					var field = $(this).attr('data-field');
					var id = $(this).attr('data-id');
					var switchStatus = $(this).is(':checked');

					if (switchStatus) {
						var url = href + '/' + id + '?field=' + field + '&value=1';
						window.location.href = url;
					} else {
						var url = href + '/' + id + '?field=' + field + '&value=0';
						window.location.href = url;
					}
				});
			},
			"columnDefs": [{
				"searchable": false,
				"orderable": false,
				"targets": disableOrderCols
			}],
			"order": [
				defaultOrderCols
			]
		});

		if (autoNumber) {
			t.on('order.dt search.dt', function() {
				t.column(0, {
					search: 'applied',
					order: 'applied'
				}).nodes().each(function(cell, i) {
					cell.innerHTML = i + 1;
				});
			}).draw();
		}

		return t;
	}

	/* Parameter*/
	function setParameter(name, value) {
		var data_post = {
			name: name,
			value: value,
			'<?= csrf_token() ?>': '<?= csrf_hash() ?>',
		};

		$('.loading').show();

		$.ajax({
				url: '<?= base_url('api/parameter/create') ?>',
				type: 'POST',
				dataType: 'json',
				data: data_post,
			})
			.done(function(res) {
				window.location.reload();
			})
			.fail(function(res) {
				Swal.fire({
					title: 'Oups',
					text: 'Parameter gagal disimpan',
					type: 'warning',
					showConfirmButton: false,
					timer: 3000
				});
			})
			.always(function() {
				$('.loading').hide();
			});

		return false;
	}

	/* Dropzone*/
	Dropzone.autoDiscover = false;

	function setDropzone(domID, routePath, acceptedFiles = 'application/pdf', maxFiles = 1, maxFilesize = 10, writePath = false) {
		var baseUrl = BASEURL + "/" + routePath;
		if (maxFiles > 1) {
			url = baseUrl + "/do_upload";
			paramName = "file";
		} else {
			url = baseUrl + "/do_upload";
			paramName = "file";
		}

		var myDropzone = new Dropzone('#' + domID, {
			dictDefaultMessage: "<span class='text-muted'><i class='material-icons mt-2'>cloud_upload</i><br>DRAG &amp; DROP FILES HERE OR CLICK TO UPLOAD</span>",
			url: url,
			paramName: paramName,
			maxFiles: maxFiles,
			maxFilesize: maxFilesize,
			addRemoveLinks: true,
			acceptedFiles: acceptedFiles,
			renameFile: function(file) {
				var filename = new Date().getTime() + '_' + file.name.toLowerCase().replace(' ', '_');
				console.log("renameFile");
				console.log(filename);

				return filename;
			},
			accept: function(file, done) {
				console.log("accept");
				console.log(file);
				done();
			},
			init: function() {
				this.on("maxfilesexceeded", function(file) {
					console.log("max files exceeded");
					console.log(file);
				});
			},
			success: function(file, response) {
				console.log('success');
				console.log(response);

				var uuid = file.upload.uuid;
				var value = response.data.name;
				var name = domID + '[' + uuid + ']';

				$('#' + domID + '_listed').append('<input type="hidden" name="' + name + '" value="' + value + '" />');
			},
			removedfile: function(file) {
				console.log('removedfile');
				// console.log(file);
				var name = "";

				var path = WRITEPATH + '/' + 'uploads';

				if (writePath) {
					path = writePath;
				}

				if (file.upload !== undefined) {
					name = file.upload.filename;
				} else {
					name = file.name;
					path = ROOTPATH + '/public/uploads/' + routePath;
				}

				$.ajax({
					type: 'POST',
					url: baseUrl + "/do_delete",
					data: "name=" + name + "&path=" + path,
					dataType: 'html'
				});

				$('input[name="' + domID + '[' + file.upload.uuid + ']"]').remove();

				var _ref;
				return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
				return true;

			}
		});

		return myDropzone;
	}

	function initDropzones() {
		Dropzone.autoDiscover = false;
		$('.dropzone').each(function() {
			let dropzoneControl = $(this)[0].dropzone;
			if (dropzoneControl) {
				dropzoneControl.destroy();
			}
		})
	}

	/* Unique ID */
	function get_unique_id(length) {
		var result = '';
		var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for (var i = 0; i < length; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}

	/* Axios */
	const getData = async (url, dom, selected = false, label = '-All-') => {
		await axios.get(url)
			.then(res => {
				$(dom).html('<option value="">Loading...</option>');
				var output = '<option value="">' + label + '</option>';
				$.each(res.data, function(key, val) {
					output += '<option value="' + val.code + '" data-text="' + val.name + '">' + val.name.toUpperCase() + '</option>';
				});
				$(dom).html(output);
				if (selected) {
					$(dom).val(selected);
				}
			})
			.catch(err => {
				console.log(err)
			});
	}

	function deleteConfirm(callback) {
		Swal.fire({
			title: 'Anda yakin ?',
			text: "Data yang sudah dihapus tidak dapat dikembalikan lagi",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(callback);
	}

	function deleteInfo(success = true) {
		if (success) {
			Swal.fire({
				title: 'Berhasil',
				text: 'Data berhasil dihapus',
				type: 'success',
				showConfirmButton: false,
				timer: 3000
			});
		} else {
			Swal.fire({
				title: 'Gagal',
				text: 'Data gagal dihapus',
				type: 'warning',
				showConfirmButton: false,
				timer: 2000
			});
		}
	}

	function successInfo(success = true) {
		if (success) {
			Swal.fire({
				title: 'Berhasil',
				text: `Data berhasil disimpan`,
				type: 'success',
				showConfirmButton: false,
				timer: 3000
			});
		} else {
			Swal.fire({
				title: 'Gagal',
				text: `Data gagal disimpan`,
				type: 'warning',
				showConfirmButton: false,
				timer: 2000
			});
		}
	}

	function makeAjaxCall(url, methodType, callback) {
		return $.ajax({
			url: url,
			method: methodType,
			dataType: "json"
		})
	}

	function makeAjaxCall2(url, methodType, callback) {
		$.ajax({
			url: url,
			method: methodType,
			dataType: "json",
			success: callback,
			error: function(reason, xhr) {
				console.log("Error in processing your request", reason);
			}
		});
	}

	function unquote(string) {
		return string && string.replace(/^['"]|['"]$/g, '');
	}

	function formatAmount(num, thousand_separator = '.', decimal_separator = ',') {
		var number = num.toString();
		var number_string = number.replace(/[^,\d]/g, "").toString(),
			split = number_string.split(decimal_separator),
			mod = split[0].length % 3,
			amount = split[0].substr(0, mod),
			thousand = split[0].substr(mod).match(/\d{3}/gi);

		if (thousand) {
			separator = mod ? thousand_separator : "";
			amount += separator + thousand.join(thousand_separator);
		}

		amount = split[1] != undefined ? amount + decimal_separator + split[1] : amount;
		return amount;
	}

	function formatRp(num, thousand_separator = '.', decimal_separator = ',') {
		var amount = formatAmount(num, thousand_separator, decimal_separator);

		return "Rp. " + amount;
	}

	function getRadioStatus(domID) {
		var status = true;
		$('#' + domID + ' input:radio').each(function() {
			var name = $(this).attr("name");
			if (!$('#' + domID + ' input:radio[name="' + name + '"]').is(':checked')) {
				status = false;
			}
		});

		return status;
	}

	function getInputStatus(domID) {
		var status = true;

		if ($('#' + domID).val().length == 0) {
			status = false;
		}

		return status;
	}

	function addToLocalStorage(itemName, itemValue) {
		var value = +localStorage.getItem(itemName);

		if (value) {
			value += itemValue;
		} else {
			value = itemValue;
		}

		localStorage.setItem(itemName, value);

		if (itemName == 'count_all') {
			var count_all = +localStorage.getItem('count_all');
			$('.bmn_count_all').html(formatAmount(count_all) + ' NUP');
		}

		if (itemName == 'amount_all') {
			var amount_all = +localStorage.getItem('amount_all');
			$('.bmn_amount_all').html(formatRp(amount_all));
		}

		if (itemName == 'count_psp0') {
			var count_psp0 = +localStorage.getItem('count_psp0');
			$('.bmn_count_psp0').html(formatAmount(count_psp0) + ' NUP');
		}

		if (itemName == 'amount_psp0') {
			var amount_psp0 = +localStorage.getItem('amount_psp0');
			$('.bmn_amount_psp0').html(formatRp(amount_psp0));
		}

		if (itemName == 'count_kondisi3') {
			var count_kondisi3 = +localStorage.getItem('count_kondisi3');
			$('.bmn_count_kondisi3').html(formatAmount(count_kondisi3) + ' NUP');
		}

		if (itemName == 'amount_kondisi3') {
			var amount_kondisi3 = +localStorage.getItem('amount_kondisi3');
			$('.bmn_amount_kondisi3').html(formatRp(amount_kondisi3));
		}
	}

	function displayBMN(bmn_00, isAddToLocalStorage = false) {
		// Total Aset
		var _dom_count_all = '.' + bmn_00 + '_count_all';
		var _url_count_all = $(_dom_count_all).data('url');
		$(_dom_count_all).html('loading...');
		makeAjaxCall(_url_count_all, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('count_all', +respJson.data);
			}
			$(_dom_count_all).html(formatAmount(respJson.data) + ' NUP');
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		var _dom_amount_all = '.' + bmn_00 + '_amount_all';
		var _url_amount_all = $(_dom_amount_all).data('url');
		$(_dom_amount_all).html('loading...');
		makeAjaxCall(_url_amount_all, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('amount_all', +respJson.data);
			}
			$(_dom_amount_all).html(formatRp(respJson.data));
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		// Belum PSP
		var _dom_count_psp0 = '.' + bmn_00 + '_count_psp0';
		var _url_count_psp0 = $(_dom_count_psp0).data('url');
		$(_dom_count_psp0).html('loading...');
		makeAjaxCall(_url_count_psp0, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('count_psp0', +respJson.data);
			}
			$(_dom_count_psp0).html(formatAmount(respJson.data) + ' NUP');
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		var _dom_amount_psp0 = '.' + bmn_00 + '_amount_psp0';
		var _url_amount_psp0 = $(_dom_amount_psp0).data('url');
		$(_dom_amount_psp0).html('loading...');
		makeAjaxCall(_url_amount_psp0, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('amount_psp0', +respJson.data);
			}
			$(_dom_amount_psp0).html(formatRp(respJson.data));
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		//Rusak Berat
		var _dom_count_kondisi3 = '.' + bmn_00 + '_count_kondisi3';
		var _url_count_kondisi3 = $(_dom_count_kondisi3).data('url');
		$(_dom_count_kondisi3).html('loading...');
		makeAjaxCall(_url_count_kondisi3, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('count_kondisi3', +respJson.data);
			}
			$(_dom_count_kondisi3).html(formatAmount(respJson.data) + ' NUP');
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		var _dom_amount_kondisi3 = '.' + bmn_00 + '_amount_kondisi3';
		var _url_amount_kondisi3 = $(_dom_amount_kondisi3).data('url');
		$(_dom_amount_kondisi3).html('loading...');
		makeAjaxCall(_url_amount_kondisi3, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('amount_kondisi3', +respJson.data);
			}
			$(_dom_amount_kondisi3).html(formatRp(respJson.data));
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		if (bmn_00 == 'bmn_03') {
			// Belum SIP
			var _dom_count_sip0 = '.' + bmn_00 + '_count_sip0';
			var _url_count_sip0 = $(_dom_count_sip0).data('url');
			$(_dom_count_sip0).html('loading...');
			makeAjaxCall(_url_count_sip0, "GET").then(function(respJson) {
				$(_dom_count_sip0).html(formatAmount(respJson.data) + ' NUP');
			}, function(reason) {
				console.log("Error in processing your request", reason);
			});

			var _dom_amount_sip0 = '.' + bmn_00 + '_amount_sip0';
			var _url_amount_sip0 = $(_dom_amount_sip0).data('url');
			$(_dom_amount_sip0).html('loading...');
			makeAjaxCall(_url_amount_sip0, "GET").then(function(respJson) {
				$(_dom_amount_sip0).html(formatRp(respJson.data));
			}, function(reason) {
				console.log("Error in processing your request", reason);
			});
		}

	}

	function getDropdown(domID, uriParam = '', title = 'Pilih', selectedCode = false, includeCode = true) {
		var _dom = '#' + domID;
		var _url = $(_dom).data('url');
		if (uriParam.length > 0) {
			_url = _url + uriParam;
		}

		$("#" + domID).empty();
		$('#' + domID).append('<option value="">Loading...</option>');
		makeAjaxCall(_url, "GET").then(function(respJson) {
			$("#" + domID).empty();
			$('#' + domID).append('<option value="">' + title + '</option>');
			$.each(respJson, function(index, itemData) {
				var code = itemData.code;
				var text = itemData.text;
				var selected = '';

				if (code == selectedCode) {
					selected = 'selected';
				}

				if (includeCode) {
					text = +itemData.code + ' - ' + itemData.text;
				}
				$('#' + domID).append('<option value="' + code + '" ' + selected + '>' + text + ' </option>');
			});
		}, function(reason) {
			console.log(reason);
			$("#" + domID).empty();
			$('#' + domID).append('<option value="">Loading Error...</option>');
		});
	}

	function makeAjaxSelect(dom, url, value = false) {
		$(dom).empty();
		$(dom).append('<option value="">Loading...</option>');
		makeAjaxCall(url, "GET").then(function(respJson) {
			$(dom).empty();
			$(dom).append('<option value=""></option>');
			$.each(respJson, function(index, itemData) {
				var id = itemData.id;
				var name = itemData.name;
				var selected = '';
				if (id == value) selected = 'selected';
				$(dom).append('<option value="' + id + '" ' + selected + '>' + name + ' </option>');
			});
		}, function(reason) {
			console.log(reason);
			$(dom).empty();
			$(dom).append('<option value="">Loading Error...</option>');
		});
	}

	$('.btn-checkin').click(function() {
		var url = $(this).data('url');
		Swal.fire({
			title: 'Anda yakin?',
			text: "Memulai sesi penambahan Aset ke Keranjang untuk disimpan ke Daftar Usulan BMN.",
			type: 'info',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(function(result) {
			if (result.value) {
				window.location.href = url;
			}
		});
	});

	$('.btn-checkout').click(function() {
		var url = $(this).data('url');
		Swal.fire({
			title: 'Anda yakin?',
			text: "Mengakhiri sesi penambahan Aset ke Keranjang untuk disimpan ke Daftar Usulan BMN.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(function(result) {
			if (result.value) {
				window.location.href = url;
			}
		});
	});

	$('.btn-destroy').click(function() {
		var url = $(this).data('url');
		Swal.fire({
			title: 'Anda yakin?',
			text: "Keranjang Usulan akan dikosongkan.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(function(result) {
			if (result.value) {
				window.location.href = url;
			}
		});
	});

	$('.btn-delete').click(function() {
		var url = $(this).data('url');
		Swal.fire({
			title: 'Anda yakin?',
			text: "Aset yang terpilih akan dihapus dari Keranjang Usulan.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(function(result) {
			if (result.value) {
				window.location.href = url;
			}
		});
	});

	function checkAll(domCheckAll = '.check_all', domCheckBox = 'input.check') {
		var checkAll = $(domCheckAll);
		var checkboxes = $(domCheckBox);

		checkAll.on('ifChecked ifUnchecked', function(event) {
			if (event.type == 'ifChecked') {
				checkboxes.iCheck('check');
			} else {
				checkboxes.iCheck('uncheck');
			}
		});

		checkboxes.on('ifChanged', function(event) {
			if (checkboxes.filter(':checked').length == checkboxes.length) {
				checkAll.prop('checked', 'checked');
			} else {
				checkAll.removeProp('checked');
			}
			checkAll.iCheck('update');
		});
	}

	// Drawer
	checkAll('.cart_check_all', 'input.cart_check');
	setDataTable('#cart_tbl_bmns', disableOrderCols = [0, 8], defaultOrderCols = [1, 'asc'], autoNumber = false);

	$('#remove_from_cart').click(function() {
		var form = $('#cart_form_bmns');
		var serialize_bulk = form.serialize();
		var url = "<?= base_url('request/cart_remove') ?>" + '/?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		if (serialize_bulk.includes("&id%5B%5D=")) {
			Swal.fire({
				title: 'Anda yakin?',
				text: "Semua aset yang terpilih akan dihapus dari keranjang!",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#dd6b55',
				confirmButtonText: '<?= lang('App.btn.yes') ?>',
				cancelButtonText: '<?= lang('App.btn.no') ?>'
			}).then((result) => {
				if (result.value) {
					window.location.href = url;
				}
			});
		} else {
			Swal.fire({
				title: 'Oups',
				text: 'Aset yang terpilih kosong.',
				type: 'warning',
				showConfirmButton: false,
				timer: 2000
			});
		}

		return false;
	});

	function pad(num, size) {
		num = num.toString();
		while (num.length < size) num = "0" + num;
		return num;
	}
</script>