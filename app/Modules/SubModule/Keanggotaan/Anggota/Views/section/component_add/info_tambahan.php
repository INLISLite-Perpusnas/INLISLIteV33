<div class="row">
        <div class="col-md-12">
            <div id="accordion" class="accordion-wrapper mb-3">
                <div class="card">
                    <div class="card-header-tab card-header">
                        <button type="button" data-toggle="collapse" data-target="#collapse_madatory3"
                            aria-expanded="true" aria-controls="collapse_madatory"
                            class="text-left m-0 p-0 btn btn-link">
                            <h5 class="m-0 p-0">
                                <i class="header-icon lnr-layers icon-gradient bg-primary"></i> Info Tambahan
                            </h5>
                        </button>
                    </div>
                    <div data-parent="#accordion" id="collapse_madatory3" class="collapse" style="">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <h5>Pekerjaan</h5>
                                </div>
                            </div>
                            <div class="form-row">
                                <?php if (is_form_field_active('16', $jenis_perpustakaan_id)) : ?>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label>Pekerjaan</label>
                                        <select class="form-control" name="Job_id" id="Job_id"
                                            tabindex="-1" aria-hidden="true">
                                            <?php foreach (get_ref_table('master_pekerjaan', 'id,Pekerjaan',null,'data') as $row): ?>
								            <option value="<?=$row->id?>" <?=set_select('Job_id',$row->id)?>> <?=$row->Pekerjaan?></option>
							        <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (is_form_field_active('26', $jenis_perpustakaan_id)) : ?>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="name">Nama Institusi</label>
                                        <div>
                                            <input type="text" class="form-control" id="frm_create_InstitutionName"
                                                name="InstitutionName" placeholder="Nama Institusi"
                                                value="<?=set_value('InstitutionName');?>" />
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (is_form_field_active('27', $jenis_perpustakaan_id)) : ?>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="name">Alamat Institusi</label>
                                        <div>
                                            <input type="text" class="form-control" id="frm_create_InstitutionAddress"
                                                name="InstitutionAddress" placeholder="Alamat Institusi"
                                                value="<?=set_value('InstitutionAddress');?>" />
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (is_form_field_active('28', $jenis_perpustakaan_id)) : ?>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="name">Telepon Institusi</label>
                                        <div>
                                            <input type="text" class="form-control" id="frm_create_InstitutionPhone"
                                                name="InstitutionPhone" placeholder="Telepon Institusi"
                                                value="<?=set_value('InstitutionPhone');?>" />
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <h5>Pendidikan</h5>
                                </div>
                            </div>
                            <div class="form-row">
                                <?php if (is_form_field_active('19', $jenis_perpustakaan_id)) : ?>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label>Jenjang Pendidikan</label>
                                        <select class="form-control" name="JenjangPendidikan_id" id="JenjangPendidikan_id"
                                            tabindex="-1" aria-hidden="true">
                                            <option value="">Pilih Jenjang Pendidikan</option>
                                            <?php foreach (get_ref_table('master_pendidikan', 'id, Nama',null,'data') as $row): ?>
								           <option value="<?=$row->id?>" <?=set_select('JenjangPendidikan_id',$row->id)?>><?=$row->Nama?></option>
							                <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <?php endif;?>
                                
                                <!-- Fakultas Field -->
                                 <?php if (is_form_field_active('37', $jenis_perpustakaan_id)) : ?>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label>Fakultas</label>
                                        <select class="form-control" name="Fakultas_id" id="Fakultas_id">
                                            <option value="">Pilih Fakultas</option>
                                            <?php foreach (get_ref_table('master_fakultas', 'id,Nama',null,'data') as $row): ?>
								            <option value="<?=$row->id?>" <?=set_select('Fakultas_id',$row->id)?>><?=$row->Nama?></option>
							            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Jurusan Field -->
                                 <?php if (is_form_field_active('38', $jenis_perpustakaan_id)) : ?>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label>Jurusan</label>
                                        <select class="form-control" name="Jurusan_id" id="Jurusan_id">
                                            <option value="">Pilih Jurusan</option>
                                            <!-- Options akan diisi via JavaScript berdasarkan Fakultas yang dipilih -->
                                        </select>
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