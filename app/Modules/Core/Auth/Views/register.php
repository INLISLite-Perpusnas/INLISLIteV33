<?php helper('app'); ?>
<?= $this->extend('App\Views\layout\blank' ); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-container">
      <div class="h-100 bg-corporate-primary">
            <div class="d-flex h-100 justify-content-center align-items-center">
                  <div class="mx-auto app-login-box col-md-8">
                        <div class="app-logo-inverse mx-auto mb-3"></div>
                        <div class="modal-dialog w-100">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <div class="h5 modal-title">
                                                <?= lang('Auth.registerTitle') ?>
                                                <h6 class="mt-1 mb-0 opacity-8"><span><?=get_parameter('site-name')?></span></h6>
                                          </div>
                                    </div>
                                    <form class="" action="<?= route_to('register') ?>" method="post">
                                          <?= csrf_field() ?>

                                          <div class="modal-body">
                                                <div id="infoMessage">
                                                      <?= view('Myth\Auth\Views\_message_block') ?>
                                                </div>

                                                <div class="form-row">
                                                      <div class="col-md-12">
                                                            <div class="position-relative form-group">
                                                                  <input name="email" placeholder="<?=lang('Auth.email')?>" type="email" class="form-control <?php if(session('errors.email')) : ?>is-invalid<?php endif ?>" value="<?= old('email') ?>">
                                                                  <small class="form-text text-muted"><?=lang('Auth.weNeverShare')?></small>
                                                            </div>
                                                      </div>
                                                      <div class="col-md-12">
                                                            <div class="position-relative form-group">
                                                                  <input name="username" placeholder="<?=lang('Auth.username')?>" type="text" class="form-control <?php if(session('errors.username')) : ?>is-invalid<?php endif ?>" value="<?= old('username') ?>">
                                                            </div>
                                                      </div>
                                                      <div class="col-md-12">
                                                            <div class="position-relative form-group">
                                                                  <input name="password" placeholder="<?=lang('Auth.password')?>" type="password" class="form-control <?php if(session('errors.password')) : ?>is-invalid<?php endif ?>"  autocomplete="off">
                                                            </div>
                                                      </div>
                                                      <div class="col-md-12">
                                                            <div class="position-relative form-group">
                                                                  <input name="pass_confirm" placeholder="<?=lang('Auth.repeatPassword')?>" type="password" class="form-control <?php if(session('errors.pass_confirm')) : ?>is-invalid<?php endif ?>"  autocomplete="off">
                                                            </div>
                                                      </div>
                                                </div>

                                                <!-- <div class="mt-3 position-relative form-check">
                                                      <input name="check" id="exampleCheck" type="checkbox" class="form-check-input">
                                                      <label for="exampleCheck" class="form-check-label">Accept our <a href="javascript:void(0);">Terms and Conditions</a>.</label>
                                                </div> -->

                                                <div class="divider row"></div>
                                                <h6 class="mb-0">
                                                      <?=lang('Auth.alreadyRegistered')?>  
                                                      <a href="<?= route_to('login') ?>" class="text-primary"><?=lang('Auth.signIn')?></a> 

                                                      <?php if ($config->activeResetter): ?>
                                                      | <a href="<?= route_to('forgot') ?>" class="text-primary"><?=lang('Auth.forgotYourPassword')?></a>
                                                      <?php endif; ?>
                                                </h6>
                                          </div>

                                          <div class="modal-footer clearfix">
                                                <div class="float-right">
                                                      <button type="submit" class="btn btn-primary btn-lg"><?=lang('Auth.registerAction')?></button>
                                                </div>
                                          </div>

                                    </form>
                              </div>
                        </div>
                        <div class="text-center text-white opacity-8 mt-3"><?=get_parameter('site-copyright', '&copy; 2021 Adminigiter')?></div>
                  </div>
            </div>
      </div>
</div>
<?= $this->endSection('page'); ?>