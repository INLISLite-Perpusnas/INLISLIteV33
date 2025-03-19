<?php helper('adminigniter');?>
<div class="app-header-right">
    <div class="header-dots">
        <div class="dropdown">
            <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
                <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                    <span class="icon-wrapper-bg bg-danger"></span>
                    <i class="icon text-danger icon-anim-pulse ion-android-notifications"></i> 
                    <span class="badge badge-dot badge-dot-sm badge-danger">012</span>
                </span>
            </button>
        </div>
    </div>

    <div class="header-btn-lg pr-0">
        <div class="widget-content p-0">
            <div class="widget-content-wrapper">
                <div class="widget-content-left">
                    <div class="btn-group">
                        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                            <?php 
                                $default = base_url('themes/uigniter/images/avatars/2.jpg'); 
                                $image = base_url('uploads/user/'.user()->avatar);
                                if(empty(user()->avatar)){
                                    $image = $default;
                                }
                            ?>

                            <img width="42" class="rounded-circle" src="<?=$image?>" onerror="this.onerror=null;this.src='<?=$default?>';" alt="">
                            
                            <i class="fa fa-angle-down ml-2 opacity-8"></i>
                        </a>
                        <div tabindex="-1" role="menu" aria-hidden="true" class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right">
                            <div class="dropdown-menu-header">
                                <div class="dropdown-menu-header-inner bg-primary">
                                    <div class="menu-header-image opacity-2" style="background-image: url('<?= base_url('themes/uigniter'); ?>/images/dropdown-header/city3.jpg');"></div>
                                    <div class="menu-header-content text-left">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left mr-3">
                                                    <img width="42" class="rounded-circle" src="<?=$image?>" onerror="this.onerror=null;this.src='<?=$default?>';" alt="">
                                                </div>
                                                <div class="widget-content-left">
                                                    <div class="widget-heading text-light"><?= user()->username ?>
                                                    </div>
                                                    <div class="widget-subheading opacity-8 text-light"><?=character_limiter(user()->company ?? '',50)?>
                                                    </div>
                                                </div>
                                                <div class="widget-content-right mr-2">
                                                    <a class="btn btn-pill btn-warning" href="<?= base_url('logout'); ?>"><i class="fa fa-power-off"></i> Logout</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="scroll-area-xs" style="height: 150px;">
                                <div class="scrollbar-container ps">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a href="<?= base_url('user/profile'); ?>" class="nav-link">Profil Saya
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('user/change_password'); ?>" class="nav-link">Ubah Password
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php if (is_member('admin')) : ?>
                                <ul class="nav flex-column">
                                    <li class="nav-item-divider mb-0 nav-item"></li>
                                </ul>
                                <div class="grid-menu grid-menu-2col">
                                    <div class="no-gutters row">
                                        <div class="col-sm-6">
                                            <a href="<?= base_url('dashboard/index') ?>" class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-primary">
                                                <i class="pe-7s-display1 icon-gradient bg-primary btn-icon-wrapper mb-2"></i>
                                                Dashboard
                                            </a>
                                        </div>
                                        <div class="col-sm-6">
                                            <a href="<?= base_url('param/index') ?>" class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-primary">
                                                <i class="pe-7s-config icon-gradient bg-primary btn-icon-wrapper mb-2"></i>
                                                <b>Setting</b>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="widget-content-left  ml-3 header-user-info">
                    <div class="widget-heading">
                        <?= user()->first_name . ' ' . user()->last_name  ?>
                    </div>
                    <div class="widget-subheading">
                        <?= user()->company ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="header-btn-lg">
        <button type="button" class="hamburger hamburger--elastic open-right-drawer">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>
    </div> -->
</div>