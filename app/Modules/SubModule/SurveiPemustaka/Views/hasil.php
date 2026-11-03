<?php
/**
 * Isi modal "Hasil Survey": satu grafik pie per pertanyaan pilihan ganda.
 * @var array $charts
 */
?>
<?php foreach ($charts as $chart): ?>
    <canvas id="myChart<?= $chart['id'] ?>" height="160"
        data-chart="<?= esc(json_encode(['title' => $chart['title'], 'labels' => $chart['labels'], 'data' => $chart['data']]), 'attr') ?>"></canvas>
    <hr>
<?php endforeach; ?>
<?php if (!$charts): ?>
    <center><h3>Data Kosong</h3></center>
<?php endif; ?>
