    <div class="header-dots">
        <div class="dropdown">
            <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
                <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                    <span class="icon-wrapper-bg bg-danger"></span>
                    <i class="icon text-danger icon-anim-pulse ion-android-notifications"></i>
                    <span class="badge badge-dot badge-dot-sm badge-danger">Notifications</span>
                </span>
            </button>
            <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu-xl rm-pointers dropdown-menu dropdown-menu-right">
                <div class="dropdown-menu-header mb-0">
                    <div class="dropdown-menu-header-inner bg-deep-blue">
                        <div class="menu-header-image opacity-1" style="background-image: url('<?= base_url('uigniter'); ?>/assets/images/dropdown-header/city3.jpg');"></div>
                        <div class="menu-header-content text-dark">
                            <h5 class="menu-header-title">Notifications</h5>
                            <h6 class="menu-header-subtitle">You have <b>21</b> unread messages</h6>
                        </div>
                    </div>
                </div>
                <ul class="tabs-animated-shadow tabs-animated nav nav-justified tabs-shadow-bordered p-3">
                    <li class="nav-item">
                        <a role="tab" class="nav-link active" data-toggle="tab" href="#tab-messages-header">
                            <span>Messages</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a role="tab" class="nav-link" data-toggle="tab" href="#tab-events-header">
                            <span>Events</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a role="tab" class="nav-link" data-toggle="tab" href="#tab-errors-header">
                            <span>System Errors</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-messages-header" role="tabpanel">
                        <div class="scroll-area-sm">
                            <div class="scrollbar-container">
                                <div class="p-3">
                                    <div class="notifications-box">
                                        <div class="vertical-time-simple vertical-without-time vertical-timeline vertical-timeline--one-column">
                                            <div class="vertical-timeline-item dot-danger vertical-timeline-element">
                                                <div><span class="vertical-timeline-element-icon bounce-in"></span>
                                                    <div class="vertical-timeline-element-content bounce-in">
                                                        <h4 class="timeline-title">All Hands Meeting</h4><span class="vertical-timeline-element-date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vertical-timeline-item dot-warning vertical-timeline-element">
                                                <div><span class="vertical-timeline-element-icon bounce-in"></span>
                                                    <div class="vertical-timeline-element-content bounce-in">
                                                        <p>Yet another one, at <span class="text-success">15:00 PM</span></p><span class="vertical-timeline-element-date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vertical-timeline-item dot-success vertical-timeline-element">
                                                <div><span class="vertical-timeline-element-icon bounce-in"></span>
                                                    <div class="vertical-timeline-element-content bounce-in">
                                                        <h4 class="timeline-title">Build the production release
                                                            <span class="badge badge-danger ml-2">NEW</span>
                                                        </h4>
                                                        <span class="vertical-timeline-element-date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vertical-timeline-item dot-primary vertical-timeline-element">
                                                <div><span class="vertical-timeline-element-icon bounce-in"></span>
                                                    <div class="vertical-timeline-element-content bounce-in">
                                                        <h4 class="timeline-title">Something not important
                                                            <div class="avatar-wrapper mt-2 avatar-wrapper-overlap">
                                                                <div class="avatar-icon-wrapper avatar-icon-sm">
                                                                    <div class="avatar-icon"><img src="<?= base_url('themes/uigniter'); ?>/images/avatars/1.jpg" alt=""></div>
                                                                </div>
                                                                <div class="avatar-icon-wrapper avatar-icon-sm">
                                                                    <div class="avatar-icon"><img src="<?= base_url('themes/uigniter'); ?>/images/avatars/2.jpg" alt=""></div>
                                                                </div>
                                                                <div class="avatar-icon-wrapper avatar-icon-sm">
                                                                    <div class="avatar-icon"><img src="<?= base_url('themes/uigniter'); ?>/images/avatars/3.jpg" alt=""></div>
                                                                </div>
                                                                <div class="avatar-icon-wrapper avatar-icon-sm">
                                                                    <div class="avatar-icon"><img src="<?= base_url('themes/uigniter'); ?>/images/avatars/4.jpg" alt=""></div>
                                                                </div>
                                                                <div class="avatar-icon-wrapper avatar-icon-sm">
                                                                    <div class="avatar-icon"><img src="<?= base_url('themes/uigniter'); ?>/images/avatars/5.jpg" alt=""></div>
                                                                </div>
                                                                <div class="avatar-icon-wrapper avatar-icon-sm">
                                                                    <div class="avatar-icon"><img src="<?= base_url('themes/uigniter'); ?>/images/avatars/9.jpg" alt=""></div>
                                                                </div>
                                                                <div class="avatar-icon-wrapper avatar-icon-sm">
                                                                    <div class="avatar-icon"><img src="<?= base_url('themes/uigniter'); ?>/images/avatars/7.jpg" alt=""></div>
                                                                </div>
                                                                <div class="avatar-icon-wrapper avatar-icon-sm">
                                                                    <div class="avatar-icon"><img src="<?= base_url('themes/uigniter'); ?>/images/avatars/8.jpg" alt=""></div>
                                                                </div>
                                                                <div class="avatar-icon-wrapper avatar-icon-sm avatar-icon-add">
                                                                    <div class="avatar-icon"><i>+</i></div>
                                                                </div>
                                                            </div>
                                                        </h4>
                                                        <span class="vertical-timeline-element-date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vertical-timeline-item dot-info vertical-timeline-element">
                                                <div><span class="vertical-timeline-element-icon bounce-in"></span>
                                                    <div class="vertical-timeline-element-content bounce-in">
                                                        <h4 class="timeline-title">This dot has an info state</h4><span class="vertical-timeline-element-date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vertical-timeline-item dot-danger vertical-timeline-element">
                                                <div><span class="vertical-timeline-element-icon bounce-in"></span>
                                                    <div class="vertical-timeline-element-content bounce-in">
                                                        <h4 class="timeline-title">All Hands Meeting</h4><span class="vertical-timeline-element-date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vertical-timeline-item dot-warning vertical-timeline-element">
                                                <div><span class="vertical-timeline-element-icon bounce-in"></span>
                                                    <div class="vertical-timeline-element-content bounce-in">
                                                        <p>Yet another one, at <span class="text-success">15:00 PM</span></p><span class="vertical-timeline-element-date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vertical-timeline-item dot-success vertical-timeline-element">
                                                <div><span class="vertical-timeline-element-icon bounce-in"></span>
                                                    <div class="vertical-timeline-element-content bounce-in">
                                                        <h4 class="timeline-title">Build the production release
                                                            <span class="badge badge-danger ml-2">NEW</span>
                                                        </h4>
                                                        <span class="vertical-timeline-element-date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vertical-timeline-item dot-dark vertical-timeline-element">
                                                <div><span class="vertical-timeline-element-icon bounce-in"></span>
                                                    <div class="vertical-timeline-element-content bounce-in">
                                                        <h4 class="timeline-title">This dot has a dark state</h4><span class="vertical-timeline-element-date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-events-header" role="tabpanel">
                        <div class="scroll-area-sm">
                            <div class="scrollbar-container">
                                <div class="p-3">
                                    <div class="vertical-without-time vertical-timeline vertical-timeline--animate vertical-timeline--one-column">
                                        <div class="vertical-timeline-item vertical-timeline-element">
                                            <div><span class="vertical-timeline-element-icon bounce-in"><i class="badge badge-dot badge-dot-xl badge-success"> </i></span>
                                                <div class="vertical-timeline-element-content bounce-in">
                                                    <h4 class="timeline-title">All Hands Meeting</h4>
                                                    <p>Lorem ipsum dolor sic amet, today at <a href="javascript:void(0);">12:00 PM</a></p><span class="vertical-timeline-element-date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vertical-timeline-item vertical-timeline-element">
                                            <div><span class="vertical-timeline-element-icon bounce-in"><i class="badge badge-dot badge-dot-xl badge-warning"> </i></span>
                                                <div class="vertical-timeline-element-content bounce-in">
                                                    <p>Another meeting today, at <b class="text-danger">12:00 PM</b></p>
                                                    <p>Yet another one, at <span class="text-success">15:00 PM</span></p><span class="vertical-timeline-element-date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vertical-timeline-item vertical-timeline-element">
                                            <div><span class="vertical-timeline-element-icon bounce-in"><i class="badge badge-dot badge-dot-xl badge-danger"> </i></span>
                                                <div class="vertical-timeline-element-content bounce-in">
                                                    <h4 class="timeline-title">Build the production release</h4>
                                                    <p>Lorem ipsum dolor sit amit,consectetur eiusmdd tempor incididunt ut labore et dolore magna elit enim at minim veniam quis nostrud</p><span class="vertical-timeline-element-date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vertical-timeline-item vertical-timeline-element">
                                            <div><span class="vertical-timeline-element-icon bounce-in"><i class="badge badge-dot badge-dot-xl badge-primary"> </i></span>
                                                <div class="vertical-timeline-element-content bounce-in">
                                                    <h4 class="timeline-title text-success">Something not important</h4>
                                                    <p>Lorem ipsum dolor sit amit,consectetur elit enim at minim veniam quis nostrud</p><span class="vertical-timeline-element-date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vertical-timeline-item vertical-timeline-element">
                                            <div><span class="vertical-timeline-element-icon bounce-in"><i class="badge badge-dot badge-dot-xl badge-success"> </i></span>
                                                <div class="vertical-timeline-element-content bounce-in">
                                                    <h4 class="timeline-title">All Hands Meeting</h4>
                                                    <p>Lorem ipsum dolor sic amet, today at <a href="javascript:void(0);">12:00 PM</a></p><span class="vertical-timeline-element-date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vertical-timeline-item vertical-timeline-element">
                                            <div><span class="vertical-timeline-element-icon bounce-in"><i class="badge badge-dot badge-dot-xl badge-warning"> </i></span>
                                                <div class="vertical-timeline-element-content bounce-in">
                                                    <p>Another meeting today, at <b class="text-danger">12:00 PM</b></p>
                                                    <p>Yet another one, at <span class="text-success">15:00 PM</span></p><span class="vertical-timeline-element-date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vertical-timeline-item vertical-timeline-element">
                                            <div><span class="vertical-timeline-element-icon bounce-in"><i class="badge badge-dot badge-dot-xl badge-danger"> </i></span>
                                                <div class="vertical-timeline-element-content bounce-in">
                                                    <h4 class="timeline-title">Build the production release</h4>
                                                    <p>Lorem ipsum dolor sit amit,consectetur eiusmdd tempor incididunt ut labore et dolore magna elit enim at minim veniam quis nostrud</p><span class="vertical-timeline-element-date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vertical-timeline-item vertical-timeline-element">
                                            <div><span class="vertical-timeline-element-icon bounce-in"><i class="badge badge-dot badge-dot-xl badge-primary"> </i></span>
                                                <div class="vertical-timeline-element-content bounce-in">
                                                    <h4 class="timeline-title text-success">Something not important</h4>
                                                    <p>Lorem ipsum dolor sit amit,consectetur elit enim at minim veniam quis nostrud</p><span class="vertical-timeline-element-date"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-errors-header" role="tabpanel">
                        <div class="scroll-area-sm">
                            <div class="scrollbar-container">
                                <div class="no-results pt-3 pb-0">
                                    <div class="swal2-icon swal2-success swal2-animate-success-icon">
                                        <div class="swal2-success-circular-line-left" style="background-color: rgb(255, 255, 255);"></div>
                                        <span class="swal2-success-line-tip"></span>
                                        <span class="swal2-success-line-long"></span>
                                        <div class="swal2-success-ring"></div>
                                        <div class="swal2-success-fix" style="background-color: rgb(255, 255, 255);"></div>
                                        <div class="swal2-success-circular-line-right" style="background-color: rgb(255, 255, 255);"></div>
                                    </div>
                                    <div class="results-subtitle">All caught up!</div>
                                    <div class="results-title">There are no system errors!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item-divider nav-item"></li>
                    <li class="nav-item-btn text-center nav-item">
                        <button class="btn-shadow btn-wide btn-pill btn btn-focus btn-sm">View Latest Changes</button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="dropdown">
            <button type="button" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
                <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                    <span class="icon-wrapper-bg bg-focus"></span>
                    <span class="language-icon opacity-8 flag large <?= (strtoupper(get_lang()) === 'ID') ? 'ID' : 'GB'; ?>"></span>
                </span>
            </button>
            <div tabindex="-1" role="menu" aria-hidden="true" class="rm-pointers dropdown-menu dropdown-menu-right">
                <div class="dropdown-menu-header">
                    <div class="dropdown-menu-header-inner pt-2 pb-2 bg-focus">
                        <div class="menu-header-content text-center text-white">
                            <h6 class="menu-header-subtitle mt-0">
                                <?= lang('App.lang_choose'); ?>
                            </h6>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('lang/id'); ?>" class="dropdown-item switch_lang">
                    <span class="mr-3 opacity-8 flag large ID"></span>
                    Bahasa
                </a>
                <a href="<?= base_url('lang/en'); ?>" class="dropdown-item switch_lang" data-lang="en">
                    <span class="mr-3 opacity-8 flag large GB"></span>
                    English
                </a>
            </div>
        </div>
    </div>