@extends($activeTheme.'layouts.main')
@section('title', ___('Contact Us'))
@section('content')
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ ___('Contact Us') }}</h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li>{{ ___('Contact Us') }}</li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    {!! ads_on_top() !!}
    <div class="container margin-bottom-50">
        <div class="business-info">
            <div class="row">
                <div class="col-sm-8">
                    <div class="contactUs">
                        <h2 class="margin-bottom-30">{{ ___('Contact Us') }}</h2>
                        <form id="contact-form" class="contact-form" name="contact-form" method="post" action="{{ route('contact') }}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" class="with-border" required="required"
                                               placeholder="{{ ___('Your Name') }}" name="name">
                                    </div>
                                    @error('name')
                                    <span class="status-not-available">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="email" class="with-border" required="required"
                                               placeholder="{{ ___('Your E-Mail') }}" name="email">
                                    </div>
                                    @error('email')
                                    <span class="status-not-available">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input type="text" class="with-border" required="required"
                                               placeholder="{{ ___('Subject') }}" name="subject">
                                    </div>
                                    @error('subject')
                                    <span class="status-not-available">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <textarea name="message" id="message" required="required" class="with-border"
                                                  rows="7" placeholder="{{ ___('Message') }}"></textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    {!! display_captcha() !!}
                                    @error('g-recaptcha-response')
                                    <span class="status-not-available">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="Submit" class="button">{{ ___('Send Message') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Enquiry Form-->
                <!-- contact-detail -->
                <div class="col-sm-4">
                    <div class="dashboard-box margin-top-0">
                        <div class="headline">
                            <h3>{{ ___('Get In Touch') }}</h3>
                        </div>
                        <div class="content with-padding">
                            {{ ___('Please get in touch and our expert support team will answer all your questions.') }}
                        </div>
                    </div>
                    <div class="dashboard-box">
                        <div class="headline">
                            <h3>{{ ___('Contact Information') }}</h3>
                        </div>
                        <div class="content with-padding">
                            <ul>
                                @if($settings->contact_address)
                                    <li class="job-property margin-bottom-10"><i
                                            class="la la-map-marker"></i> {{ $settings->contact_address }}</li>
                                @endif
                                @if($settings->contact_phone)
                                    <li class="job-property margin-bottom-10"><i class="la la-phone"></i>
                                        <a href="tel:{{ $settings->contact_phone }}"
                                           rel="nofollow">{{ $settings->contact_phone }}</a></li>
                                @endif
                                @if($settings->contact_email)
                                    <li class="job-property margin-bottom-10"><i class="la la-envelope"></i>
                                        <a href="mailto:{{ $settings->contact_email }}"
                                           rel="nofollow">{{ $settings->contact_email }}</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- contact-detail -->
            </div>
            <!-- row -->
        </div>
    </div>
    {!! ads_on_bottom() !!}
    @push('scripts_at_bottom')
        {!! google_captcha() !!}
    @endpush
@endsection
