<div class="tab-pane" id="quick_international">
    <form method="post" class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" data-ajax-sidepanel="true">
        <div class="quick-card card">
            <div class="card-header">
                <h5>{{ ___('International') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-2">
                    <div class="col-lg-6">
                        <label class="form-label" for="lang">{{ ___('Set Default language') }} *</label>
                        <select name="lang" id="lang" class="form-control">
                            @foreach ($admin_languages as $adminLanguage)
                                <option value="{{ $adminLanguage->code }}"
                                    {{ (env('DEFAULT_LANGUAGE') == $adminLanguage->code) ? "selected" : "" }}>
                                {{ $adminLanguage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="timezone">{{ ___('Timezone') }} *</label>
                        <select name="timezone" id="timezone" class="form-select">
                            @foreach (config('timezones') as $timezoneKey => $timezoneValue)
                                <option value="{{ $timezoneKey }}"
                                    {{ ($settings->timezone == $timezoneKey) ? 'selected' : '' }}>
                                    {{ $timezoneValue }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-12">
                        <label class="form-label" for="currency_code">{{ ___('Currency Code') }} *</label>
                        <input type="text" name="currency_code" id="currency_code" class="form-control"
                               value="{{ $settings->currency_code }}" placeholder="USD"
                               required>
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label" for="currency_sign">{{ ___('Currency Symbol') }} *</label>
                        <input type="text" name="currency_sign" id="currency_sign" class="form-control"
                               value="{{ $settings->currency_sign }}" placeholder="$"
                               required>
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label" for="currency_pos">{{ ___('Currency position') }} *</label>
                        <select name="currency_pos" id="currency_pos" class="form-select">
                            <option value="1" {{ $settings->currency_pos == 1 ? 'selected' : '' }}>
                                {{ ___('Before price') }}</option>
                            <option value="0" {{ $settings->currency_pos == 0 ? 'selected' : '' }}>
                                {{ ___('After price') }}</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="international_setting" value="1">
                <button name="submit" type="submit" class="btn btn-primary">{{ ___('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
