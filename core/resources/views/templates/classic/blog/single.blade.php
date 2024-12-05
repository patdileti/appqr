@extends($activeTheme.'layouts.main')
@section('title', $blog->title)
@section('description', text_shorting(strip_tags($blog->description), 150))
@section('og_image', asset('storage/blog/'.$blog->image))
@section('content')
    <div id="titlebar" class="gradient">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{$blog->title}}</h2>
                    <span>{{ ___('By :author', ['author' => $blog->user->name]) }}</span>

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
    <!-- Post Content -->
    <div class="container">
        <div class="row">

            <!-- Inner Content -->
            <div class="col-xl-8 col-lg-8">
                <!-- Blog Post -->
                <div class="blog-post single-post">

                @if($settings->blog_banner && isset($blog->image))
                    <!-- Blog Post Thumbnail -->
                        <div class="blog-post-thumbnail">
                            <div class="blog-post-thumbnail-inner">
                                <img src="{{ asset('storage/blog/'.$blog->image) }}" alt="{{$blog->title}}">
                            </div>
                        </div>
                @endif
                <!-- Blog Post Content -->
                    <div class="blog-post-content">
                        <h3 class="margin-bottom-10">{{$blog->title}}</h3>
                        <div class="blog-post-info-list margin-bottom-20">
                            <span class="blog-post-info"><i class="la la-clock-o"></i> {{ date_formating($blog->created_at)}}</span>
                            <span class="blog-post-info"><i class="fa fa-folder-o"></i>
                            @foreach ($blog->categories as $category)
                                    <a href="{{ route('blog.category', $category->slug) }}">{{ $category->title }}</a>
                                @endforeach
                            </span>
                        </div>
                        <div class="user-html">{!! $blog->description !!}</div>
                        @if(!empty($blog->tags))
                            <div class="task-tags margin-bottom-20">
                                {{ ___('Tags') }}:
                                @foreach($blog->tags as $tag)
                                    @if(!empty(trim($tag)))
                                        <a href="{{ route('blog.tag', trim($tag)) }}">{{ trim($tag) }}</a>
                                    @endif
                                @endforeach
                            </div>
                    @endif
                    <!-- Share Buttons -->
                        <div class="share-buttons margin-top-25">
                            <div class="share-buttons-trigger"><i class="icon-feather-share-2"></i></div>
                            <div class="share-buttons-content">
                                <span>{{ ___('Interesting?') }} <strong>{{ ___('Share It!') }}</strong></span>
                                <ul class="share-buttons-icons">

                                    <li><a href="mailto:?subject={{$blog->title}}&body={{ url()->current() }}"
                                           data-button-color="#dd4b39"
                                           title="{{ ___('Share on Email') }}" data-tippy-placement="top" rel="nofollow"
                                           target="_blank"><i class="fa fa-envelope"></i></a></li>
                                    <li><a href="https://facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                                           data-button-color="#3b5998" title="{{ ___('Share on Facebook') }}"
                                           data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-facebook"></i></a></li>
                                    <li>
                                        <a href="https://twitter.com/share?url={{ url()->current() }}&text={{$blog->title}}"
                                           data-button-color="#1da1f2" title="{{ ___('Share on Twitter') }}"
                                           data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-twitter"></i></a></li>
                                    <li>
                                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}"
                                           data-button-color="#0077b5" title="{{ ___('Share on LinkedIn') }}"
                                           data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-linkedin"></i></a></li>
                                    <li>
                                        <a href="https://pinterest.com/pin/create/bookmarklet/?&url={{ url()->current() }}&description={{$blog->title}}"
                                           data-button-color="#bd081c" title="{{ ___('Share on Pinterest') }}"
                                           data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-pinterest-p"></i></a></li>
                                    <li><a href="https://api.whatsapp.com/send?text={{ url()->current() }}"
                                           data-button-color="#25d366"
                                           title="{{ ___('Share on WhatsApp') }}" data-tippy-placement="top"
                                           rel="nofollow"
                                           target="_blank"><i class="fa fa-whatsapp"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Blog Post Content / End -->
                <div id="comments">
                    @if($settings->blog_comment_enable)
                        <div class="blog-widget">
                            <h3 class="widget-title margin-bottom-25">{{ ___('Comments') }}
                                ({{ $blogComments->count() }})</h3>

                            <div class="latest-comments">
                                <ul>
                                    @forelse ($blogComments as $blogComment)
                                        @include($activeTheme.'blog.comment', [
                                            'blog_id' => $blog->id,
                                            'blogComment' => $blogComment,
                                            'level' => 1
                                        ])
                                    @empty
                                        <li><span class="text-muted mb-10">{{ ___('No comments found') }}</span></li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        {{ $blogComments->links($activeTheme.'pagination/default') }}
                        @if($settings->blog_comment_user || (!$settings->blog_comment_user && auth()->check()))
                        <!-- Leave a Comment -->
                            <div class="blog-widget" id="respond">
                                <h3 class="widget-title">{{ ___('Post a Comment') }}
                                    <small><a rel="nofollow" id="cancel-comment-reply-link" href="javascript:void(0)"
                                              style="display: none;">{{ ___('Cancel reply') }}</a></small>
                                </h3>

                                <div>
                                    <form action="{{ route('blog.comment', [$blog->id, $blog->slug]) }}#respond"
                                          method="post" id="commentform"
                                          class="blog-comment-form">
                                        @csrf
                                        <div class="row">
                                            @if(!auth()->check())
                                                <div class="col-xl-6">
                                                    <div class="input-with-icon-left no-border">
                                                        <i class="icon-material-outline-account-circle"></i>
                                                        <input class="input-text with-border" type="text"
                                                               placeholder="{{ ___('Your Name') }} *" name="user_name"
                                                               value="{{ old('user_name') }}" required="">
                                                    </div>
                                                    @error('user_name')
                                                    <span class="status-not-available">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="input-with-icon-left no-border">
                                                        <i class="icon-material-baseline-mail-outline"></i>
                                                        <input class="input-text with-border" type="email"
                                                               placeholder="{{ ___('Your Email') }} *"
                                                               name="user_email" value="{{ old('user_email') }}"
                                                               required>
                                                    </div>
                                                    @error('user_email')
                                                    <span class="status-not-available">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                            @else
                                                <div class="col-md-12">
                                                    <p>{{ ___('You are commenting as:') }}
                                                        <strong>{{ request()->user()->name }}</strong></p>
                                                </div>
                                            @endif
                                            <div class="col-md-12">
                                                <textarea class="with-border" rows="5" id="comment-field" name="comment"
                                                          placeholder="{{ ___('Your comment...') }}"
                                                          required></textarea>

                                                {!! display_captcha() !!}
                                                @error('g-recaptcha-response')
                                                <span class="status-not-available">{{ $message }}</span>
                                                @enderror

                                                <button type="submit" id="submit" name="comment-submit"
                                                        class="button ripple-effect">{{ ___('Submit') }}</button>
                                                <input type="hidden" name="comment_parent" id="comment_parent"
                                                       value="0">
                                                <input type="hidden" name="comment_post_ID" value="{{$blog->id}}"
                                                       id="comment_post_ID">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="blog-widget">
                                {{ ___('Please login to post a comment.') }}
                            </div>
                        @endif

                    @endif


                </div>

            </div>
            <!-- Inner Content / End -->

            @include($activeTheme.'blog.sidebar')

        </div>
    </div>

    {!! ads_on_top() !!}
    @push('scripts_at_bottom')
        {!! google_captcha() !!}
        <script src="{{ asset($activeThemeAssets.'js/comment-reply.js') }}"></script>
    @endpush
@endsection
