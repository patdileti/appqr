@extends($activeTheme.'layouts.app')
@section('title', ___('Manage Menu').' - '.$post->title)
@section('header_buttons')
    <a href="#add-image-menu-dialog" class="popup-with-zoom-anim button ripple-effect button-sliding-icon margin-left-auto">
        {{___('Add Menu')}}<i class="icon-feather-plus"></i>
    </a>
@endsection
@section('content')
    <div class="notification notice margin-bottom-30">{{___('The current restaurant theme supports images only.')}}</div>

    <div class="sortable-container" data-reorder-route="{{route('restaurants.reorderImageMenuItem', $post->id)}}">
        @foreach($post->image_menus as $menu)
            <div class="dashboard-box margin-top-0 margin-bottom-15" data-id="{{$menu->id}}">
                <div class="headline small">
                    <h3><i class="icon-feather-menu quickad-js-handle"></i>
                        <img class="menu-avatar margin-right-5" src="{{asset('storage/menu/'.$menu->image)}}"
                             alt="{{$menu->name}}"> {{$menu->name}}
                    </h3>
                    <div class="margin-left-auto line-height-1">
                        <a href="#" data-id="{{$menu->id}}" data-data="{{str($menu)->jsonSerialize()}}"
                           class="button ripple-effect btn-sm edit-image-menu-item"
                           title="{{___('Edit Menu')}}" data-tippy-placement="top"><i class="icon-feather-edit margin-left-0"></i></a>
                        <a href="{{ route('restaurants.deleteImageMenuItem', $post->id) }}" data-id="{{$menu->id}}"
                           class="button red ripple-effect btn-sm delete-item"
                           title="{{___('Delete Menu')}}"
                           data-tippy-placement="top"><i class="icon-feather-trash-2 margin-left-0"></i></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add Menu Item Popup -->
    <div id="add-image-menu-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <!--Tabs -->
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Add Image Menu')}}</a></li>
            </ul>
            <div class="popup-tabs-container">
                <form id="add-image-menu-form" method="post" action="{{route('restaurants.addImageMenuItem', $post->id)}}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="popup-tab-content">
                        <div class="submit-field">
                            <input name="name" type="text" class="with-border" placeholder="{{___('Name')}}" required>
                        </div>
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
    <div id="edit-image-menu-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <!--Tabs -->
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a>{{___('Edit Image Menu')}}</a></li>
            </ul>
            <div class="popup-tabs-container">
                <form id="edit-image-menu-form" method="post" action="{{route('restaurants.updateImageMenuItem', $post->id)}}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="popup-tab-content">
                        <div class="submit-field">
                            <input name="name" type="text" class="with-border" placeholder="{{___('Name')}}" required>
                        </div>
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
                        <div class="submit-field">
                            <label class="switch padding-left-40">
                                <input name="active" value="1" type="checkbox" checked>
                                <span class="switch-button"></span> {{___('Available')}}
                            </label>
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
    </script>
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
    <script
        src="{{ asset('assets/global/js/jquery.ui.touch-punch.min.js') }}"></script>
    <script src="{{ asset($activeThemeAssets.'js/menu.js?var='.config('appinfo.version')) }}"></script>
@endpush
