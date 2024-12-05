@extends($activeTheme.'layouts.app')
@section('title', ___('QR Code Generator'))
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="dashboard-box">
                <div class="headline">
                    <h3><i class="icon-feather-settings"></i> {{ ___('QR Code Generator') }}</h3>
                </div>
                <div class="content with-padding">
                    <form method="post" action="{{ route('restaurants.qrbuilder', $post->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex align-items-center submit-field">
                            <div class="flex-grow-1">
                                <h5 class="margin-bottom-0">{{ ___('Foreground Color') }}</h5></div>
                            <div>
                                <div class="qr-fg-color-wrapper">
                                    <button class="bm-color-picker"></button>
                                    <input type="hidden" class="color-input" name="qr_fg_color" value="{{ $post_options->qr_fg_color ?? '#000000' }}">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center submit-field">
                            <div class="flex-grow-1">
                                <h5 class="margin-bottom-0">{{ ___('Background Color') }}</h5></div>
                            <div>
                                <div class="qr-bg-color-wrapper">
                                    <button class="bm-color-picker"></button>
                                    <input type="hidden" class="color-input" name="qr_bg_color" value="{{ $post_options->qr_bg_color ?? '#ffffff' }}">
                                </div>
                            </div>
                        </div>
                        <div class="submit-field">
                            <h5>{{ ___('Padding') }}</h5>
                            <input class="range-slider-single" id="qr-padding" name="qr_padding" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="1" data-slider-value="{{ $post_options->qr_padding ?? 2 }}" value="{{ $post_options->qr_padding ?? 2 }}">
                        </div>
                        <div class="submit-field">
                            <h5>{{ ___('Corner Radius') }}</h5>
                            <input class="range-slider-single" id="qr-radius" name="qr_radius" type="text" data-slider-min="0" data-slider-max="50" data-slider-step="10" data-slider-value="{{ $post_options->qr_radius ?? 50 }}" value="{{ $post_options->qr_radius ?? 50 }}">
                        </div>
                        <div class="submit-field">
                            <h5>{{ ___('Mode') }}</h5>
                            @php $qr_mode = $post_options->qr_mode ?? 2 @endphp
                            <select id="qr-mode" name="qr_mode" class="with-border selectpicker">
                                <option value="0" @if($qr_mode=="0") selected @endif>{{ ___('Normal') }}</option>
                                <option value="2" @if($qr_mode=="2") selected @endif>{{ ___('Text') }}</option>
                                <option value="4" @if($qr_mode=="4") selected @endif>{{ ___('Image') }}</option>
                            </select>
                        </div>
                        <div id="qr-mode-customization">
                            <div id="qr-mode-label">
                                <div class="submit-field">
                                    <h5>{{ ___('Text') }}</h5>
                                    <input id="qr-text" class="with-border" name="qr_text" type="text" value="{{ $post_options->qr_text ?? $post->title }}">
                                </div>
                                <div class="d-flex align-items-center submit-field">
                                    <div class="flex-grow-1">
                                        <h5 class="margin-bottom-0">{{ ___('Text Color') }}</h5></div>
                                    <div>
                                        <div class="qr-text-color-wrapper">
                                            <button class="bm-color-picker"></button>
                                            <input type="hidden" class="color-input" name="qr_text_color"
                                                   value="{{ $post_options->qr_text_color ?? $post->color }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="qr-mode-image">
                                <div class="submit-field">
                                    <h5>{{ ___('Image') }}</h5>
                                    <div class="uploadButton">
                                        <input class="uploadButton-input" type="file" accept="image/*" id="qr-image"
                                               name="qr_image"/>
                                        @php
                                            $qr_image = asset('storage/logo/'.$settings->site_logo);
                                            if(!empty($post_options->qr_image)){
                                                $qr_image = asset('storage/restaurant/logo/'.$post_options->qr_image);
                                            }
                                        @endphp
                                        <img id="img-buffer" src="{{ $qr_image }}" class="d-none">
                                        <label class="uploadButton-button ripple-effect" for="qr-image">{{ ___('Upload Image') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="submit-field">
                                <h5>{{ ___('Size') }}</h5>
                                <input class="range-slider-single" name="qr_mode_size" id="qr-mode-size" type="text" data-slider-min="1"
                                       data-slider-max="15" data-slider-step="1" data-slider-value="{{ $post_options->qr_mode_size ?? 10 }}" value="{{ $post_options->qr_mode_size ?? 10 }}">
                            </div>
                            <div class="submit-field">
                                <h5>{{ ___('Position X') }}</h5>
                                <input class="range-slider-single" name="qr_position_x" id="qr-position-x" type="text"
                                       data-slider-min="0" data-slider-max="100" data-slider-step="1"
                                       data-slider-value="{{ $post_options->qr_position_x ?? 50 }}" value="{{ $post_options->qr_position_x ?? 50 }}">
                            </div>
                            <div class="submit-field">
                                <h5>{{ ___('Position Y') }}</h5>
                                <input class="range-slider-single" name="qr_position_y" id="qr-position-y" type="text"
                                       data-slider-min="0" data-slider-max="100" data-slider-step="1"
                                       data-slider-value="{{ $post_options->qr_position_y ?? 50 }}" value="{{ $post_options->qr_position_y ?? 50 }}">
                            </div>
                        </div>
                        <button name="submit" type="submit" class="button">{{ ___('Save Settings') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4 sticky-sidebar">
            <div class="dashboard-box">
                <div class="headline">
                    <h3><i class="icon-feather-grid"></i> {{ ___('QR Code') }}</h3>
                </div>
                <div class="content with-padding">
                    <div id="qr-code-wrapper" class="margin-bottom-20" data-url="{{ route('publicView', $post->slug). '?qr-id=' . urlencode(quick_xor_encrypt($post->slug, 'quick-qr')) }}"></div>
                    <button class="button ripple-effect qr-code-downloader"><i class="icon-feather-download"></i> {{ ___('Download Image') }}</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-box">
                <div class="headline">
                    <h3><i class="icon-feather-link"></i> {{ ___('QR Templates') }}</h3>
                </div>
                <div class="content with-padding">
                    <div class="single-carousel margin-bottom-20">
                        <div><img src="{{asset('storage/qr-templates/template-1.png')}}" alt=""></div>
                        <div><img src="{{asset('storage/qr-templates/template-1.png')}}" alt=""></div>
                        <div><img src="{{asset('storage/qr-templates/template-1.png')}}" alt=""></div>
                    </div>
                    <a href="{{asset('storage/qr-templates/qr-templates.zip')}}" class="button ripple-effect" download=""><i class="icon-feather-download"></i> {{ ___('Download Templates') }}</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts_vendor')
        <script>
            var LANG_COPIED = @json(___('Copied successfully'))
        </script>
        <link rel="stylesheet" href="{{ asset($activeThemeAssets.'css/color-picker.min.css') }}">
        <script src="{{ asset($activeThemeAssets.'js/color-picker.es5.min.js') }}"></script>
        <script src="{{ asset($activeThemeAssets.'js/sticky-sidebar.js') }}"></script>
        <script src="{{ asset($activeThemeAssets.'js/jquery-qrcode.min.js') }}"></script>
        <script src="{{ asset($activeThemeAssets.'js/script.js?ver='.config('appinfo.version')) }}"></script>
    @endpush
@endsection
