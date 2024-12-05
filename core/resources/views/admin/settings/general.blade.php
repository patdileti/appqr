<div class="tab-pane active" id="quick_settings_general">
    <form method="POST" class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}">
        <div class="quick-card card">
            <div class="card-header">
                <h5>{{ ___('General') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">

                    <div class="col-lg-12">
                        <label class="form-label" for="site_title">{{ ___('Site Title') }} *</label>
                        <input type="text" id="site_title" name="site_title" class="form-control"
                               value="{{ @$settings->site_title }}" required>
                        <small class="form-text">{{ ___('The site title is what you would like your website to be known as, this will be used in emails and in the title of your webpages.') }}</small>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="meta_keywords">{{ ___('Meta Keywords') }} *</label>
                        <textarea type="text" id="meta_keywords" name="meta_keywords" class="form-control"
                                  required>{{ @$settings->meta_keywords }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="meta_description">{{ ___('Meta Description') }} *</label>
                        <textarea type="text" id="meta_description" name="meta_description" class="form-control"
                                   required>{{ @$settings->meta_description }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(___('Allergies'), 'admin_allergies', @$settings->admin_allergies == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(___('Send New Order Notification to Restaurants'), 'admin_send_order_notification', @$settings->admin_send_order_notification == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(___('Allow Online Payment For Restaurants'), 'admin_allow_online_payment', @$settings->admin_allow_online_payment == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(___('Allow Text Editor For Restaurant Description'), 'restaurant_text_editor', @$settings->restaurant_text_editor == '1')}}
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="default_user_plan">{{ ___('Default Membership Plan for New Users') }} *</label>
                        <select name="default_user_plan" id="default_user_plan" class="form-control">
                            <option value="free" {{ ($settings->default_user_plan == "free")? "selected" : "" }}>{{ ___('Free') }}</option>
                            <option value="trial" {{ ($settings->default_user_plan == "trial")? "selected" : "" }}>{{ ___('Trial') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        {{ quick_switch(___('Show/hide Verify Email Message'), 'non_active_msg', @$settings->non_active_msg == '1', ___('When disallow, An verify email message will be hide to non-verified users.')) }}
                    </div>
                    <div class="col-lg-6">
                        {{ quick_switch(___('Allow Non-verified users to post content'), 'non_active_allow', @$settings->non_active_allow == '1', ___('When disallow, An error message will be shown to non-verified users to verify their email address.')) }}
                    </div>
                    <div class="col-lg-6">
                        {{ quick_switch(___('Allow User Language Selection'), 'userlangsel', @$settings->userlangsel == '1') }}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(___('Disable Landing Page'), 'disable_landing_page', @$settings->disable_landing_page == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(___('New Users Registration'), 'enable_user_registration', @$settings->enable_user_registration ?? 1)}}
                    </div>

                    <div class="col-lg-6">
                        {{quick_switch(___('Force SSL'), 'enable_force_ssl', @$settings->enable_force_ssl == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(___('Include Language Code in URL'), 'include_language_code', @$settings->include_language_code == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{quick_switch(___('FAQs'), 'enable_faqs', @$settings->enable_faqs == '1')}}
                    </div>
                    <div class="col-lg-6">
                        {{ quick_switch(___('Show membership plan on home page'), 'show_membershipplan_home', @$settings->show_membershipplan_home == '1') }}
                    </div>
                    <div class="col-lg-6">
                        {{ quick_switch(___('Show partners slider on home page'), 'show_partner_logo_home', @$settings->show_partner_logo_home == '1') }}
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="termcondition_link">{{ ___('Term & Condition Page Link') }} *</label>
                        <input type="text" id="termcondition_link" name="termcondition_link" class="form-control"
                               value="{{ @$settings->termcondition_link }}" required>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label" for="privacy_link">{{ ___('Privacy Page Link') }} *</label>
                        <input type="text" id="privacy_link" name="privacy_link" class="form-control"
                               value="{{ @$settings->privacy_link }}" required>
                    </div>
                    <div class="col-lg-6">
                        {{ quick_switch(___('Show/hide Cookie Consent Box'), 'cookie_consent', @$settings->cookie_consent == '1') }}
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="cookie_link">{{ ___('Cookie Policy Page Link') }}</label>
                        <input type="text" id="cookie_link" name="cookie_link" class="form-control"
                               value="{{ @$settings->cookie_link }}">
                    </div>
                    <div class="col-lg-6">
                        {{ quick_switch(___('Show Developer Credit'), 'developer_credit', @$settings->developer_credit == '1') }}
                    </div>
                    <div class="col-lg-6 {{ (env('APP_DEBUG')) ? '' : 'd-none' }}">
                        {{quick_switch(___('Enable Debug'), 'quickad_debug', env('APP_DEBUG'))}}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="general_setting" value="1">
                <button type="submit" class="btn btn-primary">{{ ___('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
