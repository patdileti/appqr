@extends($activeTheme.'layouts.app')
@section('title', ___('All Restaurants'))
@section('content')
    <div class="dashboard-box">
        <div class="headline">
            <h3><i class="far fa-utensils"></i> {{ ___('All Restaurants') }}</h3>
            <a href="{{ route('restaurants.create') }}" class="button ripple-effect"><i
                    class="icon-feather-plus"></i> {{ ___('Add Restaurant') }}</a>
        </div>
        <div class="content">
            <ul class="dashboard-box-list" id="js-table-list">
                @forelse(request()->user()->posts as $post)
                    <li class="ajax-item-listing" data-item-id="{{ $post->id }}">
                        <!-- Job Listing -->
                        <div class="job-listing">
                            <!-- Job Listing Details -->
                            <div class="job-listing-details">
                                <!-- Logo -->
                                <a href="{{ route('publicView', $post->slug) }}" class="job-listing-company-logo">
                                    <img src="{{ asset('storage/restaurant/logo/'.$post->main_image) }}"
                                         alt="{{ $post->title }}">
                                </a>

                                <!-- Details -->
                                <div class="job-listing-description">
                                    <h3 class="job-listing-title">
                                        <a href="{{ route('publicView', $post->slug) }}">{{ $post->title }}</a>
                                    </h3>

                                    <!-- Job Listing Footer -->
                                    <div class="job-listing-footer">
                                        <ul>
                                            <li>
                                                <i class="icon-feather-activity"></i> {{ ___('Scans') }}:
                                                <strong>{{ count($post->views) }}</strong>
                                            </li>
                                            <li>
                                                <i class="icon-feather-calendar"></i> {{ ___('Created') }}:
                                                <strong>{{ date_formating($post->created_at, 'd M, Y') }}</strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="buttons-to-right always-visible">
                            <a href="{{ route('restaurants.menu', $post->id) }}"
                               class="button ripple-effect"><i
                                    class="far fa-file-text"></i> {{ ___('Menus') }}</a>
                            <a href="{{ route('restaurants.qrbuilder', $post->id) }}" target="_blank"
                               class="button gray ripple-effect ico"
                               title="{{ ___('QR Code') }}" data-tippy-placement="top"><i
                                    class="far fa-qrcode"></i></a>
                            <a href="{{ route('publicView', $post->slug) }}" target="_blank"
                               class="button gray ripple-effect ico live-preview-button"
                               title="{{ ___('Public View') }}" data-tippy-placement="top"><i
                                    class="icon-feather-eye"></i></a>
                            <a href="{{ route('restaurants.edit', $post->id) }}"
                               class="button gray ripple-effect ico" title="{{ ___('Edit') }}"
                               data-tippy-placement="top"><i
                                    class="icon-feather-edit"></i></a>
                            @if(is_plugin_enabled('quickorder'))
                                <a href="{{ route('restaurants.whatsappOrdering', $post->id) }}"
                                   class="button whatsapp ripple-effect ico" title="{{ ___('WhatsApp Ordering') }}"
                                   data-tippy-placement="top"><i
                                        class="fab fa-whatsapp"></i></a>
                            @endif
                            <a href="{{ route('restaurants.destroy', $post->id) }}"
                               class="button red ripple-effect ico item-ajax-button"
                               data-alert-message="{{ ___('Are you sure?') }}" data-ajax-action="deleteVCard"
                               title="{{ ___('Delete') }}" data-tippy-placement="top"><i
                                    class="icon-feather-trash-2"></i></a>
                        </div>
                    </li>
                @empty
                    <li class="ajax-item-listing">
                        <!-- Project Listing -->
                        <div class="job-listing width-adjustment">
                            <!-- Project Listing Details -->
                            <div class="job-listing-details">
                                {{ ___('No restaurants available.') }}
                                <a href="{{ route('restaurants.create') }}"
                                   class="margin-left-5">{{ ___('Add New') }}</a>
                            </div>
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
@push('scripts_at_bottom')
    <style>
        a.whatsapp {
            background-color: #25d366 !important;
            color: #fff !important;
        }

        a.whatsapp i {
            font-size: 18px !important;
        }
    </style>
@endpush
