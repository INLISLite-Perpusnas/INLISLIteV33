<?php
$request = service('request');
$worksheet_id = $catalog->Worksheet_id ?? 1;

$select2 = select_two();
$rda = $request->getGet('rda') ?? 1;
?>
<div class="card-header" style="min-height:100px">
	<div class="form-group col-md-12">
	<div class="row">
		<div class="col-md-6">
			<label for="worksheet_id" class="form-label">
				<i class="fa fa-check text-primary"></i> Jenis Bahan
			</label>
			<select class="form-control" name="Worksheet_id" id="worksheet_id">
				<?php foreach ($worksheets as $row) : ?>
					<option value="<?= $row->ID ?>" <?= set_select('Worksheet_id', $row->ID, ($catalog->Worksheet_id == $row->ID)); ?>>
						<?= $row->Name ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		
		<div class="col-md-6">
			<label for="catalog_type" class="form-label">
				<i class="fa fa-check text-primary"></i> Pedoman Katalog
			</label>
			<select class="form-control" name="IsRDA" id="catalog_type">
				<option value="0" <?= set_select('IsRDA', '0', ($catalog->IsRDA == 0)); ?>>
					Katalog AACR
				</option>
				<option value="1" <?= set_select('IsRDA', '1', ($catalog->IsRDA == 1)); ?>>
					Katalog RDA
				</option>
			</select>
		</div>
	</div>
</div>
</div>

<div class="card-header">
	<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Data Bibliografis
	<div class="btn-actions-pane-right actions-icon-btn">
		<div class="col-md-6">
			<a href="/katalog/edit_marc/<?= $catalog->ID ?>" class="btn btn-dark btn-lg"><i class="fa fa-th mr-2"></i>Tampilkan Form MARC</a>
		</div>
	</div>
