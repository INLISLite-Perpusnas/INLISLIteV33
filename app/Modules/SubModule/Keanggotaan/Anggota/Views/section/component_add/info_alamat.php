<div class="row">
    <div class="col-md-12">
        <div id="accordion_alamat" class="accordion-wrapper mb-3">
            <div class="card">
                <div class="card-header-tab card-header">
                  <button data-target="#collapse_alamat" data-toggle="collapse" aria-expanded="true" aria-controls="collapse_madatory" class="text-left m-0 p-0 btn btn-link">
                        <h5 class="m-0 p-0">
                            <i class="header-icon lnr-layers icon-gradient bg-primary"> </i>
                            Info Alamat
                        </h5>
                    </button>
                </div>
               <div data-parent="#accordion_alamat" id="collapse_alamat" class="collapse show">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-12">
                                <h5>Alamat sesuai identitas</h5>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label for="name">Alamat</label>
                                    <div>
                                        <input type="text" class="form-control" id="Address" name="Address" placeholder="Alamat" value="<?= set_value('Address'); ?>" />
                                    </div>
                                </div>
                            </div>

                            <?php if (is_form_field_active('7', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Provinsi <span class="text-danger">*</span></label>
                                    <div>
                                        <select required data-error="Pilih Provinsi" class="form-control select2" id="Province" name="Province" style="width:100%" data-value="<?= set_value('Province', 'Province') ?>"></select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                              <?php if (is_form_field_active('6', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Kota <span class="text-danger">*</span></label>
                                    <div>
                                        <select required data-error="Pilih Kota" class="form-control select2" id="City" name="City" style="width:100%" data-value="<?= set_value('City', 'City') ?>">
                                            <option value="">-Select-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                              <?php if (is_form_field_active('39', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Kecamatan <span class="text-danger">*</span></label>
                                    <div>
                                        <select required data-error="Pilih Kecamatan" class="form-control select2" id="District" name="Kecamatan" style="width:100%" data-value="<?= set_value('Kecamatan', 'Kecamatan') ?>">
                                            <option value="">-Select-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                              <?php if (is_form_field_active('40', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Kelurahan <span class="text-danger">*</span></label>
                                    <div>
                                        <select required data-error="Pilih Kelurahan" class="form-control select2" id="SubDistrict" name="Kelurahan" style="width:100%" data-value="<?= set_value('Kelurahan', 'Kelurahan') ?>">
                                            <option value="">-Select-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                         
                            <div class="col-md-3">
                                <div class="row">
                                      <?php if (is_form_field_active('41', $jenis_perpustakaan_id)) : ?>
                                    <div class="col">
                                        <div class="position-relative form-group">
                                            <label for="name">RT</label>
                                            <div>
                                                <input type="text" class="form-control" id="RT" name="RT" placeholder="RT" value="<?= set_value('RT'); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                    <?php if (is_form_field_active('42', $jenis_perpustakaan_id)) : ?>
                                    <div class="col">
                                        <div class="position-relative form-group">
                                            <label for="name">RW</label>
                                            <div>
                                                <input type="text" class="form-control" id="RW" name="RW" placeholder="RW" value="<?= set_value('RW'); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-12">
                                <h5>Alamat domisili</h5>
                            </div>
                        </div>
                        <div class="form-row">
                         
                            <div class="col-md-12">
                                <input type="checkbox" class="is_similar" name="is_similar" id="is_similar" value="">
                                <label for="is_similar">Centang jika alamat domisili sama dengan alamat
                                    identitas</label>
                            </div>
                           
                             <?php if (is_form_field_active('8', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label for="name">Alamat</label>
                                    <div>
                                        <input type="text" class="form-control" id="AddressNow" name="AddressNow" placeholder="Alamat" value="<?= set_value('AddressNow'); ?>" />
                                    </div>
                                </div>
                            </div>
                             <?php endif; ?>
                              <?php if (is_form_field_active('10', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Provinsi <span class="text-danger">*</span></label>
                                    <div>
                                        <select required data-error="Pilih Provinsi" class="form-control select2" id="ProvinceNow" name="ProvinceNow" style="width:100%" data-value="<?= set_value('ProvinceNow', 'ProvinceNow') ?>"></select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                              <?php if (is_form_field_active('9', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Kota <span class="text-danger">*</span></label>
                                    <div>
                                        <select required data-error="Pilih Kota" class="form-control select2" id="CityNow" name="CityNow" style="width:100%" data-value="<?= set_value('CityNow', 'CityNow') ?>">
                                            <option value="">-Select-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (is_form_field_active('43', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Kecamatan <span class="text-danger">*</span></label>
                                    <div>
                                        <select required data-error="Pilih Kecamatan" class="form-control select2" id="DistrictNow" name="KecamatanNow" style="width:100%" data-value="<?= set_value('KecamatanNow', 'KecamatanNow') ?>">
                                            <option value="">-Select-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                             <?php if (is_form_field_active('44', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Kelurahan <span class="text-danger">*</span></label>
                                    <div>
                                        <select required data-error="Pilih Kelurahan" class="form-control select2" id="SubDistrictNow" name="KelurahanNow" style="width:100%" data-value="<?= set_value('KelurahanNow', 'KelurahanNow') ?>">
                                            <option value="">-Select-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                          
                            <div class="col-md-3">
                                <div class="row">
                                      <?php if (is_form_field_active('45', $jenis_perpustakaan_id)) : ?>
                                    <div class="col">
                                        <div class="position-relative form-group">
                                            <label for="name">RT</label>
                                            <div>
                                                <input type="text" class="form-control" id="RTNow" name="RTNow" placeholder="RT" value="<?= set_value('RTNow'); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (is_form_field_active('46', $jenis_perpustakaan_id)) : ?>
                                    <div class="col">
                                        <div class="position-relative form-group">
                                            <label for="name">RW</label>
                                            <div>
                                                <input type="text" class="form-control" id="RWNow" name="RWNow" placeholder="RW" value="<?= set_value('RWNow'); ?>" />
                                            </div>
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