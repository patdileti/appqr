@extends($activeTheme.'layouts.main')
@section('title', $title ?? ___('Blog'))
@section('content')
    <div id="titlebar" class="white margin-bottom-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ $title ?? ___('Blog') }}</h2>
                    <span>{{ ___('Recent Blog') }}</span>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li><a href="{{ route('blog.index') }}">{{ ___('Blog') }}</a></li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    {!! ads_on_top() !!}
    <div class="section gray">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-8">

                    <!-- Section Headline -->
                    <div class="section-headline margin-top-60 margin-bottom-35">
                        <h4>{{ ___('Recent Blog') }}</h4>
                    </div>
                    @if ($blogs->count() > 0)
                        @foreach ($blogs as $blog)
                            <a href="{{ route('blog.single', [$blog->id, $blog->slug]) }}" class="blog-post">
                                <!-- Blog Post Thumbnail -->
                                <div class="blog-post-thumbnail">
                                    <div class="blog-post-thumbnail-inner">
                                        <span class="blog-item-tag">{{ $blog->user->name }}</span>
                                        @if($settings->blog_banner)
                                            <img src="{{ asset('storage/blog/'.$blog->image) }}"
                                                 alt="{{ $blog->title }}">
                                        @endif
                                    </div>
                                </div>
                                <!-- Blog Post Content -->
                                <div class="blog-post-content">
                                    <span class="blog-post-date">{{ $blog->created_at->diffForHumans() }}</span>
                                    <h3 class="margin-bottom-0">{{ $blog->title }}</h3>
                                    <div class="margin-bottom-15">
                                        @php
                                            $categories = [];
                                            foreach ($blog->categories as $category){
                                               $categories[] = $category->title;
                                            }
                                        @endphp
                                        {{ implode(', ', $categories) }}
                                    </div>
                                    <p>{!! text_shorting(strip_tags($blog->description), 100) !!}</p>
                                </div>
                                <!-- Icon -->
                                <div class="entry-icon"></div>
                            </a>
                        @endforeach
                        <div class="clearfix"></div>
                        {{ $blogs->links($activeTheme.'pagination/default') }}
                    @else
                        <div class="blog-not-found">
                            <h2><span>:</span>(</h2>
                            <p>
                                {{ ___('Sorry, we could not found the blog you are looking for!') }}
                            </p>
                        </div>
                    @endif
                </div>

                @include($activeTheme.'blog.sidebar', ['margin' => true])
            </div>
        </div>

        <!-- Spacer -->
        <div class="padding-top-40"></div>
        <!-- Spacer -->
    </div>

    {!! ads_on_top() !!}
@endsection
