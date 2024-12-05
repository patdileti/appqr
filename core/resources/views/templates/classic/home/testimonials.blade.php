@extends($activeTheme.'layouts.main')
@section('title', ___('Testimonials'))
@section('content')
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ ___('Testimonials') }}</h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li>{{ ___('Testimonials') }}</li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    {!! ads_on_top() !!}
    <div class="container margin-bottom-50">
        <div class="row">
            @foreach($testimonials as $testimonial)
            <div class="col-md-4">
                <div class="single-testimonial">
                    <div class="single-inner">
                        <div class="testimonial-content">
                            <p>{{ !empty($testimonial->translations->{get_lang()}->content)
                                        ? $testimonial->translations->{get_lang()}->content
                                        : $testimonial->content }}</p>
                        </div>
                        <div class="testi-author-info">
                            <div class="image"><img src="{{ asset('storage/testimonials/'.$testimonial->image) }}" alt="{{$testimonial->name}}"></div>
                            <h5 class="name">{{$testimonial->name}}</h5>
                            <span class="designation">{{ !empty($testimonial->translations->{get_lang()}->designation)
                                        ? $testimonial->translations->{get_lang()}->designation
                                        : $testimonial->designation }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        {{ $testimonials->links($activeTheme.'pagination/default') }}
    </div>
    {!! ads_on_bottom() !!}
@endsection
