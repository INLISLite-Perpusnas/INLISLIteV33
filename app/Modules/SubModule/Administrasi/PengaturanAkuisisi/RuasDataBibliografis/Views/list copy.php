<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-database me-2"></i>
                                <?= $title ?? 'Ruas Data Bibliografis' ?>
                            </h4>
                            <p class="mb-0 small">
                                <i class="fas fa-info-circle me-1"></i>
                                Kelola ruas data bibliografis untuk setiap jenis bahan
                            </p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light btn-sm" id="refreshBtn">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
           <div>
              <div class="position-relative form-group">
                                    <label>Jenis Bahan</label>
                                    <select class="form-control" name="jenis_bahan" id="jenis_bahan" placeholder="Jenis identitas">
                                        <option value="" disabled selected>
                                            Jenis identitas
                                        </option>
                                        <?php foreach (get_table('worksheets', 'ID,  Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= set_select('ID', $row->ID) ?>><?= $row->Name ?></option>
                                        <?php endforeach; ?>
                                    </select>
            </div>
           </div>
         
        </div>
    </div>
</div>



<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->endSection('script'); ?>