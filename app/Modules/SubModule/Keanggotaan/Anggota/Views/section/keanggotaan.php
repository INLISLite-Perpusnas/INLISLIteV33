<form id="frm" method="post" action="<?= base_url('anggota/edit/' . $anggota->ID) ?>">
    <div class="form-group mt-1">
        <?php if (!$is_anggota) : ?>
            <a target="_blank" href="<?= base_url('anggota/printanggota/' . $anggota->ID . '?slug=template-1'); ?>" data-toggle="tooltip" data-placement="top" title="Cetak Kartu" class="btn btn-lg btn-primary"><i class="fa fa-print"></i> Cetak Kartu Anggota
            </a>
            <a href="javascript:void(0);" data-href="<?= base_url('anggota/printkartubelakang/' . $anggota->ID . '?slug=template-2'); ?>" data-toggle="tooltip" data-placement="top" title="Cetak Kartu" class="btn btn-lg btn-primary remove-data"><i class="fa fa-print"></i> Cetak Kartu Belakang
            </a>

            <a href="javascript:void(0);" data-href="<?= base_url('anggota/bebaspustaka/' . $anggota->ID); ?>" data-toggle="tooltip" data-placement="top" title="Cetak Kartu" class="btn btn-lg btn-primary cetak-kartu"><i class="fa fa-print"></i> Cetak Bebas pustaka
            </a>
        <?php endif; ?>
    </div>
    <!-- info personal -->
    <?= $this->include("Anggota\Views\section\component_update\info_personal"); ?>

    <!-- info anggota -->
    <?= $this->include("Anggota\Views\section\component_update\info_anggota"); ?>

    <!-- info alamat -->
    <?= $this->include("Anggota\Views\section\component_update\info_alamat"); ?>
    
    
    <!-- info tambahan -->
    <?= $this->include("Anggota\Views\section\component_update\info_tambahan"); ?>

    <!-- upload foto -->
    <?= $this->include("Anggota\Views\section\component_update\upload_foto"); ?>

  
        <div class="form-group mt-1">
            <button type="submit" class="btn btn-lg btn-primary" id="btn-submit" name="submit">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
  
</form>