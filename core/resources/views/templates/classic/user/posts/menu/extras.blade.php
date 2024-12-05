@extends($activeTheme.'layouts.app')
@section('title', $menu->name.' - '.$post->title)
@section('header_buttons')
    <a href="{{route('restaurants.menu', $post->id)}}" class="button ripple-effect margin-left-auto margin-right-5">
        <i class="far fa-arrow-left margin-left-0 margin-right-5"></i> {{___('Back')}}
    </a>
    @if(!empty($menu_languages))
        <div class="btn-group bootstrap-select user-lang-switcher margin-right-0">
            <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown"
                    title="{{$default_menu_language->name}}">
                <span class="filter-option pull-left"
                      id="user-lang">{{ strtoupper($default_menu_language->code)}}</span>&nbsp;
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu scrollable-menu open">
                <ul class="dropdown-menu inner">
                    @foreach($menu_languages as $lang)
                        <li data-code="{{$lang->code}}">
                            <a role="menuitem" tabindex="-1" rel="alternate"
                               href="#">{{$lang->name}}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection
@section('content')

    <div class="js-accordion">

        <!-- Variants Options -->
        <div class="dashboard-box js-accordion-item margin-top-0 margin-bottom-30">
            <div class="headline js-accordion-header">
                <h3>{{___('Variant Options')}}</h3>
                <div class="margin-left-auto line-height-1">
                    <a href="#add-variant-option"
                       class="popup-with-zoom-anim btn-sm button ripple-effect add-variant-option"
                       data-tippy-placement="top" title="{{___('Add Variant Option')}}">
                        <i class="icon-feather-plus color-white margin-left-0"></i>
                    </a>
                </div>
            </div>
            <div class="content with-padding padding-bottom-10 js-accordion-body dark" style="display: none">
                <div class="js-accordion sortable-container"
                     data-reorder-route="{{route('restaurants.menuReorderVariantOption', ['restaurant' => $post->id, 'menu' => $menu->id])}}">
                    @forelse($menu->variantOptions as $option)
                        <div class="dashboard-box js-accordion-item margin-top-0 margin-bottom-30"
                             data-id="{{$option->id}}">
                            <!-- Headline -->
                            <div class="headline js-accordion-header small">
                                <h3><i class="icon-feather-menu quickad-js-handle"></i> <span
                                        class="extra-title">{{$option->title}}</span></h3>
                                <div class="margin-left-auto line-height-1">
                                    <a href="{{route('restaurants.menuDeleteVariantOption', ['restaurant' => $post->id, 'menu' => $menu->id])}}"
                                       data-id="{{$option->id}}"
                                       class="button red ripple-effect btn-sm delete-item"
                                       title="{{___('Delete')}}"
                                       data-tippy-placement="top"><i class="icon-feather-trash-2 margin-left-0"></i></a>
                                </div>
                            </div>
                            <div class="content with-padding padding-bottom-10 js-accordion-body" style="display: none">
                                <form class="extra-ajax-form-submit" method="post"
                                      action="{{route('restaurants.menuUpdateVariantOption', ['restaurant' => $post->id, 'menu' => $menu->id])}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="submit-field">
                                                <h5>{{___('Title')}}</h5>
                                                <input type="text" class="with-border option-title"
                                                       name="title" value="{{$option->title}}"
                                                       placeholder="{{___('Title (Ex. Size)')}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="submit-field">
                                                <h5>{{___('Options')}}</h5>
                                                <input type="text" class="with-border" name="options"
                                                       value="{{implode(', ', $option->options)}}"
                                                       placeholder="{{___('Options (Ex. Small, Medium, Large)')}}"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="submit-field">
                                                <label class="switch padding-left-40">
                                                    <input name="active" value="1"
                                                           type="checkbox"
                                                        @checked($option->active)>
                                                    <span class="switch-button"></span> {{___('Available')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="submit-field">
                                        <input type="hidden" name="id" value="{{$option->id}}">
                                        <button type="submit" name="submit"
                                                class="button ripple-effect">{{___('Save')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p>{{ ___('No items available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Variants -->
        <div class="dashboard-box js-accordion-item margin-top-0 margin-bottom-30">
            <div class="headline js-accordion-header">
                <h3>{{___('Variants')}}</h3>
                <div class="margin-left-auto line-height-1">
                    <a href="#add-variant"
                       class="popup-with-zoom-anim btn-sm button ripple-effect add-variant"
                       data-tippy-placement="top" title="{{___('Add Variant')}}">
                        <i class="icon-feather-plus color-white margin-left-0"></i>
                    </a>
                </div>
            </div>
            <div class="content with-padding padding-bottom-10 js-accordion-body dark" style="display: none">
                <div class="js-accordion sortable-container"
                     data-reorder-route="{{route('restaurants.menuReorderVariant', ['restaurant' => $post->id, 'menu' => $menu->id])}}">
                    @forelse($variants as $variant)
                        <div class="dashboard-box js-accordion-item margin-top-0 margin-bottom-30"
                             data-id="{{$variant->id}}">
                            <!-- Headline -->
                            <div class="headline js-accordion-header small">
                                <h3><i class="icon-feather-menu quickad-js-handle"></i> <span
                                        class="extra-title">{{$variant->title}}</span></h3>
                                <div class="margin-left-auto line-height-1">
                                    <a href="{{route('restaurants.menuDeleteVariant', ['restaurant' => $post->id, 'menu' => $menu->id])}}"
                                       data-id="{{$variant->id}}"
                                       class="button red ripple-effect btn-sm delete-item"
                                       title="{{ ___('Delete') }}"
                                       data-tippy-placement="top"><i class="icon-feather-trash-2 margin-left-0"></i></a>
                                </div>
                            </div>
                            <div class="content with-padding padding-bottom-10 js-accordion-body" style="display: none">
                                <form class="extra-ajax-form-submit" method="post"
                                      action="{{route('restaurants.menuUpdateVariant', ['restaurant' => $post->id, 'menu' => $menu->id])}}">
                                    @csrf
                                    <div class="row">
                                        @foreach($menu->variantOptions as $option)
                                            <div class="col-md-6">
                                                <div class="submit-field">
                                                    <h5>{{$option->title}}</h5>
                                                    <select class="with-border selectpicker"
                                                            name="options[{{$option->id}}]">
                                                        @foreach($option->options as $key => $value)
                                                            <option
                                                                value="{{$key}}"
                                                                @selected(
    isset($variant->options->{$option->id}) && $variant->options->{$option->id} ==  $key
    )>
                                                                {{$value}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-md-6">
                                            <div class="submit-field">
                                                <h5>{{___('Price')}}</h5>
                                                <input type="number" class="with-border"
                                                       name="price" step="0.01"
                                                       value="{{$variant->price}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="submit-field">
                                                <label class="switch padding-left-40">
                                                    <input name="active" value="1"
                                                           type="checkbox" @checked($variant->active)>
                                                    <span class="switch-button"></span> {{___('Available')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="submit-field">
                                        <input type="hidden" name="id" value="{{$variant->id}}">
                                        <button type="submit" name="submit"
                                                class="button ripple-effect">{{___('Save')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p>{{ ___('No items available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Extras -->
        <div class="dashboard-box js-accordion-item margin-top-0 margin-bottom-30">
            <!-- Headline -->
            <div class="headline js-accordion-header">
                <h3>{{___('Extras')}}</h3>
                <div class="margin-left-auto line-height-1">
                    <a href="#add-extras"
                       class="popup-with-zoom-anim btn-sm button ripple-effect add-menu-extras"
                       data-tippy-placement="top" title="{{___('Add Extras')}}">
                        <i class="icon-feather-plus color-white margin-left-0"></i>
                    </a>
                </div>
            </div>
            <div class="content with-padding padding-bottom-10 js-accordion-body dark" style="display: none">
                <div class="js-accordion sortable-container"
                     data-reorder-route="{{route('restaurants.menuReorderExtra', ['restaurant' => $post->id, 'menu' => $menu->id])}}">
                    @forelse($menu->extras as $extra)
                        <div class="dashboard-box js-accordion-item margin-top-0 margin-bottom-30"
                             data-id="{{$extra->id}}">
                            <!-- Headline -->
                            <div class="headline js-accordion-header small">
                                <h3><i class="icon-feather-menu quickad-js-handle"></i> <span
                                        class="extra-title">{{$extra->title}}</span></h3>
                                <div class="margin-left-auto line-height-1">
                                    <a href="{{route('restaurants.menuDeleteExtra', ['restaurant' => $post->id, 'menu' => $menu->id])}}"
                                       data-id="{{$extra->id}}"
                                       class="button red ripple-effect btn-sm delete-item"
                                       title="{{ ___('Delete') }}"
                                       data-tippy-placement="top"><i class="icon-feather-trash-2 margin-left-0"></i></a>
                                </div>
                            </div>
                            <div class="content with-padding padding-bottom-10 js-accordion-body" style="display: none">
                                <form class="extra-ajax-form-submit" method="post"
                                      action="{{route('restaurants.menuUpdateExtra', ['restaurant' => $post->id, 'menu' => $menu->id])}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="submit-field">
                                                <h5>{{___('Title')}}</h5>
                                                <input type="text" class="with-border"
                                                       name="title" value="{{$extra->title}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="submit-field">
                                                <h5>{{___('Price')}}</h5>
                                                <input type="number" step="0.01" class="with-border"
                                                       name="price" value="{{$extra->price}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="submit-field">
                                                <label class="switch padding-left-40">
                                                    <input name="active" value="1"
                                                           type="checkbox" @checked($extra->active)>
                                                    <span class="switch-button"></span> {{___('Available')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="submit-field">
                                        <input type="hidden" name="id" value="{{$extra->id}}">
                                        <button type="submit" name="submit"
                                                class="button ripple-effect">{{___('Save')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p>{{ ___('No items available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>


    <!-- Add Variant Option -->
    <div id="add-variant-option" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Add Variant Option')}}</a></li>
            </ul>
            <div class="popup-tabs-container">
                <form
                    action="{{route('restaurants.menuAddVariantOption', ['restaurant' => $post->id, 'menu' => $menu->id])}}"
                    method="post">
                    @csrf
                    <!-- Tab -->
                    <div class="popup-tab-content">
                        <div class="submit-field margin-bottom-0">
                            <input type="text" class="with-border" name="title"
                                   placeholder="{{___('Title (Ex. Size)')}}" required>
                        </div>
                        <div class="submit-field">
                            <input type="text" class="with-border margin-bottom-0" name="options"
                                   placeholder="{{___('Options (Ex. Small, Medium, Large)')}}" required>
                            <small>{{___('Enter options separated by comma.')}}</small>
                        </div>
                        <!-- Button -->
                        <button class="margin-top-0 button button-sliding-icon ripple-effect" type="submit"
                                id="save-variant-option">{{___('Save')}} <i
                                class="icon-material-outline-arrow-right-alt"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Variant Option / End -->

    <!-- Add Variant -->
    <div id="add-variant" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Add Variant')}}</a></li>
            </ul>
            <div class="popup-tabs-container">
                <!-- Tab -->
                <div class="popup-tab-content">
                    @if(count($menu->variantOptions))
                        <form method="post"
                              action="{{route('restaurants.menuAddVariant', ['restaurant' => $post->id, 'menu' => $menu->id])}}">
                            @csrf
                            @foreach($menu->variantOptions as $option)
                                <div class="submit-field">
                                    <h5 class="margin-bottom-8">{{$option->title}}</h5>
                                    <select class="with-border selectpicker"
                                            name="options[{{$option->id}}]">
                                        @foreach($option->options as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                            <div class="submit-field">
                                <h5 class="margin-bottom-8">{{___('Price')}}</h5>
                                <input type="number" step="0.01" class="with-border" name="price" required>
                            </div>
                            <!-- Button -->
                            <button class="margin-top-0 button button-sliding-icon ripple-effect" type="submit">
                                {{___('Save')}} <i class="icon-material-outline-arrow-right-alt"></i></button>
                        </form>
                    @else
                        <div id="variant-error"
                             class="notification error">{{ ___('Please create a variant option first.') }}</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <!-- Add Variant / End -->

    <!-- Add Extra -->
    <div id="add-extras" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Add Extra')}}</a></li>
            </ul>
            <div class="popup-tabs-container">
                <!-- Tab -->
                <div class="popup-tab-content">
                    <form
                        action="{{route('restaurants.menuAddExtra', ['restaurant' => $post->id, 'menu' => $menu->id])}}"
                        method="post">
                        @csrf
                        <div class="submit-field margin-bottom-0">
                            <input type="text" class="with-border" name="title"
                                   placeholder="{{___('Title')}}" required>
                        </div>
                        <div class="submit-field">
                            <input type="number" step="0.01" class="with-border" name="price"
                                   placeholder="{{___('Price')}}" required>
                        </div>
                        <!-- Button -->
                        <button class="margin-top-0 button button-sliding-icon ripple-effect" type="submit"
                                id="save-menu-extras">{{___('Save')}} <i
                                class="icon-material-outline-arrow-right-alt"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Extra / End -->

@endsection

@push('scripts_vendor')
    <script>
        const LANG_ARE_YOU_SURE = @json(___('All data within this section will be deleted. Are you sure?'));
        $('.user-lang-switcher').on('click', '.dropdown-menu li', function (e) {
            e.preventDefault();
            var code = $(this).data('code');
            if (code != null) {
                $('#user-lang').html(code.toUpperCase());
                $.cookie('Quick_user_lang_code', code, {path: '/'});
                location.reload();
            }
        });
    </script>
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
    <script
        src="{{ asset('assets/global/js/jquery.ui.touch-punch.min.js') }}"></script>
    <script src="{{ asset($activeThemeAssets.'js/menu-edit.js?var='.config('appinfo.version')) }}"></script>
@endpush
