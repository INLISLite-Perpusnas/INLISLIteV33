<?php
$request = service('request');
// $select2 = select_two();
// $rda = $request->getGet('rda') ?? 1;
?>

<div class="main-card mb-3 card w-100">
  <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i> Edisi Serial
    <div class="btn-actions-pane-right actions-icon-btn">
      <?php if (is_allowed('katalog/edit')) : ?>
        <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus font-weight-bold"></i> Tambah Edisi Serial</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <?= get_message('message'); ?>
    <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
      <thead>
        <tr>
          <th class="text-center" width="35">No</th>
          <th class="text-center">No Edisi Serial</th>
          <th class="text-center" width="100">Tanggal Terbit<br>Edisi Serial</th>
          <th class="text-center" width="90">Tanggal Dibuat</th>
          <th class="text-center" width="90">Tanggal Diperbarui</th>
          <th class="text-center" width="180">Aksi</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<?= $this->section('script'); ?>
<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Edisi Serial
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="frm_add" method="post" action="">
        <div class="modal-body">
          <div id="frm_add_message"></div>

          <div class="form-row">
            <div class="col-lg-12">
              <div class="form-group">
                <label for="Nama">Nomor Edisi Serial</label>
                <div>
                  <input required type="text" class="form-control" id="no_edisi_serial" name="no_edisi_serial" placeholder="Nomor Edisi Serial" value="" />
                </div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-group">
                <label for="Nama">Tanggal Terbit Edisi Serial</label>
                <div>
                  <input required type="date" class="form-control" id="tgl_edisi_serial" name="tgl_edisi_serial" placeholder="Tanggal Terbit" value="" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary" name="submit" id="btnAdd">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  let MainTable;
  let CatalogID = "<?= $catalog->ID ?>";
  $(document).ready(function() {
    MainTable = $('#tbl_data').DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": '<?php echo site_url('api/katalog/datatable-edisi-serial/' . $catalog->ID) ?>',
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
          data: 'no',
          className: 'text-center',
          orderable: false,
          searchable: false
        },
        {
          data: 'no_edisi_serial'
        },
        {
          data: 'tgl_edisi_serial',
          className: 'text-center'
        },
        {
          data: 'CreateDate',
          className: 'text-center'
        },
        {
          data: 'UpdateDate',
          className: 'text-center'
        },
        {
          data: 'action',
          className: 'text-center',
          orderable: false,
          searchable: false
        },
      ],
      "order": [
        [1, "desc"]
      ],
      "drawCallback": function(data, type, full, meta) {
        var api = this.api();
        var data = api.rows().data();
        $('[data-toggle="tooltip"]').tooltip();
      },
      "initComplete": function(settings, json) {
        var $searchInput = $('div.dataTables_filter input');
        $searchInput.unbind();
        $searchInput.bind('keyup', function(e) {
          if (e.keyCode == 13) {
            if (this.value.length == 0) {
              t.search('').draw();
            }

            if (this.value.length >= 3) {
              t.search(this.value).draw();
            }
          }
        });
      }
    });
  });

  $("body").on("click", ".remove-data", function() {
    var url = $(this).attr('data-href');
    // console.log(url);
    Swal.fire({
      title: '<?= lang('App.swal.are_you_sure') ?>',
      text: "<?= lang('App.swal.can_not_be_restored') ?>",
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
    return false;
  });

  $('#frm_add').submit(function(event) {
    event.preventDefault();

    const url = "<?= base_url('api/katalog/create-edisi-serial/') ?>";
    const data_post = $(this).serializeArray();
    data_post.push({name: "catalog_id", value:CatalogID})

    $("#btnAdd").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
    $("#btnAdd").attr('disabled', true);

    $.ajax({
        url: url,
        type: 'POST',
        data: data_post,
      })
      .done(function(res) {
        // console.log(res)

        if (res.error == false) {
          Swal.fire({
            title: 'Berhasil',
            html: 'Edisi serial berhasil ditambah.',
            type: 'success',
            showConfirmButton: false,
            timer: 5000,
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({
            title: 'Oups',
            text: res.message,
            type: 'error',
            showConfirmButton: false,
            timer: 5000
          }).then(() => {
            $("#btnAdd").attr('disabled', false);
            $("#btnAdd").html('Simpan');
          });
        }
      })
      .fail(function(res) {
        console.error(res);

        Swal.fire({
          title: 'Oups',
          text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
          type: 'error',
          showConfirmButton: false,
          timer: 5000
        }).then(() => {
          $("#btnAdd").attr('disabled', false);
          $("#btnAdd").html('Simpan');
        });
      });

    return false;
  });

  $('#modal_create').on('hidden.bs.modal', function() {
    $(this).find('form').trigger('reset');
    $('#frm_add_message').html('');
  });
</script>
<?= $this->endSection('script'); ?>
