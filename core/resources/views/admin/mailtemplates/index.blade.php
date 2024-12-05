@extends('admin.layouts.main')
@section('title', ___('Email Templates'))
@section('header_buttons')

@endsection
@section('content')

    <form method="post" class="ajax_submit_form" action="{{ route('admin.mailtemplates.update') }}" id="saveEmailTemplate">
        <div class="quick-card card">
            <div class="card-header d-flex align-items-center">
                <h5>{{ ___('Email Notifications') }}</h5>
                <div class="card-header-right">
                    <a href="{{ route('admin.settings.index') }}#quick_smtp"
                       class="btn btn-primary ripple-effect">
                        <i class="icon-feather-settings me-1"></i> {{ ___('Settings') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="quick-accordion" id="accordion">
                    @foreach ($email_template as $i => $template)
                        <div class="card quick-card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="mb-0 d-flex align-items-center">
                                    <button class="accordion-button" type="button" data-bs-parent="#accordion" data-bs-toggle="collapse" data-bs-target="#notification_{{ $template['id'] }}" aria-expanded="false" aria-controls="notification_{{ $template['id'] }}">
                                        {{ $i+1 }}. {{ $template['title'] }}
                                    </button>
                                </h5>
                            </div>
                            <div class="accordion-collapse collapse" id="notification_{{ $template['id'] }}"
                                 aria-labelledby="notification_{{ $template['id'] }}"
                                 data-bs-parent="#accordion">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ ___('Subject') }}</label>
                                        <input name="{{ $template['subject'] }}"
                                               class="form-control" type="text"
                                               value="{{ @$settings->{$template['subject']} }}">

                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="pageContent">{{ ___('Message') }}</label>
                                        <textarea name="{{ $template['message'] }}"
                                                  id="pageContent" rows="6"
                                                  class="form-control tiny-editor">
                                                                {{ @$settings->{$template['message']} }}
                                                            </textarea>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">{{ ___('Shortcodes') }}</label>
                                                <div class="quick-shortcode-wrapper">
                                                    @foreach ($template['shortcodes'] as $shortcode)
                                                        <div class="quick-shortcode-box">
                                                            <div class="bg-light" title="{{ $shortcode['title'] }}" data-tippy-placement="top">
                                                                {{ $shortcode['code'] }}
                                                            </div>
                                                            <button class="btn-icon" title="{{ ___('Copy') }}" data-tippy-placement="top" type="button" data-code="{{ $shortcode['code'] }}"><i class="icon-feather-copy"></i></button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer">
                <button name="email_setting" type="submit"
                        class="btn btn-primary mr-1 save-changes">{{ ___('Save') }}</button>
                <button class="btn btn-default" type="reset">{{ ___('Reset') }}</button>
            </div>
        </div>
    </form>

    @push('scripts_at_top')
        <script id="quick-sidebar-menu-js-extra">
            "use strict";
            var QuickMenu = {"page": "email-template"};
        </script>
    @endpush
    @push('scripts_vendor')
        <script src="{{ asset('assets/admin/plugins/tinymce/tinymce.min.js') }}"></script>
    @endpush
@endsection
