<?php

/**
 * Isi modal "Hasil Survey": satu grafik pie per pertanyaan pilihan ganda dan daftar jawaban isian.
 * @var array $charts
 * @var array $isianData
 */
?>
<?php foreach ($charts as $chart): ?>
    <canvas id="myChart<?= $chart['id'] ?>" height="160"
        data-chart="<?= esc(json_encode(['title' => $chart['title'], 'labels' => $chart['labels'], 'data' => $chart['data']]), 'attr') ?>"></canvas>
    <hr>
<?php endforeach; ?>

<?php foreach ($isianData as $isian): ?>
    <h6><?= esc($isian['title']) ?></h6>
    <?php if ($isian['answers']): ?>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th width="60" class="text-center">No</th>
                        <th>Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($isian['answers'] as $index => $answer): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td><?= nl2br(esc($answer['Isian'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">Belum ada jawaban</p>
    <?php endif; ?>
    <hr>
    </div>
<?php endforeach; ?>

<?php if (!$charts && !$isianData): ?>
    <center>
        <h3>Data Kosong</h3>
    </center>
<?php endif; ?>