@extends($activeTheme.'layouts.main')
@section('title', $page->title)
@section('description', text_shorting(strip_tags($page->content), 100))
@section('content')
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ $page->title }}</h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li>{{ $page->title }}</li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    {!! ads_on_top() !!}
    <div class="container margin-bottom-50">
        <div class="section html-pages">{!! $page->content !!}</div>
        <!-- faq-page -->
    </div>
    {!! ads_on_bottom() !!}
@endsection
