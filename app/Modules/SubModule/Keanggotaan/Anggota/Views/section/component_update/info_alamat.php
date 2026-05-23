<div class="row">
	<div class="col-md-12">
		<div id="accordion_alamat" class="accordion-wrapper mb-3">
			<div class="card">
				<div class="card-header-tab card-header">
					<button type="button" data-bs-toggle="collapse" data-bs-target="#collapse_madatory1"
						aria-expanded="true" aria-controls="collapse_madatory1"
						class="text-left m-0 p-0 btn btn-link">
						<h5 class="m-0 p-0">
							<i class="header-icon lnr-home icon-gradient bg-primary"></i>
							Info Alamat
						</h5>
					</button>
				</div>
				<div data-bs-parent="#accordion_alamat" id="collapse_madatory1" class="collapse show">
					<div class="card-body">
						<!-- ALAMAT SESUAI IDENTITAS -->
						<div class="form-row">
							<div class="col-md-12">
								<h5>Alamat sesuai identitas</h5>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="Address">Alamat</label>
									<input type="text" class="form-control" id="Address" name="Address"
										placeholder="Alamat lengkap"
										value="<?= set_value('Address', $anggota->Address ?? ''); ?>" />
								</div>
							</div>

							<!-- PROVINSI KTP -->
							<?php if (is_form_field_active('7', $jenis_perpustakaan_id)) : ?>
								<div class="col-md-3">
									<div class="position-relative form-group">
										<label for="Province">Provinsi <span class="text-danger">*</span></label>
										<select class="form-control select2"
											id="Province"
											name="Province"
											style="width:100%"
											data-placeholder="Pilih Provinsi">
											<option value="">Pilih Provinsi</option>
											<!-- Options akan diisi via JavaScript -->
										</select>
									</div>
								</div>
							<?php endif; ?>

							<!-- KOTA KTP -->
							<?php if (is_form_field_active('6', $jenis_perpustakaan_id)) : ?>
								<div class="col-md-3">
									<div class="position-relative form-group">
										<label for="City">Kota/Kabupaten <span class="text-danger">*</span></label>
										<select class="form-control select2"
											id="City"
											name="City"
											style="width:100%"
											data-placeholder="Pilih Kota">
											<option value="">Pilih Kota</option>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<!-- KECAMATAN KTP -->
							<?php if (is_form_field_active('39', $jenis_perpustakaan_id)) : ?>
								<div class="col-md-3">
									<div class="position-relative form-group">
										<label for="District">Kecamatan <span class="text-danger">*</span></label>
										<select class="form-control select2"
											id="District"
											name="Kecamatan"
											style="width:100%"
											data-placeholder="Pilih Kecamatan">
											<option value="">Pilih Kecamatan</option>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<!-- KELURAHAN KTP -->
							<?php if (is_form_field_active('40', $jenis_perpustakaan_id)) : ?>
								<div class="col-md-3">
									<div class="position-relative form-group">
										<label for="SubDistrict">Kelurahan <span class="text-danger">*</span></label>
										<select class="form-control select2"
											id="SubDistrict"
											name="Kelurahan"
											style="width:100%"
											data-placeholder="Pilih Kelurahan">
											<option value="">Pilih Kelurahan</option>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<!-- RT & RW KTP -->
							<div class="col-md-3">
								<div class="row">
									<?php if (is_form_field_active('41', $jenis_perpustakaan_id)) : ?>
										<div class="col">
											<div class="position-relative form-group">
												<label for="RT">RT</label>
												<input type="text" class="form-control" id="RT" name="RT"
													placeholder="000"
													value="<?= set_value('RT', $anggota->RT ?? ''); ?>"
													maxlength="3" />
											</div>
										</div>
									<?php endif; ?>

									<?php if (is_form_field_active('42', $jenis_perpustakaan_id)) : ?>
										<div class="col">
											<div class="position-relative form-group">
												<label for="RW">RW</label>
												<input type="text" class="form-control" id="RW" name="RW"
													placeholder="000"
													value="<?= set_value('RW', $anggota->RW ?? ''); ?>"
													maxlength="3" />
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>

					<!-- ALAMAT DOMISILI -->
					<div class="card-body border-top">
						<div class="form-row">
							<div class="col-md-12">
								<h5>Alamat Domisili</h5>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12 mb-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input"
										name="is_similar" id="is_similar" value="1">
									<label class="custom-control-label" for="is_similar">
										Centang jika alamat domisili sama dengan alamat identitas
									</label>
								</div>
							</div>

							<!-- ALAMAT DOMISILI -->
							<?php if (is_form_field_active('8', $jenis_perpustakaan_id)) : ?>
								<div class="col-md-12">
									<div class="position-relative form-group">
										<label for="AddressNow">Alamat</label>
										<input type="text" class="form-control" id="AddressNow" name="AddressNow"
											placeholder="Alamat lengkap"
											value="<?= set_value('AddressNow', $anggota->AddressNow ?? ''); ?>" />
									</div>
								</div>
							<?php endif; ?>

							<!-- PROVINSI DOMISILI -->
							<?php if (is_form_field_active('10', $jenis_perpustakaan_id)) : ?>
								<div class="col-md-3">
									<div class="position-relative form-group">
										<label for="ProvinceNow">Provinsi <span class="text-danger">*</span></label>
										<select class="form-control select2"
											id="ProvinceNow"
											name="ProvinceNow"
											style="width:100%"
											data-placeholder="Pilih Provinsi">
											<option value="">Pilih Provinsi</option>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<!-- KOTA DOMISILI -->
							<?php if (is_form_field_active('9', $jenis_perpustakaan_id)) : ?>
								<div class="col-md-3">
									<div class="position-relative form-group">
										<label for="CityNow">Kota/Kabupaten <span class="text-danger">*</span></label>
										<select class="form-control select2"
											id="CityNow"
											name="CityNow"
											style="width:100%"
											data-placeholder="Pilih Kota">
											<option value="">Pilih Kota</option>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<!-- KECAMATAN DOMISILI -->
							<?php if (is_form_field_active('43', $jenis_perpustakaan_id)) : ?>
								<div class="col-md-3">
									<div class="position-relative form-group">
										<label for="DistrictNow">Kecamatan <span class="text-danger">*</span></label>
										<select class="form-control select2"
											id="DistrictNow"
											name="KecamatanNow"
											style="width:100%"
											data-placeholder="Pilih Kecamatan">
											<option value="">Pilih Kecamatan</option>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<!-- KELURAHAN DOMISILI -->
							<?php if (is_form_field_active('44', $jenis_perpustakaan_id)) : ?>
								<div class="col-md-3">
									<div class="position-relative form-group">
										<label for="SubDistrictNow">Kelurahan <span class="text-danger">*</span></label>
										<select class="form-control select2"
											id="SubDistrictNow"
											name="KelurahanNow"
											style="width:100%"
											data-placeholder="Pilih Kelurahan">
											<option value="">Pilih Kelurahan</option>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<!-- RT & RW DOMISILI -->
							<div class="col-md-3">
								<div class="row">
									<?php if (is_form_field_active('45', $jenis_perpustakaan_id)) : ?>
										<div class="col">
											<div class="position-relative form-group">
												<label for="RTNow">RT</label>
												<input type="text" class="form-control" id="RTNow" name="RTNow"
													placeholder="000"
													value="<?= set_value('RTNow', $anggota->RTNow ?? ''); ?>"
													maxlength="3" />
											</div>
										</div>
									<?php endif; ?>

									<?php if (is_form_field_active('46', $jenis_perpustakaan_id)) : ?>
										<div class="col">
											<div class="position-relative form-group">
												<label for="RWNow">RW</label>
												<input type="text" class="form-control" id="RWNow" name="RWNow"
													placeholder="000"
													value="<?= set_value('RWNow', $anggota->RWNow ?? ''); ?>"
													maxlength="3" />
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>