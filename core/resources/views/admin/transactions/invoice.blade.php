<!DOCTYPE html>
<html lang="{{ get_lang() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ ___('Invoice') . ' #'. $transaction->id}}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/logo/'.$settings->site_favicon) }}">
    <meta name="theme-color" content="{{ $settings->theme_color }}">
    <style>
        :root {
            --theme-color: {{ $settings->theme_color }};
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/invoice.css') }}">
</head>
<body>
<!-- Print Button -->
<div class="print-button-container">
    <a href="javascript:window.print()" class="print-button">{{___('Print this invoice')}}</a>
</div>
<!-- Invoice -->
<div id="invoice">
    <!-- Header -->
    <div class="row">
        <div class="col-xl-6">
            <div id="logo">
                <img src="{{ asset('storage/logo/'.$settings->site_logo) }}" alt="{{ @$settings->site_title }}">
            </div>
        </div>
        <div class="col-xl-6">
            <p id="details">
                <strong>{{___('Invoice')}}
                    :</strong> {{config('settings.invoice_nr_prefix') ?? 'INV-'}}{{$transaction->id}} <br>
                <strong>{{___('Date')}}:</strong> {{ date_formating($transaction->created_at) }} </p>
        </div>
    </div>


    <!-- Client & Supplier -->
    <div class="row">
        <div class="col-xl-12">
            <h2>{{___('Invoice')}}</h2>
        </div>
        <div class="col-md-6">
            <h3>{{___('Supplier')}}</h3>
            <p>
                @if(!empty(config('settings.invoice_admin_name')))
                    <strong>{{___('Name')}}</strong> {{config('settings.invoice_admin_name')}}<br>
                @endif
                @if(!empty(config('settings.invoice_admin_address')))
                    <strong>{{___('Address')}}</strong> {{config('settings.invoice_admin_address')}}<br>
                @endif
                @if(!empty(config('settings.invoice_admin_city')))
                    <strong>{{___('City')}}</strong> {{config('settings.invoice_admin_city')}}<br>
                @endif
                @if(!empty(config('settings.invoice_admin_state')))
                    <strong>{{___('State')}}</strong> {{config('settings.invoice_admin_state')}}<br>
                @endif
                @if(!empty(config('settings.invoice_admin_zipcode')))
                    <strong>{{___('Postal Code')}}</strong> {{config('settings.invoice_admin_zipcode')}}<br>
                @endif
                @if(!empty(config('settings.invoice_admin_country')))
                    <strong>{{___('Country')}}</strong> {{config('settings.invoice_admin_country')}}<br>
                @endif
                @if(!empty(config('settings.invoice_admin_tax_type')) && !empty(config('settings.invoice_admin_tax_id')))
                    <strong>{{config('settings.invoice_admin_tax_type')}}</strong> {{config('settings.invoice_admin_tax_id')}}
                    <br>
                @endif
            </p>
        </div>
        <div class="col-md-6">
            <h3>{{___('Customer')}}</h3>
            <p>
                @if(!empty($transaction->billing->name))
                    <strong>{{___('Name')}}</strong> {{ $transaction->billing->name }}<br>
                @endif
                @if(!empty($transaction->billing->address))
                    <strong>{{___('Address')}}</strong> {{ $transaction->billing->address }}<br>
                @endif
                @if(!empty($transaction->billing->city))
                    <strong>{{___('City')}}</strong> {{ $transaction->billing->city }}<br>
                @endif
                @if(!empty($transaction->billing->state))
                    <strong>{{___('State')}}</strong> {{ $transaction->billing->state }}<br>
                @endif
                @if(!empty($transaction->billing->zipcode))
                    <strong>{{___('Postal Code')}}</strong> {{ $transaction->billing->zipcode }}<br>
                @endif
                @if(!empty($transaction->billing->country))
                    <strong>{{___('Country')}}</strong> {{ get_country_name($transaction->billing->country) }}<br>
                @endif
                    @if($transaction->billing->type == 'business' && !empty($transaction->billing->tax_id))
                        <strong>
                            @if($settings->invoice_admin_tax_type)
                                {{ $settings->invoice_admin_tax_type }}
                            @else
                                {{ ___("Tax ID") }}
                            @endif
                        </strong> {{ $transaction->billing->tax_id }}<br>
                    @endif
            </p>
        </div>
    </div>
    <!-- Invoice -->
    <div class="row">
        <div class="col-xl-12">
            <table>
                <tr>
                    <th>{{___('Item')}}</th>
                    <th>{{___('Amount')}}</th>
                </tr>
                <tr>
                    <td>{{ $title }}</td>
                    <td>{{ price_symbol_format($transaction->base_amount ?? $transaction->amount) }}</td>
                </tr>
                @if ($transaction->coupon)
                    @php
                        $discount = ($transaction->base_amount * $transaction->coupon->percentage) / 100;
                    @endphp
                    <tr>
                        <td>
                            {{___('Discount')}} ({{ $transaction->coupon->percentage }}%)
                            <br><small>{{___('Coupon') }}: <strong>{{$transaction->coupon->code}}</strong></small>
                        </td>
                        <td class="text-danger">- {{ price_symbol_format($discount) }}</td>
                    </tr>
                    <tr>
                        <td>{{___('Subtotal')}}</td>
                        <td>{{ price_symbol_format($transaction->base_amount - $discount) }}</td>
                    </tr>
                @endif

                @foreach($applied_taxes as $tax)
                    <tr>
                        <td>
                            {{$tax->name}} <small>({{$tax->type == 'inclusive' ? ___("Inclusive") : ___("Exclusive")}})</small>
                            <br><small>{{$tax->description}}</small>
                        </td>
                        <td>{{$tax->type != 'inclusive' ? '+' : ''}} {{ price_symbol_format($tax['value']) }}</td>
                    </tr>
                @endforeach
            </table>
            <table id="totals">
                <tr>
                    <th>{{___('Total')}}
                        @if ($transaction->transaction_gatway)
                            <br>
                            <small>{{___('Paid via')}} {{ ucfirst($transaction->transaction_gatway) }}</small>
                        @endif
                    </th>
                    <th><span>{{ price_symbol_format($transaction->amount) }}</span></th>
                </tr>
            </table>
        </div>
    </div>
    <!-- Footer -->
    <div class="row">
        <div class="col-xl-12">
            <ul id="footer">
                <li><span>{{url('/')}}</span></li>
                <li>{{config('settings.invoice_admin_email')}}</li>
                <li>{{config('settings.invoice_admin_phone')}}</li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
