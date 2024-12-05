@extends('post_templates.layout')

@push('style_at_top')
    <link rel="stylesheet" href="{{ asset('assets/global/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet"
          href="{{ asset('assets/post_templates/'.$theme.'/css/style.css?ver='.config('appinfo.version')) }}">
    <link rel="stylesheet" href="{{ asset('assets/post_templates/'.$theme.'/lib/lightbox/lightgallery.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/post_templates/'.$theme.'/lib/node-waves/waves.min.css') }}">
@endpush

@section('content')
    <!-- Preloading -->
    <div class="preloading">
        <div class="wrap-preload">
            <div class="cssload-loader"></div>
        </div>
    </div>
    <!-- .Preloading -->
    <!-- Sidebar left -->
    <nav id="sidebarleft" class="sidenav">
        <div class="sidebar-header">
            <img src="{{ asset('storage/restaurant/cover/'.$post->cover_image) }}" alt="{{$post->title}}">
        </div>
        <div class="heading">
            <div class="title col-secondary font-weight-normal">{{ ___('All Categories') }}</div>
        </div>
        <ul class="list-unstyled components">
            @foreach($post->menu_categories as $key => $category)
                {{-- Break loop if category limit exceeded --}}
                @if($plan->settings->category_limit != "999"
                        && $key >= $plan->settings->category_limit)
                    @break
                @endif
                <li>
                    <a href="#" data-catid="{{$category->id}}" class="menu-category"><i
                            class="icon-material-outline-restaurant"></i> {{$category->name}}</a>
                </li>
            @endforeach
        </ul>
    </nav>
    <!-- .Sidebar left -->

    <!-- Header  -->
    <nav class="navbar navbar-expand-lg navbar-light bg-header">
        <div class="container-fluid flex-nowrap">
            <button type="button" id="sidebarleftbutton" class="btn mr-4">
                <i class="icon-feather-menu"></i>
            </button>
            @if(@$postOptions->allow_call_waiter)
                <button type="button" class="btn btn-default ml-auto mr-1" id="call-the-waiter-btn"
                        title="{{ ___('Call the Waiter') }}">
                    <i class="fa fa-bell"></i>
                    <span class="d-none d-sm-inline">{{ ___('Call the Waiter') }}</span>
                </button>
            @endif
            @if(!empty($menu_languages))
                <div class="btn-group user-lang-switcher">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" title="{{$default_menu_language->name}}">
                        <span class="filter-option">{{ strtoupper($default_menu_language->code)}}</span>
                    </button>
                    <div class="dropdown-menu">
                        @foreach($menu_languages as $lang)
                            <a href="#" class="dropdown-item" data-code="{{$lang->code}}">
                                {{$lang->name}}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </nav>
    <!-- .Header  -->
    <!-- Content  -->
    <div id="content">
        <!-- Content Wrap  -->
        <div class="content-wrap">
            <div class="single-page-header detail-header"
                 data-background-image="{{ asset('storage/restaurant/cover/'.$post->cover_image) }}">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="single-page-header-inner">
                                <div class="left-side">
                                    <div class="header-image">
                                        <img src="{{ asset('storage/restaurant/logo/'.$post->main_image) }}"
                                             alt="{{$post->title}}">
                                    </div>
                                    <div class="header-details">
                                        <h3>{{$post->title}}<span>{{$post->sub_title}}</span></h3>
                                        <ul>
                                            @if(!empty($post->timing))
                                                <li>
                                                    <i class="icon-feather-watch margin-right-5"></i> {{$post->timing}}
                                                </li>
                                            @endif
                                            <li>
                                                <i class="icon-feather-map margin-right-5"></i>
                                                <a target="_blank"
                                                   href="https://www.google.com/maps/search/?api=1&amp;query={{$post->address}}">{{$post->address}}</a>
                                            </li>
                                            @if(!empty($post->phone))
                                                <li><i class="icon-feather-phone margin-right-5"></i> <a
                                                        href="tel:{{$post->phone}}">{{$post->phone}}</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach($post->menu_categories as $key => $category)
                {{-- Break loop if category limit exceeded --}}
                @if($plan->settings->category_limit != "999"
                        && $key >= $plan->settings->category_limit)
                    @break
                @endif

                @if(count($category->subcategories))
                    <div id="accordion{{$category->id}}" class="accordion menu-category-item menu-category-{{$category->id}}">
                        @foreach($category->subcategories as $subcategory)
                            <div class="card">
                                <div class="card-header collapsed waves-effect" data-toggle="collapse" href="#collapse{{$subcategory->id}}">
                                    <a class="card-title">{{$subcategory->name}}</a>
                                </div>
                                <div id="collapse{{$subcategory->id}}" class="card-body collapse" data-parent="#accordion{{$category->id}}">
                                    @include('post_templates.'.$theme.'.includes.menu-item', ['menus' => $subcategory->menus->where('active', '1')])
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="card-body menu-category-item menu-category-{{$category->id}}">
                    @include('post_templates.'.$theme.'.includes.menu-item', ['menus' => $category->menus->where('active', '1')])
                </div>
            @endforeach
        </div>
    </div>
    <!-- .Content  -->
    {{-- Footer --}}
    <div class="bg-dark">
        <p class="text-center text-white py-2 m-0">&copy; {{ date('Y')  }} {{$post->title}}
            @if(!@$plan->settings->hide_branding)
                &nbsp; | &nbsp; <a class="text-white" href="{{ route('home') }}"
                                   target="_blank">{{ ___('Provided by QuickQR') }}</a>
            @endif</p>
    </div>

    <!-- Bottom Panel  -->
    <div class="footer none" id="view-order-wrapper">
        <div class="clearfix"></div>
        <div class="order-footer">
            <div class="view-order">
                <div class="">
                    <div class="item"><span id="view-order-quantity">1</span> {{ ___('Item(s)') }}</div>
                    <span class="price"><span id="view-order-price"></span></span>
                </div>
                <button class="order-btn" id="viewOrderBtn">{{ ___('View Order') }} <i
                        class="icon-material-outline-keyboard-arrow-right"></i></button>
            </div>
        </div>
    </div>
    <!-- Bottom Panel  -->

    <!-- Customized Menu -->
    <div id="viewOrder" class="sidenav bottom">
        <div class="sidebar-header bg-white">
            <div class="navbar-heading">
                <h4>{{ ___('My Order') }}</h4>
            </div>
            <button type="button" id="dismiss" class="btn ml-auto">
                <i class="icon-feather-x"></i>
            </button>
        </div>
        <div class="your-order-content">
            <form type="post" action="{{route('restaurant.sendOrder', $post->id)}}" id="send-order-form">
                @csrf
                <div class="sidebar-wrapper">
                    <div class="section">
                        <div class="your-order-items"></div>
                    </div>
                    <div class="section3">
                        <div class="total-price">
                            <div class="grand-total">
                                <span>{{ ___('Grand Total') }}</span><span class="float-right"><span
                                        class="your-order-price"></span></span>
                            </div>
                        </div>
                    </div>
                    @if($allow_order)
                        <div class="section">
                            <div class="col-text font-medium my-2">{{ ___('Ordering type') }}</div>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="ordering-type" id="ordering-type" class="form-control" required>
                                        @if($allow_on_table)
                                            <option value="on-table">{{ ___('On table') }}</option>
                                        @endif
                                        @if($allow_takeaway)
                                            <option value="takeaway">{{ ___('Takeaway') }}</option>
                                        @endif
                                        @if($allow_delivery)
                                            <option value="delivery">
                                                {{ ___('Delivery') }}
                                                @if(@$postOptions->restaurant_delivery_charge)
                                                    (+ {{ price_symbol_format($postOptions->restaurant_delivery_charge) }}
                                                    )
                                                @endif
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if($allow_payment)
                            <div class="section py-0">
                                <div class="col-text font-medium my-2">{{ ___('Pay Via') }}</div>
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="pay_via" id="pay_via" class="form-control" required>
                                            <option value="pay_on_counter">{{ ___('Pay On Counter') }}</option>
                                            <option value="pay_online">{{ ___('Pay Online') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @else
                            <input name="pay_via" id="pay_via" type="hidden" value="pay_on_counter">
                        @endif
                        <div class="section">
                            <div class="col-text font-medium my-2">{{ ___('Ordering For') }}</div>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="name" class="form-control" placeholder="{{ ___('Your Name') }}" required>
                                </div>
                            </div>
                            <div class="form-group" id="table-number-field">
                                <div class="form-line">
                                    <input type="number" name="table" class="form-control"
                                           placeholder="{{ ___('Table Number') }}">
                                </div>
                            </div>
                            <div class="form-group" id="phone-number-field">
                                <div class="form-line">
                                    <input type="number" name="phone-number" class="form-control"
                                           placeholder="{{ ___('Phone Number') }}">
                                </div>
                            </div>
                            <div class="form-group" id="address-field">
                                <div class="form-line">
                            <textarea class="form-control" name="address" placeholder="{{ ___('Address') }}"
                                      rows="1"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-line">
                            <textarea class="form-control" name="message" placeholder="{{ ___('Message') }}"
                                      rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @if($allow_order)
                <!-- Bottom Panel  -->
                <div class="footer footer-extras">
                    <div class="clearfix"></div>
                    <div class="section">
                        <small class="form-error"></small>
                        <button type="submit" class="btn btn-primary btn-block"
                                id="submit-order-button">{{ ___('Send Order') }}
                        </button>
                    </div>
                </div>
                <!-- Bottom Panel  -->
                @endif
            </form>
        </div>
        <div class="order-success-message none">
            <i class="icon-feather-check qr-success-icon"></i>
            <h4>{{ ___('Sent Successfully') }}</h4>
        </div>
    </div>
    <!--Customized Menu-->

    <!-- Customized Menu -->
    <div id="menuCustomize" class="sidenav bottom">
        <div class="sidebar-header">
            <div class="navbar-heading">
                <h4></h4>
            </div>
            <button type="button" id="dismiss" class="btn ml-auto">
                <i class="icon-feather-x"></i>
            </button>
        </div>
        <div class="sidebar-wrapper">
            <div class="section">
                <p class="mb-0 customize-item-description"></p>
            </div>
            <div class="line-separate mt-0"></div>
            <div id="menu-variants">
                <div>
                    <div class="section">
                        <div class="extras-heading">
                            <div class="title"></div>
                        </div>
                        <div class="menu-variant-option">
                            <div class="extras menu-extra-item">
                                <label for="checkbox0" class="extra-item-title mb-0"></label>
                                <div class="d-flex align-items-center">
                                    <div class="custom-control custom-radio mr-sm-2">
                                        <input type="radio" name="variant" class="custom-control-input" id="checkbox0">
                                        <label class="custom-control-label" for="checkbox0"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="extras menu-extra-item">
                                <label for="checkbox1" class="extra-item-title mb-0"></label>
                                <div class="d-flex align-items-center">
                                    <div class="custom-control custom-radio mr-sm-2">
                                        <input type="radio" name="variant" class="custom-control-input" id="checkbox1">
                                        <label class="custom-control-label" for="checkbox1"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="extras menu-extra-item">
                                <label for="checkbox2" class="extra-item-title mb-0"></label>
                                <div class="d-flex align-items-center">
                                    <div class="custom-control custom-radio mr-sm-2">
                                        <input type="radio" name="variant" class="custom-control-input" id="checkbox2">
                                        <label class="custom-control-label" for="checkbox2"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="line-separate mt-0"></div>
                </div>
            </div>
            <div class="section menu-extra-wrapper">
                <div class="extras-heading">
                    <div class="title">{{ ___('Extras') }}</div>
                    <small>{{___('Please select extra items')}}</small>
                </div>
                <div id="customize-extras">
                </div>
            </div>
        </div>
        <!-- Bottom Panel  -->
        <div class="footer footer-extras">
            <div class="clearfix"></div>
            <div class="section">
                <div class="row no-gutters">
                    <div class="col-3 p-r-10">
                        <div class="add-menu">
                            <div class="add-btn add-item-btn">
                                <div class="wrapper h-100">
                                    <div class="addition menu-order-quantity-decrease">
                                        <i class="icon-feather-minus"></i>
                                    </div>
                                    <div class="count">
                                        <span class="num" id="menu-order-quantity">1</span>
                                    </div>
                                    <div class="addition menu-order-quantity-increase">
                                        <i class="icon-feather-plus"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-9 p-l-10">
                        <button type="button" class="btn btn-primary btn-block" id="add-order-button">{{ ___('Add') }} <span
                                id="order-price"></span></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bottom Panel  -->
    </div>
    <!--Customized Menu-->

    <!-- Call the waiter -->
    <div id="call-waiter-box" class="sidenav bottom">
        <div class="sidebar-header bg-white">
            <div class="navbar-heading">
                <h4>{{ ___('Call the Waiter') }}</h4>
            </div>
            <button type="button" id="dismiss" class="btn ml-auto">
                <i class="icon-feather-x"></i>
            </button>
        </div>
        <div>
            <form type="post" action="{{ route('restaurant.callTheWaiter', $post->id) }}" id="call-waiter-form">
                @csrf
                <div class="sidebar-wrapper">
                    <div class="section">
                        <div class="form-group" id="table-number-field">
                            <div class="form-line">
                                <input type="number" name="table" class="form-control"
                                       placeholder="{{ ___('Table Number') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bottom Panel  -->
                <div class="footer footer-extras">
                    <div class="clearfix"></div>
                    <div class="section">
                        <small class="form-error"></small>
                        <button type="submit" class="btn btn-primary btn-block" id="send-call-waiter">{{ ___('Send') }}
                        </button>
                    </div>
                </div>
                <!-- Bottom Panel  -->
            </form>
        </div>
    </div>
    <!-- Call the waiter -->

    <div class="overlay"></div>
@endsection

@push('scripts_at_bottom')
    <script>
        $('.user-lang-switcher').on('click', '.dropdown-item', function (e) {
            e.preventDefault();
            var code = $(this).data('code');
            if (code != null) {
                $('#user-lang').html(code.toUpperCase());
                $.cookie('Quick_user_lang_code', code, {path: '/'});
                location.reload();
            }
        });
    </script>
    <script src="{{ asset('assets/global/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery.lazyload.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/tippy.all.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery.cookie.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/md5.min.js') }}"></script>
    <script src="{{ asset('assets/post_templates/'.$theme.'/lib/lightbox/lightgallery.min.js') }}"></script>
    <script src="{{ asset('assets/post_templates/'.$theme.'/lib/node-waves/waves.min.js') }}"></script>
@endpush
