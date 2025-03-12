<div class='content-wrapper'>
    <div class='container-fluid'>

        <!--page title-->
        <div class='page-title mb-4 d-flex align-items-center'>
            <div class='mr-auto'>
                <h4 class='weight500 d-inline-block pr-3 mr-3 border-right'>Collections Form</h4>
                <nav aria-label='breadcrumb' class='d-inline-block '>
                    <ol class='breadcrumb p-0'>
                        <?php foreach ($breadcrumb as $bc) : ?>
                            <li class='breadcrumb-item'><a href='#'><?= $bc ?></a></li>
                        <?php endforeach ?>
                    </ol>
                </nav>
            </div>
        </div>
        <!--/page title-->

        <div class='row'>
            <div class='col-xl-12'>
                <div class='card card-shadow mb-4'>

                    <div class='card-body'>

                        <?php echo form_open($action); ?>
                        <!-- <form action="<//?php echo $action; ?>" method="post"> -->

                        <div class='card-header border-0'>
                            <div class='custom-title-wrap bar-primary'>
                                <button type="button" class="btn btn-purple" data-toggle="modal" data-target=".bd-example-modal-lg">Pilih1 Judul</button>
                                <button type="submit" class="btn btn-primary" id="submit"><?php echo $button ?></button>
                            </div>
                        </div>

                        <?php
                        $errors = session()->getFlashdata('error');
                        $success = session()->getFlashdata('success');
                        if (!empty($errors)) { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php foreach ($errors as $error) : ?>
                                    <?= $error ?>
                                <?php endforeach ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="card card-shadow mb-4" id="modal_judul">

                        </div>
                        <div class="card card-shadow mb-4">
                            <div class="card-header border-0">
                                <div class="custom-title-wrap bar-primary">
                                    <div class="custom-title">Cardex (Edisi Serial)</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="varchar">Edisi Serial</label>
                                    <input type="text" oninvalid="InvalidMsg('Edisi Serial',this);" oninput="InvalidMsg('Edisi Serial',this);" class="form-control" name="EDISISERIAL" id="EDISISERIAL" placeholder="EDISISERIAL" value='<?= set_value('EDISISERIAL', '') ?>' required />
                                    <small id='EDISISERIALError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('EDISISERIAL') : ''; ?></small>
                                </div>
                                <div class="form-group" id="judul-sebelumnya">
                                    <label for="varchar">Tanggal Terbit Edisi Serial</label>
                                    <div>
                                        <div class="input-group date dpYears" data-date-viewmode="years" data-date-format="yyyy-mm-dd" data-date="<?= date('Y-m-d'); ?>">
                                            <input type="text" class="form-control" name="TANGGAL_TERBIT_EDISI_SERIAL" id="TANGGAL_TERBIT_EDISI_SERIAL" aria-label="Right Icon" aria-describedby="dp-ig" value="<?= date('Y-m-d'); ?>">
                                            <div class="input-group-append">
                                                <button id="dp-ig" class="btn btn-primary" type="button"><i class="fa fa-calendar f14"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="varchar">Bahan Sertaan</label>
                                    <input type="text" oninvalid="InvalidMsg('Bahan Sertaan',this);" oninput="InvalidMsg('Bahan Sertaan',this);" class="form-control" name="BAHAN_SERTAAN" id="BAHAN_SERTAAN" placeholder="BAHAN SERTAAN" value='<?= set_value('BAHAN_SERTAAN', '') ?>' required />
                                    <small id='BAHAN_SERTAANError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('BAHAN_SERTAAN') : ''; ?></small>
                                </div>
                                <div class="form-group">
                                    <label for="varchar">Keterangan Lain</label>
                                    <input type="text" class="form-control" name="KETERANGAN_LAIN" id="KETERANGAN_LAIN" placeholder="KETERANGAN LAIN" value='<?= set_value('KETERANGAN_LAIN', '') ?>' />
                                    <small id='KETERANGAN_LAINError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('KETERANGAN_LAIN') : ''; ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="card card-shadow mb-4">
                            <div class="card-header border-0">
                                <div class="custom-title-wrap bar-primary">
                                    <div class="custom-title">Data Pengadaan</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group" style="width: 25%;">
                                    <label for="varchar">Jumlah Eksemplar</label>
                                    <input type="number" class="form-control" id="jml_eksemplar" oninvalid="InvalidMsg('Jumlah Eksemplar',this);" oninput="InvalidMsg('Jumlah Eksemplar',this);" name="jml_eksemplar" value="1" required />
                                </div>
                                <div class="card card-shadow mb-4">
                                    <div class="card-body" id="group_jml_eksemplar">
                                        <div class="form-row">
                                            <div class="col-4">
                                                <label for="varchar">Barcode</label>
                                                <input type="text" id="Barcode" class="form-control" value="<?= BarcodeNumber_helper(); ?>" disabled>
                                            </div>
                                            <div class="col-4">
                                                <label for="varchar">No. Induk</label>
                                                <input type="text" class="form-control" value="<?= NoInduk_helper(); ?>" disabled>
                                            </div>
                                            <div class="col-4">
                                                <label for="varchar">RFID</label>
                                                <input type="text" class="form-control" value="<?= RFID_helper(); ?>" disabled>
                                            </div>
                                        </div>
                                        <div id="container"></div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                        <label for="varchar">Edisi Serial</label>
                                        <input type="text" class="form-control" name="EDISISERIAL" id="EDISISERIAL" placeholder="EDISISERIAL" value='<?= set_value('EDISISERIAL', '') ?>' />
                                        <small id='EDISISERIALError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('EDISISERIAL') : ''; ?></small>
                                    </div> -->
                                <div class="form-group" id="judul-sebelumnya">
                                    <label for="varchar">Tanggal Pengadaan</label>
                                    <div>
                                        <div class="input-group date dpYears" data-date-viewmode="years" data-date-format="yyyy-mm-dd" data-date="<?= date('Y-m-d'); ?>">
                                            <input type="text" class="form-control" name="TANGGAL_PENGADAAN" id="TANGGAL_PENGADAAN" aria-label="Right Icon" aria-describedby="dp-ig" value="<?= date('Y-m-d'); ?>">
                                            <div class="input-group-append">
                                                <button id="dp-ig" class="btn btn-primary" type="button"><i class="fa fa-calendar f14"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Jenis Sumber</label>
                                    <!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
                                        <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
                                    <select class="search form-control" id="Source_id" name="Source_id">
                                        <?php foreach ($select2['src'] as $key => $src) : ?>
                                            <option value="<?= $src->ID ?>">
                                                <?= $src->Name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Nama Sumber</label>
                                    <!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
                                        <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
                                    <select class="search form-control" id="Partner_id" name="Partner_id">
                                        <?php foreach ($select2['partner'] as $key => $src) : ?>
                                            <option value="<?= $src->ID ?>">
                                                <?= $src->Name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Bentuk Media</label>
                                    <!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
                                        <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
                                    <select class="search form-control" id="Media_id" name="Media_id">
                                        <?php foreach ($select2['media'] as $key => $media) : ?>
                                            <option value="<?= $media->ID ?>">
                                                <?= $media->Name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Kategori</label>
                                    <!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
                                        <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
                                    <select class="search form-control" id="Category_id" name="Category_id">
                                        <?php foreach ($select2['category'] as $key => $category) : ?>
                                            <option value="<?= $category->ID ?>">
                                                <?= $category->Name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Akses</label>
                                    <!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
                                        <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
                                    <select class="search form-control" id="Rule_id" name="Rule_id">
                                        <?php foreach ($select2['rule'] as $key => $rule) : ?>
                                            <option value="<?= $rule->ID ?>">
                                                <?= $rule->Name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Lokasi Perpustakaan</label>
                                    <!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
                                        <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
                                    <select class="search form-control" id="Location_Library_id" name="Location_Library_id">
                                        <?php foreach ($select2['lokasi_perpus'] as $key => $lokasi_perpus) : ?>
                                            <option value="<?= $lokasi_perpus->ID ?>">
                                                <?= $lokasi_perpus->Name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Lokasi Ruang Perpustakaan</label>
                                    <!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
                                        <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
                                    <select class="search form-control" id="Location_id" name="Location_id">
                                        <?php foreach ($select2['locations'] as $key => $locations) : ?>
                                            <option value="<?= $locations->ID ?>">
                                                <?= $locations->Name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Ketersediaan</label>
                                    <!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
                                        <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
                                    <select class="search form-control" id="Status_id" name="Status_id">
                                        <?php foreach ($select2['collectionstatus'] as $key => $collectionstatus) : ?>
                                            <option value="<?= $collectionstatus->ID ?>">
                                                <?= $collectionstatus->Name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Currency</label>
                                    <!-- <input type="text" class="form-control" name="bentuk_karya_tulis" id="bentuk_karya_tulis" placeholder="Bentuk Karya Tulis" value='<//?= set_value('bentuk_karya_tulis', 'Bentuk Karya Tulis') ?>' />
                                        <small id='LanguagesError' class='form-text text-muted text-danger'><//?= isset($validation) ? $validation->getError('Languages') : ''; ?></small> -->
                                    <select class="search form-control" id="Currency" name="Currency">
                                        <?php foreach ($select2['currency'] as $key => $Cur) : ?>
                                            <option value="<?= $Cur->Currency ?>">
                                                <?= $Cur->Description ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Harga</label>
                                    <div class="form-row">
                                        <div class="col-10">
                                            <input type="number" class="form-control" name="Price" id="Price" placeholder="Harga" value='<?= set_value('Price', '') ?>' />
                                            <small id='PriceError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('Price') : ''; ?></small>
                                        </div>
                                        <div class="col-2">
                                            <select class="search form-control" id="PriceType" name="PriceType">
                                                <option value="Per jilid">
                                                    Per Jilid
                                                </option>
                                                <option value="Per eksemplar">
                                                    Per Eksemplar
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">CallNumber</label>
                                    <input type="text" class="form-control" name="CallNumber" id="CallNumber" placeholder="CallNumber" value='<?= set_value('CallNumber', '') ?>' />
                                    <small id='CallNumberError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('CallNumber') : ''; ?></small>
                                </div>

                            </div>
                            <div class="form-check form-group">
                                <div>
                                    <input type="hidden" class="iCheck-square" name="IsOPAC" id="IsOPAC" value="0">
                                    <input type="checkbox" class="iCheck-square" name="IsOPAC" id="IsOPAC" value="1">
                                    <label class="  control-label">Tampil di OPAC</label>
                                </div>
                            </div>
                        </div>

                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myLargeModalLabel">Pilih Judul Katalog</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- <form> -->
                        <!-- <form action="<?//php echo $proses; ?>" method="post"> -->
                        <div class='table-responsive'>
                            <table id='data_table' class='table table-bordered table-striped' cellspacing='0' style="width: 200%;">
                                <thead>
                                    <tr id='tr'>
                                        <!-- <th>ControlNumber</th> -->
                                        <th>No</th>
                                        <th>Pilih</th>
                                        <th>BIBID</th>
                                        <th>Title</th>
                                        <!-- <th>Author</th> -->
                                        <th>Edition</th>
                                        <th>Publisher</th>
                                        <th>PhysicalDescription</th>
                                        <!-- <th>ISBN</th> -->
                                        <th>CallNumber</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($catalogslist as $catalogs) {
                                    ?>
                                        <tr>
                                            <!-- <td><//?php echo $catalogs['ControlNumber'] ?></td> -->
                                            <td><?= $i++; ?></td>
                                            <td><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" onclick="myFunction(<?php echo $catalogs['ID'] ?>)">Pilih</button></td>
                                            <td><?php echo $catalogs['BIBID'] ?></td>
                                            <td><?php echo $catalogs['Title'] ?></td>
                                            <!-- <td><//?php echo $catalogs['Author'] ?></td> -->
                                            <td><?php echo $catalogs['Edition'] ?></td>
                                            <td><?php echo $catalogs['Publisher'] ?></td>
                                            <td><?php echo $catalogs['PhysicalDescription'] ?></td>
                                            <!-- <td><//?php echo $catalogs['ISBN'] ?></td> -->
                                            <td><?php echo $catalogs['CallNumber'] ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?= form_close() ?>
                        <!-- </form> -->
                        <!-- Woohoo, you're reading this text in a modal! -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>