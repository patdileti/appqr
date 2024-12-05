@extends('admin.layouts.main')
@section('title', ___('Payment Gateways'))
@section('content')

    <div class="quick-card card">
        <div class="card-body">
            <div class="dataTables_wrapper">
                <table id="basic_datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ ___('Logo') }}</th>
                            <th>{{ ___('Title') }}</th>
                            <th>{{ ___('Status') }}</th>
                            <th width="20" class="no-sort" data-priority="1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gateways as $gateway)
                            <tr class="item">
                                <td>
                                    <img src="{{ asset('storage/payment_gateways/'.$gateway->payment_folder.'.png') }}" width="100" alt="{{$gateway->payment_folder}}">
                                </td>
                                <td>
                                    {{ $gateway->payment_title }}
                                </td>
                                <td>
                                    @if ($gateway->payment_install)
                                        <span class="badge bg-success">{{ ___('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ ___('Disabled') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" data-url="{{ route('admin.gateways.edit', $gateway->id) }}" data-toggle="slidePanel" title="{{___('Edit')}}" data-tippy-placement="top" class="btn btn-default btn-icon"><i class="icon-feather-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts_at_top')
        <script id="quick-sidebar-menu-js-extra">
            "use strict";
            var QuickMenu = {"page": "gateways"};
        </script>
    @endpush
@endsection
