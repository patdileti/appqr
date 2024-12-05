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
                                            <span class="text-capitalize text-muted">({{ plan_interval_text($transaction->frequency) }})</span></td>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="button ripple-effect full-width" onclick="midetrans_paynow()">{{ ___('Pay Now') }}</button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ $cancel_url }}"
                                       class="button gray ripple-effect-dark full-width">{{ ___('Cancel Payment') }}</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </div>
    @push('scripts_at_bottom')
        <script src="{{ $url }}" data-client-key="{{ $mt_client_key }}"></script>
        <script type="text/javascript">
            let midetrans_paynow = function () {
                // SnapToken acquired from previous step
                snap.pay(@json($snapToken), {
                    // Optional
                    onSuccess: function (result) {
                        //console.log(result);
                        window.location = @json($return_url);
                    },
                    // Optional
                    onPending: function (result) {
                        window.location = @json($return_url);
                    },
                    // Optional
                    onError: function (result) {
                        window.location = @json($cancel_url);
                    },
                    // Optional
                    onClose: function (result) {
                        window.location = @json($cancel_url);
                    }
                });
            };
        </script>
    @endpush

@endsection

