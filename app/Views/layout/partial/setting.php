<div class="ui-theme-settings">
    <button type="button" id="TooltipDemo" class="btn-open-options btn btn-danger" style="bottom:20px;">
        <i class="fa fa-cog fa-w-16 fa-spin fa-2x"></i>
    </button>
    <div class="theme-settings__inner">
        <div class="scrollbar-container">
            <div class="theme-settings__options-wrapper">
                <h3 class="themeoptions-heading">Layout Options
                </h3>
                <div class="p-3">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left mr-3">
                                        <div class="switch has-switch switch-container-class" data-class="fixed-header">
                                            <div class="switch-animate switch-on">
                                                <input type="checkbox" class="apply-container-class" data-param="container-header-class" data-class="fixed-header" <?= (get_parameter('container-header-class') == 'fixed-header') ? 'checked' : '' ?> data-toggle="toggle" data-onstyle="success">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="widget-content-left">
                                        <div class="widget-heading">Fixed Header
                                        </div>
                                        <div class="widget-subheading">Makes the header top fixed, always visible!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left mr-3">
                                        <div class="switch has-switch switch-container-class" data-class="fixed-sidebar">
                                            <div class="switch-animate switch-on">
                                                <input type="checkbox" class="apply-container-class" data-param="container-sidebar-class" data-class="fixed-sidebar" <?= (get_parameter('container-sidebar-class') == 'fixed-sidebar') ? 'checked' : '' ?> data-toggle="toggle" data-onstyle="success">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="widget-content-left">
                                        <div class="widget-heading">Fixed Sidebar
                                        </div>
                                        <div class="widget-subheading">Makes the sidebar left fixed, always visible!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left mr-3">
                                        <div class="switch has-switch switch-container-class" data-class="fixed-footer">
                                            <div class="switch-animate switch-off">
                                                <input type="checkbox" class="apply-container-class" data-param="container-footer-class" data-class="fixed-footer" <?= (get_parameter('container-footer-class') == 'fixed-footer') ? 'checked' : '' ?> data-toggle="toggle" data-onstyle="success">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="widget-content-left">
                                        <div class="widget-heading">Fixed Footer
                                        </div>
                                        <div class="widget-subheading">Makes the app footer bottom fixed, always visible!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <h3 class="themeoptions-heading">
                    <div>
                        Header Option
                    </div>
                    <button type="button" class="btn-pill btn-shadow btn-wide ml-auto btn btn-focus btn-sm apply-header-cs-class" data-class="">
                        Apply
                    </button>
                </h3>
                <div class="p-3">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <h5 class="pb-2">Header Text Light
                            </h5>
                            <div class="theme-settings-swatches">
                                <div class="swatch-holder bg-primary switch-header-cs-class" data-class="bg-primary header-text-light" title="bg-primary header-text-light"></div>
                                <div class="swatch-holder bg-secondary switch-header-cs-class" data-class="bg-secondary header-text-light" title="bg-secondary header-text-light"></div>
                                <div class="swatch-holder bg-success switch-header-cs-class" data-class="bg-success header-text-light" title="bg-success  header-text-light"></div>
                                <div class="swatch-holder bg-info switch-header-cs-class" data-class="bg-info header-text-light" title="bg-info header-text-light"></div>
                                <div class="swatch-holder bg-warning switch-header-cs-class" data-class="bg-warning header-text-light" title="bg-warning header-text-light"></div>
                                <div class="swatch-holder bg-danger switch-header-cs-class" data-class="bg-danger header-text-light" title="bg-danger header-text-light"></div>
                                <div class="swatch-holder bg-light switch-header-cs-class" data-class="bg-light header-text-light" title="bg-light header-text-light"></div>
                                <div class="swatch-holder bg-dark switch-header-cs-class" data-class="bg-dark header-text-light" title="bg-dark header-text-light"></div>
                                <div class="swatch-holder bg-focus switch-header-cs-class" data-class="bg-focus header-text-light" title="bg-focus header-text-light"></div>
                                <div class="swatch-holder bg-alternate switch-header-cs-class" data-class="bg-alternate header-text-light" title="bg-alternate header-text-light"></div>
                                <div class="divider"></div>
                                <div class="swatch-holder bg-vicious-stance switch-header-cs-class" data-class="bg-vicious-stance header-text-light" title="bg-vicious-stance header-text-light"></div>
                                <div class="swatch-holder bg-midnight-bloom switch-header-cs-class" data-class="bg-midnight-bloom header-text-light" title="bg-midnight-bloom header-text-light"></div>
                                <div class="swatch-holder bg-night-sky switch-header-cs-class" data-class="bg-night-sky header-text-light" title="bg-night-sky header-text-light"></div>
                                <div class="swatch-holder bg-slick-carbon switch-header-cs-class" data-class="bg-slick-carbon header-text-light" title="bg-slick-carbon header-text-light"></div>
                                <div class="swatch-holder bg-asteroid switch-header-cs-class" data-class="bg-asteroid header-text-light" title="bg-asteroid header-text-light"></div>
                                <div class="swatch-holder bg-royal switch-header-cs-class" data-class="bg-royal header-text-light" title="bg-royal header-text-light"></div>
                                <div class="swatch-holder bg-warm-flame switch-header-cs-class" data-class="bg-warm-flame header-text-light" title="bg-warm-flame header-text-light"></div>
                                <div class="swatch-holder bg-night-fade switch-header-cs-class" data-class="bg-night-fade header-text-light" title="bg-night-fade header-text-light"></div>
                                <div class="swatch-holder bg-sunny-morning switch-header-cs-class" data-class="bg-sunny-morning header-text-light" title="bg-sunny-morning header-text-light"></div>
                                <div class="swatch-holder bg-tempting-azure switch-header-cs-class" data-class="bg-tempting-azure header-text-light" title="bg-tempting-azure header-text-light"></div>
                                <div class="swatch-holder bg-amy-crisp switch-header-cs-class" data-class="bg-amy-crisp header-text-light" title="bg-amy-crisp header-text-light"></div>
                                <div class="swatch-holder bg-heavy-rain switch-header-cs-class" data-class="bg-heavy-rain header-text-light" title="bg-heavy-rain header-text-light"></div>
                                <div class="swatch-holder bg-mean-fruit switch-header-cs-class" data-class="bg-mean-fruit header-text-light" title="bg-mean-fruit header-text-light"></div>
                                <div class="swatch-holder bg-malibu-beach switch-header-cs-class" data-class="bg-malibu-beach header-text-light" title="bg-malibu-beach header-text-light"></div>
                                <div class="swatch-holder bg-deep-blue switch-header-cs-class" data-class="bg-deep-blue header-text-light" title="bg-deep-blue header-text-light"></div>
                                <div class="swatch-holder bg-ripe-malin switch-header-cs-class" data-class="bg-ripe-malin header-text-light" title="bg-ripe-malin header-text-light"></div>
                                <div class="swatch-holder bg-arielle-smile switch-header-cs-class" data-class="bg-arielle-smile header-text-light" title="bg-arielle-smile header-text-light"></div>
                                <div class="swatch-holder bg-plum-plate switch-header-cs-class" data-class="bg-plum-plate header-text-light" title="bg-plum-plate header-text-light"></div>
                                <div class="swatch-holder bg-happy-fisher switch-header-cs-class" data-class="bg-happy-fisher header-text-light" title="bg-happy-fisher header-text-light"></div>
                                <div class="swatch-holder bg-happy-itmeo switch-header-cs-class" data-class="bg-happy-itmeo header-text-light" title="bg-happy-itmeo header-text-light"></div>
                                <div class="swatch-holder bg-mixed-hopes switch-header-cs-class" data-class="bg-mixed-hopes header-text-light" title="bg-mixed-hopes header-text-light"></div>
                                <div class="swatch-holder bg-strong-bliss switch-header-cs-class" data-class="bg-strong-bliss header-text-light" title="bg-strong-bliss header-text-light"></div>
                                <div class="swatch-holder bg-grow-early switch-header-cs-class" data-class="bg-grow-early header-text-light" title="bg-grow-early header-text-light"></div>
                                <div class="swatch-holder bg-love-kiss switch-header-cs-class" data-class="bg-love-kiss header-text-light" title="bg-love-kiss header-text-light"></div>
                                <div class="swatch-holder bg-premium-dark switch-header-cs-class" data-class="bg-premium-dark header-text-light" title="bg-premium-dark header-text-light"></div>
                                <div class="swatch-holder bg-happy-green switch-header-cs-class" data-class="bg-happy-green header-text-light" title="bg-happy-green header-text-light"></div>
                                <div class="divider"></div>
                                <div class="swatch-holder bg-corporate-primary switch-header-cs-class" data-class="bg-corporate-primary header-text-light" title="bg-corporate-primary header-text-light"></div>
                                <div class="swatch-holder bg-corporate-primary2 switch-header-cs-class" data-class="bg-corporate-primary2 header-text-light" title="bg-corporate-primary2 header-text-light"></div>
                                <div class="swatch-holder bg-corporate-secondary switch-header-cs-class" data-class="bg-corporate-secondary header-text-light" title="bg-corporate-secondary header-text-light"></div>
                                <div class="swatch-holder bg-corporate-secondary2 switch-header-cs-class" data-class="bg-corporate-secondary2 header-text-light" title="bg-corporate-secondary2 header-text-light"></div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <h5 class="pb-2">Header Text Dark
                            </h5>
                            <div class="theme-settings-swatches">
                                <div class="swatch-holder bg-primary switch-header-cs-class" data-class="bg-primary header-text-dark" title="bg-primary header-text-dark"></div>
                                <div class="swatch-holder bg-secondary switch-header-cs-class" data-class="bg-secondary header-text-dark" title="bg-secondary header-text-dark"></div>
                                <div class="swatch-holder bg-success switch-header-cs-class" data-class="bg-success header-text-dark" title="bg-success  header-text-dark"></div>
                                <div class="swatch-holder bg-info switch-header-cs-class" data-class="bg-info header-text-dark" title="bg-info header-text-dark"></div>
                                <div class="swatch-holder bg-warning switch-header-cs-class" data-class="bg-warning header-text-dark" title="bg-warning header-text-dark"></div>
                                <div class="swatch-holder bg-danger switch-header-cs-class" data-class="bg-danger header-text-dark" title="bg-danger header-text-dark"></div>
                                <div class="swatch-holder bg-light switch-header-cs-class" data-class="bg-light header-text-dark" title="bg-light header-text-dark"></div>
                                <div class="swatch-holder bg-dark switch-header-cs-class" data-class="bg-dark header-text-dark" title="bg-dark header-text-dark"></div>
                                <div class="swatch-holder bg-focus switch-header-cs-class" data-class="bg-focus header-text-dark" title="bg-focus header-text-dark"></div>
                                <div class="swatch-holder bg-alternate switch-header-cs-class" data-class="bg-alternate header-text-dark" title="bg-alternate header-text-dark"></div>
                                <div class="divider"></div>
                                <div class="swatch-holder bg-vicious-stance switch-header-cs-class" data-class="bg-vicious-stance header-text-dark" title="bg-vicious-stance header-text-dark"></div>
                                <div class="swatch-holder bg-midnight-bloom switch-header-cs-class" data-class="bg-midnight-bloom header-text-dark" title="bg-midnight-bloom header-text-dark"></div>
                                <div class="swatch-holder bg-night-sky switch-header-cs-class" data-class="bg-night-sky header-text-dark" title="bg-night-sky header-text-dark"></div>
                                <div class="swatch-holder bg-slick-carbon switch-header-cs-class" data-class="bg-slick-carbon header-text-dark" title="bg-slick-carbon header-text-dark"></div>
                                <div class="swatch-holder bg-asteroid switch-header-cs-class" data-class="bg-asteroid header-text-dark" title="bg-asteroid header-text-dark"></div>
                                <div class="swatch-holder bg-royal switch-header-cs-class" data-class="bg-royal header-text-dark" title="bg-royal header-text-dark"></div>
                                <div class="swatch-holder bg-warm-flame switch-header-cs-class" data-class="bg-warm-flame header-text-dark" title="bg-warm-flame header-text-dark"></div>
                                <div class="swatch-holder bg-night-fade switch-header-cs-class" data-class="bg-night-fade header-text-dark" title="bg-night-fade header-text-dark"></div>
                                <div class="swatch-holder bg-sunny-morning switch-header-cs-class" data-class="bg-sunny-morning header-text-dark" title="bg-sunny-morning header-text-dark"></div>
                                <div class="swatch-holder bg-tempting-azure switch-header-cs-class" data-class="bg-tempting-azure header-text-dark" title="bg-tempting-azure header-text-dark"></div>
                                <div class="swatch-holder bg-amy-crisp switch-header-cs-class" data-class="bg-amy-crisp header-text-dark" title="bg-amy-crisp header-text-dark"></div>
                                <div class="swatch-holder bg-heavy-rain switch-header-cs-class" data-class="bg-heavy-rain header-text-dark" title="bg-heavy-rain header-text-dark"></div>
                                <div class="swatch-holder bg-mean-fruit switch-header-cs-class" data-class="bg-mean-fruit header-text-dark" title="bg-mean-fruit header-text-dark"></div>
                                <div class="swatch-holder bg-malibu-beach switch-header-cs-class" data-class="bg-malibu-beach header-text-dark" title="bg-malibu-beach header-text-dark"></div>
                                <div class="swatch-holder bg-deep-blue switch-header-cs-class" data-class="bg-deep-blue header-text-dark" title="bg-deep-blue header-text-dark"></div>
                                <div class="swatch-holder bg-ripe-malin switch-header-cs-class" data-class="bg-ripe-malin header-text-dark" title="bg-ripe-malin header-text-dark"></div>
                                <div class="swatch-holder bg-arielle-smile switch-header-cs-class" data-class="bg-arielle-smile header-text-dark" title="bg-arielle-smile header-text-dark"></div>
                                <div class="swatch-holder bg-plum-plate switch-header-cs-class" data-class="bg-plum-plate header-text-dark" title="bg-plum-plate header-text-dark"></div>
                                <div class="swatch-holder bg-happy-fisher switch-header-cs-class" data-class="bg-happy-fisher header-text-dark" title="bg-happy-fisher header-text-dark"></div>
                                <div class="swatch-holder bg-happy-itmeo switch-header-cs-class" data-class="bg-happy-itmeo header-text-dark" title="bg-happy-itmeo header-text-dark"></div>
                                <div class="swatch-holder bg-mixed-hopes switch-header-cs-class" data-class="bg-mixed-hopes header-text-dark" title="bg-mixed-hopes header-text-dark"></div>
                                <div class="swatch-holder bg-strong-bliss switch-header-cs-class" data-class="bg-strong-bliss header-text-dark" title="bg-strong-bliss header-text-dark"></div>
                                <div class="swatch-holder bg-grow-early switch-header-cs-class" data-class="bg-grow-early header-text-dark" title="bg-grow-early header-text-dark"></div>
                                <div class="swatch-holder bg-love-kiss switch-header-cs-class" data-class="bg-love-kiss header-text-dark" title="bg-love-kiss header-text-dark"></div>
                                <div class="swatch-holder bg-premium-dark switch-header-cs-class" data-class="bg-premium-dark header-text-dark" title="bg-premium-dark header-text-dark"></div>
                                <div class="swatch-holder bg-happy-green switch-header-cs-class" data-class="bg-happy-green header-text-dark" title="bg-happy-green header-text-dark"></div>
                                <div class="divider"></div>
                                <div class="swatch-holder bg-corporate-primary switch-header-cs-class" data-class="bg-corporate-primary header-text-dark" title="bg-corporate-primary header-text-dark"></div>
                                <div class="swatch-holder bg-corporate-primary2 switch-header-cs-class" data-class="bg-corporate-primary2 header-text-dark" title="bg-corporate-primary2 header-text-dark"></div>
                                <div class="swatch-holder bg-corporate-secondary switch-header-cs-class" data-class="bg-corporate-secondary header-text-dark" title="bg-corporate-secondary header-text-dark"></div>
                                <div class="swatch-holder bg-corporate-secondary2 switch-header-cs-class" data-class="bg-corporate-secondary2 header-text-dark" title="bg-corporate-secondary2 header-text-dark"></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <h3 class="themeoptions-heading">
                    <div>Sidebar Options</div>
                    <button type="button" class="btn-pill btn-shadow btn-wide ml-auto btn btn-focus btn-sm apply-sidebar-cs-class" data-class="">
                        Apply
                    </button>
                </h3>
                <div class="p-3">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <h5 class="pb-2">Sidebar Text Light
                            </h5>
                            <div class="theme-settings-swatches">
                                <div class="swatch-holder bg-primary switch-sidebar-cs-class" data-class="bg-primary sidebar-text-light" title="bg-primary sidebar-text-light"></div>
                                <div class="swatch-holder bg-secondary switch-sidebar-cs-class" data-class="bg-secondary sidebar-text-light" title="bg-secondary sidebar-text-light"></div>
                                <div class="swatch-holder bg-success switch-sidebar-cs-class" data-class="bg-success sidebar-text-light" title="bg-success  sidebar-text-light"></div>
                                <div class="swatch-holder bg-info switch-sidebar-cs-class" data-class="bg-info sidebar-text-light" title="bg-info sidebar-text-light"></div>
                                <div class="swatch-holder bg-warning switch-sidebar-cs-class" data-class="bg-warning sidebar-text-light" title="bg-warning sidebar-text-light"></div>
                                <div class="swatch-holder bg-danger switch-sidebar-cs-class" data-class="bg-danger sidebar-text-light" title="bg-danger sidebar-text-light"></div>
                                <div class="swatch-holder bg-light switch-sidebar-cs-class" data-class="bg-light sidebar-text-light" title="bg-light sidebar-text-light"></div>
                                <div class="swatch-holder bg-dark switch-sidebar-cs-class" data-class="bg-dark sidebar-text-light" title="bg-dark sidebar-text-light"></div>
                                <div class="swatch-holder bg-focus switch-sidebar-cs-class" data-class="bg-focus sidebar-text-light" title="bg-focus sidebar-text-light"></div>
                                <div class="swatch-holder bg-alternate switch-sidebar-cs-class" data-class="bg-alternate sidebar-text-light" title="bg-alternate sidebar-text-light"></div>
                                <div class="divider"></div>
                                <div class="swatch-holder bg-vicious-stance switch-sidebar-cs-class" data-class="bg-vicious-stance sidebar-text-light" title="bg-vicious-stance sidebar-text-light"></div>
                                <div class="swatch-holder bg-midnight-bloom switch-sidebar-cs-class" data-class="bg-midnight-bloom sidebar-text-light" title="bg-midnight-bloom sidebar-text-light"></div>
                                <div class="swatch-holder bg-night-sky switch-sidebar-cs-class" data-class="bg-night-sky sidebar-text-light" title="bg-night-sky sidebar-text-light"></div>
                                <div class="swatch-holder bg-slick-carbon switch-sidebar-cs-class" data-class="bg-slick-carbon sidebar-text-light" title="bg-slick-carbon sidebar-text-light"></div>
                                <div class="swatch-holder bg-asteroid switch-sidebar-cs-class" data-class="bg-asteroid sidebar-text-light" title="bg-asteroid sidebar-text-light"></div>
                                <div class="swatch-holder bg-royal switch-sidebar-cs-class" data-class="bg-royal sidebar-text-light" title="bg-royal sidebar-text-light"></div>
                                <div class="swatch-holder bg-warm-flame switch-sidebar-cs-class" data-class="bg-warm-flame sidebar-text-light" title="bg-warm-flame sidebar-text-light"></div>
                                <div class="swatch-holder bg-night-fade switch-sidebar-cs-class" data-class="bg-night-fade sidebar-text-light" title="bg-night-fade sidebar-text-light"></div>
                                <div class="swatch-holder bg-sunny-morning switch-sidebar-cs-class" data-class="bg-sunny-morning sidebar-text-light" title="bg-sunny-morning sidebar-text-light"></div>
                                <div class="swatch-holder bg-tempting-azure switch-sidebar-cs-class" data-class="bg-tempting-azure sidebar-text-light" title="bg-tempting-azure sidebar-text-light"></div>
                                <div class="swatch-holder bg-amy-crisp switch-sidebar-cs-class" data-class="bg-amy-crisp sidebar-text-light" title="bg-amy-crisp sidebar-text-light"></div>
                                <div class="swatch-holder bg-heavy-rain switch-sidebar-cs-class" data-class="bg-heavy-rain sidebar-text-light" title="bg-heavy-rain sidebar-text-light"></div>
                                <div class="swatch-holder bg-mean-fruit switch-sidebar-cs-class" data-class="bg-mean-fruit sidebar-text-light" title="bg-mean-fruit sidebar-text-light"></div>
                                <div class="swatch-holder bg-malibu-beach switch-sidebar-cs-class" data-class="bg-malibu-beach sidebar-text-light" title="bg-malibu-beach sidebar-text-light"></div>
                                <div class="swatch-holder bg-deep-blue switch-sidebar-cs-class" data-class="bg-deep-blue sidebar-text-light" title="bg-deep-blue sidebar-text-light"></div>
                                <div class="swatch-holder bg-ripe-malin switch-sidebar-cs-class" data-class="bg-ripe-malin sidebar-text-light" title="bg-ripe-malin sidebar-text-light"></div>
                                <div class="swatch-holder bg-arielle-smile switch-sidebar-cs-class" data-class="bg-arielle-smile sidebar-text-light" title="bg-arielle-smile sidebar-text-light"></div>
                                <div class="swatch-holder bg-plum-plate switch-sidebar-cs-class" data-class="bg-plum-plate sidebar-text-light" title="bg-plum-plate sidebar-text-light"></div>
                                <div class="swatch-holder bg-happy-fisher switch-sidebar-cs-class" data-class="bg-happy-fisher sidebar-text-light" title="bg-happy-fisher sidebar-text-light"></div>
                                <div class="swatch-holder bg-happy-itmeo switch-sidebar-cs-class" data-class="bg-happy-itmeo sidebar-text-light" title="bg-happy-itmeo sidebar-text-light"></div>
                                <div class="swatch-holder bg-mixed-hopes switch-sidebar-cs-class" data-class="bg-mixed-hopes sidebar-text-light" title="bg-mixed-hopes sidebar-text-light"></div>
                                <div class="swatch-holder bg-strong-bliss switch-sidebar-cs-class" data-class="bg-strong-bliss sidebar-text-light" title="bg-strong-bliss sidebar-text-light"></div>
                                <div class="swatch-holder bg-grow-early switch-sidebar-cs-class" data-class="bg-grow-early sidebar-text-light" title="bg-grow-early sidebar-text-light"></div>
                                <div class="swatch-holder bg-love-kiss switch-sidebar-cs-class" data-class="bg-love-kiss sidebar-text-light" title="bg-love-kiss sidebar-text-light"></div>
                                <div class="swatch-holder bg-premium-dark switch-sidebar-cs-class" data-class="bg-premium-dark sidebar-text-light" title="bg-premium-dark sidebar-text-light"></div>
                                <div class="swatch-holder bg-happy-green switch-sidebar-cs-class" data-class="bg-happy-green sidebar-text-light" title="bg-happy-green sidebar-text-light"></div>
                                <div class="divider"></div>
                                <div class="swatch-holder bg-corporate-primary switch-sidebar-cs-class" data-class="bg-corporate-primary sidebar-text-light" title="bg-corporate-primary sidebar-text-light"></div>
                                <div class="swatch-holder bg-corporate-primary2 switch-sidebar-cs-class" data-class="bg-corporate-primary2 sidebar-text-light" title="bg-corporate-primary2 sidebar-text-light"></div>
                                <div class="swatch-holder bg-corporate-secondary switch-sidebar-cs-class" data-class="bg-corporate-secondary sidebar-text-light" title="bg-corporate-secondary sidebar-text-light"></div>
                                <div class="swatch-holder bg-corporate-secondary2 switch-sidebar-cs-class" data-class="bg-corporate-secondary2 sidebar-text-light" title="bg-corporate-secondary2 sidebar-text-light"></div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <h5 class="pb-2">Sidebar Text Dark
                            </h5>
                            <div class="theme-settings-swatches">
                                <div class="swatch-holder bg-primary switch-sidebar-cs-class" data-class="bg-primary sidebar-text-dark" title="bg-primary sidebar-text-dark"></div>
                                <div class="swatch-holder bg-secondary switch-sidebar-cs-class" data-class="bg-secondary sidebar-text-dark" title="bg-secondary sidebar-text-dark"></div>
                                <div class="swatch-holder bg-success switch-sidebar-cs-class" data-class="bg-success sidebar-text-dark" title="bg-success  sidebar-text-dark"></div>
                                <div class="swatch-holder bg-info switch-sidebar-cs-class" data-class="bg-info sidebar-text-dark" title="bg-info sidebar-text-dark"></div>
                                <div class="swatch-holder bg-warning switch-sidebar-cs-class" data-class="bg-warning sidebar-text-dark" title="bg-warning sidebar-text-dark"></div>
                                <div class="swatch-holder bg-danger switch-sidebar-cs-class" data-class="bg-danger sidebar-text-dark" title="bg-danger sidebar-text-dark"></div>
                                <div class="swatch-holder bg-light switch-sidebar-cs-class" data-class="bg-light sidebar-text-dark" title="bg-light sidebar-text-dark"></div>
                                <div class="swatch-holder bg-dark switch-sidebar-cs-class" data-class="bg-dark sidebar-text-dark" title="bg-dark sidebar-text-dark"></div>
                                <div class="swatch-holder bg-focus switch-sidebar-cs-class" data-class="bg-focus sidebar-text-dark" title="bg-focus sidebar-text-dark"></div>
                                <div class="swatch-holder bg-alternate switch-sidebar-cs-class" data-class="bg-alternate sidebar-text-dark" title="bg-alternate sidebar-text-dark"></div>
                                <div class="divider"></div>
                                <div class="swatch-holder bg-vicious-stance switch-sidebar-cs-class" data-class="bg-vicious-stance sidebar-text-dark" title="bg-vicious-stance sidebar-text-dark"></div>
                                <div class="swatch-holder bg-midnight-bloom switch-sidebar-cs-class" data-class="bg-midnight-bloom sidebar-text-dark" title="bg-midnight-bloom sidebar-text-dark"></div>
                                <div class="swatch-holder bg-night-sky switch-sidebar-cs-class" data-class="bg-night-sky sidebar-text-dark" title="bg-night-sky sidebar-text-dark"></div>
                                <div class="swatch-holder bg-slick-carbon switch-sidebar-cs-class" data-class="bg-slick-carbon sidebar-text-dark" title="bg-slick-carbon sidebar-text-dark"></div>
                                <div class="swatch-holder bg-asteroid switch-sidebar-cs-class" data-class="bg-asteroid sidebar-text-dark" title="bg-asteroid sidebar-text-dark"></div>
                                <div class="swatch-holder bg-royal switch-sidebar-cs-class" data-class="bg-royal sidebar-text-dark" title="bg-royal sidebar-text-dark"></div>
                                <div class="swatch-holder bg-warm-flame switch-sidebar-cs-class" data-class="bg-warm-flame sidebar-text-dark" title="bg-warm-flame sidebar-text-dark"></div>
                                <div class="swatch-holder bg-night-fade switch-sidebar-cs-class" data-class="bg-night-fade sidebar-text-dark" title="bg-night-fade sidebar-text-dark"></div>
                                <div class="swatch-holder bg-sunny-morning switch-sidebar-cs-class" data-class="bg-sunny-morning sidebar-text-dark" title="bg-sunny-morning sidebar-text-dark"></div>
                                <div class="swatch-holder bg-tempting-azure switch-sidebar-cs-class" data-class="bg-tempting-azure sidebar-text-dark" title="bg-tempting-azure sidebar-text-dark"></div>
                                <div class="swatch-holder bg-amy-crisp switch-sidebar-cs-class" data-class="bg-amy-crisp sidebar-text-dark" title="bg-amy-crisp sidebar-text-dark"></div>
                                <div class="swatch-holder bg-heavy-rain switch-sidebar-cs-class" data-class="bg-heavy-rain sidebar-text-dark" title="bg-heavy-rain sidebar-text-dark"></div>
                                <div class="swatch-holder bg-mean-fruit switch-sidebar-cs-class" data-class="bg-mean-fruit sidebar-text-dark" title="bg-mean-fruit sidebar-text-dark"></div>
                                <div class="swatch-holder bg-malibu-beach switch-sidebar-cs-class" data-class="bg-malibu-beach sidebar-text-dark" title="bg-malibu-beach sidebar-text-dark"></div>
                                <div class="swatch-holder bg-deep-blue switch-sidebar-cs-class" data-class="bg-deep-blue sidebar-text-dark" title="bg-deep-blue sidebar-text-dark"></div>
                                <div class="swatch-holder bg-ripe-malin switch-sidebar-cs-class" data-class="bg-ripe-malin sidebar-text-dark" title="bg-ripe-malin sidebar-text-dark"></div>
                                <div class="swatch-holder bg-arielle-smile switch-sidebar-cs-class" data-class="bg-arielle-smile sidebar-text-dark" title="bg-arielle-smile sidebar-text-dark"></div>
                                <div class="swatch-holder bg-plum-plate switch-sidebar-cs-class" data-class="bg-plum-plate sidebar-text-dark" title="bg-plum-plate sidebar-text-dark"></div>
                                <div class="swatch-holder bg-happy-fisher switch-sidebar-cs-class" data-class="bg-happy-fisher sidebar-text-dark" title="bg-happy-fisher sidebar-text-dark"></div>
                                <div class="swatch-holder bg-happy-itmeo switch-sidebar-cs-class" data-class="bg-happy-itmeo sidebar-text-dark" title="bg-happy-itmeo sidebar-text-dark"></div>
                                <div class="swatch-holder bg-mixed-hopes switch-sidebar-cs-class" data-class="bg-mixed-hopes sidebar-text-dark" title="bg-mixed-hopes sidebar-text-dark"></div>
                                <div class="swatch-holder bg-strong-bliss switch-sidebar-cs-class" data-class="bg-strong-bliss sidebar-text-dark" title="bg-strong-bliss sidebar-text-dark"></div>
                                <div class="swatch-holder bg-grow-early switch-sidebar-cs-class" data-class="bg-grow-early sidebar-text-dark" title="bg-grow-early sidebar-text-dark"></div>
                                <div class="swatch-holder bg-love-kiss switch-sidebar-cs-class" data-class="bg-love-kiss sidebar-text-dark" title="bg-love-kiss sidebar-text-dark"></div>
                                <div class="swatch-holder bg-premium-dark switch-sidebar-cs-class" data-class="bg-premium-dark sidebar-text-dark" title="bg-premium-dark sidebar-text-dark"></div>
                                <div class="swatch-holder bg-happy-green switch-sidebar-cs-class" data-class="bg-happy-green sidebar-text-dark" title="bg-happy-green sidebar-text-dark"></div>
                                <div class="divider"></div>
                                <div class="swatch-holder bg-corporate-primary switch-sidebar-cs-class" data-class="bg-corporate-primary sidebar-text-dark" title="bg-corporate-primary sidebar-text-dark"></div>
                                <div class="swatch-holder bg-corporate-primary2 switch-sidebar-cs-class" data-class="bg-corporate-primary2 sidebar-text-dark" title="bg-corporate-primary2 sidebar-text-dark"></div>
                                <div class="swatch-holder bg-corporate-secondary switch-sidebar-cs-class" data-class="bg-corporate-secondary sidebar-text-dark" title="bg-corporate-secondary sidebar-text-dark"></div>
                                <div class="swatch-holder bg-corporate-secondary2 switch-sidebar-cs-class" data-class="bg-corporate-secondary2 sidebar-text-dark" title="bg-corporate-secondary2 sidebar-text-dark"></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- <h3 class="themeoptions-heading">
                    <div>Main Content Options</div>
                    <button type="button" class="btn-pill btn-shadow btn-wide ml-auto active btn btn-focus btn-sm">Restore Default
                    </button>
                </h3>
                <div class="p-3">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <h5 class="pb-2">Page Section Tabs
                            </h5>
                            <div class="theme-settings-swatches">
                                <div role="group" class="mt-2 btn-group">
                                    <button type="button" class="btn-wide btn-shadow btn-primary btn btn-secondary switch-theme-class" data-class="body-tabs-line">
                                        Line
                                    </button>
                                    <button type="button" class="btn-wide btn-shadow btn-primary active btn btn-secondary switch-theme-class" data-class="body-tabs-shadow">
                                        Shadow
                                    </button>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <h5 class="pb-2">Light Color Schemes
                            </h5>
                            <div class="theme-settings-swatches">
                                <div role="group" class="mt-2 btn-group">
                                    <button type="button" class="btn-wide btn-shadow btn-primary active btn btn-secondary switch-theme-class" data-class="app-theme-white">
                                        White Theme
                                    </button>
                                    <button type="button" class="btn-wide btn-shadow btn-primary btn btn-secondary switch-theme-class" data-class="app-theme-gray">
                                        Gray Theme
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div> -->
            </div>
        </div>
    </div>