</div>
<div class="card-body">
	<div class="card card-shadow mb-3">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Judul dan Penanggungjawab</div>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label for="judul">Judul Utama</label>
						<input type="text" class="form-control" name="judul[a]" id="judul" placeholder="" value="<?= ltrim_words($str_245['a'] ?? '') ?>" />
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label for="anakJudul">Anak Judul</label>
						<input type="text" class="form-control" name="judul[b]" id="anakJudul" placeholder="" value="<?= ltrim_words($str_245['b'] ?? '') ?>" />
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label for="penanggungJawab">Penanggung Jawab</label>
						<input type="text" class="form-control" name="judul[c]" id="penanggungJawab" placeholder="" value="<?= ltrim_words($str_245['c'] ?? '') ?>" />
					</div>
				</div>
				<?php if ($rda == 1) : ?>
					<div class="col-md-12">
						<div class="form-group">
							<label for="variasiBentukJudul">Variasi Bentuk Judul</label>
							<div id="variasiBentukJudul">
								<?php if (!empty(get_catalog_ruas_tag($catalog->ID, '246'))) : ?>
									<?php $idx_246 = 0; ?>
									<?php foreach (get_catalog_ruas_tag($catalog->ID, '246') as $row) : ?>
										<div id="variasiBentukJudul<?= $row->ID ?>" class="form input-group mb-2" title="">
											<input type="text" class="form-control" name="variasiBentukJudul[]" placeholder="" value="<?= trim(preg_replace('/\$a /', '', $row->Value)) ?>" />
											<div class="input-group-append">
												<span data-id="<?= $row->ID ?>" class="<?= ($idx_246 == 0) ? 'add-variasiBentukJudul' : 'remove-variasiBentukJudul' ?> btn btn-outline-secondary"><i class="fa fa-<?= ($idx_246 == 0) ? 'plus' : 'minus' ?>"></i></span>
											</div>
										</div>
										<?php $idx_246++; ?>
									<?php endforeach; ?>
								<?php else : ?>
									<div id="variasiBentukJudul0" class="form input-group mb-2" title="">
										<input type="text" class="form-control" name="variasiBentukJudul[]" placeholder="" value="" />
										<div class="input-group-append">
											<span data-id="0" class="add-variasiBentukJudul btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label for="judulAsli">Judul Asli</label>
							<div id="judulAsli">
								<?php if (!empty(get_catalog_ruas_tag($catalog->ID, '740'))) : ?>
									<?php $idx_740 = 0; ?>
									<?php foreach (get_catalog_ruas_tag($catalog->ID, '740') as $row) : ?>
										<div id="judulAsli<?= $row->ID ?>" class="form input-group mb-2" title="">
											<input type="text" class="form-control" name="judulAsli[]" placeholder="" value="<?= trim(preg_replace('/\$a /', '', $row->Value)) ?>" />
											<div class="input-group-append">
												<span data-id="<?= $row->ID ?>" class="<?= ($idx_740 == 0) ? 'add-judulAsli' : 'remove-judulAsli' ?> btn btn-outline-secondary"><i class="fa fa-<?= ($idx_740 == 0) ? 'plus' : 'minus' ?>"></i></span>
											</div>
										</div>
										<?php $idx_740++; ?>
									<?php endforeach; ?>
								<?php else : ?>
									<div id="judulAsli0" class="form input-group mb-2" title="">
										<input type="text" class="form-control" name="judulAsli[]" placeholder="" value="" />
										<div class="input-group-append">
											<span data-id="0" class="add-judulAsli btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php if ($catalog->Worksheet_id == 4) : ?>
						<div class="col-md-12 terbitanBerkala" style="display: block;">
							<div class="form-group">
								<label for="judulSeragam">Judul Seragam</label>
								<input type="text" class="form-control" name="judulSeragam" id="judulSeragam" placeholder="" value="<?= $str_240 ?? "" ?>" />
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($catalog->Worksheet_id == 4) : ?>
					<div class=" col-md-12 terbitanBerkala" style="display: block;">
						<div class="form-group">
							<label for="judulSebelumnya">Judul Sebelumnya</label>
							<input type="text" class="form-control" name="judulSebelumnya" id="judulSebelumnya" placeholder="" value="<?= $str_247['a'] ?? "" ?>" />
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-3">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Tajuk Pengarang</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group" id="pengarangUtamaGroup">
				<label for="pengarangUtama">Tajuk Pengarang Utama</label>
				<?php if (count(get_catalog_ruas_tags($catalog->ID, ['100', '110', '111'])) > 0) : ?>
					<?php $idxPengarangUtama = 0; ?>
					<?php foreach (get_catalog_ruas_tags($catalog->ID, ['100', '110', '111']) as $row) : ?>
						<div class="form-group pengarangUtamaWrapper" id="pengarangUtamaWrapper<?= $row->ID ?>">
							<div class="form input-group">
								<div class="input-group-prepend" style="width: 20%;">
									<select class="custom-select pengarangUtamaSelect" id="pengarangUtama-<?= $row->ID ?>">
										<option value="100" <?= ($row->Tag == '100') ? 'selected' : '' ?>>Nama Orang</option>
										<option value="110" <?= ($row->Tag == '110') ? 'selected' : '' ?>>Nama Badan</option>
										<option value="111" <?= ($row->Tag == '111') ? 'selected' : '' ?>>Nama Pertemuan</option>
									</select>
								</div>
								<input type="text" class="form-control" id="pengarangUtamaInput-<?= $row->ID ?>" name="pengarangUtama[<?= $row->Tag ?>-<?= $row->Indicator1 ?>][]" placeholder="" value="<?= trim(preg_replace('/\$a /', '', $row->Value)) ?>" />
							</div>
							<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
								<fieldset class="form-group">
									<div class="row" style="margin: 4px;">
										<div class="col-sm-4">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input pengarangUtamaRadio" type="radio" data-id="pengarangUtama-<?= $row->ID ?>" data-input="pengarangUtamaInput-<?= $row->ID ?>" name="pengarangUtamaRadio[]" value="0" <?= ($row->Indicator1 == 0) ? 'checked' : '' ?>>
													Nama Depan
												</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input pengarangUtamaRadio" type="radio" data-id="pengarangUtama-<?= $row->ID ?>" data-input="pengarangUtamaInput-<?= $row->ID ?>" name="pengarangUtamaRadio[]" value="1" <?= ($row->Indicator1 == 1) ? 'checked' : '' ?>>
													Nama Belakang
												</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input pengarangUtamaRadio" type="radio" data-id="pengarangUtama-<?= $row->ID ?>" data-input="pengarangUtamaInput-<?= $row->ID ?>" name="pengarangUtamaRadio[]" value="2" <?= ($row->Indicator1 == 2) ? 'checked' : '' ?>>
													Nama Keluarga
												</label>
											</div>
										</div>
									</div>
								</fieldset>
							</div>
						</div>
						<?php $idxPengarangUtama++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="form-group pengarangUtamaWrapper" id="pengarangUtamaWrapper">
						<div class="form input-group">
							<div class="input-group-prepend" style="width: 20%;">
								<select class="custom-select pengarangUtamaSelect" id="pengarangUtama">
									<option value="100" selected>Nama Orang</option>
									<option value="110">Nama Badan</option>
									<option value="111">Nama Pertemuan</option>
								</select>
							</div>
							<input type="text" class="form-control" id="pengarangUtamaInput" name="pengarangUtama[100-0][]" placeholder="" value="" />
						</div>
						<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
							<fieldset class="form-group">
								<div class="row" style="margin: 4px;">
									<div class="col-sm-4">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input pengarangUtamaRadio" type="radio" data-id="pengarangUtama" name="pengarangUtamaRadio[]" value="0" checked>
												Nama Depan
											</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input pengarangUtamaRadio" type="radio" data-id="pengarangUtama" name="pengarangUtamaRadio[]" value="1">
												Nama Belakang
											</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input pengarangUtamaRadio" type="radio" data-id="pengarangUtama" name="pengarangUtamaRadio[]" value="2">
												Nama Keluarga
											</label>
										</div>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<div class="form-group" id="pengarangTambahanGroup">
				<label for="pengarangTambahan">Tajuk Pengarang Tambahan</label>
				<?php if (count(get_catalog_ruas_tags($catalog->ID, ['700', '710', '711'])) > 0) : ?>
					<?php $idxPengarangTambahan = 0; ?>
					<?php foreach (get_catalog_ruas_tags($catalog->ID, ['700', '710', '711']) as $row) : ?>
						<div class="form-group pengarangTambahanWrapper mb-2" id="pengarangTambahanWrapper<?= $row->ID ?>">
							<div class="form input-group">
								<div class="input-group-prepend" style="width: 20%;">
									<select class="custom-select pengarangTambahanSelect" id="pengarangTambahanSelect-<?= $row->ID ?>" data-input="pengarangTambahanInput-<?= $row->ID ?>">
										<option value="700" <?= ($row->Tag == '700') ? 'selected' : '' ?>>Nama Orang</option>
										<option value="710" <?= ($row->Tag == '710') ? 'selected' : '' ?>>Nama Badan</option>
										<option value="711" <?= ($row->Tag == '711') ? 'selected' : '' ?>>Nama Pertemuan</option>
									</select>
								</div>
								<input type="text" class="form-control" id="pengarangTambahanInput-<?= $row->ID ?>" name="pengarangTambahan[<?= $row->Tag ?>-<?= $row->Indicator1 ?>][]" placeholder="" value="<?= trim(preg_replace('/\$a /', '', $row->Value)) ?>" />
								<div class="input-group-append">
									<span data-id="<?= $row->ID ?>" class="<?= ($idxPengarangTambahan == 0) ? 'add-pengarangTambahan' : 'remove-pengarangTambahan' ?> btn btn-outline-secondary"><i class="fa fa-<?= ($idxPengarangTambahan == 0) ? 'plus' : 'minus' ?>"></i></span>
								</div>
							</div>
							<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
								<fieldset class="form-group">
									<div class="row" style="margin: 4px;">
										<div class="col-sm-4">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect-<?= $row->ID ?>" data-input="pengarangTambahanInput-<?= $row->ID ?>" name="pengarangTambahanRadio[<?= $idxPengarangTambahan ?>][]" value="0" <?= ($row->Indicator1 == 0) ? 'checked' : '' ?>>
													Nama Depan
												</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect-<?= $row->ID ?>" data-input="pengarangTambahanInput-<?= $row->ID ?>" name="pengarangTambahanRadio[<?= $idxPengarangTambahan ?>][]" value="1" <?= ($row->Indicator1 == 1) ? 'checked' : '' ?>>
													Nama Belakang
												</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect-<?= $row->ID ?>" data-input="pengarangTambahanInput-<?= $row->ID ?>" name="pengarangTambahanRadio[<?= $idxPengarangTambahan ?>][]" value="2" <?= ($row->Indicator1 == 2) ? 'checked' : '' ?>>
													Nama Keluarga
												</label>
											</div>
										</div>
									</div>
								</fieldset>
							</div>
						</div>
						<?php $idxPengarangTambahan++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="form-group pengarangTambahanWrapper" id="pengarangTambahanWrapper0">
						<div class="form input-group">
							<div class="input-group-prepend" style="width: 20%;">
								<select class="custom-select pengarangTambahanSelect" id="pengarangTambahanSelect" data-input="pengarangTambahanInput">
									<option value="700" selected>Nama Orang</option>
									<option value="710">Nama Badan</option>
									<option value="711">Nama Pertemuan</option>
								</select>
							</div>
							<input type="text" class="form-control" id="pengarangTambahanInput" name="pengarangTambahan[700-0][]" placeholder="" value="" />
							<div class="input-group-append">
								<span data-id="0" class="add-pengarangTambahan btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
							</div>
						</div>
						<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
							<fieldset class="form-group">
								<div class="row" style="margin: 4px;">
									<div class="col-sm-4">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect" data-input="pengarangTambahanInput" name="pengarangTambahanRadio[0][]" value="0" checked>
												Nama Depan
											</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect" data-input="pengarangTambahanInput" name="pengarangTambahanRadio[0][]" value="1">
												Nama Belakang
											</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input pengarangTambahanRadio" type="radio" data-select="pengarangTambahanSelect" data-input="pengarangTambahanInput" name="pengarangTambahanRadio[0][]" value="2">
												Nama Keluarga
											</label>
										</div>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-3">
        <div class="card-header">
            <div class="custom-title-wrap bar-primary">
                <div class="custom-title">Penerbitan</div>
            </div>
        </div>
        <div class="card-body">
            <?php 
                // Jika RDA (1) gunakan str_264, jika AACR (0) gunakan str_260
                // Kita gunakan operator null coalescing (??) untuk mencegah error jika variabel tidak ada
                $pubData = ($catalog->IsRDA == 1) ? ($str_264 ?? []) : ($str_260 ?? []);
            ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="varchar">Tempat Terbit</label>
                        <input type="text" class="form-control" name="penerbit[a]" id="PublishLocation" placeholder="" value="<?= trim($pubData['a'] ?? '') ?>" />
                    </div>
                </div>
                <div class=" col-md-6">
                    <div class="form-group">
                        <label for="varchar">Penerbit</label>
                        <input type="text" class="form-control" name="penerbit[b]" id="Publisher" placeholder="" value="<?= trim($pubData['b'] ?? '') ?>" />
                    </div>
                </div>
                <div class=" col-md-6">
                    <div class="form-group">
                        <label for="varchar">Tahun Terbit</label>
                        <input type="text" class="form-control" name="penerbit[c]" id="PublishYear" placeholder="" value="<?= trim($pubData['c'] ?? '') ?>" />
                    </div>
                </div>

                <?php if ($catalog->Worksheet_id == 4) : ?>
                    <div class=" col-md-6 terbitanBerkala" style="display: block;">
                        <div class="form-group">
                            <label for="frekuensi">Frekuensi Saat Ini</label>
                            <input type="text" class="form-control" name="frekuensi" id="frekuensi" placeholder="" value="<?= trim($str_310['a'] ?? '') ?>" />
                        </div>
                    </div>
                    <div class=" col-md-12 terbitanBerkala" style="display: block;">
                        <div class="form-group">
                            <label for="frekuensiSebelumnya">Frekuensi Publikasi Sebelumnya</label>
                            <div id="frekuensiSebelumnya">
                                <?php if (get_catalog_ruas_tag($catalog->ID, '321')) : ?>
                                    <?php $idx_321 = 0; ?>
                                    <?php foreach (get_catalog_ruas_tag($catalog->ID, '321') as $row) : ?>
                                        <div id="frekuensiSebelumnya<?= $row->ID ?>" class="form input-group mb-2" title="">
                                            <input type="text" class="form-control" name="frekuensiSebelumnya[]" placeholder="" value="<?= trim(preg_replace('/\$a /', '', $row->Value)) ?>" />
                                            <div class="input-group-append">
                                                <span data-id="<?= $row->ID ?>" class="<?= ($idx_321 == 0) ? 'add-frekuensiSebelumnya' : 'remove-frekuensiSebelumnya' ?> btn btn-outline-secondary"><i class="fa fa-<?= ($idx_321 == 0) ? 'plus' : 'minus' ?>"></i></span>
                                            </div>
                                        </div>
                                        <?php $idx_321++; ?>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div id="frekuensiSebelumnya0" class="form input-group mb-2" title="">
                                        <input type="text" class="form-control" name="frekuensiSebelumnya[]" placeholder="" value="" />
                                        <div class="input-group-append">
                                            <span data-id="0" class="add-frekuensiSebelumnya btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

	<div class="card card-shadow mb-3">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Deskripsi Fisik</div>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="varchar">Jumlah Halaman</label>
						<input type="text" class="form-control" name="PhysicalDescription[a]" id="jumlahHalaman" placeholder="" value="<?= trim($str_300['a'] ?? '') ?>" />
					</div>
				</div>
				<div class=" col-md-6">
					<div class="form-group">
						<label for="varchar">Ket. Ilustrasi</label>
						<input type="text" class="form-control" name="PhysicalDescription[b]" id="ketIllus" placeholder="" value="<?= trim($str_300['b'] ?? '') ?>" />
					</div>
				</div>
				<div class=" col-md-6">
					<div class="form-group">
						<label for="varchar">Dimensi</label>
						<input type="text" class="form-control" name="PhysicalDescription[c]" id="dimensi" placeholder="" value="<?= trim($str_300['c'] ?? '') ?>" />
					</div>
				</div>
				<div class=" col-md-6">
					<div class="form-group">
						<label for="varchar">Bahan Sertaan</label>
						<input type="text" class="form-control" name="PhysicalDescription[e]" id="bahanSertaan" placeholder="" value="<?= trim($str_300['d'] ?? '') ?>" />
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-3">
		<div class="card-body">
			<div class="row">
				<?php if ($rda == 1) : ?>
					<div class="col-md-6">
						<div class="form-group">
							<label for="jenisIsi">Jenis Isi</label>
							<input type="text" class="form-control" name="jenisIsi" id="jenisIsi" placeholder="" value="<?= trim($str_336[0] ?? '') ?>" />
						</div>
					</div>
					<div class=" col-md-6">
						<div class="form-group">
							<label for="jenisMedia">Jenis Media</label>
							<input type="text" class="form-control" name="jenisMedia" id="jenisMedia" placeholder="" value="<?= trim($str_337[0] ?? '') ?>" />
						</div>
					</div>
					<div class=" col-md-6">
						<div class="form-group">
							<label for="jenisWadah">Jenis Wadah</label>
							<input type="text" class="form-control" name="jenisWadah" id="jenisWadah" placeholder="" value="<?= trim($str_338[0] ?? '') ?>" />
						</div>
					</div>
				<?php endif; ?>
				<div class=" col-md-6">
					<div class="form-group">
						<label for="varchar">Edisi</label>
						<input type="text" class="form-control" name="Edition" id="Edition" placeholder="" value="<?= trim($catalog->Edition  ?? '') ?>" />
					</div>
				</div>
			</div>
			<div class="form-group" id="subjectGroup">
				<label for="subject">Subject</label>
				<?php if (get_catalog_ruas_tags($catalog->ID, ['600', '650', '651'])) : ?>
					<?php $idxSubject = 0; ?>
					<?php foreach (get_catalog_ruas_tags($catalog->ID, ['600', '650', '651']) as $row) : ?>
						<div class="form-group subjectWrapper mb-2" id="subjectWrapper<?= $row->ID ?>">
							<div class="form input-group">
								<div class="input-group-prepend" style="width: 20%;">
									<select class="custom-select subjectSelect" id="subjectSelect-<?= $row->ID ?>" data-input="subjectInput-<?= $row->ID ?>">
										<option value="600" <?= ($row->Tag == '600') ? 'selected' : '' ?>>Nama Orang</option>
										<option value="650" <?= ($row->Tag == '650') ? 'selected' : '' ?>>Topikal</option>
										<option value="651" <?= ($row->Tag == '651') ? 'selected' : '' ?>>Nama Geografis</option>
									</select>
								</div>
								<input type="text" class="form-control" id="subjectInput-<?= $row->ID ?>" name="subject[<?= $row->Tag ?>-<?= $row->Indicator1 ?>][]" placeholder="" value="<?= trim(preg_replace('/\$a /', '', $row->Value)) ?>" />
								<div class="input-group-append">
									<span data-id="<?= $row->ID ?>" class="<?= ($idxSubject == 0) ? 'add-subject' : 'remove-subject' ?> btn btn-outline-secondary"><i class="fa fa-<?= ($idxSubject == 0) ? 'plus' : 'minus' ?>"></i></span>
								</div>
							</div>
						</div>
						<?php $idxSubject++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="form-group subjectWrapper" id="subjectWrapper0">
						<div class="form input-group">
							<div class="input-group-prepend" style="width: 20%;">
								<select class="custom-select subjectSelect" id="subjectSelect-0" data-input="subjectInput-0">
									<option value="600" selected>Nama Orang</option>
									<option value="650">Topikal</option>
									<option value="651">Nama Geografis</option>
								</select>
							</div>
							<input type="text" class="form-control" id="subjectInput-0" name="subject[600-0][]" placeholder="" value="" />
							<div class="input-group-append">
								<span data-id="0" class="add-subject btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<label for="varchar">No. Klas DDC</label>
				<input type="text" class="form-control" name="DeweyNo" id="DeweyNo" placeholder="" value="<?= trim($str_082[0] ?? '') ?>" />
			</div>
			<div class=" form-group">
				<label for="CallNumber">No. Panggil</label>
				<div id="CallNumber">
					<?php if (count(get_catalog_ruas_tags($catalog->ID, ['084'])) > 0) : ?>
						<?php $idx_084 = 0; ?>
						<?php foreach (get_catalog_ruas_tag($catalog->ID, '084') as $row) : ?>
							<div id="CallNumber<?= $row->ID ?>" class="form input-group mb-2" title="">
								<input type="text" class="form-control" name="CallNumber[]" placeholder="" value="<?= trim(preg_replace('/\$a /', '', $row->Value)) ?>" />
								<div class="input-group-append">
									<span data-id="<?= $row->ID ?>" class="<?= ($idx_084 == 0) ? 'add-CallNumber' : 'remove-CallNumber' ?> btn btn-outline-secondary"><i class="fa fa-<?= ($idx_084 == 0) ? 'plus' : 'minus' ?>"></i></span>
								</div>
							</div>
							<?php $idx_084++; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="form input-group mb-2" title="">
							<input type="text" class="form-control" name="CallNumber[]" placeholder="" value="" />
							<div class="input-group-append">
								<span data-id="0" class="add-CallNumber btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="form-group">
				<label for="ISBN">ISBN</label>
				<div id="ISBN">
					<?php if (get_catalog_ruas_tag($catalog->ID, '020')) : ?>
						<?php $idx_020 = 0; ?>
						<?php foreach (get_catalog_ruas_tag($catalog->ID, '020') as $row) : ?>
							<div id="ISBN<?= $row->ID ?>" class="form input-group mb-2" title="">
								<input type="text" class="form-control" name="ISBN[]" placeholder="" value="<?= trim(preg_replace('/\$a /', '', $row->Value)) ?>" />
								<div class="input-group-append">
									<span data-id="<?= $row->ID ?>" class="<?= ($idx_020 == 0) ? 'add-ISBN' : 'remove-ISBN' ?> btn btn-outline-secondary"><i class="fa fa-<?= ($idx_020 == 0) ? 'plus' : 'minus' ?>"></i></span>
								</div>
							</div>
							<?php $idx_020++; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="form input-group mb-2" title="">
							<input type="text" class="form-control" name="ISBN[]" placeholder="" value="" />
							<div class="input-group-append">
								<span data-id="0" class="add-ISBN btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</div>

	<div class="card card-shadow mb-3">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Catatan</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group" id="catatanGroup">
				<label for="varchar">Catatan</label>
				<?php if (count(get_catalog_ruas_tags($catalog->ID, ['520', '502', '504', '505', '500'])) > 0) : ?>
					<?php $idxCatatan = 0; ?>
					<?php foreach (get_catalog_ruas_tags($catalog->ID, ['520', '502', '504', '505', '500']) as $row) : ?>
						<div class="form-group catatanWrapper" id="catatanWrapper<?= $row->ID ?>">
							<div class="form input-group">
								<textarea class="form-control" id="catatanInput-<?= $row->ID ?>" name="catatan[<?= $row->Tag ?>-0][]" placeholder="" rows="1"><?= trim(preg_replace('/\$a /', '', $row->Value)) ?></textarea>
								<div class="input-group-append">
									<span data-id="<?= $row->ID ?>" class="<?= ($idxCatatan == 0) ? 'add-catatan' : 'remove-catatan' ?> btn btn-outline-secondary"><i class="fa fa-<?= ($idxCatatan == 0) ? 'plus' : 'minus' ?>"></i></span>
								</div>
							</div>
							<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
								<fieldset class="form-group">
									<div class="row" style="margin: 4px;">
										<div class="col-sm-2">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-<?= $row->ID ?>" name="catatanRadio[<?= $idxCatatan ?>][0]" value="520" <?= ($row->Tag == 520) ? 'checked' : '' ?>>
													Abstrak / Anotasi
												</label>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-<?= $row->ID ?>" name="catatanRadio[<?= $idxCatatan ?>][0]" value="502" <?= ($row->Tag == 502) ? 'checked' : '' ?>>
													Catatan Disertasi
												</label>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-<?= $row->ID ?>" name="catatanRadio[<?= $idxCatatan ?>][0]" value="504" <?= ($row->Tag == 504) ? 'checked' : '' ?>>
													Catatan Bibliografi
												</label>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-<?= $row->ID ?>" name="catatanRadio[<?= $idxCatatan ?>][0]" value="505" <?= ($row->Tag == 505) ? 'checked' : '' ?>>
													Rincian Isi
												</label>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-<?= $row->ID ?>" name="catatanRadio[<?= $idxCatatan ?>][0]" value="500" <?= ($row->Tag == 500) ? 'checked' : '' ?>>
													Catatan Umum
												</label>
											</div>
										</div>
									</div>
								</fieldset>
							</div>
						</div>
						<?php $idxCatatan++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="form-group catatanWrapper" id="catatanWrapper0">
						<div class="form input-group">
							<textarea class="form-control" id="catatanInput-0" name="catatan[520-0][]" placeholder="" rows="1"></textarea>
							<div class="input-group-append">
								<span data-id="0" class="add-catatan btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
							</div>
						</div>
						<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
							<fieldset class="form-group">
								<div class="row" style="margin: 4px;">
									<div class="col-sm-2">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-0" name="catatanRadio[0]" value="520" checked>
												Abstrak / Anotasi
											</label>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-0" name="catatanRadio[0]" value="502">
												Catatan Disertasi
											</label>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-0" name="catatanRadio[0]" value="504">
												Catatan Bibliografi
											</label>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-0" name="catatanRadio[0]" value="505">
												Rincian Isi
											</label>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-check">
											<label class="form-check-label">
												<input class="form-check-input catatanRadio" type="radio" data-input="catatanInput-0" name="catatanRadio[0]" value="500">
												Catatan Umum
											</label>
										</div>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-3">
		<div class="card-body">
			<div class="form-group">
				<label for="varchar">Bahasa</label>
				<select class="search form-control" id="Languages" name="Languages[lang]">
					<?php foreach ($select2['lang'] as $key => $lang) : ?>
						<option value="<?= $lang->Code ?>" <?= ($lang->Code == $cr_008_lang) ? 'selected' : '' ?>>
							<?= $lang->Name ?>
						</option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="form-group">
				<label for="varchar">Bentuk Karya Tulis</label>
				<select class="search form-control" id="bent-karya-tulis" name="Languages[bkt]">
					<?php foreach ($select2['karya-tulis'] as $key => $lang) : ?>
						<option value="<?= $lang->Code ?>" <?= ($lang->Code == $cr_008_bkt) ? 'selected' : '' ?>>
							<?= $lang->Name ?>
						</option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="form-group">
				<label for="varchar">Kelompok Sasaran</label>
				<select class="search form-control" id="kelompok-sasaran" name="Languages[ks]">
					<?php foreach ($select2['kelompok-sasaran'] as $key => $lang) : ?>
						<option value="<?= $lang->Code ?>" <?= ($lang->Code == $cr_008_ks) ? 'selected' : '' ?>>
							<?= $lang->Name ?>
						</option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-3">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Lokasi Koleksi Daring</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="LocCollDaring">Lokasi Koleksi Daring</label>
				<div id="LocCollDaring">
					<?php if (count(get_catalog_ruas_tag($catalog->ID, '856')) > 0) : ?>
						<?php $idx_856 = 0; ?>
						<?php foreach (get_catalog_ruas_tag($catalog->ID, '856') as $row) : ?>
							<div id="LocCollDaring<?= $row->ID ?>" class="form input-group mb-2" title="">
								<input type="text" class="form-control" name="LocCollDaring[]" placeholder="" value="<?= trim(preg_replace('/\$a /', '', $row->Value)) ?>" />
								<div class="input-group-append">
									<span data-id="<?= $row->ID ?>" class="<?= ($idx_856 == 0) ? 'add-LocCollDaring' : 'remove-LocCollDaring' ?> btn btn-outline-secondary"><i class="fa fa-<?= ($idx_856 == 0) ? 'plus' : 'minus' ?>"></i></span>
								</div>
							</div>
							<?php $idx_856++; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<div id="LocCollDaring0" class="form input-group mb-2" title="">
							<input type="text" class="form-control" name="LocCollDaring[]" placeholder="" />
							<div class="input-group-append">
								<span data-id="0" class="add-LocCollDaring btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div>
			<label for="IsOPAC">Tampil di OPAC </label><br>
			<input type="checkbox" class="apply-status" name="IsOPAC" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= $catalog->IsOPAC == 1 ? 'checked' : '' ?>>
		</div>
	</div>
	<div class="table-responsive">
    <table class="table table-bordered table-striped mb-0">
        <tbody>
            <tr>
                <th scope="row">Dibuat Oleh</th>
                <td><?= $CreateBy ?></td>
            </tr>
            <tr>
                <th scope="row">Diperbarui Oleh</th>
                <td><?= $UpdateBy ?></td>
            </tr>
            <tr>
                <th scope="row">Dibuat Pada</th>
                <td><?= $catalog->CreateDate ?></td>
            </tr>
            <tr>
                <th scope="row">Diperbarui Pada</th>
                <td><?= $catalog->UpdateDate ?></td>
            </tr>
        </tbody>
    </table>
</div>


	<div class="card-footer p-0 pt-3">
		<div class="form-group">
		
				<div class="form-check form-check-inline">
					<input type="hidden" name="IsRDA" value="<?= $rda ?? 1 ?>">
					<input type="hidden" name="IsRedirect" value="0">
					<input class="form-check-input" type="checkbox" name="IsRedirect" value="1">
					<label class="form-check-label" for="IsRedirect">Tutup form setelah simpan</label>
				</div>

				<button type="submit" class="btn btn-primary btn-lg" name="submit"><i class="fa fa-save mr-2"></i> Simpan</button>
		
		</div>
	</div>
</div>