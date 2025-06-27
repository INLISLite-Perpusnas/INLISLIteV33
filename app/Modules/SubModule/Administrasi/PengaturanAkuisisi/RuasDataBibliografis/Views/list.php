<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    .table-wrapper {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    
    .data-table {
        margin-top: 20px;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .toggle-switch {
        position: relative;
        width: 50px;
        height: 25px;
        background-color: #ccc;
        border-radius: 25px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .toggle-switch.active {
        background-color: #28a745;
    }
    
    .toggle-slider {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 21px;
        height: 21px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.3s;
    }
    
    .toggle-switch.active .toggle-slider {
        transform: translateX(25px);
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="card shadow-sm ">
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

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="jenis_bahan" class="form-label fw-bold">
                                <i class="fas fa-filter me-1"></i>Jenis Bahan
                            </label>
                            <select class="form-control" name="jenis_bahan" id="jenis_bahan">
                                <option value="" disabled <?= empty($selected_worksheet_id) ? 'selected' : '' ?>>
                                    -- Pilih Jenis Bahan --
                                </option>
                                <?php foreach (get_table('worksheets', 'ID, Name', null, 'data') as $row) : ?>
                                    <option value="<?= $row->ID ?>" <?= ($selected_worksheet_id == $row->ID) ? 'selected' : '' ?>>
                                        <?= $row->Name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" id="clearFilterBtn">
                                <i class="fas fa-times me-1"></i>Clear Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table Section -->
            <?php if (!empty($data_bibliografis)): ?>
                <div class="card shadow-sm data-table">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Data Ruas Bibliografis
                            <span class="badge bg-primary ms-2"><?= count($data_bibliografis) ?> item</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="15%">Tag</th>
                                        <th width="50%">Nama Field</th>
                                        <th width="20%">Status</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data_bibliografis as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= $item->Tag ?></span>
                                            </td>
                                            <td><?= $item->Name ?></td>
                                            <td>
                                                <div class="status-badge">
                                                    <div class="toggle-switch <?= $item->Active ? 'active' : '' ?>" 
                                                         data-id="<?= $item->ID ?>" 
                                                         data-active="<?= $item->Active ?>">
                                                        <div class="toggle-slider"></div>
                                                    </div>
                                                    <span class="ms-2 status-text">
                                                        <?= $item->Active ? 'Aktif' : 'Tidak Aktif' ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif (!empty($selected_worksheet_id)): ?>
                <div class="card shadow-sm data-table">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada data</h5>
                        <p class="text-muted">Tidak ada ruas data bibliografis untuk jenis bahan yang dipilih.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm data-table">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Pilih Jenis Bahan</h5>
                        <p class="text-muted">Silakan pilih jenis bahan terlebih dahulu untuk menampilkan data ruas bibliografis.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
$(document).ready(function() {
    // Filter berdasarkan jenis bahan
    $('#jenis_bahan').on('change', function() {
        const worksheetId = $(this).val();
        if (worksheetId) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('worksheet_id', worksheetId);
            window.location.href = currentUrl.toString();
        }
    });

    // Clear filter
    $('#clearFilterBtn').on('click', function() {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.delete('worksheet_id');
        window.location.href = currentUrl.toString();
    });

    // Refresh button
    $('#refreshBtn').on('click', function() {
        location.reload();
    });

    // Toggle switch untuk mengubah status active
    $('.toggle-switch').on('click', function() {
        const $this = $(this);
        const id = $this.data('id');
        const currentActive = $this.data('active');
        const newActive = currentActive ? 0 : 1;
        
        // Tampilkan loading
        $this.css('pointer-events', 'none').css('opacity', '0.6');
        
        $.ajax({
            url: '<?= base_url('ruas-data-bibliografis/updateActive') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                active: newActive
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Update tampilan
                    $this.toggleClass('active');
                    $this.data('active', newActive);
                    $this.closest('tr').find('.status-text').text(newActive ? 'Aktif' : 'Tidak Aktif');
                    
                    // Tampilkan notifikasi sukses
                    showNotification('success', response.message);
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                showNotification('error', 'Terjadi kesalahan saat mengupdate status');
                console.error('Error:', error);
            },
            complete: function() {
                // Hapus loading
                $this.css('pointer-events', 'auto').css('opacity', '1');
            }
        });
    });

    // Function untuk menampilkan notifikasi
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const notification = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('body').append(notification);
        
        // Auto hide after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>
<?= $this->endSection('script'); ?>