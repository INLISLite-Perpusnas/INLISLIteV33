<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>

<?= $this->endSection('style'); ?>
<?php
$branch = get_ref_single('branchs', 'ID=' . $user->branch_id, 'data');
if (!empty($branch)) {
    $provinsi = get_ref_single('t_region', 'npp=' . $branch->NPP_Provinsi_id);
    $kabkota = get_ref_single('t_region', 'npp=' . $branch->NPP_KabKota_id);
    $kecamatan = get_ref_single('t_region', 'npp=' . $branch->NPP_Kecamatan_id);
    $kelurahan = get_ref_single('t_region', 'npp=' . $branch->NPP_Kelurahan_id);
}

$provinsi_title = "";
$kabkota_title = "";
$npp_provinsi_id = user()->npp_provinsi_id;
if (!empty($npp_provinsi_id)) {
    $regionModel = new \Region\Models\RegionModel();
    $region = $regionModel->where('code', user()->npp_provinsi_id)->where('level', 1)->first();
    if (!empty($region)) {
        $provinsi_title = "PROVINSI : " . $region->name;
        $kabkota_title = "";
    }
}

$npp_kabkota_id = user()->npp_kabkota_id;
if (!empty($npp_kabkota_id)) {
    $regionModel = new \Region\Models\RegionModel();
    $region = $regionModel->where('code', user()->npp_kabkota_id)->where('level', 2)->first();
    if (!empty($region)) {
        $kabkota_title = "KABUPATEN / KOTA : " . $region->name;
    }
}
?>
<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-user icon-gradient bg-strong-bliss"></i>
                </div>
                <div>User
                    <div class="page-title-subheading">
                        <?php if ($is_profile) : ?>
                            Profil Saya
                        <?php else : ?>
                            Detail User
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <?php if ($is_profile) : ?>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Profil Saya</li>
                        </ol>
                    <?php else : ?>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Otorisasi</li>
                            <li class="breadcrumb-item" aria-current="page">User</li>
                            <li class="breadcrumb-item" aria-current="page">Detail</li>
                        </ol>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="card-shadow-dark profile-responsive card-border mb-3 card">
                <div class="dropdown-menu-header">
                    <div class="dropdown-menu-header-inner bg-primary">
                        <div class="menu-header-image" style="background-image: url('<?= base_url('themes/uigniter') ?>/images/dropdown-header/abstract4.jpg')"></div>
                        <div class="menu-header-content btn-pane-right">
                            <div class="avatar-icon-wrapper mr-2 avatar-icon-xl">
                                <div class="avatar-icon">
                                    <?php
                                    $default = base_url('themes/uigniter/images/avatars/2.jpg');
                                    $image = base_url('uploads/user/' . $user->avatar);
                                    if (empty($user->avatar)) {
                                        $image = $default;
                                    }
                                    ?>

                                    <img src="<?= $image ?>" onerror="this.onerror=null;this.src='<?= $default ?>';" alt="User Profile">
                                </div>
                            </div>
                            <div>
                                <h5 class="menu-header-title"><?= $user->first_name ?? ''; ?> <?= $user->last_name ?? ''; ?></h5>
                                <h6 class="menu-header-subtitle"><?= $user->username; ?></h6>
                            </div>
                            <div class="menu-header-btn-pane">
                                <a href="javascript:void(0);" data-id="<?= $user->id ?>" data-format=".jpg,.png" data-format-title="Format (JPG|PNG). Max 1 Files @ 1MB" data-field="avatar" data-title="Upload Avatar" class="mb-2 mr-2 btn btn-pill btn-primary upload-data">
                                    <i class="fa fa-user"></i> Update Avatar
                                </a>
                                <a data-bs-toggle="modal" data-bs-target="#modal_edit" data-toggle="modal" data-target="#modal_edit" href="javascript:void(0);" class="mb-2 mr-2 btn btn-pill btn-warning" title="">
                                    <i class="fa fa-edit"></i>
                                    <?php if ($is_profile) : ?>
                                        Update Profil
                                    <?php else : ?>
                                        Update User
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="widget-content-left">
                                    <div class="widget-heading">Username</div>
                                </div>
                                <div class="widget-content-right">
                                    <?= $user->username ?? ''; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <i class="fa fa-envelope"></i>
                                </div>
                                <div class="widget-content-left">
                                    <div class="widget-heading">Email</div>
                                </div>
                                <div class="widget-content-right">
                                    <?= $user->email ?? ''; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <i class="fa fa-phone"></i>
                                </div>
                                <div class="widget-content-left">
                                    <div class="widget-heading">No Telepon</div>
                                </div>
                                <div class="widget-content-right">
                                    <?= $user->phone ?? ''; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="widget-content-left">
                                    <div class="widget-heading">Status</div>
                                </div>
                                <div class="widget-content-right">
                                    <?php if ($user->active == 1) : ?>
                                        <span class="mb-2 mr-2 badge badge-pill badge-success">Active</span>
                                    <?php else : ?>
                                        <span class="mb-2 mr-2 badge badge-pill badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="widget-content-left">
                                    <div class="widget-heading">Group</div>
                                </div>
                                <div class="widget-content-right">
                                    <?php foreach ($currentGroups as $group) : ?>
                                        <span class="mb-2 mr-2 badge badge-pill badge-secondary"><?= $group; ?></span>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <i class="fa fa-map-marker"></i>
                                </div>
                                <div class="widget-content-left">
                                    <div class="widget-heading">Provinsi</div>
                                </div>
                                <div class="widget-content-right">
                                    <?= $provinsi_title ?? ''; ?>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left mr-3">
                                    <i class="fa fa-map-marker-alt"></i>
                                </div>
                                <div class="widget-content-left">
                                    <div class="widget-heading">Kabupaten / Kota</div>
                                </div>
                                <div class="widget-content-right">
                                    <?= $kabkota_title ?? ''; ?>
                                </div>
                            </div>
                        </div>
                    </li>

                </ul>

                <?php if (is_member('admin') || is_member('sa_prov') || is_member('sa_kabkot')) : ?>
                <?php else : ?>
                    <div class="card-border m-3 card">
                        <div class="card-body">
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left mr-3">
                                        <i class="fa fa-building"></i>
                                    </div>
                                    <div class="widget-content-left">
                                        <div class="widget-heading">Informasi NPP (Branch)</div>
                                    </div>
                                    <div class="widget-content-right">
                                    </div>
                                </div>
                                <p class="mt-3">
                                <dl>
                                    <dt>Nomor</dt>
                                    <dd><?= strtoupper($branch->NPP_id ?? '-') ?></dd>
                                    <dt>Nama</dt>
                                    <dd><?= strtoupper($branch->Name ?? '-') ?></dd>
                                    <dt>Jenis</dt>
                                    <dd><?= ($branch->NPP_Jenis ?? '') ?></dd>
                                    <dt>Provinsi</dt>
                                    <dd><?= strtoupper($provinsi->name ?? '-') ?></dd>
                                    <dt>Kab/Kota</dt>
                                    <dd><?= strtoupper($kabkota->name ?? '-') ?></dd>
                                    <dt>Kecamatan</dt>
                                    <dd><?= strtoupper($kecamatan->name ?? '-') ?></dd>
                                    <dt>Kelurahan</dt>
                                    <dd><?= strtoupper($kelurahan->name ?? '-') ?></dd>
                                    <dt>Alamat</dt>
                                    <dd><?= ($branch->Address ?? '-') ?></dd>
                                </dl>

                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    Dropzone.autoDiscover = false;
</script>
<?= $this->include('User\Views\update_modal'); ?>
<?= $this->include('User\Views\upload_modal'); ?>
<?= $this->endSection('script'); ?>