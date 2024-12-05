@extends($activeTheme.'layouts.auth')
@section('title', ___('Reset Password'))
@section('content')
    <!-- Welcome Text -->
    <div class="welcome-text">
        <h3>{{ ___('Reset Password') }}</h3>
    </div>
    @error('email')
    <span class="status-not-available">{{ $message }}</span>
    @enderror
    <form action="{{ route('password.email') }}" method="post">
        @csrf
        <div class="input-with-icon-left">
            <i class="la la-envelope"></i>
            <input type="email" class="input-text with-border" name="email" id="email"
                   placeholder="{{ ___('Email address') }}" value="{{ old('email') }}" required/>
        </div>
        {!! display_captcha() !!}
        @error('g-recaptcha-response')
        <span class="status-not-available">{{ $message }}</span>
        @enderror
        <button class="button full-width button-sliding-icon ripple-effect margin-top-10" name="submit"
                type="submit">{{ ___('Reset') }} <i class="icon-feather-arrow-right"></i>
        </button>
    </form>
@endsection