</div>

<?= $this->section('script'); ?>
<script>
    $(".apply-container-class").on('change', function() {
        var switchStatus = $(this).is(':checked');
        var paramName = $(this).attr('data-param');
        var paramValue = $(this).attr('data-class');

        if (switchStatus) {
            setParameter(paramName, paramValue);
        } else {
            setParameter(paramName, '');
        }
    });

    $('.apply-header-cs-class').click(function() {
        var paramName = 'header-cs-class'
        var paramValue = $(this).attr('data-class');
        setParameter(paramName, paramValue);
    });

    $('.apply-sidebar-cs-class').click(function() {
        var paramName = 'sidebar-cs-class'
        var paramValue = $(this).attr('data-class');
        setParameter(paramName, paramValue);
    });

    $('.switch-container-class').on('click', function() {
        var classToSwitch = $(this).attr('data-class');
        var containerElement = '.app-container';
        $(containerElement).toggleClass(classToSwitch);

        $(this).parent().find('.switch-container-class').removeClass('active');
        $(this).addClass('active');
    });

    $('.switch-header-cs-class').on('click', function() {
        var classToSwitch = $(this).attr('data-class');
        var containerElement = '.app-header';

        $('.switch-header-cs-class').removeClass('active');
        $(this).addClass('active');

        $(containerElement).attr('class', 'app-header');
        $(containerElement).addClass('header-shadow ' + classToSwitch);

        $('.apply-header-cs-class').attr('data-class', classToSwitch);
    });

    $('.switch-sidebar-cs-class').on('click', function() {
        var classToSwitch = $(this).attr('data-class');
        var containerElement = '.app-sidebar';

        $('.switch-sidebar-cs-class').removeClass('active');
        $(this).addClass('active');

        $(containerElement).attr('class', 'app-sidebar');
        $(containerElement).addClass('sidebar-shadow ' + classToSwitch);

        $('.apply-sidebar-cs-class').attr('data-class', classToSwitch);
    });

    $('.btn-open-options').click(function() {
        $('.ui-theme-settings').toggleClass('settings-open');
    });

    $('.switch-theme-class').on('click', function() {
        var classToSwitch = $(this).attr('data-class');
        var containerElement = '.app-container';

        if (classToSwitch == 'app-theme-white') {
            $(containerElement).removeClass('app-theme-gray');
            $(containerElement).addClass(classToSwitch);
        }

        if (classToSwitch == 'app-theme-gray') {
            $(containerElement).removeClass('app-theme-white');
            $(containerElement).addClass(classToSwitch);
        }

        if (classToSwitch == 'body-tabs-line') {
            $(containerElement).removeClass('body-tabs-shadow');
            $(containerElement).addClass(classToSwitch);
        }

        if (classToSwitch == 'body-tabs-shadow') {
            $(containerElement).removeClass('body-tabs-line');
            $(containerElement).addClass(classToSwitch);
        }

        $(this).parent().find('.switch-theme-class').removeClass('active');
        $(this).addClass('active');

    });
</script>
<?= $this->endSection('script'); ?>