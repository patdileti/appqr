@extends($transaction->transaction_method == 'order' ? $activeTheme.'layouts.empty' : $activeTheme.'layouts.main')
@section('title', ___('Payment confirm'))
@section('content')
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{___('Payment')}}</h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li>{{___('Payment')}}</li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    <div class="container margin-bottom-50">
        <div class="row">
            <div class="col-md-6 margin-0-auto">
                <h1 class="margin-bottom-30">{{ ___('Payment details') }}</h1>
                <div class="dashboard-box">
                    <div class="content with-padding">
                        <table class="basic-table margin-bottom-20">
                            <tbody>
                            <tr>
                                <td class="p-3"><strong>{{ ___('Title') }}</strong></td>
                                <td class="p-3">{{ $transaction->product_name }}
                                    @if(!empty(@$transaction->frequency))
                                        <span class="text-capitalize text-muted">({{ plan_interval_text($transaction->frequency) }})</span>
                                </td>
                                @endif
                            </tr>
                            <tr>
                                <td class="p-3">
                                    <h5 class="mb-0"><strong>{{ ___('Total') }}</strong></h5>
                                </td>
                                <td class="p-3">
                                    <h5 class="mb-0">
                                        <strong>{{ price_code_format($transaction->amount) }}</strong>
                                    </h5>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <form action="{{ route('ipn', 'two_checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{$transaction->id}}">
                        <!-- CREDIT CARD FORM STARTS HERE -->
                            <div class="row payment-form-row">
                                <div class="col-12">
                                    <div class="card-label form-group">
                                        <input type="text" class="form-control"
                                               name="checkoutCardNumber"
                                               placeholder="{{___('CARD NUMBER')}}"
                                               autocomplete="cc-number" autofocus/>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="card-label form-group">
                                        <input type="tel" class="form-control" name="checkoutCardExpiry"
                                               placeholder="MM / YYYY" autocomplete="cc-exp"
                                               aria-required="true"
                                               aria-invalid="false">
                                    </div>
                                </div>
                                <div class="col-5 pull-right">
                                    <div class="card-label form-group">
                                        <input type="tel" class="form-control" name="checkoutCardCVC"
                                               placeholder="CVV" autocomplete="cc-csc"/>
                                    </div>
                                </div>
                            </div>
                            <!-- CREDIT CARD FORM ENDS HERE -->
                            <div>
                                <small class="form-error"></small>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="button ripple-effect full-width"
                                            id="btn-confirm">{{ ___('Pay Now') }}</button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ $cancel_url }}"
                                       class="button gray ripple-effect-dark full-width">{{ ___('Cancel Payment') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts_at_bottom')
        <script src="{{ asset('assets/global/js/jquery.payment.js') }}"></script>
        <script src="//www.2checkout.com/checkout/api/2co.min.js"></script>
        <script>
            $(document).ready(function () {
                /* Pull in the public encryption key for our environment (2Checkout) */
                TCO.loadPubKey(@json($checkout_public_key));

                /* Fancy restrictive input formatting via jQuery.payment library */
                $('input[name=checkoutCardNumber]').payment('formatCardNumber');
                $('input[name=checkoutCardCVC]').payment('formatCardCVC');
                $('input[name=checkoutCardExpiry]').payment('formatCardExpiry');

                $('#btn-confirm').on('click', function (e) {
                    e.preventDefault();

                    if (validateData()) {

                        var $this = $(this),
                            $form = $this.parents('form');

                        $this.addClass('button-progress').prop('disabled', true);

                        /* Setup token request arguments */
                        var checkoutCardExpiry = $('input[name=checkoutCardExpiry]').payment('cardExpiryVal');

                        var args = {
                            sellerId: @json($checkout_seller_id),
                            publishableKey: @json($checkout_public_key),
                            ccNo: $('input[name=checkoutCardNumber]').val().replace(/\s/g, ''),
                            cvv: $('input[name=checkoutCardCVC]').val(),
                            expMonth: checkoutCardExpiry.month,
                            expYear: checkoutCardExpiry.year
                        };

                        /* Make the token request */
                        TCO.requestToken(function (data) {

                            $('.form-error').hide();

                            /* Set the token as the value for the token input */
                            var checkoutToken = data.response.token.token;
                            $form.append($('<input type="hidden" name="2checkoutToken" />').val(checkoutToken));

                            /* IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop. */
                            $form.submit();

                        }, function (data) {
                            if (data.errorCode === 200) {
                                tokenRequest();
                            } else {
                                $this.removeClass('button-progress').prop('disabled', false);

                                $('.form-error').html(data.errorMsg).show();
                            }
                        }, args);
                    }

                });

                function validateData() {

                    $('.form-error').hide();

                    if (!$.payment.validateCardNumber($('input[name=checkoutCardNumber]').val())) {
                        $('.form-error').html(@json(___('Invalid card number.'))).show();
                        return false;
                    }

                    var expiry = $('input[name=checkoutCardExpiry]').payment('cardExpiryVal');

                    if (!$.payment.validateCardExpiry(expiry.month, expiry.year)) {
                        $('.form-error').html(@json(___('Invalid expiration date.'))).show();
                        return false;
                    }

                    return true;
                }

            });
        </script>
    @endpush
@endsection

