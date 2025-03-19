<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	.tox.tox-tinymce.tox-fullscreen {
		z-index: 1050;
		top: 60px !important;
		left: 85px !important;
		width: calc(100% - 85px) !important;
	}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>


<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-users icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Kontak <?= ucwords(unslugify($slug)) ?>
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url('cms/kontak') ?>">Kontak</a></li>
						<li class="active breadcrumb-item" aria-current="page">Tambah Kontak</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Kontak
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>

			<form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('cms/kontak/create?slug=' . $slug); ?>">
				<div class="form-row">
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label for="name">Nama*</label>
							<div>
								<input type="text" class="form-control" id="name" name="name" placeholder="Nama" value="<?= set_value('name'); ?>" />
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label for="email">Email*</label>
							<div>
								<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= set_value('email'); ?>" />
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label for="phone">No. Telepon*</label>
							<div>
								<input type="number" class="form-control" id="phone" name="phone" placeholder="No. Telepon" value="<?= set_value('phone'); ?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="subject">Subjek</label>
					<div>
						<input type="text" id="subject" name="subject" placeholder="Subjek" rows="2" class="form-control" value="<?= set_value('subject'); ?>" />
					</div>
				</div>

				<div class="form-group">
					<label for="message">Pesan</label>
					<div>
						<textarea id="message" name="message" placeholder="Pesan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('message') ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="submit">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->endSection('script'); ?>