<div class="row">
    <div class="col-md-12">
        <div id="accordion" class="accordion-wrapper mb-3">
            <div class="card">
                <div class="card-header-tab card-header">
                    <button type="button" data-toggle="collapse" data-target="#collapse_madatory0" aria-expanded="true" aria-controls="collapse_madatory" class="text-left m-0 p-0 btn btn-link">
                        <h5 class="m-0 p-0">
                            <i class="header-icon lnr-layers icon-gradient bg-primary">
                            </i>
                            Info Personal
                        </h5>
                    </button>
                </div>
                <div data-parent="#accordion" id="collapse_madatory0" class="collapse show" style="">
                    <div class="card-body">
                       <?php if($TipeNomorAnggota=="Manual"):?>
                         <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="name">Nomor Anggota <span class="text-danger">*</span></label>
                                    <div>
                                        <input type="text" class="form-control" id="frm_create_ID" name="MemberNo" placeholder="Nomor Anggota" value="<?= set_value('MemberNo'); ?>"/>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <?php endif ?>
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Nama Anggota <span class="text-danger">*</span></label>
                                    <div>
                                        <input type="text" class="form-control" id="Fullname" name="Fullname" placeholder="Nama Anggota" value="<?= set_value('Fullname'); ?>" required />
                                    </div>
                                </div>
                            </div>

                            

                            <?php // Gunakan helper untuk "Jenis Identitas" 
                            ?>
                            <?php if (is_form_field_active('13', $jenis_perpustakaan_id)) : ?>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label>Jenis Identitas</label>
                                        <select class="form-control" name="IdentityType_id" id="IdentityType_id" placeholder="Jenis identitas">
                                            <option value="" disabled selected>Jenis identitas</option>
                                            <?php foreach (get_table('master_jenis_identitas', 'id, Nama', null, 'data') as $row) : ?>
                                                <option value="<?= $row->id ?>" <?= set_select('IdentityType_id', $row->id) ?>><?= $row->Nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php // Gunakan helper untuk "Nomor Identitas" 
                            ?>
                            <?php if (is_form_field_active('14', $jenis_perpustakaan_id)) : ?>
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="name">Nomor Identitas</label>
                                        <div>
                                            <input type="text" class="form-control" id="frm_create_IdentityNo" name="IdentityNo" placeholder="Nomor identitas" value="<?= set_value('IdentityNo'); ?>" maxlength="16" required />
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Tempat Lahir</label>
                                    <div>
                                        <input type="text" class="form-control" id="frm_create_PlaceOfBirth" name="PlaceOfBirth" placeholder="Tempat Lahir" value="<?= set_value('PlaceOfBirth'); ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="position-relative form-group" id="tgl1">
                                    <label for="name">Tanggal Lahir</label>
                                    <div>
                                        <input type="date" class="form-control" id="date-time" name="DateOfBirth" placeholder="Tanggal Lahir" value="<?= set_value('DateOfBirth'); ?>" />
                                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                                    </div>
                                </div>
                            </div>
                              <?php if (is_form_field_active('20', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label>Status Perkawinan</label>
                                    <select class="form-control" name="MaritalStatus_id" id="MaritalStatus_id">
                                        <?php foreach (get_table('master_status_perkawinan', 'id, Nama', null, 'data') as $row) : ?>
                                            <option value="<?= $row->id ?>" <?= set_select('MaritalStatus_id', $row->id) ?>><?= $row->Nama ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                            <?php endif; ?>
                             <?php if (is_form_field_active('17', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label>Agama</label>
                                    <select class="form-control" name="Agama_id" id="Agama_id">
                                        <?php foreach (get_table('agama', 'ID, Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= set_select('Agama_id', $row->ID) ?>><?= $row->Name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                             <?php if (is_form_field_active('15', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label>Jenis Kelamin</label>
                                    <select class="form-control" name="Sex_id" id="Sex_id" placeholder="Jenis kelamin">
                                        <?php foreach (get_table('jenis_kelamin', 'ID, Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= set_select('Sex_id', $row->ID) ?>><?= $row->Name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <h5>Kontak</h5>
                            </div>
                        </div>
                       
                        <div class="form-row">
                             <?php if (is_form_field_active('29', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Alamat Email <span class="text-danger">*</span></label>
                                    <div>
                                        <input type="email" class="form-control" id="Email" name="Email" placeholder="Alamat Email" value="<?= set_value('Email'); ?>" required />
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (is_form_field_active('11', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">No. HP</label>
                                    <div>
                                        <input type="text" class="form-control" id="frm_create_NoHp" name="Phone" placeholder="No. Telepon" value="<?= set_value('Phone'); ?>" maxlength="15" />
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