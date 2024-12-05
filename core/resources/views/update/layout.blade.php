<!DOCTYPE html>
<html class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{___('Update')}} - {{config('appinfo.name')}}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/logo/favicon.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/fonts/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/installer/style.css') }}">
</head>
<body class="d-flex flex-column">
<div class="d-flex flex-column flex-fill mt-5">
    <div class="bg-base-1 flex-fill">
        <h1 class="h3 text-center mb-0">{{config('appinfo.name')}} - {{___('Update')}}</h1>
        <div class="container">
            <div class="row h-100 justify-content-center align-items-center py-5">
                <div class="col-lg-6">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
