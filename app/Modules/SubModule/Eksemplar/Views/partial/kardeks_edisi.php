<?php
/**
 * Rincian Edisi Serial satu katalog + eksemplar per edisi
 * (setara _subEdisiserial.php dan _subEksemplar.php INLISLite 3).
 * @var int   $catalogId
 * @var array $edisi
 * @var array $byEdisi
 */
?>
<?php if (!$edisi): ?>
    <p class="text-muted mb-0 p-2">Belum ada eksemplar untuk katalog ini.</p>
<?php else: ?>
    <table class="table table-sm table-bordered mb-0 bg-white">
        <thead class="bg-warning">
            <tr>
                <th style="width:40px;"></th>
                <th class="text-center" style="width:40px;">#</th>
                <th>Edisi Serial</th>
                <th>Tanggal Terbit Edisi Serial</th>
                <th class="text-center" style="width:100px;">Eksemplar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($edisi as $i => $row): ?>
                <?php
                $collapseId = 'kardeks-' . $catalogId . '-' . $i;
                $items = $byEdisi[$row['EDISISERIAL'] . '|' . $row['TANGGAL_TERBIT_EDISI_SERIAL']] ?? [];
                ?>
                <tr>
                    <td class="text-center">
                        <a role="button" data-toggle="collapse" href="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>" title="Lihat eksemplar">
                            <i class="fa fa-caret-square-down fa-lg"></i>
                        </a>
                    </td>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td><?= esc($row['EDISISERIAL'] ?: '-') ?></td>
                    <td><?= $row['TANGGAL_TERBIT_EDISI_SERIAL'] ? date('d-m-Y', strtotime($row['TANGGAL_TERBIT_EDISI_SERIAL'])) : '-' ?></td>
                    <td class="text-center"><?= (int) $row['Eksemplar'] ?></td>
                </tr>
                <tr class="collapse" id="<?= $collapseId ?>">
                    <td colspan="5" class="p-2">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th class="text-center" style="width:40px;">#</th>
                                    <th>Nomor Barcode</th>
                                    <th>No. Induk</th>
                                    <th>Jenis Sumber</th>
                                    <th>Bentuk Fisik</th>
                                    <th>Kategori</th>
                                    <th class="text-right">Harga</th>
                                    <th>Tanggal Pengadaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $j => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $j + 1 ?></td>
                                        <td><?= esc($item['NomorBarcode']) ?></td>
                                        <td><?= esc($item['NoInduk']) ?></td>
                                        <td><?= esc($item['Source'] ?? '-') ?></td>
                                        <td><?= esc($item['Media'] ?? '-') ?></td>
                                        <td><?= esc($item['Category'] ?? '-') ?></td>
                                        <td class="text-right"><?= $item['Price'] !== null ? number_format((float) $item['Price'], 0, ',', '.') : '-' ?></td>
                                        <td><?= $item['TanggalPengadaan'] ? date('d-m-Y', strtotime($item['TanggalPengadaan'])) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
