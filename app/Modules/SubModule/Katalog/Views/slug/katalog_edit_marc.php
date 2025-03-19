<?php
$request = service('request');
$select2 = select_two();
?>

<div class="card-header">
	<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Data Bibliografis
	<div class="btn-actions-pane-right actions-icon-btn">
		<div class="col-md-6">
			<a href="<?= base_url('katalog/edit/' . $catalog->ID . '?slug=katalog') ?>" class="btn btn-dark btn-lg"><i class="fa fa-th mr-2"></i>Tampilkan Form Sederhana</a>
		</div>
	</div>
</div>
<div class="card-body">
	<div class="card card-shadow mb-4">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Judul</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="varchar">Judul Utama</label>
				<input type="text" class="form-control" name="judul[a]" id="judul" placeholder="" value="<?= $str_245[0] ?? "" ?>" />
				<small id='TitleError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('Title') : ''; ?></small>
			</div>
			<div class="form-group">
				<label for="varchar">Anak Judul</label>
				<input type="text" class="form-control" name="judul[b]" id="anakJudul" placeholder="" value="<?= $str_245[1] ?? "" ?>" />

			</div>
			<div class="form-group">
				<label for="varchar">Penanggung Jawab</label>
				<input type="text" class="form-control" name="judul[c]" id="penanggungJawab" placeholder="" value="<?= $str_245[2] ?? "" ?>" />

			</div>
		</div>
	</div>

	<div class="card card-shadow mb-4">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Tajuk Pengarang</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="varchar">Tajuk Pengarang Utama</label>
				<div>
					<div class="form input-group colorpicker-component" title="">
						<div class="input-group-prepend" style="width: 20%;">
							<!-- <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</button>
								<div class="dropdown-menu">
									<a class="dropdown-item" href="#">Action</a>
									<a class="dropdown-item" href="#">Another action</a>
									<a class="dropdown-item" href="#">Something else here</a>
									<div role="separator" class="dropdown-divider"></div>
									<a class="dropdown-item" href="#">Separated link</a>
								</div> -->
							<select class="custom-select" id="inputGroupSelect01">
								<option value="100" selected>Nama Orang</option>
								<option value="110">Nama Badan</option>
								<option value="111">Nama Pertemuan</option>
							</select>
						</div>
						<input type="text" class="form-control" name="Author[][100]" id="Author" placeholder="Author" value='<?= set_value('Tajuk Pengarang Utama', $TajukPengarangUtama) ?>' />
					</div>
				</div>
				<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
					<fieldset class="form-group">
						<div class="row" style="margin: 4px;">
							<div class="col-sm-4">
								<div class="form-check">
									<label class="form-check-label">
										<input class="form-check-input" type="radio" name="tajuk-Radio" id="Radio-tajuk" value="0" checked>
										Nama Depan
									</label>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-check">
									<label class="form-check-label">
										<input class="form-check-input" type="radio" name="tajuk-Radio" id="Radio-tajuk" value="1">
										Nama Belakang
									</label>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-check">
									<label class="form-check-label">
										<input class="form-check-input" type="radio" name="tajuk-Radio" id="Radio-tajuk" value="2">
										Nama Keluarga
									</label>
								</div>
							</div>
						</div>
					</fieldset>
					<div class="form input-group colorpicker-component" title="">
					</div>
				</div>
			</div>
			<div class="form-group" id="tajuk-pengarang">
				<label for="varchar">Tajuk Pengarang Tambahan</label>
				<div id="tajuk-pengarang-reapet">
					<div class="form input-group colorpicker-component">
						<div class="input-group-prepend" style="width: 20%;">
							<select class="custom-select" id="inputGroupSelect02">
								<option value="700" selected>Nama Orang</option>
								<option value="710">Nama Badan</option>
								<option value="711">Nama Pertemuan</option>
							</select>
						</div>
						<input type="text" class="form-control" style="height: 38px;" name="pengarang_tambahan[][700]" id="pengarang_tambahan" placeholder="Tajuk Pengarang Tambahan" value='<?= set_value('Tajuk Pengarang Tambahan', $TajukPengarangTambahan1) ?>' />
						<div class="input-group-append" style="height: 38px;">
							<span class="add-tajuk btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
					<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
						<fieldset class="form-group">
							<div class="row" style="margin: 4px;">
								<div class="col-sm-4">
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="pengarang_tambahan[0][ind1]" id="reapet-Radio-tajuk" value="0" checked>
											Nama Depan
										</label>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="pengarang_tambahan[0][ind1]" id="reapet-Radio-tajuk" value="1">
											Nama Belakang
										</label>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="pengarang_tambahan[0][ind1]" id="reapet-Radio-tajuk" value="2">
											Nama Keluarga
										</label>
									</div>
								</div>
							</div>
						</fieldset>
						<div class="form input-group colorpicker-component" title="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-4">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Penerbit</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="varchar">PublishLocation</label>
				<input type="text" class="form-control" name="penerbit[a]" id="PublishLocation" placeholder="PublishLocation" value='<?= set_value('PublishLocation', $catalog->PublishLocation) ?>' />
				<small id='PublishLocationError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('PublishLocation') : ''; ?></small>
			</div>
			<div class="form-group">
				<label for="varchar">Publisher</label>
				<input type="text" class="form-control" name="penerbit[b]" id="Publisher" placeholder="Publisher" value='<?= set_value('Publisher', $catalog->Publisher) ?>' />
				<small id='PublisherError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('Publisher') : ''; ?></small>
			</div>
			<div class="form-group">
				<label for="varchar">PublishYear</label>
				<input type="text" class="form-control" name="penerbit[c]" id="PublishYear" placeholder="PublishYear" value='<?= set_value('PublishYear', $catalog->PublishYear) ?>' />
				<small id='PublishYearError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('PublishYear') : ''; ?></small>
			</div>
			<div class="form-group">
				<label for="varchar">Frekuensi Saat Ini</label>
				<input type="text" class="form-control" name="frekuensi_saat_ini" id="frekuensi_saat_ini" placeholder="Frekuensi Saat Ini" value='<?= set_value('frekuensi_saat_ini', 'Frekuensi Saat Ini') ?>' />
				<small id='PublishYearError' class='form-text text-muted text-danger'></small>
			</div>
			<div class="form-group" id="frekuensiSebelumnya">
				<label for="varchar">Frekuensi Publikasi Sebelumnya</label>
				<div>
					<div class="form input-group colorpicker-component" title="">
						<input type="text" class="form-control" name="frekuensiSebelumnya[]" id="frekuensiSebelumnya[]" placeholder="Frekuensi Publikasi Sebelumnya" value='<?= set_value('frekuensiSebelumnya', 'Frekuensi Publikasi Sebelumnya') ?>' />
						<div class="input-group-append">
							<span class="add-frekuensi btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
				</div>
			</div>
			<!-- <div class="form-group">
					<label for="varchar">Publikasi</label>
					<input type="text" class="form-control" name="penerbit[a]" id="Publikasi" placeholder="Publikasi" value='<?= set_value('Publikasi', 'penjual') ?>' />
					<small id='PublikasiError' class='form-text text-muted text-danger'></small>
				</div> -->
		</div>
	</div>

	<div class="card card-shadow mb-4">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Deskripsi Fisik</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="varchar">Jumlah Halaman</label>
				<input type="text" class="form-control" name="PhysicalDescription[a]" id="jumlahHalaman" placeholder="Jumlah Halaman" value='<?= set_value('jumlahHalaman', $halaman) ?>' />
				<small id='PhysicalDescriptionError' class='form-text text-muted text-danger'></small>
			</div>
			<div class="form-group">
				<label for="varchar">Ket. Ilustrasi</label>
				<input type="text" class="form-control" name="PhysicalDescription[b]" id="ketIllus" placeholder="Ket. Ilustrasi" value='<?= set_value('ketIllus', $ilus) ?>' />
				<small id='PhysicalDescriptionError' class='form-text text-muted text-danger'></small>
			</div>
			<div class="form-group">
				<label for="varchar">Dimensi</label>
				<input type="text" class="form-control" name="PhysicalDescription[c]" id="deimensi" placeholder="Dimensi" value='<?= set_value('deimensi', $dimensi) ?>' />
				<small id='PhysicalDescriptionError' class='form-text text-muted text-danger'></small>
			</div>
			<div class="form-group">
				<label for="varchar">Bahan Sertaan</label>
				<input type="text" class="form-control" name="PhysicalDescription[e]" id="bahanSertaan" placeholder="Bahan Sertaan" value='<?= set_value('bahanSertaan', $bahanSertaan) ?>' />
				<small id='PhysicalDescriptionError' class='form-text text-muted text-danger'></small>
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-4">
		<div class="card-body">
			<!-- <div class="form-group">
					<label for="varchar">Edition</label>
					<input type="text" class="form-control" name="Edition" id="Edition" placeholder="Edition" value='' />
					<small id='EditionError' class='form-text text-muted text-danger'></small>
				</div> -->
			<!-- <div class="form-group">
					<label for="varchar">Subject</label>
					<input type="text" class="form-control" name="Subject" id="Subject" placeholder="Subject" value='' />
					<small id='SubjectError' class='form-text text-muted text-danger'></small>
				</div> -->
			<div>
				<div class="form-group">
					<label for="varchar">Edisi</label>
					<input type="text" class="form-control" name="Editon" id="Edition" placeholder="Edition" value='<?= set_value('Edition', $catalog->Edition) ?>' />
					<small id='EditionError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('Edition') : ''; ?></small>
				</div>
				<div class="form-group" id="subject">
					<label for="varchar">Subjek</label>
					<!-- <div>
						<div class="form input-group colorpicker-component" title="">
							<input type="text" class="form-control" name="Subject" id="Subject" placeholder="Frekuensi Publikasi Sebelumnya" value='<?= set_value('Subject', 'Subjek') ?>' />
							<div class="input-group-append">
								<span class="add-subject btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
							</div>
						</div>
					</div> -->

					<div class="form input-group colorpicker-component" title="">
						<div class="input-group-prepend" style="width: 20%;">
							<select class="custom-select" id="inputGroupSelect03" style="height: 100%;">
								<option value="600" selected>Nama Orang</option>
								<option value="650">Topikal</option>
								<!-- <option value="651">Nama Geografis</option> -->
							</select>
						</div>
						<input type="text" class="form-control" name="Subject[][600]" id="Subject" placeholder="Subject" value='<?= set_value('Subject', $catalog->Subject) ?>' />
						<div class="input-group-append">
							<span class="add-subject btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="varchar">No. Klas DDC</label>
				<input type="text" class="form-control" name="DeweyNo" id="DeweyNo" placeholder="DeweyNo" value='<?= set_value('DeweyNo', $catalog->DeweyNo) ?>' />
				<small id='DeweyNoError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('DeweyNo') : ''; ?></small>
			</div>
			<!-- <div class="form-group">
					<label for="varchar">No. Panggil</label>
					<input type="text" class="form-control" name="CallNumber" id="CallNumber" placeholder="CallNumber" value='' />
					<small id='CallNumberError' class='form-text text-muted text-danger'></small>
				</div> -->
			<div class="form-group" id="CallNumber">
				<label for="varchar">No. Panggil</label>
				<div>
					<div class="form input-group colorpicker-component" title="">
						<input type="text" class="form-control" name="CallNumber" id="CallNumber" placeholder="No. Panggil" value='<?= set_value('CallNumber', $catalog->CallNumber) ?>' />
						<div class="input-group-append">
							<span class="add-callNumber btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group" id="ISBN">
				<label for="varchar">ISBN</label>
				<div>
					<div class="form input-group colorpicker-component" title="">
						<input type="text" class="form-control" name="ISBN[]" id="ISBN[]" placeholder="" value='<?= set_value('ISBN', $catalog->ISBN) ?>' />
						<div class="input-group-append">
							<span class="add-ISBN btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-4">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Catatan</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group" id="Note-group">
				<label for="varchar">Catatan</label>
				<div id="Note" class="form-radio-group colorpicker-component">
					<div class="form-radio input-group colorpicker-component" title="">
						<input type="text" class="form-control" name="RadioNote[][520]" id="Note_fix" placeholder="Catatan" value='<?= set_value('Note', $catalog->Note) ?>' />
						<div class="input-group-append">
							<span class="add-Note btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
					<div class="form-radio" style="background-color: #C0C0C0; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
						<fieldset class="form-group">
							<div class="row" style="margin: 4px;">
								<div class="col-sm-3">
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="list-Radio" id="RadioNote1" value="520" checked>
											Abstrak / Anotasi
										</label>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="list-Radio" id="RadioNote1" value="502">
											Catatan Disertasi
										</label>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="list-Radio" id="RadioNote1" value="504">
											Catatan Bibliografi
										</label>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="list-Radio" id="RadioNote1" value="505">
											Rincian Isi
										</label>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="list-Radio" id="RadioNote1" value="500">
											Catatan Umum
										</label>
									</div>
								</div>
							</div>
						</fieldset>
						<div class="form input-group colorpicker-component" title="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card card-shadow mb-4">
		<div class="card-body">
			<div class="form-group">
				<label for="varchar">Bahasa</label>
				<!-- <input type="text" class="form-control" name="Languages" id="Languages" placeholder="Languages" value='<//?= set_value('Languages', 'IDN') ?>' /> -->
				<!-- <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->

				<!-- <label class="col-sm-4 col-form-label col-form-label-sm">Array Data</label> -->
				<!-- <div class="col-sm-4"> -->
				<select class="search form-control" id="Languages" name="Languages[lang]">
					<?php foreach ($select2['lang'] as $key => $lang) : ?>
						<option value="<?= $lang->Code ?>">
							<?= $lang->Name ?>
						</option>
					<?php endforeach ?>
				</select>
				<!-- </div> -->

			</div>
			<div class="form-group">
				<label for="varchar">Bentuk Karya Tulis</label>
				<!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
					<small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
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
				<!-- <input type="text" class="form-control" name="kelompok_sasaran" id="kelompok_sasaran" placeholder="Kelompok Sasaran" value='<//?= set_value('kelompok_sasaran', 'Kelompok Sasaran') ?>' />
					<small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
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

	<div class="card card-shadow mb-4">
		<div class="card-header">
			<div class="custom-title-wrap bar-primary">
				<div class="custom-title">Lokasi Koleksi Daring</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group" id="LocCollDaring">
				<label for="varchar">Lokasi Koleksi Daring</label>
				<div>
					<div class="form input-group colorpicker-component" title="">
						<input type="text" class="form-control" name="LocCollDaring[]" id="LocCollDaring" placeholder="Lokasi Koleksi Daring" value='<?= set_value('LocCollDaring') ?>' />
						<div class="input-group-append">
							<span class="add-LocCollDaring btn btn-outline-secondary"><i class="fa fa-plus"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-check form-group">
		<div>
			<input type="hidden" class="iCheck-square" name="IsOPAC" id="IsOPAC" value="0">
			<input type="checkbox" class="iCheck-square" name="IsOPAC" id="IsOPAC" value="1">
			<label class="  control-label">Tampil di OPAC</label>
		</div>
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-primary">Simpan</button>
		<a href="<?php echo base_url('backend/catalogs') ?>" class="btn btn-default">Cancel</a>
	</div>

</div>