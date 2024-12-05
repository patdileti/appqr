<!DOCTYPE html>
<html lang="{{ get_lang() }}" dir="{{ current_language()->direction }}">
<head>
    <title>@yield('title')</title>
    <!--Loop for Theme Color codes-->
    <style>
        :root {
        @php
                $themecolor = $color;
                list($r, $g, $b) = sscanf($themecolor, "#%02x%02x%02x");

                $i = 0.01;
                while($i <= 1){
                    echo "--theme-color-".str_replace('.','_',$i).": rgba($r,$g,$b,$i);";
                    $i += 0.01;
                }
                echo "--theme-color-1: rgba($r,$g,$b,1);";
                @endphp
}
    </style>
    <link rel="stylesheet"
          href="{{ asset('assets/templates/classic/css/style.css?ver='.config('appinfo.version')) }}">
    <link rel="stylesheet"
          href="{{ asset('assets/templates/classic/css/color.css?ver='.config('appinfo.version')) }}">

    @if(current_language()->direction == 'rtl')
        <link rel="stylesheet"
              href="{{ asset('assets/templates/classic/css/rtl.css?ver='.config('appinfo.version')) }}">
    @endif

    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    @stack('style_at_top')
</head>
<body class="{{ current_language()->direction }}">

@yield('content')

@stack('scripts_at_bottom')
</body>
</html>
