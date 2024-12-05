@extends($activeTheme.'layouts.main')
@section('content')
    <div class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center hero-content">
                        <img class="lazy-load"
                             src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                             data-original="{{ asset($activeThemeAssets.'images/qr-screen.png') }}">

                        <h1 class="margin-bottom-10">
                            <strong>{{___('QUICK QR SCAN')}}</strong>
                            <br>{{___('QUICK QR SCAN')}}
                            {{___('Contactless Digital Menu')}}
                        </h1>

                        <p>
                            <span>{!! ___('Thousands of restaurants use <strong>QuickQR</strong><br>to turn their ideas into reality.') !!}</span>
                        </p>
                        @if(auth()->check())
                            <a href="{{route('restaurants.index')}}" class="button ripple-effect">{{___('Restaurants')}}</a>
                        @else
                            <a href="#sign-in-dialog"
                               class="popup-with-zoom-anim button ripple-effect">{{___('Join Now')}}</a>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
    {!! ads_on_top() !!}
    <div class="section padding-top-65 padding-bottom-75">
        <div class="container">
            <div class="service">
                <div class="row align-items-center flex-row-reverse tabs">
                    <div class="col-lg-5">
                        <div class="service_content tabs-header">
                            <div class="section_title undefined"><h6>{{___('What we do?')}}</h6>
                                <h2>{{___('Innovative solutions to create self design QR digital menu')}}</h2>
                            </div>
                            <p>{{___('You can create your own menu with self design QR Code. Also multiple QR template makes it better.')}}</p>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item active">
                                    <a class="nav-link" href="#tab-1" data-tab-id="1">{{___('Create your menu')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#tab-2" data-tab-id="2">{{___('Design QR & Download')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#tab-3" data-tab-id="3">{{___('Getting Live')}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="tabs-content" id="myTabContent">
                            <div class="tab active" data-tab-id="1">
                                <div class="service_img"><img src="{{ asset($activeThemeAssets.'images/menu.png') }}" alt="" width="500px"></div>
                            </div>
                            <div class="tab" data-tab-id="2">
                                <div class="service_img"><img src="{{ asset($activeThemeAssets.'images/qrcode-genrate.png') }}" alt="" width="500px"></div>
                            </div>
                            <div class="tab" data-tab-id="3">
                                <div class="service_img"><img src="{{ asset($activeThemeAssets.'images/golive.png') }}" alt="" width="500px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! ads_on_home_1() !!}
    @if(@$settings->testimonials_enable && @$settings->show_testimonials_home && $testimonials->count() > 0)
        <div class="section gray padding-top-65 padding-bottom-55">

            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <!-- Section Headline -->
                        <div class="section-headline centered margin-top-0 margin-bottom-5">
                            <h3>{{ ___('Testimonials')  }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Carousel -->
            <div class="fullwidth-carousel-container margin-top-20">
                <div class="testimonial-carousel testimonials">
                    <!-- Item -->
                    @foreach ($testimonials as $testimonial)
                        <div class="fw-carousel-review">
                            <div class="testimonial-box">
                                <div class="testimonial-avatar">
                                    <img src="{{ asset('storage/testimonials/'.$testimonial->image) }}"
                                         alt="{{$testimonial->name}}">
                                </div>
                                <div class="testimonial-author">
                                    <h4>{{$testimonial->name}}</h4>
                                    <span>{{ !empty($testimonial->translations->{get_lang()}->designation)
                                        ? $testimonial->translations->{get_lang()}->designation
                                        : $testimonial->designation }}</span>
                                </div>
                                <div class="testimonial">{{ !empty($testimonial->translations->{get_lang()}->content)
                                        ? $testimonial->translations->{get_lang()}->content
                                        : $testimonial->content }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Categories Carousel / End -->

        </div>
    @endif

    @if(@$settings->show_membershipplan_home)
        <div class="section padding-top-60 padding-bottom-75">
            <div class="container">
                <div class="row">

                    <div class="col-xl-12">
                        <!-- Section Headline -->
                        <div class="section-headline centered margin-top-0 margin-bottom-75">
                            <h3>{{ ___('Membership Plans')  }}</h3>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <form action="{{route('checkout.index')}}" method="get">
                            <div class="billing-cycle-radios margin-bottom-70">
                                @if($total_monthly)
                                    <div class="radio billed-monthly-radio">
                                        <input id="radio-monthly" name="interval" type="radio" value="monthly"
                                               checked="">
                                        <label for="radio-monthly"><span class="radio-label"></span> {{___('Monthly')}}
                                        </label>
                                    </div>
                                @endif
                                @if($total_annual)
                                    <div class="radio billed-yearly-radio">
                                        <input id="radio-yearly" name="interval" type="radio" value="yearly">
                                        <label for="radio-yearly"><span class="radio-label"></span> {{___('Yearly')}}
                                        </label>
                                    </div>
                                @endif
                                @if($total_lifetime)
                                    <div class="radio billed-lifetime-radio">
                                        <input id="radio-lifetime" name="interval" type="radio" value="lifetime">
                                        <label for="radio-lifetime"><span
                                                class="radio-label"></span> {{___('Lifetime')}}
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <!-- Pricing Plans Container -->
                            <div class="pricing-plans-container">
                                @foreach ([$free_plan, $trial_plan] as $plan)
                                    @include($activeTheme.'layouts.includes.pricing-table')
                                @endforeach

                                @foreach ($plans as $plan)
                                    @include($activeTheme.'layouts.includes.pricing-table')
                                @endforeach
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {!! ads_on_home_2() !!}
    @if (@$settings->blog_enable && @$settings->show_blog_home && $blogArticles->count() > 0)
        <div class="section padding-top-65 padding-bottom-50 gray">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">

                        <!-- Section Headline -->
                        <div class="section-headline margin-top-0 margin-bottom-45">
                            <h3>{{ ___('Recent Blogs')  }}</h3>
                            <a href="{{ route('blog.index') }}" class="headline-link">{{ ___('View Blog')  }}</a>
                        </div>

                        <div class="row">
                            <!-- Blog Post Item -->
                            @foreach ($blogArticles as $blogArticle)
                                <div class="col-xl-4">
                                    <a href="{{ route('blog.single', [$blogArticle->id, $blogArticle->slug]) }}"
                                       class="blog-compact-item-container">
                                        <div class="blog-compact-item">
                                            <img src="{{ asset('storage/blog/'.$blogArticle->image) }}"
                                                 alt="{{ $blogArticle->title }}">
                                            <span class="blog-item-tag">{{ $blogArticle->user->name }}</span>
                                            <div class="blog-compact-item-content">
                                                <ul class="blog-post-tags">
                                                    <li>{{ $blogArticle->created_at->diffForHumans() }}</li>
                                                </ul>
                                                <h3>{{ $blogArticle->title }}</h3>
                                                <p>{!! text_shorting(strip_tags($blogArticle->description), 100) !!}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                            <!-- Blog post Item / End -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (@$settings->show_partner_logo_home)
        <div class="section border-top padding-top-45 padding-bottom-45">
            <!-- Logo Carousel -->
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <!-- Carousel -->
                        <div class="col-md-12">
                            <div class="logo-carousel">
                                @foreach(glob(public_path('/storage/partner/') . '*') as $path)
                                    <div class="carousel-item">
                                        <img src="{{ asset('storage/partner/'.basename($path)) }}"
                                             alt="{{ basename($path) }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Carousel / End -->
                    </div>
                </div>
            </div>
        </div>
    @endif

    {!! ads_on_bottom() !!}
@endsection
