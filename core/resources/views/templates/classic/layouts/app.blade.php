<!DOCTYPE html>
<html lang="{{ get_lang() }}" dir="{{ current_language()->direction }}">
<head>
    @include($activeTheme.'layouts.includes.head')
    @include($activeTheme.'layouts.includes.styles')
    {!! head_code() !!}
</head>
<body class="{{ current_language()->direction }}">
@include($activeTheme.'layouts.includes.header')

<!-- Dashboard Container -->
<div class="dashboard-container">
    @include($activeTheme.'user.includes.dashboard-sidebar')
    <!-- Dashboard Content
        ================================================== -->
    <div class="dashboard-content-container" data-simplebar>
        <div class="dashboard-content-inner">

            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3>@yield('title')</h3>

                @hasSection('header_buttons')
                    <div class="headline-right">
                        @yield('header_buttons')
                    </div>
                @else
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li>@yield('title')</li>
                        </ul>
                    </nav>
                @endif
            </div>
            {!! ads_on_dashboard_top() !!}
            @yield('content')
            {!! ads_on_dashboard_bottom() !!}
            <!-- Footer -->
            <div class="dashboard-footer-spacer"></div>
            <div class="small-footer margin-top-15">
                <div class="small-footer-copyrights">
                    {{ ___('Copyright © :current_year Bylancer. All Rights Reserved.', ['current_year' => date('Y')]) }}
                </div>
                <ul class="footer-social-links">
                    @if($settings->facebook_link)
                        <li><a href="{{ $settings->facebook_link }}" target="_blank" rel="nofollow"><i
                                    class="fab fa-facebook"></i></a></li>
                    @endif
                    @if($settings->twitter_link)
                        <li><a href="{{ $settings->twitter_link }}" target="_blank"
                               rel="nofollow">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 14"
                                     style="fill: currentcolor;     height: .9em; overflow: visible; width: 1em;">
                                    <path
                                        d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
                                </svg>
                            </a></li>
                    @endif
                    @if($settings->instagram_link)
                        <li><a href="{{ $settings->instagram_link }}" target="_blank"
                               rel="nofollow"><i class="fab fa-instagram"></i></a></li>
                    @endif
                    @if($settings->linkedin_link)
                        <li><a href="{{ $settings->linkedin_link }}" target="_blank" rel="nofollow"><i
                                    class="fab fa-linkedin"></i></a></li>
                    @endif
                    @if($settings->pinterest_link)
                        <li><a href="{{ $settings->pinterest_link }}" target="_blank"
                               rel="nofollow"><i class="fab fa-pinterest"></i></a></li>
                    @endif
                    @if($settings->youtube_link)
                        <li><a href="{{ $settings->youtube_link }}" target="_blank"
                               rel="nofollow"><i class="fab fa-youtube"></i></a></li>
                    @endif
                </ul>
                <div class="clearfix"></div>
            </div>

        </div>
    </div>
</div>
<script>
    "use strict";
    $(document).ready(function () {
        $("#header-container").removeClass('transparent-header').addClass('dashboard-header sticky');
    });
</script>

@include($activeTheme.'layouts.includes.addons')
@include($activeTheme.'layouts.includes.scripts')
</body>
</html>
