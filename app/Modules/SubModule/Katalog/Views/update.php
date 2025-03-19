<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'katalog_edit';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
				</div>
				<div><?= ($is_allowed ? 'Edit' : 'Detail') ?> Katalog
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url('katalog') ?>">Katalog</a></li>
						<li class="active breadcrumb-item" aria-current="page">Edit</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav mb-3">
		<li class="nav-item">
			<a class="nav-link <?= ($slug == 'katalog_edit') ? 'active' : '' ?>" href="<?= base_url('katalog/edit/' . $catalog->ID) ?>">
				<span>KATALOG</span>
			</a>
		</li>
		<?php foreach (array('eksemplar', 'cover', 'konten_digital') as $group) : ?>
			<li class="nav-item">
				<a class="nav-link <?= ($slug == trim($group)) ? 'active' : '' ?>" href="<?= base_url('katalog/edit/' . $catalog->ID . '?slug=' . $group) ?>">
					<span><?= strtoupper(unslugify($group)) ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<form id="frm_edit" class="main-card mb-3 card" method="post" action="">
		<?= $this->include("Katalog\Views\slug\\$slug"); ?>
	</form>

	<a href="<?= base_url('katalog') ?>" class="btn btn-secondary btn-lg mb-3"><i class="fa fa-list mr-2"></i> Kembali ke Daftar Katalog</a>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('Katalog\Views\add_script'); ?>
<script>
	$(document).ready(function() {
		bsCustomFileInput.init();

		//Submit pendaftaran tahap tiga
		$('#formUploadBerkas').on('submit', function(e) {
			e.preventDefault();

			$.ajax({
				url: "<?php echo base_url('datapendaftaran/saveTransfer') ?>",
				method: "POST",
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				dataType: "JSON",
				success: function(res) {
					//Data error 

					//Pendaftaran tahap tiga sukses
					if (res.success) {
						Swal.fire({
							position: 'top-end',
							icon: 'success',
							title: 'Upload Transfer berhasil!',
							showConfirmButton: false,
							timer: 1500
						});
						window.location.replace(res.link);
					}

				}

			});

		});
	});
	//-------------------------------------------------------------------
</script>
<script>
	//Preview pas photo yang di upload peserta
	function previewFile(input) {
		var file = $("input[type=file]").get(0).files[0];
		if (file) {
			var reader = new FileReader();
			reader.onload = function() {
				$("#previewImg").attr("src", reader.result);
			}
			reader.readAsDataURL(file);
		}
	}
	//-------------------------------------------------------------------
</script>

<script>
var t;
$(document).ready(function() {
    t = $('#tbl_data').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": '<?php echo site_url('api/eksemplar/datatable/0/' . $catalog->ID) ?>',
        },
        "dom": "<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'f><'col-md-6 col-sm-4 col-xs-12 d-none d-sm-block text-right'p>>" +
            "<'row'<'col-md-12'tr>>" +
            "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12 text-right'i>>",
        "pagingType": "full_numbers",
        "oLanguage": {
            "sSearch": "<i class='fa fa-search'></i> _INPUT_",
            "sLengthMenu": "_MENU_",
            "oPaginate": {
                "sNext": "<i class='fa fa-chevron-right'></i>",
                "sPrevious": "<i class='fa fa-chevron-left'></i>",
                "sLast": "<i class='fa fa-chevron-double-right'></i>",
                "sFirst": "<i class='fa fa-chevron-double-left'></i>",
            }
        },
        "columns": [{
                data: 'NomorBarcode',
                className: 'text-left'
            },
            {
                data: 'TanggalPengadaan'
            },
            {
                data: 'NoInduk'
            },
            {
                data: 'Catalog_id'
            },
            {
                data: 'IsOPAC',
                className: 'text-center',
                orderable: false
            },
            {
                data: 'ISDRM',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (data == 1) {
                            return '<span class="badge" style="background-color: #28a745; color: white;">Ya</span>';
                        } else {
                            return '<span class="badge" style="background-color: #ffc107; color: black;">Tidak</span>';
                        }
                    }
                    return data;
                }
            },
            {
                data: 'action',
                className: 'text-center',
                orderable: false
            },
        ],
        "order": [
            [0, "desc"]
        ],
        "drawCallback": function(data, type, full, meta) {
            var api = this.api();
            var data = api.rows().data();
            $('[data-toggle="tooltip"]').tooltip();
        },
        "initComplete": function(settings, json) {
            // var $searchInput = $('div.dataTables_filter input');
            // $searchInput.unbind();
            // $searchInput.bind('keyup', function(e) {
            //     if(e.keyCode == 13){
            //         if(this.value.length == 0){
            //             t.search('').draw();
            //         }

            //         if(this.value.length >= 3){
            //             t.search( this.value ).draw();
            //         }
            //     } 
            // });
        }
    });
});
</script>

<?= $this->endSection('script'); ?>