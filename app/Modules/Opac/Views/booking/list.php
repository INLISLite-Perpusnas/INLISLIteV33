<?php
/**
 * Isi modal "Keranjang Pesan" (setara opac/views/booking/_bookingList.php INLISLite 3).
 * @var string|null $memberNo
 * @var array $booking
 */
?>
<?php if ($memberNo === null): ?>
    <p class="text-muted mb-0">Silakan login sebagai anggota untuk melihat koleksi yang Anda pesan.</p>
<?php elseif (!$booking): ?>
    <p class="text-muted mb-0">Belum ada koleksi yang Anda pesan.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover table-sm mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width:40px;">No</th>
                    <th>Judul Buku</th>
                    <th>No. Barcode</th>
                    <th>Batas Ambil</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($booking as $i => $row): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><a href="<?= base_url('opac/detail/' . (int) $row->CatalogID) ?>"><?= esc($row->Title) ?></a></td>
                        <td><code><?= esc($row->NomorBarcode) ?></code></td>
                        <td class="text-nowrap"><?= date('d-m-Y H:i', strtotime($row->BookingExpiredDate)) ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm" data-booking-cancel="<?= (int) $row->ID ?>">Batal</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="small text-muted mt-2 mb-0">Tunjukkan tanda terima ke petugas dan ambil koleksi sebelum batas waktu.</p>
<?php endif; ?>
