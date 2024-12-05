<div class="tab-pane" id="quick_theme_setting">
    <form method="post" class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" data-ajax-sidepanel="true">
        <div class="quick-card card">
            <div class="card-header">
                <h5>{{ ___('Theme Settings') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-2">
                    <div class="col-lg-12">
                        <label class="form-label" for="theme_color">{{ ___('Primary Color') }} *</label>
                        <input type="color" name="theme_color" id="theme_color" class="form-control form-control-lg"
                               value="{{ $settings->theme_color }}">
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label" for="contact_address">{{ ___('Contact Page Address') }}</label>
                        <input type="text" id="contact_address" name="contact_address" class="form-control"
                               value="{{ @$settings->contact_address }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="contact_email">{{ ___('Contact Page Email') }}</label>
                        <input type="text" id="contact_email" name="contact_email" class="form-control"
                               value="{{ @$settings->contact_email }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="contact_phone">{{ ___('Contact Page Phone') }}</label>
                        <input type="text" id="contact_phone" name="contact_phone" class="form-control"
                               value="{{ @$settings->contact_phone }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="facebook_link">{{ ___('Footer Facebook Page Link') }}</label>
                        <input type="text" id="facebook_link" name="facebook_link" class="form-control"
                               value="{{ @$settings->facebook_link }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="twitter_link">{{ ___('Footer X (Twitter) Page Link') }}</label>
                        <input type="text" id="twitter_link" name="twitter_link" class="form-control"
                               value="{{ @$settings->twitter_link }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="instagram_link">{{ ___('Footer Instagram Page Link') }}</label>
                        <input type="text" id="instagram_link" name="instagram_link" class="form-control"
                               value="{{ @$settings->instagram_link }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="linkedin_link">{{ ___('Footer LinkedIn Page Link') }}</label>
                        <input type="text" id="linkedin_link" name="linkedin_link" class="form-control"
                               value="{{ @$settings->linkedin_link }}" >
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="pinterest_link">{{ ___('Footer Pinterest Page Link') }}</label>
                        <input type="text" id="pinterest_link" name="pinterest_link" class="form-control"
                               value="{{ @$settings->pinterest_link }}" >
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="youtube_link">{{ ___('Footer Youtube Page/Video Link') }}</label>
                        <input type="text" id="youtube_link" name="youtube_link" class="form-control"
                               value="{{ @$settings->youtube_link }}" >
                    </div>
                </div>


            </div>
            <div class="card-footer">
                <input type="hidden" name="theme_setting" value="1">
                <button name="submit" type="submit" class="btn btn-primary">{{ ___('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
