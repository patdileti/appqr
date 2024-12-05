<div class="tab-pane" id="quick_logo_favicon">
    <form method="post" class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}"
          data-ajax-sidepanel="true" enctype="multipart/form-data">

        <div class="quick-card card">
            <div class="card-header">
                <h5>{{ ___('Logo & Favicon') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Favicon Icon') }}</label>
                            <div class="mb-3">
                                <div class="quick-imgview bg-light">
                                    <img id="favicon_img"
                                         src="{{ asset('storage/logo/'.$settings->site_favicon) }}">
                                </div>
                            </div>
                            <label for="site_favicon" class="btn btn-primary w-100 mb-2" tabindex="0">
                                <i class="fas fa-upload me-2"></i>
                                {{ ___('Upload New Image') }}
                                <input name="site_favicon" type="file" id="site_favicon" hidden
                                       onchange="readURL(this,'favicon_img')"
                                       accept=".jpg, .jpeg, .png">
                            </label>
                            <small class="text-muted">{{ ___('Allowed JPG, JPEG or PNG.') }}</small>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Admin Logo') }}</label>
                            <div class="mb-3">
                                <div class="quick-imgview bg-light">
                                    <img id="admin_logo_img"
                                         src="{{ asset('storage/logo/'.$settings->site_admin_logo) }}">
                                </div>
                            </div>
                            <label for="site_admin_logo" class="btn btn-primary w-100 mb-2" tabindex="0">
                                <i class="fas fa-upload me-2"></i>
                                {{ ___('Upload New Image') }}
                                <input name="site_admin_logo" type="file" id="site_admin_logo" hidden
                                       onchange="readURL(this,'admin_logo_img')"
                                       accept=".jpg, .jpeg, .png">
                            </label>
                            <small class="text-muted">{{ ___('Allowed JPG, JPEG or PNG.') }}</small>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Site Logo (Dark)') }}</label>
                            <div class="mb-3">
                                <div class="quick-imgview bg-light">
                                    <img id="site_logo_img"
                                         src="{{ asset('storage/logo/'.$settings->site_logo) }}">
                                </div>
                            </div>
                            <label for="site_logo" class="btn btn-primary w-100 mb-2" tabindex="0">
                                <i class="fas fa-upload me-2"></i>
                                {{ ___('Upload New Image') }}
                                <input name="site_logo" type="file" id="site_logo" hidden
                                       onchange="readURL(this,'site_logo_img')"
                                       accept=".jpg, .jpeg, .png">
                            </label>
                            <small class="text-muted">{{ ___('Allowed JPG, JPEG or PNG.') }}</small>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Footer Logo (Light)') }}</label>
                            <div class="mb-3">
                                <div class="quick-imgview bg-dark">
                                    <img id="footer_logo_img"
                                         src="{{ asset('storage/logo/'.$settings->site_logo_footer) }}">
                                </div>
                            </div>
                            <label for="site_logo_footer" class="btn btn-primary w-100 mb-2" tabindex="0">
                                <i class="fas fa-upload me-2"></i>
                                {{ ___('Upload New Image') }}
                                <input name="site_logo_footer" type="file" id="site_logo_footer" hidden
                                       onchange="readURL(this,'footer_logo_img')"
                                       accept=".jpg, .jpeg, .png">
                            </label>
                            <small class="text-muted">{{ ___('Allowed JPG, JPEG or PNG.') }}</small>
                        </div>
                    </div>


                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Social Share Image') }}</label>
                            <div class="mb-3">
                                <div class="quick-img-card bg-light">
                                    <img id="social_image_img"
                                         src="{{ asset('storage/logo/'.@$settings->social_share_image) }}"
                                         width="100%">
                                </div>
                            </div>
                            <label for="social_share_image" class="btn btn-primary w-100 mb-2" tabindex="0">
                                <i class="fas fa-upload me-2"></i>
                                {{ ___('Upload New Image') }}
                                <input name="social_share_image" type="file" id="social_share_image" hidden
                                       onchange="readURL(this,'social_image_img')"
                                       accept="image/jpg, image/jpeg">
                            </label>
                            <small class="text-muted">
                                {{ ___('Allowed JPG or JPEG.') }} <strong>600x315px.</strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="logo_setting" value="1">
                <button name="submit" type="submit" class="btn btn-primary">{{ ___('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
