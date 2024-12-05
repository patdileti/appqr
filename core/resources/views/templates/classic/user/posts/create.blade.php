@extends($activeTheme.'layouts.app')
@section('title', ___('Add New Restaurant'))
@section('content')

    <form method="post" action="{{ route('restaurants.store') }}" enctype="multipart/form-data">
        @csrf
        <!-- Dashboard Box -->
        <div class="dashboard-box margin-top-0">
            <!-- Headline -->
            <div class="headline">
                <h3><i class="icon-feather-folder-plus"></i>{{ ___('Add Restaurant') }}</h3>
            </div>
            <div class="content with-padding padding-bottom-10">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="submit-field">
                            <h5>{{ ___('Name') }}</h5>
                            <div class="d-flex align-items-baseline">
                                <div class="qr-restaurant-color-wrapper padding-right-15">
                                    <button class="bm-color-picker"></button>
                                    <input type="hidden" class="color-input" name="color"
                                           value="{{ old('color') ?? $settings->theme_color }}">
                                </div>
                                <input type="text" class="with-border" name="title" value="{{ old('title') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Slug') }}</h5>
                            <input type="text" id="store-slug" class="with-border" name="slug"
                                   value="{{ old('slug') }}">
                            <div id="slug-availability-status"></div>
                            <small>{{ ___('Use only alphanumeric value without space. (Hyphen(-) allow). Slug will be used for restaurant url.') }}</small>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Subtitle') }}</h5>
                            <input type="text" class="with-border" name="sub_title" value="{{ old('sub_title') }}">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Timing') }}</h5>
                            <input type="text" class="with-border" name="timing" value="{{ old('timing') }}">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Phone') }}</h5>
                            <input type="text" class="with-border" name="phone" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="submit-field">
                            <h5>{{ ___('Description') }}</h5>
                            <textarea class="with-border tiny-editor"
                                      name="description">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="submit-field">
                            <h5>{{ ___('Address') }}</h5>
                            <input class="with-border" type="text" name="address" value="{{ old('address') }}" required>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Logo') }}</h5>
                            <div class="input-file">
                                <img src="{{asset('storage/restaurant/logo/default.png')}}" id="restro_image">
                            </div>

                            <div class="uploadButton margin-top-30">
                                <input class="uploadButton-input" type="file" accept="image/*"
                                       onchange="readImageURL(this,'restro_image')" id="image_upload"
                                       name="main_image" required/>
                                <label class="uploadButton-button ripple-effect"
                                       for="image_upload">{{ ___('Upload image') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Cover Image') }}</h5>
                            <div class="input-file">
                                <img src="{{asset('storage/restaurant/cover/default.png')}}" id="restro_cover_image">
                            </div>
                            <div class="uploadButton margin-top-30">
                                <input class="uploadButton-input" type="file" accept="image/*"
                                       onchange="readImageURL(this,'restro_cover_image')" id="cover_upload"
                                       name="cover_image" required/>
                                <label class="uploadButton-button ripple-effect"
                                       for="cover_upload">{{ ___('Upload image') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="submit-field">
                            <h5>{{ ___('Restaurant Template') }}</h5>
                            <div class="account-type row template-chooser">
                                @foreach($templates as $template)
                                    <div class="col-md-3 margin-right-0">
                                        <input type="radio" name="restaurant_template"
                                               value="{{ $template['folder'] }}" id="{{ $template['folder'] }}"
                                               class="account-type-radio" @checked($loop->first)>
                                        <label for="{{ $template['folder'] }}" class="ripple-effect-dark">
                                            <img class="margin-bottom-5" src="{{ $template['image'] }}">
                                            <strong>{{ $template['name'] }}
                                                @if("flipbook" == $template['folder'])
                                                    <i class="icon-feather-image" data-tippy-placement="top"
                                                       title="{{ ___('This template supports image only.') }}"></i>
                                                @endif</strong>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <button type="submit" name="submit" class="button ripple-effect margin-top-30">{{ ___('Save') }}</button>

    </form>

@endsection

@push('scripts_vendor')
    <link rel="stylesheet"
          href="{{ asset($activeThemeAssets.'css/color-picker.min.css') }}">
    <script
        src="{{ asset($activeThemeAssets.'js/color-picker.es5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/tinymce/tinymce.min.js') }}"></script>
@endpush

@push('scripts_at_bottom')
    <script>
        initColorPicker('.qr-restaurant-color-wrapper');

        function initColorPicker(container) {
            var $element = container + ' .bm-color-picker';
            var $input = jQuery($element).siblings('.color-input');
            var picker = Pickr.create({
                container: container,
                el: $element,
                theme: 'monolith',
                comparison: false,
                closeOnScroll: true,
                position: 'bottom-start',
                default: $input.val() || '#333333',
                components: {
                    preview: false,
                    opacity: false,
                    hue: true,
                    interaction: {
                        input: true
                    }
                }
            });
            picker.on('change', function (color, instance) {
                $input.val(color.toHEXA().toString()).trigger('change');
            });
        }

        @if(config('settings.restaurant_text_editor'))
        tinymce.init({
            selector: '.tiny-editor',
            height: 250,
            resize: true,
            plugins: 'quickbars image advlist lists autolink link wordcount help searchreplace media',
            toolbar: [
                "bold italic underline strikethrough | alignleft aligncenter alignright  | link image media | bullist numlist | removeformat"
            ],
            menubar: "",
            // link
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            link_assume_external_targets: true,
            // images
            image_advtab: true,
            extended_valid_elements: 'i[*]',
            content_style: 'body { font-size:16px }',
            smart_paste: false,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            },
            @if(current_language()->direction == 'rtl')
            directionality: "rtl"
            @endif
        });
        @endif
    </script>
@endpush
