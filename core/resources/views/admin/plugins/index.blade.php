@extends('admin.layouts.main')
@section('title', ___('Plugins'))
@section('header_buttons')
    <a href="#" data-url="{{ route('admin.plugins.create') }}" data-toggle="slidePanel" class="btn btn-primary ms-2"><i class="icon-feather-plus me-2"></i> {{ ___('Upload Plugin') }}</a>
@endsection
@section('content')

    @foreach($plugins as $plugin)
        <div
            class="card d-sm-flex flex-sm-row align-items-sm-center justify-content-between border-0 shadow-sm p-3 p-md-4 mb-4">
            <div class="d-flex align-items-start pe-sm-3">
                <a href="{{$plugin->url}}" target="_blank">
                    @if(!empty($plugin->image))
                        <img src="{{$plugin->image}}" width="80" alt="{{$plugin->name}}" onerror="pluginImage(this)">
                    @else
                        <img src="{{asset('assets/admin/images/plugin.png')}}" width="80" alt="{{$plugin->name}}">
                    @endif
                </a>
                <div class="ps-3 ps-sm-4">
                    <h6 class="mb-2">
                        <a href="{{$plugin->url}}" target="_blank">
                            {{$plugin->name}}
                        </a>
                        @if($plugin->installed)
                            <span class="badge bg-primary ms-1">{{ ___('Installed') }}</span>
                        @endif
                        @if($plugin->enabled)
                            <span class="badge bg-success ms-1">{{ ___('Enabled') }}</span>
                        @endif
                    </h6>
                    <p>
                        {{$plugin->description}}
                    </p>
                    <ul class="p-0 m-0 small">
                        <li class="d-inline-block">
                            <strong>{{ ___('Author') }}</strong>
                            <a href="{{$plugin->author_url}}" target="_blank">{{$plugin->author}}</a>
                        </li>
                        <li class="d-inline-block ms-3">
                            <strong>{{ ___('Version') }}</strong> {{$plugin->version}}
                        </li>
                    </ul>
                    @if(!$plugin->is_compatible)
                        <p class="mb-0 mt-3 text-danger">{!! ___('This plugin is not compatible with the current version of your script. Update your script to version <strong>:VERSION_CODE</strong> or higher.', ['VERSION_CODE' => $plugin->min_app_version]) !!}</p>
                    @endif
                    @if($plugin->update_available)
                        <p class="mb-0 mt-3 text-info">{!! $plugin->update_message !!}</p>
                    @endif
                </div>
            </div>
            <div class="d-flex justify-content-end pt-3 pt-sm-0">
                @if($plugin->installed)
                    @if($plugin->enabled)
                        @if(isset($plugin->settings_route))
                            <a href="{{route($plugin->settings_route)}}" class="btn btn-secondary px-3 px-xl-4 me-3">
                                <i class="far fa-cog fs-xl me-lg-1 me-xl-2"></i>
                                <span class="d-none d-lg-inline">{{ ___('Settings') }}</span>
                            </a>
                        @endif
                        <form action="{{ route('admin.plugins.disable', $plugin->id) }}" method="post">
                            @csrf
                            <button class="btn btn-outline-info px-3 px-xl-4 me-3">
                                <i class="far fa-remove fs-xl me-lg-1 me-xl-2"></i>
                                <span class="d-none d-lg-inline">{{ ___('Disable') }}</span>
                            </button>
                        </form>
                    @else
                        @if($plugin->is_compatible)
                            <form action="{{ route('admin.plugins.enable', $plugin->id) }}" method="post">
                                @csrf
                                <button class="btn btn-primary px-3 px-xl-4 me-3">
                                    <i class="far fa-check-circle fs-xl me-lg-1 me-xl-2"></i>
                                    <span class="d-none d-lg-inline">{{ ___('Enable') }}</span>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.plugins.destroy', $plugin->id) }}" method="post"
                              onsubmit='return confirm("{{___('Are you sure?')}}")'>
                            @method('delete')
                            @csrf
                            <button class="btn btn-outline-danger px-3 px-xl-4">
                                <i class="far fa-trash-alt fs-xl me-lg-1 me-xl-2"></i>
                                <span class="d-none d-lg-inline">{{ ___('Delete') }}</span>
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{$plugin->url}}" class="btn btn-outline-primary px-3 px-xl-4" target="_blank">
                        <i class="far fa-download fs-xl me-lg-1 me-xl-2"></i>
                        <span class="d-none d-lg-inline">{{ ___('Install') }}</span>
                    </a>
                @endif
            </div>
        </div>
    @endforeach

    @push('scripts_at_top')
        <script>
            "use strict";
            var QuickMenu = {"page": "plugins"};

            var pluginImage = function (image) {
                image.src = @json(asset('assets/admin/images/plugin.png'));
                return true;
            }
        </script>
    @endpush
@endsection
