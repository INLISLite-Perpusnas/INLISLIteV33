<div class="row">
    <div class="col-md-12">
        <div id="accordion" class="accordion-wrapper mb-3">
            <div class="card">
                <div class="card-header-tab card-header">
                    <button type="button" data-toggle="collapse" data-target="#collapse_madatory" aria-expanded="true" aria-controls="collapse_madatory" class="text-left m-0 p-0 btn btn-link">
                        <h5 class="m-0 p-0">
                            <i class="header-icon lnr-layers icon-gradient bg-primary"> </i>
                            Info Anggota
                        </h5>
                    </button>
                </div>
                <div data-parent="#accordion" id="collapse_madatory" class="collapse show" style="">
                    <div class="card-body">
                        <div class="form-row">

                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <div>
                                        <label>Jenis Anggota*</label>
                                        <select class="form-control" id="package" onchange="myFunction();" name="JenisAnggota_id" id="JenisAnggota_id" required>
                                            <option value="" disabled selected>
                                                Jenis Anggota
                                            </option>

                                            <?php foreach (get_ref_table('jenis_anggota', 'id, jenisanggota, MasaBerlakuAnggota', null, 'data') as $row) : ?>
                                                <option data-date="<?= $row->MasaBerlakuAnggota ?>" value="<?= $row->id ?>" <?= set_select('JenisAnggota_id', $row->id) ?>><?= $row->jenisanggota ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Tanggal Pendaftaran</label>
                                    <div>
                                        <input type="text" class="form-control datepicker" id="frm_create_RegisterDate" name="RegisterDate" placeholder="Tanggal Pendaftaran" value="<?= $date; ?>" readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label for="name">Masa Berlaku</label>
                                    <div>
                                        <input type="text" class="form-control" id="EndDate" name="EndDate" placeholder="Masa Berlaku" value="" readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="position-relative form-group">
                                    <label>Status Anggota</label>
                                    <select class="form-control" name="StatusAnggota_id" id="StatusAnggota_id">
                                        <option value="" disabled selected>
                                            Status Anggota
                                        </option>
                                        <?php foreach (get_ref_table('status_anggota', 'id, Nama', null, 'data') as $row) : ?>
                                            <option value="<?= $row->id ?>"><?= $row->Nama ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Koleksi yang dapat dipinjam*</label>
                                    <div class="select-wrapper">
                                        <select class="form-control select2" name="CategoryLoan_id[]" multiple="multiple" style="width:100%" required>
                                            <option value="">-Pilih-</option>
                                            <?php foreach (get_ref_table('collectioncategorys', 'id, Name', null, 'data') as $row) : ?>
                                                <option value="<?= $row->id ?>"><?= $row->Name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label>Lokasi Perpustakaan*</label>
                                    <div class="select-wrapper">
                                        <select class="form-control select2" name="LocationLoan_id[]" multiple="multiple" style="width:100%" required>
                                            <option value="">-Pilih-</option>
                                            <?php foreach (get_ref_table('location_library', 'ID, Name', 'Branch_id = ' . user()->branch_id ?? '', 'data') as $row) : ?>
                                                <option value="<?= $row->ID ?>"><?= $row->Name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>