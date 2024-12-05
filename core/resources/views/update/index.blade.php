@extends('update.layout')
@section('content')
    <div class="quick-card card">
        <div class="card-header">
            <h5 class="text-center mb-0">Update (V {{ config('appinfo.version') }})</h5>
        </div>
        <div class="card-body text-center">
            <p class="fw-semibold mb-40">
                The update process might take several minutes, please don't close this browser tab while update is in progress.
            </p>

            <form action="{{ route('update.run') }}" method="post">
                @csrf
                <button type="submit" href="" class="btn btn-primary">Upgrade Now</button>
            </form>

        </div>
    </div>
@endsection
