@extends('install.layout')
@section('content')
    <div class="quick-card card">
        <div class="card-header">
            <h5 class="text-center mb-0">{{ ___('Installation') }}</h5>
        </div>
        <div class="card-body text-center">
            <p>
                {{ ___('Welcome! QuickCMS is an custom content management system developed by Bylancer developers.') }}
            </p>
            <p class="fw-semibold mb-40">
                {{ ___('Installation process is very easy and it takes less than 2 minutes!') }}
            </p>

            <a href="{{ route('install.requirements') }}" class="btn btn-primary">{{ ___('Start Installation') }}</a>
        </div>
    </div>
@endsection
