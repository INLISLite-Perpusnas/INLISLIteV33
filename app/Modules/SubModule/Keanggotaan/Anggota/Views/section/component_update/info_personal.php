<div class="row">
    <div class="col-md-12">
        <div id="accordion_personal" class="accordion-wrapper mb-3">
            <div class="card">
                <div class="card-header-tab card-header">
                    <button type="button" data-toggle="collapse" data-target="#collapse_personal" aria-expanded="true" aria-controls="collapse_personal" class="text-left m-0 p-0 btn btn-link">
                        <h5 class="m-0 p-0">
                            <i class="header-icon lnr-layers icon-gradient bg-primary"></i> Info Personal
                        </h5>
                    </button>
                </div>
                <div data-parent="#accordion_personal" id="collapse_personal" class="collapse show">
                    <div class="card-body">
                      
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Nama Anggota*</label>
                                    <div>
                                        <input type="text" class="form-control" id="Fullname" name="Fullname" placeholder="Nama Anggota" value="<?= set_value('Fullname', $anggota->Fullname); ?>" required />
                                    </div>
                                </div>
                            </div>
                              <?php if (is_form_field_active('13', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label>Jenis Identitas</label>
                                    <select class="form-control" name="IdentityType_id" id="IdentityType_id" placeholder="Jenis Identitas">
                                        <option value="" disabled selected>Jenis Identitas</option>
                                        <?php foreach (get_table('master_jenis_identitas', 'id,Nama', null, 'data') as $row) : ?>
                                            <option value="<?= $row->id ?>" <?= ($row->id == $anggota->IdentityType_id) ? 'selected' : '' ?>><?= $row->Nama ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                                 <?php if (is_form_field_active('14', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Nomor Identitas</label>
                                    <div>
                                        <input type="text" class="form-control" id="frm_create_IdentityNo" name="IdentityNo" placeholder="Nomor identitas" value="<?= set_value('IdentityNo', $anggota->MemberNo); ?>" maxlength="16" required />
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Tempat Lahir</label>
                                    <div>
                                        <input type="text" class="form-control" id="frm_create_PlaceOfBirth" name="PlaceOfBirth" placeholder="Tempat Lahir" value="<?= set_value('PlaceOfBirth', $anggota->PlaceOfBirth); ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="position-relative form-group" id="tgl1">
                                    <label for="name">Tanggal Lahir</label>
                                    <div>
                                        <input type="date" class="form-control" id="date-time" name="DateOfBirth" placeholder="Tanggal Lahir" value="<?= set_value('DateOfBirth', substr(($anggota->DateOfBirth), 0, 10)); ?>" />
                                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                                    </div>
                                </div>
                            </div>
                            <?php if (is_form_field_active('20', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label>Status Perkawinan</label>
                                    <select class="form-control" name="Sex_id" id="Sex_id">
                                        <?php foreach (get_table('master_status_perkawinan', 'id, Nama', null, 'data') as $row) : ?>
                                            <option value="<?= $row->id ?>" <?= ($row->id == $anggota->Sex_id) ? 'selected' : '' ?>><?= $row->Nama ?></option>
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
                                        <option value="" disabled selected>Agama</option>
                                        <?php foreach (get_table('agama', 'ID, Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= ($row->ID == $anggota->Agama_id) ? 'selected' : '' ?>><?= $row->Name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                             <?php if (is_form_field_active('15', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label>Jenis Kelamin</label>
                                    <select class="form-control" name="Sex_id" id="Sex_id" placeholder="<?= lang('Anggota.field.Jeniskelamin') ?>">
                                        <?php foreach (get_table('jenis_kelamin', 'ID, Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= ($row->ID == $anggota->Sex_id) ? 'selected' : '' ?>><?= $row->Name ?></option>
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
                                    <label for="name">Alamat Email*</label>
                                    <div>
                                        <input type="email" class="form-control" id="Email" name="Email" placeholder="Alamat Email" value="<?= set_value('Email', $anggota->Email); ?>" />
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                             <?php if (is_form_field_active('11', $jenis_perpustakaan_id)) : ?>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">No. Telepon</label>
                                    <div>
                                        <input type="text" class="form-control" id="frm_create_NoHp" name="Phone" placeholder="No. Telepon" value="<?= set_value('Phone', $anggota->Phone); ?>" />
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