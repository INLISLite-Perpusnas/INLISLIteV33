<?php
$prefix = '';
if (isset($branch->slug) && $branch->slug != null) {
	$prefix = $branch->slug . '/';
} else if (isset($branch->Name) && trim($branch->Name) != '') {
	$prefix = str_slugify($branch->Name) . '/';
}





?>
<div class="mb-3 card">
    <div class="card-header">
        <i class="header-icon lnr-cart icon-gradient bg-success"> </i>
        TROLI PEMINJAMAN
        <div class="btn-actions-pane-right actions-icon-btn">
            <a data-toggle="modal" data-target="#modal_koleksi" href="javascript:void(0);" class="btn btn-success" title="Daftar Koleksi">
                <i class="fa fa-th-list"></i> Daftar Koleksi
            </a>
        </div>
    </div>
    <div class="card-body">
    <form method="post" action="<?= base_url(($prefix ?? '') . '/peminjaman-mandiri/create') ?>">

            <div class="row">
                <div class="col-md-4">
                    <div class="select-wrapper input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Nomor Barcode Buku</span>
                        </div>
                        <input class="form-control" type="text" name="NomorBarcode" value="" autofocus>
                        <input type="hidden" name="member_no" value="<?=$member->MemberNo?>">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table style="width: 100%;" id="tbl_carts" class="table table-hover table-striped table-bordered">
                    <thead class="bg-night-sky text-light">
                        <tr>
                            <th class="text-center">No. Barcode</th>
                            <th class="text-center">Penerbit / Judul</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cart-tbody">
                        <?php 
                        $cart = session()->get('cart') ?? [];
                        foreach ($cart as $item) : 
                        ?>
                            <tr>
                                <td>
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <i class="far fa-qrcode fa-2x text-info"></i>
                                            </div>
                                            <div class="widget-content-left">
                                                <div class="widget-heading"><?= $item['NomorBarcode'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <i class="far fa-book fa-2x text-info"></i>
                                            </div>
                                            <div class="widget-content-left">
                                                <div class="widget-heading text-primary"><?= $item['Publisher'] ?></div>
                                                <div class="widget-heading"><?= $item['Title'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger remove-item" data-barcode="<?= $item['NomorBarcode'] ?>">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-block">
                <button type="submit" class="btn btn-primary" style="min-width: 120px">
                    <i class="fa fa-save"></i> Simpan ke Daftar Peminjaman
                </button>
                <button type="button" id="empty_cart" class="btn btn-outline-danger" style="min-width: 120px">
                    <i class="fa fa-trash"></i> Kosongkan Troli
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->section('script'); ?>
<script>
// Get prefix from PHP variable 
const prefix = '<?= $prefix ?? '' ?>';
const member_no = '<?= $member_no ?>';

// Handle barcode input
document.querySelector('input[name="NomorBarcode"]').addEventListener('change', function() {
    let barcode = this.value;
    this.value = '';

    fetch(`<?= base_url() ?>/${prefix ? prefix + '/' : ''}peminjaman-mandiri/check_barcode`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ barcode: barcode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                timer: 1500
            }).then(() => {
                // Fixed redirect URL to include prefix only if it exists
                window.location.href = "<?= base_url() ?>/" + (prefix ? prefix + "/" : "") + "peminjaman-mandiri?member_no=" + member_no;
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error'
            }).then(() => {
                window.location.href = "<?= base_url() ?>/" + (prefix ? prefix + "/" : "") + "peminjaman-mandiri?member_no=" + member_no;
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan yang tidak diharapkan',
            icon: 'error'
        });
    });
});

// Handle remove single item
document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', function() {
        const barcode = this.dataset.barcode;
    
        
        fetch(`<?= base_url() ?>/${prefix}/peminjaman-mandiri/remove_item`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ barcode: barcode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = `peminjaman-mandiri?member_no=${member_no}`;
            }
        });
    });
});

// Handle empty cart
document.getElementById('empty_cart').addEventListener('click', function() {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Semua item akan dihapus dari troli!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, kosongkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= base_url() ?>/${prefix}/peminjaman-mandiri/clear_cart`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = `${prefix}/peminjaman-mandiri?member_no=${member_no}`;
                }
            });
        }
    });
});
</script>

<?= $this->include('SelfLoan\Views\modal_koleksi'); ?>
<?= $this->endSection('script'); ?>