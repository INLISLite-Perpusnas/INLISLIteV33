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
                                <button type="submit" class="btn btn-primary"><?php echo $button ?></button>
                                <a href="<?php echo base_url('backend/collections') ?>" class="btn btn-danger">Keluar</a>
                            </div>
                        </div>


                        <div class="card card-shadow mb-4" id="modal_judul">
                            <!-- readonly form -->
                        </div>
                        <?php foreach ($collections_update[0] as $key => $Value_fix) {
                            // $val001 = $Value_fix['Worksheet_id'];
                        ?>
                        <?php } ?>


                        <div class="card card-shadow mb-4">
                            <div class="card-header border-0">
                                <div class="custom-title-wrap bar-primary">
                                    <div class="custom-title">Cardex (Edisi Serial)</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="varchar">Edisi Serial</label>
                                    <input type="text" oninvalid="InvalidMsg('Edisi Serial',this);" oninput="InvalidMsg('Edisi Serial',this);" class="form-control" name="EDISISERIAL" id="EDISISERIAL" placeholder="EDISISERIAL" value='<?= !empty($collections_update[0]->EDISISERIAL) ? $collections_update[0]->EDISISERIAL : set_value('EDISISERIAL', '') ?>' required />
                                    <small id='EDISISERIALError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('EDISISERIAL') : ''; ?></small>
                                </div>
                                <div class="form-group" id="judul-sebelumnya">
                                    <label for="varchar">Tanggal Terbit Edisi Serial</label>
                                    <div>
                                        <div class="input-group date dpYears" data-date-viewmode="years" data-date-format="yyyy-mm-dd" data-date="<?= !empty($collections_update[0]->TANGGAL_TERBIT_EDISI_SERIAL) ? $collections_update[0]->TANGGAL_TERBIT_EDISI_SERIAL : set_value('TANGGAL_TERBIT_EDISI_SERIAL', date('Y-m-d')) ?>">
                                            <input type="text" class="form-control" name="TANGGAL_TERBIT_EDISI_SERIAL" id="TANGGAL_TERBIT_EDISI_SERIAL" aria-label="Right Icon" aria-describedby="dp-ig" value="<?= !empty($collections_update[0]->TANGGAL_TERBIT_EDISI_SERIAL) ? $collections_update[0]->TANGGAL_TERBIT_EDISI_SERIAL : set_value('TANGGAL_TERBIT_EDISI_SERIAL', date('Y-m-d')) ?>">
                                            <div class="input-group-append">
                                                <button id="dp-ig" class="btn btn-primary" type="button"><i class="fa fa-calendar f14"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="varchar">Bahan Sertaan</label>
                                    <input type="text" oninvalid="InvalidMsg('Bahan Sertaan',this);" oninput="InvalidMsg('Bahan Sertaan',this);" class="form-control" name="BAHAN_SERTAAN" id="BAHAN_SERTAAN" placeholder="BAHAN SERTAAN" value="<?= !empty($collections_update[0]->BAHAN_SERTAAN) ? $collections_update[0]->BAHAN_SERTAAN : set_value('BAHAN_SERTAAN', '') ?>" required />
                                    <small id='BAHAN_SERTAANError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('BAHAN_SERTAAN') : ''; ?></small>
                                </div>
                                <div class=" form-group">
                                    <label for="varchar">Keterangan Lain</label>
                                    <input type="text" class="form-control" name="KETERANGAN_LAIN" id="KETERANGAN_LAIN" placeholder="KETERANGAN LAIN" value="<?= !empty($collections_update[0]->KETERANGAN_LAIN) ? $collections_update[0]->KETERANGAN_LAIN : set_value('KETERANGAN_LAIN', '') ?>" />
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
                                    <input type="number" class="form-control" id="jml_eksemplar" name="jml_eksemplar" value="1" readonly />
                                </div>
                                <div class="card card-shadow mb-4">
                                    <div class="card-body" id="group_jml_eksemplar">
                                        <div class="form-row">
                                            <div class="col-4">
                                                <label for="varchar">Barcode</label>
                                                <input type="text" name="NomorBarcode" class="form-control" value="<?= !empty($collections_update[0]->NomorBarcode) ? $collections_update[0]->NomorBarcode : set_value('NomorBarcode', BarcodeNumber_helper()) ?>" readonly>
                                            </div>
                                            <div class="col-4">
                                                <label for="varchar">No. Induk</label>
                                                <input type="text" name="NoInduk" class="form-control" value="<?= !empty($collections_update[0]->NoInduk) ? $collections_update[0]->NoInduk : set_value('NoInduk', NoInduk_helper()) ?>" readonly>
                                            </div>
                                            <div class="col-4">
                                                <label for="varchar">RFID</label>
                                                <input type="text" name="RFID" class="form-control" value="<?= !empty($collections_update[0]->RFID) ? $collections_update[0]->RFID : set_value('RFID', RFID_helper()) ?>" readonly>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group" id="judul-sebelumnya">
                                    <label for="varchar">Tanggal Pengadaan</label>
                                    <div>
                                        <div class="input-group date dpYears" data-date-viewmode="years" data-date-format="yyyy-mm-dd" data-date="<?= date('Y-m-d'); ?>">
                                            <input type="text" class="form-control" name="TANGGAL_PENGADAAN" id="TANGGAL_PENGADAAN" aria-label="Right Icon" aria-describedby="dp-ig" value="<?= !empty($collections_update[0]->TanggalPengadaan) ? $collections_update[0]->TanggalPengadaan : set_value('TANGGAL_PENGADAAN', date('Y-m-d')) ?>">
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
                                            <option value="<?= $src->ID ?>" <?= ($src->ID == $collections_update[0]->Source_id ? 'selected' : ''); ?>>
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
                                            <option value="<?= $src->ID ?>" <?= ($src->ID == $collections_update[0]->Partner_id ? 'selected' : ''); ?>>
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
                                            <option value="<?= $media->ID ?>" <?= ($media->ID == $collections_update[0]->Media_id ? 'selected' : ''); ?>>
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
                                            <option value="<?= $category->ID ?>" <?= ($category->ID == $collections_update[0]->Category_id ? 'selected' : ''); ?>>
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
                                            <option value="<?= $rule->ID ?>" <?= ($rule->ID == $collections_update[0]->Rule_id ? 'selected' : ''); ?>>
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
                                            <option value="<?= $lokasi_perpus->ID ?>" <?= ($lokasi_perpus->ID == $collections_update[0]->Location_Library_id ? 'selected' : ''); ?>>
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
                                            <option value="<?= $locations->ID ?>" <?= ($locations->ID == $collections_update[0]->Location_id ? 'selected' : ''); ?>>
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
                                            <option value="<?= $collectionstatus->ID ?>" <?= ($collectionstatus->ID == $collections_update[0]->Status_id ? 'selected' : ''); ?>>
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
                                            <option value="<?= $Cur->Currency ?>" <?= ($Cur->Currency == $collections_update[0]->Currency ? 'selected' : ''); ?>>
                                                <?= $Cur->Description ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Harga</label>
                                    <div class="form-row">
                                        <div class="col-10">
                                            <input type="number" class="form-control" name="Price" id="Price" placeholder="Harga" value='<?= !empty($collections_update[0]->Price) ? $collections_update[0]->Price : set_value('Price', '') ?>' />
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
                                    <input type="text" class="form-control" name="CallNumber" id="CallNumber" placeholder="CallNumber" value='<?= !empty($collections_update[0]->CallNumber) ? $collections_update[0]->CallNumber : set_value('CallNumber', '') ?>' />
                                    <small id='CallNumberError' class='form-text text-muted text-danger'><?= isset($validation) ? $validation->getError('CallNumber') : ''; ?></small>
                                </div>

                            </div>
                            <div class="form-check form-group">
                                <div>
                                    <input type="hidden" class="iCheck-square" name="IsOPAC" id="IsOPAC" value="0">
                                    <input type="checkbox" class="iCheck-square" name="IsOPAC" id="IsOPAC" value="1" <?= ($collections_update[0]->ISOPAC > 0 ? 'checked' : ''); ?>>
                                    <label class="  control-label">Tampil di OPAC</label>
                                </div>
                            </div>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                    <div id="share"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var str = "<?php echo $collections_update[0]->ID ?>";

        function codeAddress() {
            var html = [];
            $.get('../collections/data_catalog/' + str, function(data) {
                console.log(data);
                html.push('<div class="card-header border-0">');
                html.push('<div class="custom-title-wrap bar-primary">');
                html.push('<div class="custom-title">Judul</div>');
                html.push('</div>');
                html.push('</div>');
                html.push('<div class="card-body">');
                // html.push('<input type="hidden" class="form-control" name = "Catalog_id" value ="' + str + '" readonly />');

                $.each(JSON.parse(data), function(index, value) {

                    html.push('<div class="form-group">');
                    html.push('<label for="varchar">' + ((index == "a") ? 'Judul Utama' : (index == "b") ? 'Anak Judul' : 'Penanggung Jawab') + '</label>');
                    html.push('<input type="text" class="form-control" value ="' + value + '" disabled />');
                    html.push('</div>');
                    // // alert( value );                
                });
                html.push('</div>');

                $("#modal_judul").append(html.join(''));
            });
            // var html = [];
            // $.ajax({
            //     url: '<//?php echo base_url('/backend/akuisisi/data_catalog/' . $str . ''); ?>',
            //     headers: {
            //         'X-Requested-With': 'XMLHttpRequest'
            //     },
            //     type: 'GET',
            //     dataType: 'html',
            //     data: {
            //         id: '11'
            //     },
            //     success: function(response) {
            //         console.log(response);
            //         // html.push('<div class="card-header border-0">');
            //         // html.push('<div class="custom-title-wrap bar-primary">');
            //         // html.push('<div class="custom-title">Judul</div>');
            //         // html.push('</div>');
            //         // html.push('</div>');
            //         // html.push('<div class="card-body">');
            //         // html.push('<input type="hidden" class="form-control" name = "Catalog_id" value ="'.mesti id.
            //         //     '" readonly />');
            //         // $.each(JSON.parse(response), function(index, value) {

            //         //     html.push('<div class="form-group">');
            //         //     html.push('<label for="varchar">' + ((index == "a") ? 'Judul Utama' : (index == "b") ? 'Anak Judul' : 'Penanggung Jawab') + '</label>');
            //         //     html.push('<input type="text" class="form-control" value ="' + value + '" disabled />');
            //         //     html.push('</div>');
            //         //     // // alert( value );                
            //         // });
            //         // html.push('</div>');
            //         // $("#modal_judul").append(html.join(''));
            //     },
            //     error: function(data) {
            //         alert('error on read data');
            //     }
            // });
        }

        window.onload = codeAddress;
    </script>