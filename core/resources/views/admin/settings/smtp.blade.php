<div class="tab-pane" id="quick_smtp">
    <form class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" method="POST">
        <div class="card">
            <div class="card-header">
                <h5>{{ ___('SMTP details') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Admin Email Address') }} *</label>
                            <input type="email" name="admin_email" class="form-control"
                                   value="{{ demo_mode() ? '' : $settings->admin_email }}"
                                   placeholder="" required>
                            <small class="form-text">{{___('This is the email address to which the contact and feedback emails will be sent.')}}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('From Email Address') }} *</label>
                            <input type="email" name="smtp_from_email" class="form-control"
                                   value="{{ demo_mode() ? '' : @$settings->smtp_from_email }}"
                                   placeholder="" required>
                            <small class="form-text">{{___('This email will be used to send emails.')}}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('From Name') }} *</label>
                            <input type="text" name="smtp_from_name" class="form-control"
                                   value="{{ demo_mode() ? '' : @$settings->smtp_from_name }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Mailer') }} </label>
                            <select name="smtp_mailer" class="form-select">
                                <option value="smtp" @if (@$settings->smtp_mailer == 'smtp') selected @endif>
                                    {{ ___('SMTP') }}
                                </option>
                                <option value="sendmail" @if (@$settings->smtp_mailer == 'sendmail') selected @endif>
                                    {{ ___('SENDMAIL') }}
                                </option>
                                <option value="log" @if (@$settings->smtp_mailer == 'log') selected @endif>
                                    {{ ___('Log') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Host') }}</label>
                            <input type="text" name="smtp_host" class=" form-control"
                                   value="{{ demo_mode() ? '' : @$settings->smtp_host }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Port') }}</label>
                            <input type="text" name="smtp_port" class=" form-control"
                                   value="{{ demo_mode() ? '' : @$settings->smtp_port }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Username') }}</label>
                            <input type="text" name="smtp_username" class="form-control "
                                   value="{{ demo_mode() ? '' : @$settings->smtp_username }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Password') }} </label>
                            <input type="password" name="smtp_password" class="form-control"
                                   value="{{ demo_mode() ? '' : @$settings->smtp_password }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ ___('Encryption') }}
                                <i class="icon-feather-help-circle" title="{{ ('If your e-mail service provider supported secure connections, you can choose security method on list.') }}" data-tippy-placement="top"></i>
                            </label>
                            <select name="smtp_secure" class="form-select">
                                <option value="tls" @if (@$settings->smtp_secure == 'tls') selected @endif>
                                    {{ ___('TLS') }}
                                </option>
                                <option value="ssl" @if (@$settings->smtp_secure == 'ssl') selected @endif>
                                    {{ ___('SSL') }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="smtp_settings" value="1">
                <button type="submit" class="btn btn-primary">{{ ___('Save Changes') }}</button>
            </div>
        </div>
    </form>

    <div class="card mt-4">
        <div class="card-header">
            <h5>{{ ___('Test SMTP') }}</h5>
        </div>
        <div class="card-body">
            <form class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" method="POST">
                <div class="mb-3">
                    <label class="form-label">{{ ___('Email Address') }} *</label>
                    <input type="email" name="email" class="form-control" placeholder="john@example.com"
                           value="{{ request()->user()->email }}">
                </div>
                <input type="hidden" name="smtp_test" value="1">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i> {{ ___('Send') }}</button>
            </form>
        </div>
    </div>
</div>
