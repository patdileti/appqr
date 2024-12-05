@extends($activeTheme.'layouts.app')
@section('title', ___('Manage Menu').' - '.$post->title)
@section('header_buttons')
    @if(!empty($menu_languages))
        <div class="btn-group bootstrap-select user-lang-switcher">
            <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown"
                    title="{{$default_menu_language->name}}">
                <span class="filter-option pull-left" id="user-lang">{{ strtoupper($default_menu_language->code)}}</span>&nbsp;
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
    <a href="javascript:void(0)" class="button ripple-effect button-sliding-icon margin-left-auto add-category">
        {{___('Add Category')}}<i class="icon-feather-plus"></i>
    </a>
@endsection
@section('content')
    <div class="js-accordion sortable-container"
         data-reorder-route="{{route('restaurants.reorderCategory', $post->id)}}">
        @foreach($post->menu_categories as $category)
            <!-- Dashboard Box -->
            <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item" data-id="{{$category->id}}">

                <!-- Headline -->
                <div class="headline js-accordion-header">
                    <h3><i class="icon-feather-menu quickad-js-handle"></i> <span
                            class="category-display-name">{{$category->name}}</span></h3>
                    <div class="margin-left-auto line-height-1">
                        <a href="#"
                           data-catid="{{$category->id}}"
                           class="button ripple-effect btn-sm add-sub-category"
                           title="{{ ___('Add Sub Category') }}"
                           data-tippy-placement="top">
                            <i class="icon-feather-folder-plus margin-left-0"></i>
                        </a>
                        <a href="#" data-catid="{{$category->id}}" class="button ripple-effect btn-sm add-menu-item"
                           title="{{ ___('Add Menu Item') }}" data-tippy-placement="top"><i
                                class="icon-feather-plus margin-left-0"></i></a>
                        <a href="#" data-catid="{{$category->id}}" class="button ripple-effect btn-sm edit-category"
                           title="{{ ___('Edit Category') }}" data-tippy-placement="top"><i
                                class="icon-feather-edit margin-left-0"></i></a>
                        <a href="{{ route('restaurants.deleteCategory', $post->id) }}" data-id="{{$category->id}}"
                           class="button red ripple-effect btn-sm delete-item"
                           title="{{ ___('Delete Category') }}" data-tippy-placement="top"><i
                                class="icon-feather-trash-2 margin-left-0"></i></a>
                    </div>
                </div>

                <div class="content with-padding padding-bottom-10 js-accordion-body dark" style="display: none">
                    @if(count($category->subcategories))
                        <div class="js-accordion sortable-container"
                             data-reorder-route="{{route('restaurants.reorderCategory', $post->id)}}">
                            @foreach($category->subcategories as $subcategory)

                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item"
                                     data-id="{{$subcategory->id}}">

                                    <div class="headline js-accordion-header small">
                                        <h3><i class="icon-feather-menu quickad-js-handle"></i> <span
                                                class="category-display-name">{{$subcategory->name}}</span></h3>
                                        <div class="margin-left-auto line-height-1">
                                            <a href="#" data-catid="{{$subcategory->id}}"
                                               class="button ripple-effect btn-sm add-menu-item"
                                               title="{{ ___('Add Menu Item') }}" data-tippy-placement="top"><i
                                                    class="icon-feather-plus margin-left-0"></i></a>
                                            <a href="#"
                                               data-parent="{{$category->id}}"
                                               data-catid="{{$subcategory->id}}"
                                               class="button ripple-effect btn-sm edit-sub-category"
                                               title="{{ ___('Edit Sub Category') }}" data-tippy-placement="top"><i
                                                    class="icon-feather-edit margin-left-0"></i></a>
                                            <a href="{{ route('restaurants.deleteCategory', $post->id) }}"
                                               data-id="{{$subcategory->id}}"
                                               class="button red ripple-effect btn-sm delete-item"
                                               title="{{ ___('Delete Sub Category') }}"
                                               data-tippy-placement="top"><i
                                                    class="icon-feather-trash-2 margin-left-0"></i></a>
                                        </div>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        @include($activeTheme.'user.posts.menu.item', ['menus' => $subcategory->menus])
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @include($activeTheme.'user.posts.menu.item', ['menus' => $category->menus])
                </div>
            </div>
        @endforeach
    </div>



    <!-- Add Category Popup -->
    <div id="add-category-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <!--Tabs -->
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Add Category')}}</a></li>
            </ul>

            <div class="popup-tabs-container">
                <!-- Tab -->
                <div class="popup-tab-content">
                    <form action="{{route('restaurants.addCategory', $post->id)}}" method="post" id="add-category-form">
                        @csrf
                        <div class="submit-field">
                            <input type="text" class="with-border" name="name" placeholder="{{ ___('Name') }}" required>
                        </div>
                        <!-- Button -->
                        <button class="margin-top-0 button button-sliding-icon ripple-effect"
                                type="submit">{{___('Save')}} <i class="icon-material-outline-arrow-right-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Category Popup / End -->

    <!-- Edit Category Popup -->
    <div id="edit-category-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <!--Tabs -->
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Edit Category')}}</a></li>
            </ul>

            <div class="popup-tabs-container">
                <!-- Tab -->
                <div class="popup-tab-content">
                    <form action="{{route('restaurants.updateCategory', $post->id)}}" method="post"
                          id="edit-category-form">
                        @csrf
                        <div class="submit-field">
                            <input type="text" class="with-border" name="name" placeholder="{{ ___('Name') }}" required>
                            <input type="hidden" class="with-border" name="id">
                        </div>
                        <!-- Button -->
                        <button class="margin-top-0 button button-sliding-icon ripple-effect"
                                type="submit">{{___('Save')}} <i class="icon-material-outline-arrow-right-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Category Popup / End -->

    <!-- Add Sub Category Popup -->
    <div id="add-sub-category-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <!--Tabs -->
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Add Sub Category')}}</a></li>
            </ul>

            <div class="popup-tabs-container">
                <!-- Tab -->
                <div class="popup-tab-content">
                    <form action="{{route('restaurants.addSubCategory', $post->id)}}" method="post"
                          id="add-sub-category-form">
                        @csrf
                        <div class="submit-field">
                            <input type="text" class="with-border" name="name" placeholder="{{ ___('Name') }}" required>
                            <input type="hidden" class="with-border" name="parent">
                        </div>
                        <!-- Button -->
                        <button class="margin-top-0 button button-sliding-icon ripple-effect"
                                type="submit">{{___('Save')}} <i class="icon-material-outline-arrow-right-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Sub Category Popup / End -->

    <!-- Edit Sub Category Popup -->
    <div id="edit-sub-category-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <!--Tabs -->
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Edit Sub Category')}}</a></li>
            </ul>

            <div class="popup-tabs-container">
                <!-- Tab -->
                <div class="popup-tab-content">
                    <form action="{{route('restaurants.updateSubCategory', $post->id)}}" method="post"
                          id="edit-sub-category-form">
                        @csrf
                        <div class="submit-field margin-bottom-5">
                            <input type="text" class="with-border" name="name" placeholder="{{ ___('Name') }}" required>
                            <input type="hidden" class="with-border" name="id">
                        </div>
                        <div class="margin-bottom-5">
                            <a href="javascript:void(0)"
                               class="show-hide-submit-field"><small><span
                                        class="plus-minus">+</span> {{___('Edit Category')}}</small></a>
                            <div class="submit-field margin-top-10" style="display: none">
                                <select class="with-border selectpicker" name="parent" data-container="body">
                                    @foreach($post->menu_categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Button -->
                        <button class="margin-top-0 button button-sliding-icon ripple-effect"
                                type="submit">{{___('Save')}} <i class="icon-material-outline-arrow-right-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Sub Category Popup / End -->

    <!-- Add Menu Item Popup -->
    <div id="add-menu-item-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <!--Tabs -->
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Add Menu Item')}}</a></li>
            </ul>
            <div class="popup-tabs-container">
                <form id="add-menu-item-form" method="post" action="{{route('restaurants.addMenuItem', $post->id)}}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="popup-tab-content">
                        <div class="submit-field">
                            <input name="name" type="text" class="with-border" placeholder="{{___('Name')}}" required>
                        </div>
                        <div class="submit-field">
                            <textarea name="description" rows="2" class="with-border"
                                      placeholder="{{___('Description')}}"></textarea>
                        </div>
                        <div class="submit-field">
                            <input name="price" type="number" step="0.01" class="with-border" placeholder="{{___('Price')}}"
                                   required>
                        </div>
                        <div class="submit-field">
                            <select name="type" class="selectpicker with-border" id="menu-item-type">
                                <option value="veg">{{___('Veg')}}</option>
                                <option value="nonveg">{{___('Non Veg')}}</option>
                            </select>
                        </div>
                        @if(config('settings.admin_allergies'))
                            <div class="submit-field">
                                <h5>{{___('Allergies')}}</h5>
                                <select name="allergies[]" class="selectpicker with-border" multiple
                                        title="{{___(' Select allergies')}}" data-container="body">
                                    @foreach($allergies as $allergy)
                                        <option value="{{$allergy->id}}">
                                            {{!empty($allergy->translations->{get_lang()}->title) ? $allergy->translations->{get_lang()}->title : $allergy->title}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="submit-field">
                            <h5>{{___('Image')}}</h5>
                            <div class="input-file">
                                <img src="{{asset('storage/menu/default.png')}}" id="menu-item-image" alt="">
                            </div>
                            <div class="uploadButton margin-top-30">
                                <input class="uploadButton-input" type="file" accept="image/*"
                                       onchange="readImageURL(this,'menu-item-image')" id="image_upload"
                                       name="image"/>
                                <label class="uploadButton-button ripple-effect"
                                       for="image_upload">{{___('Upload Image')}}</label>
                            </div>
                        </div>
                        <div class="submit-field">
                            <label class="switch padding-left-40">
                                <input name="active" value="1" type="checkbox" checked>
                                <span class="switch-button"></span> {{___('Available')}}
                            </label>
                        </div>
                        <input name="category_id" type="hidden"/>
                        <!-- Button -->
                        <button class="margin-top-0 button button-sliding-icon ripple-effect"
                                type="submit">{{___('Save')}} <i class="icon-material-outline-arrow-right-alt"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Menu Item Popup / End -->

    <!-- Edit Menu Item Popup -->
    <div id="edit-menu-item-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <!--Tabs -->
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Edit Menu Item')}}</a></li>
            </ul>
            <div class="popup-tabs-container">
                <form id="edit-menu-item-form" method="post" action="{{route('restaurants.updateMenuItem', $post->id)}}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="popup-tab-content">
                        <div class="submit-field">
                            <input name="name" type="text" class="with-border" placeholder="{{___('Name')}}" required>
                        </div>
                        <div class="submit-field">
                            <textarea name="description" rows="2" class="with-border"
                                      placeholder="{{___('Description')}}"></textarea>
                        </div>
                        <div class="submit-field">
                            <input name="price" type="number" step="0.01" class="with-border" placeholder="{{___('Price')}}"
                                   required>
                        </div>
                        <div class="submit-field">
                            <select name="type" class="selectpicker with-border" id="menu-item-type">
                                <option value="veg">{{___('Veg')}}</option>
                                <option value="nonveg">{{___('Non Veg')}}</option>
                            </select>
                        </div>
                        @if(config('settings.admin_allergies'))
                            <div class="submit-field">
                                <h5>{{___('Allergies')}}</h5>
                                <select name="allergies[]" class="selectpicker with-border" multiple
                                        title="{{___(' Select allergies')}}" data-container="body">
                                    @foreach($allergies as $allergy)
                                        <option value="{{$allergy->id}}">
                                            {{!empty($allergy->translations->{get_lang()}->title) ? $allergy->translations->{get_lang()}->title : $allergy->title}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="submit-field">
                            <h5>{{___('Image')}}</h5>
                            <div class="input-file">
                                <img src="{{asset('storage/menu/default.png')}}" id="menu-item-image1" alt="">
                            </div>
                            <div class="uploadButton margin-top-30">
                                <input class="uploadButton-input" type="file" accept="image/*"
                                       onchange="readImageURL(this,'menu-item-image1')" id="image_upload"
                                       name="image"/>
                                <label class="uploadButton-button ripple-effect"
                                       for="image_upload">{{___('Upload Image')}}</label>
                            </div>
                        </div>
                        <div class="submit-field margin-bottom-5">
                            <label class="switch padding-left-40">
                                <input name="active" value="1" type="checkbox" checked>
                                <span class="switch-button"></span> {{___('Available')}}
                            </label>
                        </div>
                        <div class="margin-bottom-5">
                            <a href="javascript:void(0)"
                               class="show-hide-submit-field"><small><span
                                        class="plus-minus">+</span> {{___('Edit Category')}}</small></a>
                            <div class="submit-field margin-top-10" style="display: none">
                                <select class="with-border selectpicker" name="category_id" data-container="body">
                                    @foreach($post->menu_categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @foreach($category->subcategories as $subcategory)
                                            <option value="{{$subcategory->id}}"> -- {{$subcategory->name}}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input name="id" type="hidden"/>
                        <!-- Button -->
                        <button class="margin-top-0 button button-sliding-icon ripple-effect"
                                type="submit">{{___('Save')}} <i class="icon-material-outline-arrow-right-alt"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Menu Item Popup / End -->

@endsection

@push('scripts_vendor')
    <script>
        const LANG_ARE_YOU_SURE = @json(___('All data within this section will be deleted. Are you sure?')),
            DEFAULT_IMAGE_URL = @json(asset('storage/menu/default.png'));

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
    <script src="{{ asset($activeThemeAssets.'js/menu.js?var='.config('appinfo.version')) }}"></script>
@endpush
