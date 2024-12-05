@stack('scripts_at_top')

<script>
    "use strict";
    var themecolor = @json($settings->theme_color);
    var template_name = @json(env('THEME_NAME'));
    var siteurl = @json(route('home'));
    var assetsUrl = @json(asset('assets'));
    var storageurl = @json(asset('storage'));
    var heartBeatRoute = @json(route('restaurants.heartbeat'));

    var LANG_LOGGED_IN_SUCCESS = @json(___('Logged in successfully'));
    var LANG_DEVELOPED_BY = @json(___('Developed by'));
    var DEVELOPER_CREDIT = @json(@$settings->developer_credit);
    var LIVE_CHAT = @json(@$settings->live_chat);

    var LANG_NEW_ORDERS = @json(___('New Orders available.'));

    if ($("body").hasClass("rtl")) {
        var rtl = true;
    } else {
        var rtl = false;
    }
</script>

<script src="{{ asset($activeThemeAssets.'js/chosen.min.js') }}"></script>
<script src="{{ asset('assets/global/js/jquery.lazyload.min.js') }}"></script>
<script src="{{ asset('assets/global/js/tippy.all.min.js') }}"></script>
<script src="{{ asset($activeThemeAssets.'js/simplebar.min.js') }}"></script>
<script src="{{ asset($activeThemeAssets.'js/bootstrap-slider.min.js') }}"></script>
<script src="{{ asset($activeThemeAssets.'js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset($activeThemeAssets.'js/counterup.min.js') }}"></script>
<script src="{{ asset($activeThemeAssets.'js/magnific-popup.min.js') }}"></script>
<script src="{{ asset($activeThemeAssets.'js/slick.min.js') }}"></script>
<script src="{{ asset('assets/global/js/jquery.cookie.min.js') }}"></script>
<script src="{{ asset($activeThemeAssets.'js/snackbar.js') }}"></script>

<link rel="stylesheet" href="{{ asset($activeThemeAssets.'css/alertify.css') }}">
<script src="{{ asset($activeThemeAssets.'js/alertify.min.js') }}"></script>

@stack('scripts_vendor')
<!--Custom JS-->
<script src="{{ asset($activeThemeAssets.'js/user-ajax.js?ver='.config('appinfo.version')) }}"></script>
<script src="{{ asset($activeThemeAssets.'js/custom.js?ver='.config('appinfo.version')) }}"></script>

@stack('scripts_at_bottom')

@if(\Session::has('quick_alert_message'))
    <script>
        Snackbar.show({
            text: @json(\Session::get('quick_alert_message')),

            @if(\Session::get('quick_alert_type') == 'error')
            actionText: '<i class="fas fa-times"></i>',
            showAction: true,
            duration: 100000,
            actionTextColor: '#ffffff',
            backgroundColor: '#ee5252'
            @elseif(\Session::get('quick_alert_type') == 'success')
            backgroundColor: '#383838'
            @elseif(\Session::get('quick_alert_type') == 'info')
            backgroundColor: '#45cfe1'
            @endif
        });
    </script>
@endif
