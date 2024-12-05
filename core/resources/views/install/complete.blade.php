@extends('install.layout')
@section('content')
<div class="quick-card card">
    <div class="card-header">
        <h5 class="text-center mb-0">{{ ___('Installation Finish') }}</h5>
    </div>
    <div class="card-body">
        <h5 class="text-center">{{ ___('Installed') }}</h5>
        <p class="text-center text-muted mb-3">{!! ___(':APP_NAME has been installed.', ['APP_NAME' => '<span class="font-weight-medium">'.config('appinfo.name').'</span>']) !!}</p>
        @if(request()->get('admin_username'))
            <p class="text-center mb-3">
                {{ ___('Admin Username') }} <strong>{{request()->get('admin_username')}}</strong>
            </p>
        @endif
        <div class="text-center">
            <a href="{{ route('home') }}" class="btn btn-primary">{{ ___('Go to Home') }}</a>
        </div>
    </div>
</div>

@endsection
