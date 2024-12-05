<tr>
    <td data-label="{{___('Table No. / Type')}}">
        @if($order->type == 'on-table')
            {{ $order->table_number }}
        @elseif($order->type == 'takeaway')
            <span class="small-label margin-left-0">{{___('Takeaway')}}</span>
        @elseif($order->type == 'delivery')
            <span class="small-label margin-left-0">{{___('Delivery')}}</span>
        @endif
    </td>
    <td data-label="{{___('Menu')}}">
        @if($order->items)
            @foreach($order->items as $item)
                @if($item->menu)
                    <div class="order-table-item">
                        <strong>
                            <i class="icon-material-outline-restaurant"></i> {{$item->menu->name}}
                            @if($item->variant_title)
                                <small>{{$item->variant_title}}</small>
                            @endif
                        </strong>
                        @if($item->quantity > 1)
                            &times; {{$item->quantity}}
                        @endif

                        @if($item->itemExtras)
                            <div class="padding-left-10">
                                @foreach($item->itemExtras as $itemExtra)
                                    <div>
                                        <i class="icon-feather-plus"></i> {{$itemExtra->extra->title}}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        @endif
    </td>
    <td data-label="{{___('Customer')}}">
        <div class="d-flex align-items-center"><i
                class="icon-feather-user"></i>&nbsp;{{ $order->customer_name }}
            @if(!empty($order->message))
                <span class="button gray ico margin-left-5" data-tippy-placement="top"
                      title="{{$order->message}}"><i
                        class="icon-feather-message-square"></i></span>
            @endif
        </div>
        @if(!empty($order->phone_number))
            <div><i class="icon-feather-phone"></i> {{$order->phone_number}}</div>
        @endif
        @if(!empty($order->address))
            <span><i class="icon-feather-map-pin"></i> {{$order->address}}</span>
        @endif
    </td>
    <td data-label="{{___('Price')}}">
        <div class="d-flex flex-wrap align-items-center">
                                        <span class="small-label margin-left-0">
                                            {{ price_symbol_format($order->price) }}
                                        </span>
            @if($order->is_paid)
                <span class="is-paid" data-tippy-placement="top" title="{{___('Paid')}}"><i
                        class="icon-feather-check"></i></span>
            @endif
        </div>
    </td>
    <td data-label="{{___('Status')}}">
        @if($order->status == 'pending')
            <span class="button gray ico order-status" data-tippy-placement="top"
                  title="{{___('Pending')}}"><i class="icon-feather-clock"></i></span>
        @else
            <span class="button green ico order-status" data-tippy-placement="top"
                  title="{{___('Completed')}}"><i class="icon-feather-check"></i></span>
        @endif
    </td>
    <td data-label="{{___('Time')}}">
        <small>{{ date_formating($order->created_at, 'd, M Y h:i A') }}</small>
    </td>
    <td>
        @if($order->status == 'pending')
            <button class="button ico qr-complete-order"
                    data-route="{{ route('restaurants.completeOrder', $order->id) }}"
                    data-tippy-placement="top"
                    title="{{___('Complete')}}"><i
                    class="icon-feather-check"></i>
            </button>
        @endif
        <button class="button red ico qr-delete-order"
                data-route="{{ route('restaurants.deleteOrder', $order->id) }}"
                data-tippy-placement="top"
                title="{{___('Delete')}}"><i
                class="icon-feather-trash-2"></i>
        </button>
        <button class="button green ico qr-view-order" data-tippy-placement="top"
                title="{{___('View Order')}}" data-id="{{$order->id}}"><i
                class="icon-feather-eye"></i>
        </button>
        <div class="order-print-tpl-{{$order->id}} d-none">
            <table>
                <tr>
                    <td>{{___('Time')}}</td>
                    <td>{{ date_formating($order->created_at, 'd, M Y h:i A') }}</td>
                </tr>
                <tr>
                    <td>{{___('Customer')}}</td>
                    <td>{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    @if($order->type == 'on-table')
                        <td>{{___('Table No.')}}</td>
                        <td>
                            {{ $order->table_number }}
                        </td>
                    @elseif($order->type == 'takeaway')
                        <td>{{___('Type')}}</td>
                        <td>
                            {{___('Takeaway')}}
                        </td>
                    @elseif($order->type == 'delivery')
                        <td>{{___('Type')}}</td>
                        <td>
                            {{___('Delivery')}}
                        </td>
                    @endif
                </tr>
                @if(!empty($order->phone_number))
                    <tr>
                        <td>{{___('Phone')}}</td>
                        <td>{{$order->phone_number}}</td>
                    </tr>
                @endif
                @if(!empty($order->address))
                    <tr>
                        <td>{{___('Address')}}</td>
                        <td>{{$order->address}}</td>
                    </tr>
                @endif
                @if(!empty($order->message))
                    <tr>
                        <td>{{___('Message')}}</td>
                        <td>{{$order->message}}</td>
                    </tr>
                @endif
                @if($order->is_paid)
                    <tr>
                        <td>{{___('Payment')}}</td>
                        <td>{{___('Paid')}}</td>
                    </tr>
                @endif
            </table>
            <div class='order-print-divider'></div>
            <table class='order-print-menu'>
                <thead>
                <tr>
                    <th>{{___('Menu')}}</th>
                    <th>{{___('Price')}}</th>
                </tr>
                </thead>
                <tbody id='order-print-menu'>
                @if($order->items)
                    @foreach($order->items as $item)
                        @if($item->menu)
                            <tr>
                                <td>{{$item->menu->name}}
                                    @if($item->variant_title)
                                        <small>{{$item->variant_title}}</small>
                                        @endif
                                        &times; {{$item->quantity}}</td>
                                <td>{{ price_symbol_format($item->menu->price * $item->quantity) }}</td>
                            </tr>
                        @endif

                        @if($item->itemExtras)
                            @foreach($item->itemExtras as $itemExtra)
                                <tr class="order-menu-extra">
                                    <td>
                                                                <span
                                                                    class="margin-left-5">
                                                                    {{$itemExtra->extra->title}}
                                                                </span>
                                    </td>
                                    <td>{{ price_symbol_format($itemExtra->extra->price * $item->quantity) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @endif
                @if($order->type == 'delivery' && $postOptions->restaurant_delivery_charge)
                    <tr>
                        <td>{{___('Delivery Charge')}}</td>
                        <td>{{price_symbol_format($postOptions->restaurant_delivery_charge)}}</td>
                    </tr>
                @endif
                </tbody>
                <tfoot>
                <tr>
                    <th>{{___('Total')}}</th>
                    <td>{{ price_symbol_format($order->price) }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </td>
</tr>
