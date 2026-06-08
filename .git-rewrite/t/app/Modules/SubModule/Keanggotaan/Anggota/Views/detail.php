<?php $core = config('Core');
$layout = (!empty($core->layout_backend)) ? $core->layout_backend : 'hamkamannan\adminigniter\Views\layout\backend\main'; ?>
<?= $this->extend($layout); ?>
<?= $this->section('style'); ?>

<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1>View Data Pendaftaran</h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <!-- Profile Image -->
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <div class="text-center">
                <img class="profile-user-img img-fluid" src="" alt="User profile picture">
              </div>

              <h3 class="profile-username text-center"></h3>

              <p class="text-muted text-center"></p>

              <ul class="list-group list-group-unbordered mb-4">
                <li class="list-group-item">
                  <b> <?= set_value('name', $anggota->name); ?></b> <a class="float-right"></a>
                </li>
                <li class="list-group-item">
                  <b>Email:</b><?= set_value('Email', $anggota->Email); ?> <a class="float-right"></a>
                </li>
                <li class="list-group-item">
                  <b>Prodi</b> <a class="float-right"></a>
                </li>
                <li class="list-group-item">
                  <b>Status</b> <a class="float-right"></a>
                </li>
              </ul>
              <!-- View Berkas Pendaftaran -->
              <a data-toggle="tooltip" data-placement="top" title="View">
                <!-- <button type="button" class="btn btn-outline-primary btn-block" type="button" data-toggle="modal" data-target="#exampleModal">
                      View Berkas Pendaftaran
                    </button> -->
                <?php if (is_allowed('anggota/cetak-kartu')) : ?>
                  <a href="javascript:void(0);" data-href="<?= base_url('anggota/cetak-kartu/' . $anggota->id); ?>" data-toggle="tooltip" data-placement="top" title="Hapus" class="btn btn-danger remove-data" style="min-width:37px"><i class="pe-7s-trash font-weight-bold"></i></a>
                <?php endif; ?>
              </a>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>

        <!-- /.col -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-header p-2">
              <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Biodata</a></li>
                <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Data Pelanggaran</a></li>
                <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Sekolah</a></li>
              </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
              <div class="tab-content">
                <!-- Biodata -->
                <div class="active tab-pane" id="activity">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Biodata</th>
                        <th>Keterangan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th>1</th>
                        <td>Nama</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>2</th>
                        <td>Tempat Lahir</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>3</th>
                        <td>Tanggal Lahir</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>4</th>
                        <td>Jenis Kelamin</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>5</th>
                        <td>Agama</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>6</th>
                        <td>No. Handphone</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>7</th>
                        <td>Alamat</td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Orangtua -->
                <div class="tab-pane" id="timeline">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Orangtua</th>
                        <th>Keterangan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th>1</th>
                        <td>Nama Orangtua</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>2</th>
                        <td>Pekerjaan Orangtua</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>3</th>
                        <td>No. Handphone Orangtua</td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Sekolah -->
                <div class="tab-pane" id="settings">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Sekolah</th>
                        <th>Keterangan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th>1</th>
                        <td>Nama Sekolah</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>2</th>
                        <td>Tahun Lulus</td>
                        <td></td>
                      </tr>
                      <tr>
                        <th>3</th>
                        <td>Alamat Sekolah</td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- /.tab-content -->
            </div><!-- /.card-body -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->


</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<!-- Preview Berkas -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">View Berkas Pendaftaran </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <embed src="" type="application/pdf" width="100%" height="450px">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
  <?= $this->endSection('script'); ?>