<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bebas Pustaka</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #000; }
        .center { text-align: center; }
        .title { font-family: Calibri, sans-serif; font-size: 21px; font-weight: bold; }
        .label { font-size: 14px; display: inline-block; width: 220px; }
        .value { font-size: 14px; }
        p { margin-bottom: 6pt; }
        .signature-area { margin-top: 40px; text-align: right; padding-right: 60px; }
    </style>
</head>

<body>

    <p class="center"><span class="title">SURAT KETERANGAN BEBAS PUSTAKA</span></p>
    <p class="center">&nbsp;</p>
    <p class="center">Saya yang bertanda tangan di bawah ini menerangkan bahwa :</p>
    <p>&nbsp;</p>

    <!-- Nama - semua jenis perpustakaan -->
    <p>
        <span class="label">Nama</span>
        <span>: </span>
        <span class="value"><?= esc($anggota->Fullname ?? '') ?></span>
    </p>

    <!-- No Identitas - semua jenis perpustakaan -->
    <p>
        <span class="label">No. Identitas</span>
        <span>: KTP / NIK, </span>
        <span class="value"><?= esc($anggota->IdentityNo ?? '') ?></span>
    </p>

    <?php if ($jenis_perpustakaan === 'UMUM' || $jenis_perpustakaan === 'KHUSUS'): ?>

        <!-- Alamat - UMUM & KHUSUS -->
        <p>
            <span class="label">Alamat</span>
            <span>: </span>
            <span class="value"><?= esc($anggota->Address ?? '') ?></span>
        </p>

    <?php elseif ($jenis_perpustakaan === 'SEKOLAH'): ?>

        <!-- Alamat & Kelas - SEKOLAH -->
        <p>
            <span class="label">Alamat</span>
            <span>: </span>
            <span class="value"><?= esc($anggota->Address ?? '') ?></span>
        </p>
        <p>
            <span class="label">Kelas</span>
            <span>: </span>
            <span class="value"><?= esc($kelas_nama ?? '-') ?></span>
        </p>

    <?php elseif ($jenis_perpustakaan === 'PERGURUAN TINGGI'): ?>

        <!-- Fakultas & Jurusan - PERGURUAN TINGGI -->
        <p>
            <span class="label">Fakultas</span>
            <span>: </span>
            <span class="value"><?= esc($fakultas_nama ?? '-') ?></span>
        </p>
        <p>
            <span class="label">Jurusan / Program Studi</span>
            <span>: </span>
            <span class="value"><?= esc($jurusan_nama ?? '-') ?></span>
        </p>

    <?php endif; ?>

    <p>&nbsp;</p>
    <p>
        Telah terbebas dari tunggakan peminjaman koleksi di
        <strong><?= esc($nama_perpustakaan) ?></strong>.
    </p>
    <p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <div class="signature-area">
        <p><?= date('d F Y') ?></p>
        <br><br><br>
        <p>(_______________________)</p>
    </div>

</body>

</html>
