@extends('post_templates.layout')

@push('style_at_top')
    <link rel="stylesheet"
          href="{{ asset('assets/global/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet"
          href="{{ asset('assets/post_templates/'.$theme.'/css/magnific-popup.css?ver='.config('appinfo.version')) }}">
    <link rel="stylesheet"
          href="{{ asset('assets/post_templates/'.$theme.'/css/style.css?ver='.config('appinfo.version')) }}">
@endpush

@section('content')
    <div class="flipbook-loader">
        <div class="flipbook-loader-container">
            <div class="flipbook-svg-wrapper">
                <svg class="icon flipbook-svg1"
                     viewBox="130 0 800 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2478"
                     xmlns:xlink="http://www.w3.org/1999/xlink" width="49" height="56">
                    <defs>
                        <style type="text/css"></style>
                    </defs>
                    <path
                        d="M835.55027 48.761905C876.805122 48.761905 910.222223 81.441158 910.222223 121.753604L910.222223 902.095C910.222223 902.095 910.222223 942.409011 876.805 975.238095L113.777778 975.238095 113.777778 24.380952 88.888889 48.761905 835.55027 48.761905ZM64 0 64 24.380952 64 1024L960 1024C835.55027 1024 904.277615 1024 960 969.325498L960 54.49204C960 54.49204 904.277615 0 835.55027 0L88.888889 0 64 0Z"
                        p-id="2479"></path>
                    <path
                        d="M775.164361 219.428572C788.910114 219.428572 800.05325 208.512847 800.05325 195.047619 800.05325 181.582391 788.910114 170.666667 775.164361 170.666667L263.111111 170.666667C249.365357 170.666667 238.222222 181.582391 238.222222 195.047619 238.222222 208.512847 249.365357 219.428572 263.111111 219.428572L775.164361 219.428572Z"
                        p-id="2481"></path>
                    <path
                        d="M775.164361 365.714285C788.910114 365.714285 800.05325 354.798562 800.05325 341.333333 800.05325 327.868105 788.910114 316.952382 775.164361 316.952382L263.111111 316.952382C249.365357 316.952382 238.222222 327.868105 238.222222 341.333333 238.222222 354.798562 249.365357 365.714285 263.111111 365.714285L775.164361 365.714285Z"
                        p-id="2482"></path>
                    <path
                        d="M775.164361 536.380951C788.910114 536.380951 800.05325 525.465229 800.05325 512 800.05325 498.534771 788.910114 487.619049 775.164361 487.619049L263.111111 487.619049C249.365357 487.619049 238.222222 498.534771 238.222222 512 238.222222 525.465229 249.365357 536.380951 263.111111 536.380951L775.164361 536.380951Z"
                        p-id="2483"></path>
                </svg>
                <svg class="icon flipbook-svg2"
                     viewBox="130 0 800 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2478"
                     xmlns:xlink="http://www.w3.org/1999/xlink" width="49" height="56">
                    <defs>
                        <style type="text/css"></style>
                    </defs>
                    <path
                        d="M835.55027 48.761905C876.805122 48.761905 910.222223 81.441158 910.222223 121.753604L910.222223 902.095C910.222223 902.095 910.222223 942.409011 876.805 975.238095L113.777778 975.238095 113.777778 24.380952 88.888889 48.761905 835.55027 48.761905ZM64 0 64 24.380952 64 1024L960 1024C835.55027 1024 904.277615 1024 960 969.325498L960 54.49204C960 54.49204 904.277615 0 835.55027 0L88.888889 0 64 0Z"
                        p-id="2479"></path>
                    <path
                        d="M775.164361 219.428572C788.910114 219.428572 800.05325 208.512847 800.05325 195.047619 800.05325 181.582391 788.910114 170.666667 775.164361 170.666667L263.111111 170.666667C249.365357 170.666667 238.222222 181.582391 238.222222 195.047619 238.222222 208.512847 249.365357 219.428572 263.111111 219.428572L775.164361 219.428572Z"
                        p-id="2481"></path>
                    <path
                        d="M775.164361 365.714285C788.910114 365.714285 800.05325 354.798562 800.05325 341.333333 800.05325 327.868105 788.910114 316.952382 775.164361 316.952382L263.111111 316.952382C249.365357 316.952382 238.222222 327.868105 238.222222 341.333333 238.222222 354.798562 249.365357 365.714285 263.111111 365.714285L775.164361 365.714285Z"
                        p-id="2482"></path>
                    <path
                        d="M775.164361 536.380951C788.910114 536.380951 800.05325 525.465229 800.05325 512 800.05325 498.534771 788.910114 487.619049 775.164361 487.619049L263.111111 487.619049C249.365357 487.619049 238.222222 498.534771 238.222222 512 238.222222 525.465229 249.365357 536.380951 263.111111 536.380951L775.164361 536.380951Z"
                        p-id="2483"></path>
                </svg>
                <svg class="loadingRun flipbook-svg3"
                     viewBox="130 0 800 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2478"
                     xmlns:xlink="http://www.w3.org/1999/xlink" width="49" height="56">
                    <defs>
                        <style type="text/css"></style>
                    </defs>
                    <path
                        d="M835.55027 48.761905C876.805122 48.761905 910.222223 81.441158 910.222223 121.753604L910.222223 902.095C910.222223 902.095 910.222223 942.409011 876.805 975.238095L113.777778 975.238095 113.777778 24.380952 88.888889 48.761905 835.55027 48.761905ZM64 0 64 24.380952 64 1024L960 1024C835.55027 1024 904.277615 1024 960 969.325498L960 54.49204C960 54.49204 904.277615 0 835.55027 0L88.888889 0 64 0Z"
                        p-id="2479"></path>
                    <path
                        d="M775.164361 219.428572C788.910114 219.428572 800.05325 208.512847 800.05325 195.047619 800.05325 181.582391 788.910114 170.666667 775.164361 170.666667L263.111111 170.666667C249.365357 170.666667 238.222222 181.582391 238.222222 195.047619 238.222222 208.512847 249.365357 219.428572 263.111111 219.428572L775.164361 219.428572Z"
                        p-id="2481"></path>
                    <path
                        d="M775.164361 365.714285C788.910114 365.714285 800.05325 354.798562 800.05325 341.333333 800.05325 327.868105 788.910114 316.952382 775.164361 316.952382L263.111111 316.952382C249.365357 316.952382 238.222222 327.868105 238.222222 341.333333 238.222222 354.798562 249.365357 365.714285 263.111111 365.714285L775.164361 365.714285Z"
                        p-id="2482"></path>
                    <path
                        d="M775.164361 536.380951C788.910114 536.380951 800.05325 525.465229 800.05325 512 800.05325 498.534771 788.910114 487.619049 775.164361 487.619049L263.111111 487.619049C249.365357 487.619049 238.222222 498.534771 238.222222 512 238.222222 525.465229 249.365357 536.380951 263.111111 536.380951L775.164361 536.380951Z"
                        p-id="2483"></path>
                </svg>
            </div>
        </div>
    </div>

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
                                @if(@$postOptions->allow_call_waiter)
                                    <button type="button" class="button" id="call-the-waiter-btn"
                                            title="{{ ___('Call the Waiter') }}">
                                        <i class="fa fa-bell"></i>
                                        <span class="d-none d-sm-inline">{{ ___('Call the Waiter') }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper shadow">
        <div class="aspect">
            <div class="aspect-inner">
                <div class="flipbook" id="flipbook">
                    @foreach($post->image_menus->where('active', '1') as $key => $menu)
                        {{-- Break loop if menu limit exceeded --}}
                        @if($plan->settings->menu_limit != "999"
                                && $key >= $plan->settings->menu_limit)
                            @break
                        @endif
                        <div class="page">
                            <img src="{{asset('storage/menu/'.$menu->image)}}" draggable="false"
                                 alt="{{$menu->name}}"/>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    {{-- Footer --}}
    <div class="bg-dark">
        <p class="text-center text-white py-2 m-0">&copy; {{ date('Y')  }} {{$post->title}}
            @if(!@$plan->settings->hide_branding)
                &nbsp; | &nbsp; <a class="text-white" href="{{ route('home') }}"
                                   target="_blank">{{ ___('Provided by QuickQR') }}</a>
            @endif</p>
    </div>

    <!-- Call the waiter -->
    <div id="call-waiter-box" class="zoom-anim-dialog mfp-hide dialog-with-tabs popup-dialog">
        <!--Tabs -->
        <div class="sign-in-form">
            <div class="popup-dialog-header">
                <h5>{{ ___('Call the Waiter') }}</h5>
            </div>
            <div class="popup-tab-content">
                <div>
                    <form type="post" action="{{ route('restaurant.callTheWaiter', $post->id) }}" id="call-waiter-form">
                        @csrf
                        <input id="table-number-field" type="number" class="form-control mb-3" name="table"
                               placeholder="{{ ___('Table Number') }}" style="height: 42px;">
                        <button type="submit" id="submit-order-button" class="button w-100"><i
                                class="icon-feather-send"></i> <span>{{ ___('Send') }}</span></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_at_bottom')
    <script>
        var LANG_THIS_FIRST_PAGE = @json(___('This is the first page.'));
        var LANG_THIS_LAST_PAGE = @json(___('This is the last page.'));
    </script>
    <script src="{{ asset('assets/post_templates/'.$theme.'/js/jquery.mobile.min.js') }}"></script>
    <script src="{{ asset('assets/post_templates/'.$theme.'/js/turn.min.js') }}"></script>
    <script src="{{ asset('assets/templates/classic/js/snackbar.js') }}"></script>
    <script src="{{ asset('assets/templates/classic/js/magnific-popup.min.js') }}"></script>
@endpush
