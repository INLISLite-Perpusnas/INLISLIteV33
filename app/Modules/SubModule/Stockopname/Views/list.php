<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
 
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    .btn-group .btn {
        margin-right: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    .page-title-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .breadcrumb {
        background: transparent;
        margin-bottom: 0;
        padding: 0;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: #6c757d;
    }
    
    .table-responsive {
        border-radius: 0.375rem;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #dee2e6;
    }
    
    .search-filter-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>
                    <h2>Stock Opname</h2>
                    <div class="page-title-subheading">
                        Kelola data stock opname perusahaan
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard') ?>">
                                <i class="fa fa-home"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            Stock Opname
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter and Search Card -->
    <div class="card search-filter-card mb-3">
        <div class="card-body">
            <form method="get" action="<?= base_url('stockopname') ?>">
                <div class="row align-items-end">
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="form-group mb-2">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="<?= service('request')->getGet('search') ?? '' ?>" 
                                   placeholder="Nama projek, koordinator...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <div class="form-group mb-2">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select class="form-control" id="tahun" name="tahun">
                                <option value="">-- Semua Tahun --</option>
                                <?php for ($year = date('Y'); $year >= 2020; $year--): ?>
                                    <option value="<?= $year ?>" <?= (service('request')->getGet('tahun') == $year) ? 'selected' : '' ?>>
                                        <?= $year ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="form-group mb-2">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fa fa-search"></i> Cari
                            </button>
                            <a href="<?= base_url('stockopname') ?>" class="btn btn-light">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 col-sm-12">
                        <div class="form-group mb-2 text-end">
                            <a href="<?= base_url('stockopname/create') ?>" class="btn btn-success">
                                <i class="fa fa-plus"></i> Tambah Baru
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fa fa-list me-2"></i>
                Daftar Stock Opname
            </h5>
            <div class="card-header-actions">
                <span class="badge badge-info">
                    Total: <?= isset($stockopnames) ? count($stockopnames) : 0 ?> data
                </span>
            </div>
        </div>
        <div class="card-body">
            <!-- Display Messages -->
            <?php if (session()->getFlashdata('message')): ?>
                <?php $message = session()->getFlashdata('message'); ?>
                <div class="alert alert-<?= $message['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= $message['text'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (isset($stockopnames) && count($stockopnames) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Nama Projek</th>
                                <th width="10%">Tahun</th>
                                <th width="20%">Koordinator</th>
                                <th width="15%">Tanggal Mulai</th>
                                <th width="10%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            if (isset($pager)) {
                                $no = ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1;
                            }
                            ?>
                            <?php foreach ($stockopnames as $stockopname): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($stockopname['ProjectName']) ?></strong>
                                        <?php if (!empty($stockopname['Keterangan'])): ?>
                                            <br><small class="text-muted"><?= esc(substr($stockopname['Keterangan'], 0, 50)) ?><?= strlen($stockopname['Keterangan']) > 50 ? '...' : '' ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($stockopname['Tahun']) ?></td>
                                    <td><?= esc($stockopname['Koordinator']) ?></td>
                                    <td><?= date('d M Y', strtotime($stockopname['TglMulai'])) ?></td>
                                    <td>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('stockopname/detail/' . $stockopname['ID']) ?>" 
                                               class="btn btn-info btn-sm" 
                                               title="Detail">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('stockopname/edit/' . $stockopname['ID']) ?>" 
                                               class="btn btn-warning btn-sm" 
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm btn-delete" 
                                                    data-id="<?= $stockopname['ID'] ?>"
                                                    data-name="<?= esc($stockopname['ProjectName']) ?>"
                                                    title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-clipboard-list"></i>
                    <h4>Belum Ada Data</h4>
                    <p>Belum ada data stock opname yang tersedia.</p>
                    <a href="<?= base_url('stockopname/create') ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Tambah Stock Opname Pertama
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus stock opname:</p>
                <p><strong id="delete-name"></strong></p>
                <p class="text-danger"><small>Data yang dihapus tidak dapat dikembalikan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
$(document).ready(function() {
    // Delete functionality
    let deleteId = null;
    
    $('.btn-delete').on('click', function() {
        deleteId = $(this).data('id');
        const name = $(this).data('name');
        
        $('#delete-name').text(name);
        $('#deleteModal').modal('show');
    });
    
    $('#confirm-delete').on('click', function() {
        if (deleteId) {
            // Show loading state
            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghapus...');
            
            $.ajax({
                url: '<?= base_url('stockopname/delete') ?>/' + deleteId,
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    
                    if (response.status === 'success') {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message, 'Berhasil');
                        } else {
                            alert(response.message);
                        }
                        
                        // Reload page after delay
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message, 'Error');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('#deleteModal').modal('hide');
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Terjadi kesalahan saat menghapus data.', 'Error');
                    } else {
                        alert('Terjadi kesalahan saat menghapus data.');
                    }
                },
                complete: function() {
                    $('#confirm-delete').prop('disabled', false).html('Hapus');
                    deleteId = null;
                }
            });
        }
    });
    
    // Reset modal when closed
    $('#deleteModal').on('hidden.bs.modal', function() {
        deleteId = null;
        $('#confirm-delete').prop('disabled', false).html('Hapus');
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});
</script>
<?= $this->endSection('script'); ?>