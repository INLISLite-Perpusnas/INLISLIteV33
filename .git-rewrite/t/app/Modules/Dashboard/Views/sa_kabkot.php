<?php

use Base\Models\DataModel;
use Base\Models\BaseModel;

$npp_kabkota_id = preg_replace('/\./', '', user()->npp_kabkota_id);
$env_db_default = env('database.data.default');
$env_db_data = env('database.data.database');
$builder = new BaseModel('users');
$query = $builder
    ->join($env_db_data . '.branchs', 'branchs.id = users.branch_id')
    ->where('branchs.NPP_KabKota_id', $npp_kabkota_id);

$total_user_active = $query->where('users.active', 1)->countAllResults(false);
$total_user_inactive = $query->where('users.active', 0)->countAllResults(false);

$builder = new DataModel('members');
$query = $builder
    ->join($env_db_data . '.branchs', 'branchs.id = members.Branch_id')
    ->where('branchs.NPP_KabKota_id', $npp_kabkota_id);

$total_anggota = $query->countAllResults(false);
$total_anggota_baru = $query->where('members.StatusAnggota_id', 1)->countAllResults(false);
$total_anggota_bebas_pustaka = $query->where('members.StatusAnggota_id', 5)->countAllResults(false);

$builder = new DataModel('memberguesses');
$query = $builder
    ->join($env_db_data . '.branchs', 'branchs.id = memberguesses.Branch_id')
    ->where('branchs.NPP_KabKota_id', $npp_kabkota_id);

$total_anggota_guest = $query->where('memberguesses.NoAnggota !=', null)->countAllResults(false);
$total_nonanggota_guest = $query->where('memberguesses.NoAnggota', null)->countAllResults(false);



$builder = new DataModel('catalogs');
$query = $builder
    ->join($env_db_data . '.branchs', 'branchs.id = catalogs.Branch_id')
    ->where('branchs.NPP_KabKota_id', $npp_kabkota_id);

$total_katalog = $query->countAllResults(false);

$builder = new DataModel('collections');
$query = $builder
    ->join($env_db_data . '.branchs', 'branchs.id = collections.Branch_id')
    ->where('branchs.NPP_KabKota_id', $npp_kabkota_id);

$total_koleksi = $query->countAllResults(false);

$builder = new DataModel('collectionloans');
$query = $builder
    ->join($env_db_data . '.branchs', 'branchs.id = collectionloans.Branch_id')
    ->where('branchs.NPP_KabKota_id', $npp_kabkota_id);

$total_peminjaman = $query->countAllResults(false);
?>
<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-display1 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Dashboard
                    <div class="page-title-subheading">Dashboard</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url(
                                                                    'dashboard'
                                                                ) ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="">
        <div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content bg-info">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">Jumlah Anggota</div>
                            <div class="widget-subheading"></div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $total_anggota ?? 0 ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content bg-primary">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">Anggota Baru</div>
                            <div class="widget-subheading"></div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $total_anggota_baru ?? 0 ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content bg-success">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">User- Aktif</div>
                            <div class="widget-subheading"></div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $total_user_active ?? 0 ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content bg-warning">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">Kunjungan Anggota</div>
                            <div class="widget-subheading"></div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $total_anggota_guest ?? 0 ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content bg-danger">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">Kunjungan Non Anggota</div>
                            <div class="widget-subheading"></div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $total_nonanggota_guest ?? 0 ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content bg-dark">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">Anggota - Bebas Pustaka</div>
                            <div class="widget-subheading"></div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $total_anggota_bebas_pustaka ?? 0 ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider mt-0" style="margin-bottom: 30px;"></div>

        <div class="row">
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Total Katalog</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= formatRupiah($total_katalog ?? 0, '') ??
                                                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Total Koleksi</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= formatRupiah($total_koleksi ?? 0, '') ??
                                                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Total Peminjaman</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_peminjaman ?? 0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page') ?>