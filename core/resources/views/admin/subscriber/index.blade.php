@extends('admin.layouts.main')
@section('title', ___('Newsletter Subscribers'))
@section('content')
    <div class="card">
        <div class="card-body">
        <div class="dataTables_wrapper">
            <table id="basic_datatable" class="table table-striped">
            <thead>
                <tr>
                    <th>{{ ___('Email') }}</th>
                    <th>{{ ___('Joined') }}</th>
                    <th width="20" class="no-sort"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subscribers as $subscriber)
                    <tr class="item">
                        <td>{{ $subscriber->email }}</td>
                        <td>{{ date_formating($subscriber->joined) }}</td>
                        <td>
                            <div class="d-flex">
                                <a href="#" data-url="{{ route('admin.subscriber.edit', $subscriber->id) }}" data-toggle="slidePanel" title="{{ ___('Edit') }}" class="btn btn-default btn-icon me-2" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                                <form action="{{ route('admin.subscriber.destroy', $subscriber->id) }}" method="POST" onsubmit='return confirm("{{___('Are you sure?')}}")'>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon btn-danger" title="{{ ___('Delete') }}" data-tippy-placement="top"><i class="icon-feather-trash-2"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        </div>
    </div>
    <!-- Site Action -->
    <div class="site-action">
        <button type="button" class="front-icon btn btn-primary btn-floating"
                data-url="{{ route('admin.subscriber.create') }}" data-toggle="slidePanel">
            <i class="icon-feather-plus animation-scale-up" aria-hidden="true"></i>
        </button>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>
    @push('scripts_at_top')
        <script type="text/javascript">
            "use strict";
            var QuickMenu = {"page": "subscribers"};
        </script>
    @endpush
@endsection
