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
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{ route('ipn', 'paystack') }}" method="POST">
                                    @csrf
                                    <button type="button" class="button ripple-effect full-width"
                                            id="btn-confirm">{{ ___('Pay Now') }}</button>
                                    <script src="//js.paystack.co/v1/inline.js"
                                            @foreach($details as $key => $value)
                                                data-{{ $key }}="{{ $value }}"
                                            @endforeach
                                            data-custom-button="btn-confirm"
                                    >
                                    </script>
                                </form>
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
@endsection

