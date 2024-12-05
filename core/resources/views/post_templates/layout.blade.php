<!DOCTYPE html>
<html lang="{{ get_lang() }}" dir="{{ current_language()->direction }}">
<head>
    <meta charset="utf-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="robots" content="index, follow">

    <meta name="author" content="{{ $settings->site_title }}">
    <meta name="title" content="{{ $post->title }}">
    <meta name="description" content="{{ text_shorting(strip_tags($post->description), 150) }}" />
    <meta property="profile:username" content="{{ $post->slug }}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/restaurant/logo/'.$post->main_image) }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/restaurant/logo/'.$post->main_image) }}">
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $post->title }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:description" content="{{ text_shorting(strip_tags($post->description), 150) }}" />
    <meta property="og:image:secure_url" content="{{ asset('storage/restaurant/logo/'.$post->main_image) }}" />
    <meta property="og:image" content="{{ asset('storage/restaurant/logo/'.$post->main_image) }}" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="{{ $post->title }}" />
    <meta name="twitter:image" content="{{ asset('storage/restaurant/logo/'.$post->main_image) }}" />
    <meta name="twitter:url" content="{{ url()->current() }}" />
    <link rel="preload" as="image" href="{{ asset('storage/restaurant/logo/'.$post->main_image) }}" />
    <title>{{ $post->title }}</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/restaurant/logo/'.$post->main_image) }}">

    <meta name="theme-color" content="{{ $post->color }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="HandheldFriendly" content="True">

    <script async>
        var themecolor = @json($post->color);
        var siteurl = @json(route('home'));
    </script>
    <!--Loop for Theme Color codes-->
    <style>
        :root{@php
        $themecolor = $post->color;
        list($r, $g, $b) = sscanf($themecolor, "#%02x%02x%02x");

        $i = 0.01;
        while($i <= 1){
            echo "--theme-color-".str_replace('.','_',$i).": rgba($r,$g,$b,$i);";
            $i += 0.01;
        }
        echo "--theme-color-1: rgba($r,$g,$b,1);";
        @endphp}
    </style>

    <link rel="stylesheet" href="{{ asset('assets/global/css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/fonts/css/fontawesome.css') }}">

    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    @stack('style_at_top')
</head>
<body class="{{ current_language()->direction }}">

@yield('content')

<script>
    var TOTAL_MENUS = @json($total_menus ?? []);
    var CURRENCY_SIGN = @json(@$postOptions->currency_sign);
    var CURRENCY_LEFT = @json(@$postOptions->currency_pos);

    // Language Var
    var LANG_ADD = @json(___('Add'));
    var LANG_PAY_NOW = @json(___('Pay Now'));
    var LANG_SEND_ORDER = @json(___('Send Order'));
</script>

@stack('scripts_at_bottom')

<script src="{{ asset('assets/post_templates/'. $theme .'/js/script.js?ver='.config('appinfo.version')) }}"></script>
</body>
</html>
