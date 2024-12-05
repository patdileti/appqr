@extends('post_templates.layout')

@push('style_at_top')
    <link rel="stylesheet" href="{{ asset('assets/templates/classic/css/style.css?ver='.config('appinfo.version')) }}">
    <link rel="stylesheet" href="{{ asset('assets/templates/classic/css/color.css?ver='.config('appinfo.version')) }}">

    @if(current_language()->direction == 'rtl')
        <link rel="stylesheet"
              href="{{ asset('assets/templates/classic/css/rtl.css?ver='.config('appinfo.version')) }}">
    @endif
@endpush

@section('content')
    <div class="single-page-header restaurant-header detail-header padding-top-30 padding-bottom-30 margin-bottom-30"
         data-background-image="{{ asset('storage/restaurant/cover/'.$post->cover_image) }}">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="single-page-header-inner">
                        <div class="left-side d-flex">
                            <div class="header-image">
                                <img class="lazy-load"
                                     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                     data-original="{{ asset('storage/restaurant/logo/'.$post->main_image) }}"
                                     alt="{{$post->title}}">
                            </div>
                            <div class="header-details margin-left-15">
                                <h3>{{$post->title}}<span>{{$post->sub_title}}</span></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Content -->
            <div class="col-xl-12">
                <form id="subscribeForm" action="{{ route('payment.pay', $transaction->id) }}" method="POST">
                    @csrf

                    <h3 class="margin-bottom-30">{{___('Payment Method')}}</h3>

                    @if(\Session::get('quick_alert_message'))
                    <div class="notification error margin-bottom-30">
                        {{ \Session::get('quick_alert_message') }}
                    </div>
                    @endif

                    @if(!empty($paymentGateways))
                        <div class="payment">
                            @foreach ($paymentGateways as $paymentGateway)
                                <div
                                    class="payment-tab @if(old('payment_method') == $paymentGateway->id) payment-tab-active @endif">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method" id="{{ $paymentGateway->payment_folder }}"
                                               type="radio" value="{{ $paymentGateway->id }}"
                                               data-name="{{ $paymentGateway->payment_folder }}"
                                               @if(old('payment_method') == $paymentGateway->id) checked @endif>
                                        <label
                                            for="{{ $paymentGateway->payment_folder }}">{{ $paymentGateway->payment_title }}</label>
                                        <img class="payment-logo {{ $paymentGateway->payment_folder }}"
                                             src="{{ asset('storage/payment_gateways/'.$paymentGateway->payment_folder.'.png') }}"
                                             alt="{{ $paymentGateway->payment_title }}">
                                    </div>
                                    <div class="payment-tab-content">
                                        @if($paymentGateway->payment_folder == 'paystack')
                                        <div class="row payment-form-row">
                                            <div class="col-6 pull-right">
                                                <div class="card-label form-group">
                                                    <input
                                                        type="email"
                                                        class="form-control"
                                                        name="email"
                                                        id="email"
                                                        placeholder="{{___('Email')}}"
                                                        value="{{ old('email') }}"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                            <p>{{ ___('You will be redirected to the payment page for complete payment.') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" name="payment_submit"
                                class="button big ripple-effect margin-top-40 margin-bottom-65 subscribeNow"
                                id="subscribeNow">{{ ___('Submit') }}</button>
                    @else
                        <div class="notification error">
                            {{ ___('No payment methods available right now please try again later.') }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts_at_bottom')
    <script>
        if (!$("input[name='payment_method']:checked").length) {
            $('.payment-tab').first().addClass('payment-tab-active');
            $('[name=payment_method]').first().prop('checked', true).trigger('change');
        }
    </script>
    <script src="{{ asset('assets/templates/classic/js/chosen.min.js') }}"></script>
    <script src="{{ asset('assets/templates/classic/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/templates/classic/js/bootstrap-slider.min.js') }}"></script>
    <script src="{{ asset('assets/templates/classic/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/templates/classic/js/snackbar.js') }}"></script>
    <script src="{{ asset('assets/templates/classic/js/magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/templates/classic/js/slick.min.js') }}"></script>
    <script src="{{ asset('assets/templates/classic/js/custom.js?ver='.config('appinfo.version')) }}"></script>
@endpush
