@extends('install.layout')
@section('content')
    <div class="quick-card card">
        <div class="card-header">
            <h5 class="text-center mb-0">{{ ___('Update Database') }}</h5>
        </div>
        <div class="card-body text-center">
            <p class="fw-semibold mb-4">
                {{ ___('It seems like the database is not empty.') }}
            </p>
            <p class="mb-4">
                {{ ___('Are you updating the script from the old php CMS? If not, then use an empty database.') }}
                {{ ___('If you are updating then please check this article.') }} <a href="https://bylancer.com/docs/other/how-update-quickcms-php-version-quickcms-laravel-version" target="_blank">How to update from QuickCMS PHP version to QuickCMS Laravel version?</a>
            </p>
            <form action="{{route('install.admin')}}">
                <div class="form-check mb-4 d-flex justify-content-center gap-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" required>
                    <label class="form-check-label fw-semibold" for="flexCheckDefault">
                        {{ ___('Yes, I am updating the script.') }}
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">{{ ___('Update') }}</button>
                <a href="{{ route('install.database') }}" class="btn btn-secondary">{{ ___('back') }}</a>
            </form>
        </div>
    </div>
@endsection
