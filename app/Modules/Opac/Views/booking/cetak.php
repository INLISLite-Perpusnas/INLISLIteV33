<?php
/**
 * Tanda terima pemesanan koleksi (setara opac/views/booking/cetak-booking.php INLISLite 3).
 * @var object|null $anggota
 * @var array $booking
 * @var string|null $namaPerpustakaan
 */
$cell = 'border:1px solid #000;padding:4px 6px;';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima Pemesanan Koleksi</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 16px; }
        table { border-collapse: collapse; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div style="text-align:center;font-size:16px;font-weight:bold;">
        TANDA TERIMA PEMESANAN KOLEKSI<br>
        <?= esc($namaPerpustakaan ?? '') ?>
    </div>
    <hr>
    <table width="100%">
        <tr>
            <td width="170">No. Anggota / No. Pengunjung</td>
            <td>: <?= esc($anggota->MemberNo ?? '') ?></td>
            <td width="80">Tanggal</td>
            <td>: <?= date('d-m-Y H:i:s') ?></td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: <?= esc($anggota->Fullname ?? '') ?></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <th style="<?= $cell ?>width:40px;">No</th>
            <th style="<?= $cell ?>">Judul Buku</th>
            <th style="<?= $cell ?>width:120px;">No. Barcode</th>
            <th style="<?= $cell ?>width:150px;">Jatuh Tempo</th>
        </tr>
        <?php foreach ($booking as $i => $row): ?>
            <tr>
                <td style="<?= $cell ?>text-align:center;"><?= $i + 1 ?></td>
                <td style="<?= $cell ?>"><?= esc($row->Title) ?></td>
                <td style="<?= $cell ?>text-align:center;"><?= esc($row->NomorBarcode) ?></td>
                <td style="<?= $cell ?>text-align:center;"><?= date('d-m-Y H:i:s', strtotime($row->BookingExpiredDate)) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$booking): ?>
            <tr><td colspan="4" style="<?= $cell ?>text-align:center;">Tidak ada pesanan aktif</td></tr>
        <?php endif; ?>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td style="text-align:center;font-weight:bold;">Peminjam</td>
            <td style="text-align:center;font-weight:bold;">Petugas</td>
        </tr>
        <tr>
            <td style="text-align:center;height:110px;vertical-align:bottom;">(......................................................)</td>
            <td style="text-align:center;height:110px;vertical-align:bottom;">(......................................................)</td>
        </tr>
    </table>
</body>
</html>
