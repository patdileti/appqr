<!DOCTYPE html>
<html lang="{{ get_lang() }}" dir="{{ current_language()->direction }}">
<head>
    @include($activeTheme.'layouts.includes.head')
    @include($activeTheme.'layouts.includes.styles')
    {!! head_code() !!}
</head>
<body class="{{ current_language()->direction }}">
@include($activeTheme.'layouts.includes.header')

<!-- Titlebar
================================================== -->
<div id="titlebar" class="gradient">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>@yield('title')</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                        <li>@yield('title')</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-5 offset-xl-3">
            <div class="login-register-page">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<div class="margin-top-70"></div>

@include($activeTheme.'layouts.includes.footer')
@include($activeTheme.'layouts.includes.addons')
@include($activeTheme.'layouts.includes.scripts')

{!! google_captcha() !!}

@if(session('status'))
    <script>
        Snackbar.show({ text: @json(session('status')) });
    </script>
@endif
</body>

</html>
