<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Transaction Details') }}</h2>
            </div>
            <div class="slidePanel-actions">
                <button class="btn btn-default btn-icon slidePanel-close" title="{{ ___('Close') }}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <div class="card mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <span>{{ ___('Title') }}</span>
                    <span>
                        {{ $title }}
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <span>{{ ___('Price') }}</span>
                    <strong>{{ price_symbol_format($transaction->base_amount ?? $transaction->amount) }}</strong>
                </li>
                @php
                    $discount = 0;
                @endphp
                @if ($transaction->coupon)
                    @php
                        $discount = ($transaction->base_amount * $transaction->coupon->percentage) / 100;
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <span>
                            {{___('Discount')}} ({{ $transaction->coupon->percentage }}%)
                            <br><small>{{___('Coupon') }}: <strong>{{$transaction->coupon->code}}</strong></small>
                        </span>
                        <span
                            class="text-danger"><strong>- {{ price_symbol_format($discount) }}</strong></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <span>{{ ___('Subtotal') }}</span>
                        <strong>{{ price_symbol_format($transaction->base_amount - $discount) }}</strong>
                    </li>
                @endif

                @foreach($applied_taxes as $tax)
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <span>{{$tax->name}} <small>({{$tax->type == 'inclusive' ? ___("Inclusive") : ___("Exclusive")}})</small><i class="fa fa-info-circle ms-1" title="{{$tax->description}}" data-tippy-placement="top"></i></span>
                        <strong>{{$tax->type != 'inclusive' ? '+' : ''}} {{ price_symbol_format($tax['value']) }}</strong>
                    </li>
                @endforeach

                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <strong class="text-dark">{{ ___('Total') }}
                        @if ($transaction->transaction_gatway)
                            <br>
                            <small>{{___('Paid via')}} {{ ucfirst($transaction->transaction_gatway) }}</small>
                        @endif
                    </strong>
                    <strong class="text-dark">{{ price_symbol_format($transaction->amount) }}</strong>
                </li>
            </ul>
        </div>
    </div>
</div>

