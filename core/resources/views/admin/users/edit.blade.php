@extends('admin.layouts.main')
@section('title', ___('Edit User'))
@section('content')
    <div class="row">
        <div class="col-lg-4 order-1 order-lg-0">
            @include('admin.users.userdetails')
        </div>
        <div class="col-lg-8 order-0 order-lg-1">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i
                            class="icon-feather-user me-2"></i>{{ ___('Account details') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.password', $user->id) }}"><i
                            class="icon-feather-lock me-2"></i>{{ ___('Password') }}</a></li>
            </ul>

            <form id="user-avatar-delete" class="d-none"
                  action="{{ route('admin.users.deleteAvatar', $user->id) }}" method="POST"
                  onsubmit='return confirm("{{___('Are you sure?').' \n' . ___('It will refresh the page.')}}")'>
                @csrf
                @method('DELETE')
            </form>

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="quick-card card">
                    <div class="card-header">
                        <h5>{{ ___('Account details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <img src="{{ asset('storage/profile/'.$user->image) }}" alt="user-avatar"
                                 class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-2" tabindex="0">
                                    <i class="fas fa-upload"></i>
                                    <span class="d-none d-sm-block ms-2">{{ ___('Upload new photo') }}</span>
                                    <input name="avatar" type="file" id="upload" hidden
                                           onchange="readURL(this,'uploadedAvatar')"
                                           accept="image/png, image/jpeg">
                                </label>
                                <button form="user-avatar-delete" class="btn btn-label-danger mb-2"><i
                                        class="fas fa-close"></i><span
                                        class="d-none d-sm-block ms-2">{{ ___('Remove') }}</span></button>

                                <p class="text-muted mb-0">{{ ___('Allowed JPG, JPEG or PNG.') }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ ___('Fullname') }} *</label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ $user->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ ___('Username') }} *</label>
                                    <input type="text" name="username" class="form-control"
                                           value="{{ $user->username }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ ___('Email Address') }} *</label>
                                    <div class="input-group mb-3">
                                        <input type="email" name="email" class="form-control"
                                               value="{{ $user->email }}" required>
                                        <button class="btn btn-primary quick-open-slide-panel" type="button"
                                                title="{{ ___('Send Email') }}" data-tippy-placement="top"
                                                data-panel-id="send-email-panel"><i
                                                class="far fa-send"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ ___('User Type') }}</label>
                                    <select name="user_type" class="form-control" required>
                                        <option
                                            value="user" {{ ($user->user_type == 'user') ? "selected" : "" }}>{{ ___('User') }}</option>
                                        <option
                                            value="admin" {{ ($user->user_type == 'admin') ? "selected" : "" }}>{{ ___('Admin') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ ___('Account status') }} *</label>
                                    <select name="status" class="form-control" required>
                                        <option value="0" {{ ($user->status == '0') ? "selected" : "" }}>{{ ___('Ban') }}</option>
                                        <option value="1" {{ ($user->status == '1') ? "selected" : "" }}>{{ ___('Active') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ ___('Email status') }} *</label>
                                    <select name="email_status" class="form-control" required>
                                        <option value="0">{{ ___('Unverified') }}</option>
                                        <option value="1" {{ (!is_null($user->email_verified_at)) ? "selected" : "" }}>{{ ___('Verified') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="">
                                    <label class="form-label">{{ ___('Country') }} *</label>
                                    <select name="country" class="form-control" required>
                                        <option value="" selected disabled>{{ ___('Choose') }}</option>
                                        @foreach (countries() as $country)
                                            <option value="{{ $country->id }}"
                                                    @if ($country->code == @$user->country_code) selected @endif>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary">{{ ___('Save Changes') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="send-email-panel"
         class="slidePanel slidePanel-right">
        <div class="slidePanel-scrollable">
            <div>
                <div class="slidePanel-content">
                    <header class="slidePanel-header">
                        <div class="slidePanel-overlay-panel">
                            <div class="slidePanel-heading">
                                <h2>{{ ___('Send Mail to') }} {{ $user->email }}</h2>
                            </div>
                            <div class="slidePanel-actions">
                                <button form="slidepanel-inner-form" class="btn btn-icon btn-primary"
                                        title="{{___('Send')}}">
                                    <i class="icon-feather-check"></i>
                                </button>
                                <button class="btn btn-icon btn-default slidePanel-close"
                                        title="{{___('Close')}}">
                                    <i class="icon-feather-x"></i>
                                </button>
                            </div>
                        </div>
                    </header>
                    <div class="slidePanel-inner">
                        <form id="slidepanel-inner-form" action="{{ route('admin.users.sendmail', $user->id) }}"
                              method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ ___('Subject') }} *</label>
                                        <input type="subject" name="subject" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ ___('Reply to') }} *</label>
                                        <input type="email" name="reply_to" class="form-control"
                                               value="{{ request()->user()->email }}" required>
                                    </div>
                                </div>
                            </div>
                            <textarea name="message" rows="10" class="form-control tiny-editor"></textarea>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts_vendor')
        <script src="{{ asset('assets/admin/plugins/tinymce/tinymce.min.js') }}"></script>
    @endpush
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "users"};
        </script>
    @endpush
@endsection
