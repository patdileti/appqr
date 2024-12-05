@extends($activeTheme.'layouts.auth')
@section('title', ___('Reset Password'))
@section('content')
    <!-- Welcome Text -->
    <div class="welcome-text">
        <h3>{{ ___('Reset Password') }}</h3>
    </div>
    <form action="{{ route('password.update') }}" method="post">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <span class="status-available">
            <strong>{{ ___('Email address') }} : </strong> {{ $email }}
        </span>
        <input type="hidden" name="email" value="{{ $email }}" readonly />
        <div class="input-with-icon-left">
            <i class="la la-unlock"></i>
            <input type="password" class="input-text with-border" name="password" id="password"
                   placeholder="{{ ___('Password') }}" required/>
        </div>
        <div class="input-with-icon-left">
            <i class="la la-unlock"></i>
            <input type="password" class="input-text with-border" name="password_confirmation" id="password_confirmation"
                   placeholder="{{ ___('Confirm password') }}" required/>
        </div>
        @error('password')
        <span class="status-not-available">{{ $message }}</span>
        @enderror
        {!! display_captcha() !!}
        @error('g-recaptcha-response')
        <span class="status-not-available">{{ $message }}</span>
        @enderror
        <button class="button full-width button-sliding-icon ripple-effect margin-top-10" name="submit" type="submit">{{ ___('Reset') }} <i class="icon-feather-arrow-right"></i></button>
    </form>

@endsection
