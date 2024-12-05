<div class="quick-card card">
    <div class="card-body">
        <div class="user-avatar-section mb-2">
            <div class="d-flex align-items-center flex-column">
                <img id="filePreview" class="img-fluid rounded my-3"
                     src="{{ asset('storage/profile/'.$user->image) }}" height="110" width="110"
                     alt="User avatar">
                <div class="user-info text-center mt-2">
                    <h4 class="mb-2">{{ $user->name }}</h4>
                </div>
            </div>
        </div>
    </div>
    <ul class="custom-list-group list-group list-group-flush border-top">
        <li class="list-group-item d-flex justify-content-between"><span>{{ ___('Username') }} :</span>
            <strong>{{ $user->username }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between"><span>{{ ___('Email') }} :</span>
            <strong>{{ $user->email }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between"><span>{{ ___('Status') }} :</span>
            @if ($user->status == "0")
                <span class="badge bg-info">{{___('Active')}}</span>
            @elseif ($user->status == "1")
                <span class="badge bg-success">{{___('Verify')}}</span>
            @elseif ($user->status == "2")
                <span class="badge bg-danger">{{___('Banned')}}</span>
            @endif
        </li>
        <li class="list-group-item d-flex justify-content-between"><span>{{ ___('Email Verify') }} :</span>
            @if ($user->email_verified_at)
                <span class="badge bg-success">{{___('Verified')}}</span>
            @else
                <span class="badge bg-warning">{{___('Unverified')}}</span>
            @endif
        </li>
        <li class="list-group-item d-flex justify-content-between"><span>{{ ___('Last Active') }} :</span>
            <strong>{{ date_formating($user->lastactive) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between"><span>{{ ___('Joined at') }} :</span>
            <strong>{{ date_formating($user->created_at) }}</strong>
        </li>
    </ul>
</div>
@php $plan = $user->plan(); @endphp
<div class="quick-card card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">{{$plan->name}}</h4>
            @if($user->upgrade && $user->upgrade->interval)
                <span class="badge bg-primary">{{plan_interval_text($user->upgrade->interval)}}</span>
            @endif
        </div>
        <ul class="mb-0 list-unstyled">
            <li class="mb-2">
                {!! ___(':plan_category_limit Menu Categories', ['plan_category_limit' => '<strong>' . ($plan->settings->category_limit == 999 ? ___('Unlimited') : number_format($plan->settings->category_limit)) . '</strong>']) !!}
            </li>
            <li class="mb-2">
                {!! ___(':plan_menu_limit Menu Items Per Category', ['plan_menu_limit' => '<strong>' . ($plan->settings->menu_limit == 999 ? ___('Unlimited') : number_format($plan->settings->menu_limit)) . '</strong>']) !!}
            </li>
            <li class="mb-2">
                {!! ___(':plan_scan_limit Scans Per Month', ['plan_scan_limit' => '<strong>' . ($plan->settings->scan_limit == 999 ? ___('Unlimited') : number_format($plan->settings->scan_limit)) . '</strong>']) !!}
            </li>
            <li class="mb-2">
                @if ($plan->settings->allow_ordering)
                    <span class="icon-text yes text-success"><i
                            class="icon-feather-check-circle margin-right-2"></i></span>
                @else
                    <span class="icon-text no text-danger"><i class="icon-feather-x-circle margin-right-2"></i></span>
                @endif
                {{ ___('Allow restaurants to accept orders') }}
            </li>
            <li class="mb-2">
                @if (@$plan->settings->hide_branding)
                    <span class="icon-text yes text-success"><i
                            class="icon-feather-check-circle margin-right-2"></i></span>
                @else
                    <span class="icon-text no text-danger"><i class="icon-feather-x-circle margin-right-2"></i></span>
                @endif
                {{ ___('Hide QuickQR Branding') }}
            </li>
            <li class="mb-2">
                @if (!@$plan->settings->advertisements)
                    <span class="icon-text yes text-success"><i
                            class="icon-feather-check-circle margin-right-2"></i></span>
                @else
                    <span class="icon-text no text-danger"><i class="icon-feather-x-circle margin-right-2"></i></span>
                @endif
                {{ ___('No Advertisements') }}
            </li>

            @if (!empty($plan->settings->custom_features))
                @foreach ($plan->settings->custom_features as $key => $value)
                    @php $planoption = plan_option($key) @endphp
                    @if($planoption)
                        <li class="mb-2">
                            @if ($value)
                                <span class="icon-text yes text-success"><i
                                        class="icon-feather-check-circle margin-right-2"></i></span>
                            @else
                                <span class="icon-text no text-danger"><i
                                        class="icon-feather-x-circle margin-right-2"></i></span>
                            @endif

                            {{ !empty($planoption->translations->{get_lang()}->title)
                                ? $planoption->translations->{get_lang()}->title
                                : $planoption->title }}
                        </li>
                    @endif
                @endforeach
            @endif
        </ul>
        <button class="btn btn-primary w-100 mt-3 quick-open-slide-panel" type="button"
                data-panel-id="change-plan-panel"><i class="icon-feather-gift me-1"></i> {{___('Change Plan')}}</button>
    </div>
</div>
<div id="change-plan-panel"
     class="slidePanel slidePanel-right">
    <div class="slidePanel-scrollable">
        <div>
            <div class="slidePanel-content">
                <header class="slidePanel-header">
                    <div class="slidePanel-overlay-panel">
                        <div class="slidePanel-heading">
                            <h2>{{___('Change Plan')}}</h2>
                        </div>
                        <div class="slidePanel-actions">
                            <button form="slidepanel-inner-form" class="btn btn-icon btn-primary"
                                    title="{{___('Save')}}">
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
                    <form id="slidepanel-inner-form" action="{{ route('admin.users.plan', $user->id) }}"
                          method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{___('Plan')}}</label>
                            <select class="form-control" name="plan">
                                <option value="free" @if($plan->id == 'free') selected @endif>{{___('Free')}}</option>
                                <option value="trial"
                                        @if($plan->id == 'trial') selected @endif>{{___('Trial')}}</option>

                                @foreach($plans as $row)
                                    <option value="{{ $row->id }}"
                                            @if($plan->id == $row->id) selected @endif>{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{___('Trial Done')}}</label>
                            <select class="form-control" name="package_trial_done">
                                <option value="1"
                                        @if(user_options($user->id, 'package_trial_done')) selected @endif>{{___('Yes')}}</option>
                                <option value="0"
                                        @if(!user_options($user->id, 'package_trial_done')) selected @endif>{{___('No')}}</option>
                            </select>
                        </div>

                        <div class="mb-3 plan_expiration_date">
                            <label class="form-label" for="id_exdate">{{___('Expiration Date')}}</label>
                            <input id="id_exdate" type="date" class="form-control" name="plan_expiration_date"
                                   value="{{ $user->upgrade ? date_formating($user->upgrade->upgrade_expires, 'Y-m-d') : date('Y-m-d') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts_vendor')
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
@endpush
@push('scripts_at_bottom')
    <script>
        $('[name="plan"]').on('change', function () {
            if ($(this).val() == 'free') {
                $('.plan_expiration_date').slideUp();
            } else {
                $('.plan_expiration_date').slideDown();
            }
        }).trigger('change');
    </script>
@endpush
