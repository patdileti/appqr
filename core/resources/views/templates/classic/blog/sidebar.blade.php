<div class="col-xl-4 col-lg-4 content-left-offset">
    <div class="sidebar-container @if(isset($margin)) margin-top-65 @endif">
        <form action="{{ route('blog.index') }}" method="GET">
            <div class="sidebar-widget margin-bottom-40">
                <div class="input-with-icon">
                    <input class="with-border" type="text" placeholder="{{ ___('Search') }}" name="search"
                           id="search-widget" value="{{ request('search') ?? '' }}">
                    <i class="icon-material-outline-search"></i>
                </div>
            </div>
        </form>

        <div class="margin-bottom-40">
            <h3 class="widget-title">{{ ___('Recent Blog') }}</h3>
            <div class="recent-post-widget">
                @forelse ($recentBlogs as $recentBlog)
                    <div>
                        @if($settings->blog_banner)
                        <a href="{{ route('blog.single', [$recentBlog->id, $recentBlog->slug]) }}">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ asset('storage/blog/'.$recentBlog->image) }}" alt="{{ $recentBlog->title }}" class="post-thumb lazy-load">
                        </a>
                        @endif
                        <div class="recent-post-widget-content">
                            <h2><a href="{{ route('blog.single', [$recentBlog->id, $recentBlog->slug]) }}">{{ $recentBlog->title }}</a></h2>
                            <div class="post-date">
                                <i class="icon-feather-clock"></i> {{ $recentBlog->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <span class="text-muted text-center">{{ ___('No articles found') }}</span>
                @endforelse
            </div>
        </div>

        <!-- Category Widget -->
        <div class="margin-bottom-40">
            <h3 class="widget-title">{{ ___('Categories') }}</h3>
            <div class="widget-content">
                <ul>
                    @forelse ($blogCategories as $blogCategory)
                        <li class="clearfix">
                            <a href="{{ route('blog.category', $blogCategory->slug) }}">
                                <span class="pull-left">{{ $blogCategory->title }}</span>
                                <span class="pull-right">({{ $blogCategory->blogs_count }})</span></a>
                        </li>
                    @empty
                        <li class="clearfix"><span class="text-muted">{{ ___('No categories found') }}</span></li>
                    @endforelse
                </ul>
            </div>
        </div>
        <!-- Category Widget / End-->

        @if($settings->testimonials_enable && $settings->show_testimonials_blog && $testimonials->count() > 0)
            <div class="sidebar-widget">
                <h3>{{ ___('Testimonials')  }}</h3>
                <div class="single-carousel">
                    @foreach ($testimonials as $testimonial)
                        <div class="single-testimonial">
                            <div class="single-inner">
                                <div class="testimonial-content">
                                    <p>{{ !empty($testimonial->translations->{get_lang()}->content)
                                        ? $testimonial->translations->{get_lang()}->content
                                        : $testimonial->content }}</p>
                                </div>
                                <div class="testi-author-info">
                                    <div class="image"><img
                                            src="{{ asset('storage/testimonials/'.$testimonial->image) }}"
                                            alt="{{$testimonial->name}}"></div>
                                    <h5 class="name">{{$testimonial->name}}</h5>
                                    <span class="designation">{{ !empty($testimonial->translations->{get_lang()}->designation)
                                        ? $testimonial->translations->{get_lang()}->designation
                                        : $testimonial->designation }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($blogTags))
        <!-- Tags Widget -->
            <div class="sidebar-widget">
                <h3>{{ ___('Tags') }}</h3>
                <div class="task-tags">
                    @foreach($blogTags as $tag)
                        @if(!empty(trim($tag)))
                            <a href="{{ route('blog.tag', trim($tag)) }}"><span>{{ trim($tag) }}</span></a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
