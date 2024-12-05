<!-- Wrapper -->
<div id="wrapper" class="">
    <!-- Header Container
    ================================================== -->
    <header id="header-container" class="fullwidth">
        <x-demo-frame />

        @if(session()->get('quick_admin_user_id'))
            <div class="notification notice margin-bottom-0 padding-bottom-10 padding-top-10">
                <div class="d-flex justify-content-between">
                    <span>{!! ___('You are logged in as :user_name.', ['user_name' => '<strong>'.request()->user()->name.'</strong>']) !!}</span>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ ___('Exit') }}
                    </a>
                </div>
            </div>
        @endif
        @auth
            @if($settings->non_active_msg == 1 && !request()->user()->hasVerifiedEmail())
                <div class="user-status-message">
                    <div class="container container-active-msg">
                        <div class="row">
                            <div class="col-lg-8">
                                <i class="icon-lock text-18"></i>
                                <span>{{ ___('Your email address is not verified. Please verify your email address to use all the features.') }}</span>
                            </div>
                            <div class="col-lg-4">
                                <form action="{{ route('verification.resend') }}" method="post">
                                    @csrf
                                    <button type="submit"
                                            class="button ripple-effect gray">{{ ___('Resend Email') }}</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endauth
    <!-- Header -->
        <div id="header">
            <div class="container">
                <!-- Left Side Content -->
                <div class="left-side">
                    <!-- Logo -->
                    <div id="logo">
                        <a href="{{ route('home') }}">
                            @php
                                $logo_white = asset('storage/logo/'.$settings->site_logo_footer);
                                $logo_dark = asset('storage/logo/'.$settings->site_logo);
                            @endphp
                            <img src="{{ $logo_dark }}" data-sticky-logo="{{ $logo_dark }}"
                                 data-transparent-logo="{{ $logo_white }}" alt="{{ @$settings->site_title }}">
                        </a>
                    </div>

                </div>
                <!-- Left Side Content / End -->


                <!-- Right Side Content / End -->
                <div class="right-side">
                @auth

                    <!-- User Menu -->
                        <div class="header-widget">

                            <!-- Messages -->
                            <div class="header-notifications user-menu">
                                <div class="header-notifications-trigger">
                                    <a href="#">
                                        <div class="user-avatar status-online"><img
                                                src="{{ asset('storage/profile/'.request()->user()->image) }}"
                                                alt="{{ request()->user()->username }}"></div>
                                    </a>
                                </div>
                                <!-- Dropdown -->
                                <div class="header-notifications-dropdown">
                                    <ul class="user-menu-small-nav">
                                        @if(request()->user()->isAdmin())
                                            <li><a href="{{ route('admin.dashboard') }}" target="_blank"><i
                                                        class="icon-feather-external-link"></i> {{ ___('Admin') }}
                                                </a></li>
                                        @endif
                                        <li><a href="{{ route('dashboard') }}"><i
                                                    class="icon-feather-grid"></i> {{ ___('Dashboard') }}
                                            </a></li>
                                        <li><a href="{{ route('restaurants.create') }}"><i
                                                    class="icon-feather-plus-square"></i> {{ ___('Add Restaurant') }}
                                            </a></li>
                                        <li><a href="{{ route('restaurants.index') }}"><i
                                                    class="far fa-utensils"></i> {{ ___('My Restaurants') }}
                                            </a></li>
                                        <li><a href="{{ route('settings') }}"><i
                                                    class="icon-feather-settings"></i> {{ ___('Account Setting') }}
                                            </a></li>
                                            <li><a href="{{ route('subscription') }}"><i
                                                        class="icon-feather-gift"></i> {{ ___('Membership') }}
                                                </a></li>
                                        <li><a href="{{ route('transactions') }}"><i
                                                    class="icon-feather-file-text"></i> {{ ___('Transactions') }}
                                            </a></li>
                                        <li><a href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                                    class="icon-feather-log-out"></i> {{ ___('Logout') }}
                                            </a></li>
                                    </ul>
                                    <form id="logout-form" class="d-inline" action="{{ route('logout') }}"
                                          method="POST">
                                        @csrf
                                    </form>
                                </div>
                            </div>

                        </div>
                        <!-- User Menu / End -->
                    @endauth
                    @guest
                        <div class="header-widget">
                            <a href="#sign-in-dialog"
                               class="popup-with-zoom-anim button ripple-effect">{{ ___('Join Now') }}</a>
                        </div>
                    @endguest

                    @if($settings->userlangsel)
                        @php
                            $language = current_language();
                        @endphp
                        <div class="header-widget">
                            <div class="btn-group bootstrap-select language-switcher">
                                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown"
                                        title="{{ $language->name }}">
                                    <span class="filter-option pull-left"
                                          id="selected_lang">{{ strtoupper($language->code) }}</span>&nbsp;
                                    <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu scrollable-menu open">
                                    <ul class="dropdown-menu inner">
                                        @foreach($languages as $language)
                                            <li data-lang="{{ $language->code }}">
                                                <a role="menuitem" tabindex="-1" rel="alternate"
                                                   href="{{ lang_url($language->code) }}">{{ $language->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- Right Side Content / End -->

            </div>
        </div>
        <!-- Header / End -->

    </header>
    <div class="clearfix"></div>
    <!-- Header Container / End -->
