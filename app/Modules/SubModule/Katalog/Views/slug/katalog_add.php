<?php
$request = service('request');
$worksheet_id = $request->getGet('worksheet_id') ?? 1;
$select2 = select_two();
$rda = $request->getGet('rda') ?? 1;
?>
<div class="card-header" style="min-height:100px">
	<div class="form-group col-md-6">
		<div class="select-wrapper input-group mt-2 mb-2">
			<select class="form-control" name="Worksheet_id" id="worksheet_id">
				<?php foreach ($worksheets as $row) : ?>
					<option value="<?= $row->ID ?>" <?= set_select('Worksheet_id', $row->ID, ($worksheet_id == $row->ID)); ?>><?= $row->Name ?></option>
				<?php endforeach; ?>
			</select>
			<div class="input-group-append">
				<button class="btn btn bg-primary text-white worksheet-btn-load" type="button"><i class="fa fa-check"></i> Pilih Jenis Bahan</button>
			</div>
		</div>
	</div>
</div>

<div class="card-header">
	<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Data Bibliografis
	<div class="btn-actions-pane-right actions-icon-btn">
		<div class="col-md-6">
			<a href="<?= base_url('katalog/create_marc?worksheet_id=1&fullscreen=1') ?>" class="btn btn-dark btn-lg"><i class="fa fa-th mr-2"></i>Tampilkan Form MARC</a>
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
						<input type="text" class="form-control" name="judul[a]" id="judul" placeholder="" value="<?= set_value('judul[a]') ?>" />
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label for="anakJudul">Anak Judul</label>
						<input type="text" class="form-control" name="judul[b]" id="anakJudul" placeholder="" value="<?= set_value('judul[b]') ?>" />
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label for="penanggungJawab">Penanggung Jawab</label>
						<input type="text" class="form-control" name="judul[c]" id="penanggungJawab" placeholder="" value="<?= set_value('judul[c]') ?>" />
					</div>
				</div>
				<?php if ($rda == 1) : ?>
					<div class="col-md-12">
						<div class="form-group">
							<label for="variasiBentukJudul">Variasi Bentuk Judul</label>
							<div id="variasiBentukJudul">
								<div id="variasiBentukJudul0" class="form input-group mb-2" title="">
									<input type="text" class="form-control" name="variasiBentukJudul[]" placeholder="" value="" />
									<div class="input-group-append">
										<span data-id="0" class="add-variasiBentukJudul btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label for="judulAsli">Judul Asli</label>
							<div id="judulAsli">
								<div id="judulAsli0" class="form input-group mb-2" title="">
									<input type="text" class="form-control" name="judulAsli[]" placeholder="" value="" />
									<div class="input-group-append">
										<span data-id="0" class="add-judulAsli btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label for="judulSeragam">Judul Seragam</label>
							<input type="text" class="form-control" name="judulSeragam" id="judulSeragam" placeholder="" value="<?= set_value('judulSeragam') ?>" />
						</div>
					</div>
				<?php endif; ?>
				<div class="col-md-12 terbitanBerkala" style="display: none;">
					<div class="form-group">
						<label for="judulSebelumnya">Judul Sebelumnya</label>
						<input type="text" class="form-control" name="judulSebelumnya" id="judulSebelumnya" placeholder="" value="<?= set_value('judulSebelumnya') ?>" />
					</div>
				</div>
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
			</div>

			<div class="form-group" id="pengarangTambahanGroup">
				<label for="pengarangTambahan">Tajuk Pengarang Tambahan</label>
				<div class="form-group pengarangTambahanWrapper" id="pengarangTambahanWrapper">
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
							<span class="add-pengarangTambahan btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
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
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="varchar">Tempat Terbit</label>
						<input type="text" class="form-control" name="penerbit[a]" id="PublishLocation" placeholder="" value="<?= set_value('penerbit[a]') ?>" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="varchar">Penerbit</label>
						<input type="text" class="form-control" name="penerbit[b]" id="Publisher" placeholder="" value="<?= set_value('penerbit[b]') ?>" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="varchar">Tahun Terbit</label>
						<input type="text" class="form-control" name="penerbit[c]" id="PublishYear" placeholder="" value="<?= set_value('penerbit[c]') ?>" />
					</div>
				</div>
				<div class="col-md-6 terbitanBerkala" style="display: none;">
					<div class="form-group">
						<label for="frekuensi">Frekuensi Saat Ini</label>
						<input type="text" class="form-control" name="frekuensi" id="frekuensi" placeholder="" value="<?= set_value('frekuensi') ?>" />
					</div>
				</div>
				<div class="col-md-12 terbitanBerkala" style="display: none;">
					<div class="form-group" id="frekuensiSebelumnya">
						<label for="frekuensiSebelumnya">Frekuensi Publikasi Sebelumnya</label>
						<div>
							<div class="form input-group" title="Publikasi Sebelumnya">
								<input type="text" class="form-control" name="frekuensiSebelumnya[]" id="frekuensiSebelumnya[]" placeholder="" value="" />
								<div class="input-group-append">
									<span class="add-frekuensiSebelumnya btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>
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
						<input type="text" class="form-control" name="PhysicalDescription[a]" id="jumlahHalaman" placeholder="" value="<?= set_value('PhysicalDescription[a]') ?>" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="varchar">Ket. Ilustrasi</label>
						<input type="text" class="form-control" name="PhysicalDescription[b]" id="ketIllus" placeholder="" value="<?= set_value('PhysicalDescription[b]') ?>" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="varchar">Dimensi</label>
						<input type="text" class="form-control" name="PhysicalDescription[c]" id="dimensi" placeholder="" value="<?= set_value('PhysicalDescription[c]') ?>" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="varchar">Bahan Sertaan</label>
						<input type="text" class="form-control" name="PhysicalDescription[e]" id="bahanSertaan" placeholder="" value="<?= set_value('PhysicalDescription[e]') ?>" />
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
							<input type="text" class="form-control" name="jenisIsi" id="jenisIsi" placeholder="" value="<?= set_value('jenisIsi') ?>" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="jenisMedia">Jenis Media</label>
							<input type="text" class="form-control" name="jenisMedia" id="jenisMedia" placeholder="" value="<?= set_value('jenisMedia') ?>" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="jenisWadah">Jenis Wadah</label>
							<input type="text" class="form-control" name="jenisWadah" id="jenisWadah" placeholder="" value="<?= set_value('jenisWadah') ?>" />
						</div>
					</div>
				<?php endif; ?>
				<div class="col-md-6">
					<div class="form-group">
						<label for="varchar">Edisi</label>
						<input type="text" class="form-control" name="Edition" id="Edition" placeholder="" value="<?= set_value('Edition') ?>" />
					</div>
				</div>
			</div>
			<div class="form-group" id="subjectGroup">
				<label for="subject">Subject</label>
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
			</div>
			<div class="form-group">
				<label for="varchar">No. Klas DDC</label>
				<input type="text" class="form-control" name="DeweyNo" id="DeweyNo" placeholder="" value='<?= set_value('DeweyNo') ?>' />
			</div>
			<div class="form-group">
				<label for="CallNumber">No. Panggil</label>
				<div id="CallNumber">
					<div class="form input-group mb-2" title="">
						<input type="text" class="form-control" name="CallNumber[]" placeholder="" value="" />
						<div class="input-group-append">
							<span data-id="0" class="add-CallNumber btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group" id="ISBN">
				<label for="varchar">ISBN</label>
				<div>
					<div class="form input-group mb-2" title="">
						<input type="text" class="form-control" name="ISBN[]" id="ISBN[]" placeholder="" value="" />
						<div class="input-group-append">
							<span class="add-ISBN btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
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
				<label for="catatan">Catatan</label>
				<div class="form-group catatanWrapper" id="catatanWrapper0">
					<div class="form input-group">
						<textarea class="form-control" id="catatanInput-0" name="catatan[520-0][]" rows="1" placeholder="" value=""></textarea>
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
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-3">
		<div class="card-body">
			<div class="form-group">
				<label for="varchar">Bahasa</label>
				<select class="search form-control" id="Languages" name="Languages[lang]">
					<?php foreach ($select2['lang'] as $key => $lang) : ?>
						<option value="<?= $lang->Code ?>">
							<?= $lang->Name ?>
						</option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="form-group">
				<label for="varchar">Bentuk Karya Tulis</label>
				<select class="search form-control" id="bent-karya-tulis" name="Languages[bkt]">
					<?php foreach ($select2['karya-tulis'] as $key => $lang) : ?>
						<option value="<?= $lang->Code ?>">
							<?= $lang->Name ?>
						</option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="form-group">
				<label for="varchar">Kelompok Sasaran</label>
				<select class="search form-control" id="kelompok-sasaran" name="Languages[ks]">
					<?php foreach ($select2['kelompok-sasaran'] as $key => $lang) : ?>
						<option value="<?= $lang->Code ?>">
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
			<div class="form-group" id="LocCollDaring">
				<label for="varchar">Lokasi Koleksi Daring</label>
				<div id="LocCollDaring">
					<div id="LocCollDaring0" class="form input-group mb-2" title="">
						<input type="text" class="form-control" name="LocCollDaring[]" placeholder="" />
						<div class="input-group-append">
							<span data-id="0" class="add-LocCollDaring btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div>
			<label for="IsOPAC">Tampil di OPAC </label><br>
			<input type="checkbox" class="apply-status" name="IsOPAC" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
		</div>
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