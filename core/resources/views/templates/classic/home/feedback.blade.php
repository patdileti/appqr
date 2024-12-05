@extends($activeTheme.'layouts.main')
@section('title', ___('Feedback'))
@section('content')
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ ___('Feedback') }}</h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li>{{ ___('Feedback') }}</li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    {!! ads_on_top() !!}
    <div class="container margin-bottom-50">
        <div class="row">
            <div class="col-xl-8 margin-0-auto">
                <h2 class="margin-bottom-30">{{ ___('Tell us what you think of us') }}</h2>
                <span>{{ ___('We would like to hear your opinions about the website. We would be grateful if you could take the time to fill out this form') }}</span>
                <div class="feed-back-form margin-top-20">
                    <form method="post" action="{{ route('feedback') }}">
                        @csrf
                        <div class="submit-field">
                            <h5>{{ ___('Your Name') }} *</h5>
                            <input type="text" class="with-border" name="name" required="">
                            @error('name')
                            <span class="status-not-available">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="submit-field">
                            <h5>{{ ___('Email Address') }} *</h5>
                            <input type="text" class="with-border" name="email" required="">
                            @error('email')
                            <span class="status-not-available">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="submit-field">
                            <h5>{{ ___('Phone Number') }}</h5>
                            <input type="text" class="with-border" name="phone">
                            @error('phone')
                            <span class="status-not-available">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="submit-field">
                            <h5>{{ ___('Subject') }} *</h5>
                            <input type="text" class="with-border" name="subject" required="">
                            @error('subject')
                            <span class="status-not-available">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="submit-field">
                            <h5>{{ ___('Is there anything you would like to tell us?') }} *</h5>
                            <textarea type="text" class="with-border" name="message" required=""></textarea>
                            @error('message')
                            <span class="status-not-available">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="submit-field">
                            {!! display_captcha() !!}
                            @error('g-recaptcha-response')
                            <span class="status-not-available">{{ $message }}</span>
                            @enderror
                        </div>
                        <input type="submit" name="Submit" class="button" value="{{ ___('Submit') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

    {!! ads_on_bottom() !!}
    @push('scripts_at_bottom')
        {!! google_captcha() !!}
    @endpush
@endsection
