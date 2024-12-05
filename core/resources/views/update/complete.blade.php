@extends('update.layout')
@section('content')
<div class="quick-card card">
    <div class="card-header">
        <h5 class="text-center mb-0">Update Finish</h5>
    </div>
    <div class="card-body">
        <h5 class="text-center">Update (V {{ config('appinfo.version') }})</h5>
        <p class="text-center text-muted mb-3">{{ config('appinfo.name') }} is updated to version {{ config('appinfo.version') }}.</p>
        <div class="text-center">
            <a href="{{ route('home') }}" class="btn btn-primary">{{ ___('Go to Home') }}</a>
        </div>
    </div>
</div>

@endsection
