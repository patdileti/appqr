@extends($activeTheme.'layouts.main')
@section('title', ___('Checkout'))
@section('content')

    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{___('Checkout')}}</h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li>{{___('Checkout')}}</li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                @if (is_numeric($plan->id))
                    @if (!$coupon)
                        <div class="js-accordion margin-bottom-40">
                            <div class="dashboard-box js-accordion-item">
                                <!-- Headline -->
                                <div class="headline js-accordion-header">
                                    <h3>
                                        <i class="icon-feather-gift"></i> {{ ___('Have a coupon? Click here to enter your code') }}
                                    </h3>
                                </div>

                                <div class="content with-padding js-accordion-body" style="display: none">
                                    <p>{{ ___('If you have a coupon code, please apply it below.') }}</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <form
                                                action="{{ request()->fullUrl() }}"
                                                method="POST">
                                                @csrf
                                                <div class="d-flex">
                                                    <input type="text" name="coupon_code"
                                                           class="with-border margin-bottom-0"
                                                           placeholder="{{ ___('Enter coupon code') }}"
                                                           value="{{ old('coupon_code') }}" required>
                                                    <button type="submit"
                                                            class="button margin-left-10"><i class="fa fa-send"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div
                            class="d-flex justify-content-between align-items-center notification notice margin-bottom-40">
                                <span>
                                    <i class="icon-feather-gift"></i> {!! ___('Coupon code :coupon_code Applied', ['coupon_code' => '<strong>'.$coupon->code.'</strong>']) !!}
                                </span>
                            <a href="">{{ ___('Remove Coupon') }}</a>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="row">

            <div class="col-xl-8 col-lg-8 content-right-offset">
                <form id="subscribeForm" action="{{ request()->fullUrl() }}" method="POST">
                    @csrf
                    @if($coupon)
                        <input type="hidden" name="coupon_code" value="{{ $coupon->code }}">
                    @endif

                    <h3>{{ ___('Billing details') }}</h3>
                    <div class="margin-top-20 margin-bottom-30">
                        <div class="submit-field">
                            <label class="form-label">{{ ___('Type') }}</label>
                            <select name="type" id="billing_details_type" class="with-border selectpicker"
                                    required>
                                <option value="personal"
                                        @if((old('type') ?? @$user_options->billing_details_type) == "personal") selected @endif>{{ ___('Personal') }}</option>
                                <option value="business"
                                        @if((old('type') ?? @$user_options->billing_details_type) == "business") selected @endif>{{ ___('Business') }}</option>
                            </select>
                        </div>
                        <div class="submit-field billing-tax-id">
                            <label class="form-label">
                                @if(@$settings->invoice_admin_tax_type)
                                    {{ $settings->invoice_admin_tax_type }}
                                @else
                                    {{ ___("Tax ID") }}
                                @endif
                            </label>
                            <input type="text" id="tax_id" name="tax_id" class="with-border"
                                   value="{{ @$user_options->billing_tax_id }}">
                        </div>
                        <div>
                            <label class="form-label">{{ ___('Name') }} *</label>
                            <input type="text" name="name" class="with-border" value="{{ old('address') ?? @$user_options->billing_name }}" required>
                        </div>
                        <div>
                            <label class="form-label">{{ ___('Address') }} <span
                                    class="required">*</span></label>
                            <input type="text" name="address" class="with-border"
                                   value="{{ old('address') ?? @$user_options->billing_address }}" required>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ ___('City') }} <span
                                            class="required">*</span></label>
                                    <input type="text" name="city" class="with-border"
                                           value="{{ old('city') ?? @$user_options->billing_city }}" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ ___('State') }} <span
                                            class="required">*</span></label>
                                    <input type="text" name="state" class="with-border"
                                           value="{{ old('state') ?? @$user_options->billing_state }}" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ ___('Postal code') }} <span
                                            class="required">*</span></label>
                                    <input type="text" name="zip" class="with-border"
                                           value="{{ old('zip') ?? @$user_options->billing_zipcode }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="submit-field">
                            <label class="form-label">{{ ___('Country') }} <span
                                    class="required">*</span></label>
                            <select name="country" class="with-border selectpicker" data-live-search="true" required>
                                @foreach (countries() as $country)
                                    <option value="{{ $country->code }}"
                                        {{ $country->code == (old('country') ?? @$user_options->billing_country) ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- / Billing address-->

                    @if($price > 0)
                        <h3>{{___('Payment Method')}}</h3>
                        <div class="payment margin-top-30">
                            @forelse ($paymentGateways as $paymentGateway)
                                <div class="payment-tab @if(old('payment_method') == $paymentGateway->id) payment-tab-active @endif">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method" id="{{ $paymentGateway->payment_folder }}"
                                               type="radio" value="{{ $paymentGateway->id }}"
                                               data-name="{{ $paymentGateway->payment_folder }}" @if(old('payment_method') == $paymentGateway->id) checked @endif>
                                        <label
                                            for="{{ $paymentGateway->payment_folder }}">{{ $paymentGateway->payment_title }}</label>
                                        <img class="payment-logo {{ $paymentGateway->payment_folder }}"
                                             src="{{ asset('storage/payment_gateways/'.$paymentGateway->payment_folder.'.png') }}"
                                             alt="{{ $paymentGateway->payment_title }}">
                                    </div>
                                    <div class="payment-tab-content">
                                            <p>{{ ___('You will be redirected to the payment page for complete payment.') }}</p>

                                        {{-- One time and Recurring --}}
                                        @foreach(['paypal', 'stripe'] as $gateway)
                                            @if($paymentGateway->payment_folder == $gateway)
                                                @if(@$settings->{$gateway.'_payment_mode'} == "both")
                                                    <h4 class="margin-bottom-10">{{___('Payment Type')}}</h4>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="radio">
                                                                <input id="one-time-{{$gateway}}" name="payment_mode"
                                                                       type="radio"
                                                                       value="one_time">
                                                                <label for="one-time-{{$gateway}}"><span
                                                                        class="radio-label"></span> {{___('One Time Payment')}}
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="radio">
                                                                <input id="recurring-{{$gateway}}" name="payment_mode"
                                                                       type="radio"
                                                                       value="recurring">
                                                                <label for="recurring-{{$gateway}}"><span
                                                                        class="radio-label"></span> {{___('Recurring Payment')}}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach

                                        @if($paymentGateway->payment_folder == 'wire_transfer')
                                            <div class="quickad-template">
                                                <table class="default-table table-alt-row PaymentMethod-infoTable">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <h4 class="PaymentMethod-heading margin-bottom-10">
                                                                <strong>{{ ___('Bank Account details') }}</strong></h4>
                                                            <span
                                                                class="PaymentMethod-info">{!! @$settings->company_bank_info !!}</span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <h4 class="PaymentMethod-heading">
                                                                <strong>{{ ___('Amount to send') }}</strong>
                                                            </h4>
                                                            <span
                                                                class="PaymentMethod-info">{{ price_symbol_format($price) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h4 class="PaymentMethod-heading margin-top-10">
                                                                <strong>{{ ___('Reference') }}</strong></h4>
                                                            <span class="PaymentMethod-info">
                                                                {{ ___('Membership Plan') }} : {{ $plan->name }}
                                                                @if($interval)
                                                                    <span
                                                                        class="text-muted text-capitalize">({{plan_interval_text($interval)}})</span>
                                                                @endif
                                                            <br>
                                                                {{ ___('Username') }}: {{ request()->user()->username }}<br>
                                                                <em><small>{{ ___('Include a note with Reference so that we know which account to credit.') }}</small></em>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="notification error">
                                    {{ ___('No payment methods available right now please try again later.') }}
                                </div>
                            @endforelse
                        </div>

                        <button type="submit" name="payment_submit"
                                class="button big ripple-effect margin-top-40 margin-bottom-65 subscribeNow"
                                id="subscribeNow">{{ ___('Submit') }}</button>
                    @else
                        <button type="submit" name="payment_submit"
                                class="button big ripple-effect margin-top-40 margin-bottom-65 subscribeNow"
                                id="subscribeNow">{{ ___('Continue') }}</button>
                    @endif
                </form>
            </div>
            <div class="col-xl-4 col-lg-4 margin-top-0 margin-bottom-60">
                <div class="boxed-widget summary margin-top-0">
                    <div class="boxed-widget-headline">
                        <h3>{{ ___('Order Summary') }}</h3>
                    </div>
                    <div class="boxed-widget-inner">
                        <ul>
                            <li>{{ ___('Membership') }}
                                <span>
                                    {{ !empty($plan->translations->{get_lang()}->name)
                                        ? $plan->translations->{get_lang()}->name
                                        : $plan->name }}
                                    @if($interval)
                                        <span
                                            class="text-muted text-capitalize">({{plan_interval_text($interval)}})</span>
                                    @endif
                                </span>
                            </li>
                            <li>{{ ___('Start Date') }} <span>{{ $planStartDate }}</span></li>
                            <li>{{ ___('Expiry Date') }} <span>{{ $planEndDate }}</span></li>
                            <li class="total-costs"></li>
                            <li>{{ ___('Plan Fee') }} <span>{{ price_symbol_format($base_amount) }}</span></li>
                            @if ($coupon)
                                <li class="text-danger">{{ ___('Discount') }}
                                    <span
                                        class="text-danger">-{{ price_symbol_format($base_amount-$price_after_discount) }}</span>
                                </li>
                                <li class="total-costs"></li>
                                <li>{{ ___('Subtotal') }} <span>{{ price_symbol_format($price_after_discount) }}</span>
                                </li>
                            @endif

                            @if(!empty($applied_taxes))
                                <li class="total-costs"></li>
                            @endif

                            @foreach($applied_taxes as $tax)
                                <li>
                                    {{$tax->name}} <i class="fa fa-question-circle"
                                                      title="{{$tax->description}}"
                                                      data-tippy-placement="top"></i>
                                    <span>{{$tax->type != 'inclusive' ? '+' : ''}} {{ $tax['value_type'] == 'percentage' ? (float) $tax['value'] .'%' : price_symbol_format($tax['value']) }}</span>
                                    <small
                                        class="d-block">{{$tax->type == 'inclusive' ? ___("Inclusive") : ___("Exclusive")}}</small>
                                </li>
                            @endforeach

                            <li class="total-costs">{{ ___('Total Cost') }} <span>{{ price_code_format($price) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts_at_bottom')
        <script>
            $('#billing_details_type').on('change', function () {

                if ($(this).val() == 'business')
                    $('.billing-tax-id').slideDown();
                else
                    $('.billing-tax-id').slideUp();
            }).trigger('change');

            $('[name=payment_method]').on('change', function () {

                var $radio = $(this).parents('.payment-tab').find('[name=payment_mode]');
                if ($radio.length) {
                    $radio.first().prop('checked', true);
                }
            });
            $('[name=payment_method]').first().trigger('change');

            if (!$("input[name='payment_method']:checked").length) {
                $('.payment-tab').first().addClass('payment-tab-active');
                $('[name=payment_method]').first().prop('checked', true).trigger('change');
            }
        </script>
    @endpush
@endsection
