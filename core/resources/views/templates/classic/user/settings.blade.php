@extends($activeTheme.'layouts.app')
@section('title', ___('Settings'))
@section('content')

    <div class="dashboard-box margin-top-0">
        <!-- Headline -->
        <div class="headline">
            <h3><i class="icon-feather-settings"></i> {{ ___('Account Setting') }}</h3>
        </div>
        <div class="content with-padding">
            <form method="post" action="{{ route('settings.editProfile') }}" accept-charset="UTF-8" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="submit-field">
                            <h5>{{ ___('Avatar') }}</h5>
                            <div class="d-flex align-items-center">
                                <img id="avatar-img" class="margin-right-20" src="{{ asset('storage/profile/'. $user->image) }}" width="90">
                                <div class="uploadButton">
                                    <input class="uploadButton-input" type="file" accept="image/jpg, image/jpeg, image/png" id="avatar" name="avatar" onchange="readImageURL(this, 'avatar-img');"/>
                                    <label class="uploadButton-button ripple-effect"
                                           for="avatar">{{ ___('Upload Avatar') }}</label>
                                    <span class="uploadButton-file-name">{{ ___('Use 150x150px for better use') }}</span>
                                </div>
                            </div>
                            @error('avatar')<span class='status-not-available'>{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-12">
                        <div class="submit-field">
                            <h5>{{ ___('Username') }} *</h5>
                            <div class="input-with-icon-left">
                                <i class="la la-user"></i>
                                <input type="text" class="with-border" id="username" name="username"
                                       value="{{ $user->username }}" required>
                            </div>
                            @error('username')<span class='status-not-available'>{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-12">
                        <div class="submit-field">
                            <h5>{{ ___('Email address') }} *</h5>
                            <div class="input-with-icon-left">
                                <i class="la la-envelope"></i>
                                <input type="text" class="with-border" id="email" name="email"
                                       value="{{ $user->email }}" required>
                            </div>
                            @error('email')<span class='status-not-available'>{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('New Password') }}</h5>
                            <input type="password" id="new_password" name="new_password" class="with-border" min="6">
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Confirm New Password') }}</h5>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                   class="with-border" min="6">
                        </div>
                    </div>
                </div>
                @error('new_password')<span class='status-not-available'>{{ $message }}</span>@enderror
                <button type="submit" name="submit" class="button ripple-effect">{{ ___('Save Changes') }}</button>
            </form>
        </div>
    </div>
    <div class="dashboard-box">
        <div class="headline">
            <h3><i class="icon-material-outline-description"></i> {{ ___('Billing Details') }}</h3>
        </div>
        <div class="content">
            <div class="content with-padding">
                <div class="notification notice">{{ ___('These details will be used in invoice and payments.') }}</div>
                @if ($errors->billing->any())
                    <span class='status-not-available'>{{ ___('All the fields with * are required') }}</span>
                @endif
                <form method="post" action="{{ route('settings.editBilling') }}" accept-charset="UTF-8">
                    @csrf
                    <div class="submit-field">
                        <h5>{{ ___('Type') }}</h5>
                        @php $billing_details_type = $user_options->billing_details_type ?? 'personal' @endphp
                        <select name="billing_details_type" id="billing_details_type" class="with-border selectpicker"
                                required>
                            <option value="personal" @if($billing_details_type == "personal") selected @endif>{{ ___('Personal') }}</option>
                            <option value="business" @if($billing_details_type == "business") selected @endif>{{ ___('Business') }}</option>
                        </select>
                    </div>
                    <div class="submit-field billing-tax-id">
                        <h5>
                            @if(@$settings->invoice_admin_tax_type)
                                {{ $settings->invoice_admin_tax_type }}
                            @else
                                {{ ___("Tax ID") }}
                            @endif
                        </h5>
                        <input type="text" id="billing_tax_id" name="billing_tax_id" class="with-border"
                               value="{{ $user_options->billing_tax_id ?? '' }}">
                    </div>
                    <div class="submit-field">
                        <h5>{{ ___('Name') }} *</h5>
                        <input type="text" id="billing_name" name="billing_name" class="with-border"
                               value="{{ $user_options->billing_name ?? '' }}" required>
                    </div>
                    <div class="submit-field">
                        <h5>{{ ___('Address') }} *</h5>
                        <input type="text" id="billing_address" name="billing_address" class="with-border"
                               value="{{ $user_options->billing_address ?? '' }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="submit-field">
                                <h5>{{ ___('City') }} *</h5>
                                <input type="text" id="billing_city" name="billing_city" class="with-border"
                                       value="{{ $user_options->billing_city ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="submit-field">
                                <h5>{{ ___('State') }} *</h5>
                                <input type="text" id="billing_state" name="billing_state" class="with-border"
                                       value="{{ $user_options->billing_state ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="submit-field">
                                <h5>{{ ___('Postal code') }} *</h5>
                                <input type="text" id="billing_zipcode" name="billing_zipcode" class="with-border"
                                       value="{{ $user_options->billing_zipcode ?? '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="submit-field">
                        <h5>{{ ___('Country') }} *</h5>
                        <select name="billing_country" id="billing_country" class="with-border selectpicker"
                                data-live-search="true" required>
                            <option value="" disabled selected>{{ ___('Choose') }}</option>
                            @foreach (countries() as $country)
                                <option value="{{ $country->code }}"
                                    {{ $country->code == @$user_options->billing_country ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" name="submit"
                            class="button ripple-effect">{{ ___('Save Changes') }}</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts_at_bottom')
        <script>
            $('#billing_details_type').on('change', function () {

                if($(this).val() == 'business')
                    $('.billing-tax-id').slideDown();
                else
                    $('.billing-tax-id').slideUp();
            }).trigger('change');
        </script>
    @endpush
@endsection

