<style>
    :root{--theme-color: {{ $settings->theme_color }};
        @php
        $themecolor = $settings->theme_color;
        list($r, $g, $b) = sscanf($themecolor, "#%02x%02x%02x");
        @endphp
        --theme-color-rgb: {{ "$r, $g, $b" }};}
</style>

<link rel="stylesheet" href="{{ asset('assets/admin/icons/css/feather-icon.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/slidePanel.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/global/fonts/css/fontawesome.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/perfect-scrollbar/perfect-scrollbar.css') }}">

@stack('styles_vendor')
{{--Can add any file--}}

<link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/responsive.css') }}">
