<!DOCTYPE html>
<html lang="{{ get_lang() }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ @$settings->site_title ?? 'Error' }} — @yield('title')</title>
    <link rel="shortcut icon" href="{{ asset($settings->site_favicon ?? '') }}">
    <link rel="stylesheet" href="{{ asset(active_theme(true).'css/style.css') }}">
    <style>
        .button { background: {{$settings->theme_color ?? ''}} !important;}
    </style>
</head>
<body>
    <div class="container margin-top-50 margin-bottom-25">
        <div class="row">
            <div class="col-xl-8 margin-0-auto">
                <section id="not-found" class="center margin-top-50 margin-bottom-25">
                    <h2>@yield('code')</h2>
                    <p>@yield('message')</p><br>
                    <p><small>@yield('description')</small></p>
                </section>
                <a href="{{ url('/') }}" class="button full-width">{{ ___('Back to home') }}</a>

            </div>
        </div>
    </div>
</body>
</html>
