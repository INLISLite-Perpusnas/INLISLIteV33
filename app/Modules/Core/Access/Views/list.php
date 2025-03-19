<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-config icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Permission</b>
                    <div class="page-title-subheading">Daftar Semua Permission</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">Otorisasi</li>
                        <li class="active breadcrumb-item" aria-current="page">Permission</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container">
        <ul class="tabs-animated-shadow tabs-animated nav nav-justified tabs-rounded-lg">
            <?php foreach ($groups as $row) : ?>
                <li class="nav-item" style="min-width:100px">
                    <a role="tab" class="nav-link <?= ($row->id == $group->id) ? 'active' : '' ?>" href="<?= base_url('access?group_id=' . $row->id) ?>" aria-selected="true">
                        <span><?= $row->name ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="btn bg-primary text-white pt-2">Cari:</span>
                </div>
                <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Ketik nama menu...">
            </div>
        </div>
    </div>

    <div class="card-columns">
        <?php $i = 1; ?>
        <?php foreach ($menus as $k => $row) : ?>
            <?php
            $sub_menus = get_sub_menu($row->id);
            $has_submenu = (count($sub_menus) > 0) ? true : false;
            ?>

            <div class="card-hover-shadow card-border mb-3 card parent-menu" data-menu="<?= strtolower($row->name); ?>">
                <div class="card-header text-left">
                    <a class="text-primary" style="text-decoration: none" data-toggle="collapse" href="#permission_<?= $row->id ?>">
                        <i class="more-less fa fa-minus"></i>
                        <?php if ($has_submenu) : ?>
                            <i class="pe-7s-keypad font-weight-bold"> </i>
                        <?php endif; ?>

                        <?= ucfirst($row->name); ?>
                    </a>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        <span class="badge badge-secondary"><?= ucfirst($row->type); ?> </span>
                    </div>
                </div>
                <div class="card-body collapseIcon collapse show" id="permission_<?= $row->id ?>">
                    <div class="row mb-3">
                        <?php foreach (explode("|", $row->permission) as $kk => $permission) : ?>
                            <div class="pb-3 col-md-12">
                                <div class="widget-content p-0">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left mr-3">
                                            <div class="switch has-switch switch-container-class" data-class="fixed-setting">
                                                <div class="switch-animate switch-on">
                                                    <input type="checkbox" class="module_checkbox check" name="permissions[]" value="<?= strtolower($row->controller . '/' . $permission) ?>" data-perm='<?= strtolower($row->controller . '/' . $permission) ?>' data-id='<?= $row->id ?>' data-module='<?= strtolower($row->controller); ?>' data-permission='<?= strtolower($permission); ?>' id='cb_<?= $kk . $k ?>' data-toggle="toggle" data-onstyle="success" <?php if (in_array(strtolower($row->controller) . '/' . strtolower($permission), $access)) echo 'checked'; ?>>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widget-content-left">
                                            <div class="widget-heading">
                                                <?= strtolower($permission) ?>&nbsp;
                                                <i class="fa fa-info-circle text-muted" data-toggle="tooltip" data-placement="right" title="<?= strtolower($row->controller . '/' . $permission) ?>"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php $j = 1; ?>
                    <?php foreach ($sub_menus as $k2 => $row2) : ?>
                        <div class="card-hover-shadow card-border mb-3 card">
                            <div class="card-header">
                                <a class="text-primary collapsed" style="text-decoration: none" data-toggle="collapse" href="#permission<?= $row->id ?>_<?= $row2->id ?>">
                                    <i class="more-less fa fa-plus"></i> <?= ucfirst($row2->name); ?>
                                </a>
                            </div>
                            <div class="card-body collapse" id="permission<?= $row->id ?>_<?= $row2->id ?>">
                                <div class="row">
                                    <?php foreach (explode("|", $row2->permission) as $kk2 => $permission2) : ?>
                                        <div class="pb-3 col-md-12">
                                            <div class="widget-content p-0">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left mr-3">
                                                        <div class="switch has-switch switch-container-class" data-class="fixed-setting">
                                                            <div class="switch-animate switch-on">
                                                                <input type="checkbox" class="module_checkbox" name="permissions[]" value="<?= strtolower($row2->controller . '/' . $permission2) ?>" data-perm='<?= strtolower($row2->controller . '/' . $permission2) ?>' data-id='<?= strtolower($row2->id); ?>' data-module='<?= strtolower($row2->controller); ?>' data-permission='<?= strtolower($permission2); ?>' id='cb_<?= $kk2 . $k2 ?>' data-toggle="toggle" data-onstyle="success" <?php if (in_array(strtolower($row2->controller) . '/' . strtolower($permission2), $access)) echo 'checked'; ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading"><?= strtolower($permission2) ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div>
                        <?php $j++; ?>
                    <?php endforeach; ?>
                </div>

            </div>
            <?php $i++; ?>
        <?php endforeach; ?>
    </div>
    <div class="row mb-3 mt-3">
        <div class="col-md-12">
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" id="btn_perms" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('themes/uigniter'); ?>/js/vendors/carousel-slider.js"></script>
<script>
    $(".slick-slider").slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 4,
        speed: 500,
        dots: true,
        arrows: false,
        adaptiveHeight: true
    });

    $('#btn_perms').click(function() {
        var data_post = $(":checkbox:checked").serializeArray();
        var url = '<?= base_url('api/access/add_to_group/' . $group->id) ?>';
        console.log(data_post);

        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: data_post,
            })
            .done(function(res) {
                console.log(res)
                if (res.status === 201) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Access berhasil disimpan',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    setTimeout(function() {
                        window.location.href = '<?= base_url('access?group_id=' . $group->id) ?>';
                    }, 2000);
                } else {
                    $('#frm_edit_message').html(res.messages.error);
                }
            })
            .fail(function(res) {
                console.log(res);
                $('#frm_edit_message').html(res.responseJSON.messages.error);
            })
            .always(function() {
                $('.loading').hide();
                $('html, body').animate({
                    scrollTop: $(document).height()
                }, 2000);
            });

        return false;
    });

    function toggleIcon(e) {
        $(e.target)
            .prev('.card-header')
            .find(".more-less")
            .toggleClass('fa-plus fa-minus');
    }

    $(".collapseIcon").on("hide.bs.collapse", function(e) {
        toggleIcon(e);
    });

    $(".collapseIcon").on("show.bs.collapse", function(e) {
        toggleIcon(e);
    });

    function checkbox_checked() {
        var values = [];
        $.each($(":checkbox:checked"), function(i) {
            values[i] = $(this).val();
        });

        console.log(values);

        return values;
    }

    $('#keyword').keyup(function() {
        var keyword = $(this).val().toLowerCase();
        $('.parent-menu').each(function() {
            var menu = $(this).data('menu');
            if (menu.includes(keyword)) {
                $(this).show();
            } else {
                $(this).hide()
            }
        });
    });
</script>
<?= $this->endSection('script'); ?>