@extends('admin.layouts.main')
@section('title', ___('Translate').' ' . $language->name)

@section('content')

    <div class="card">
        <form
            action="{{ route('admin.languages.translates.update', $language->id) }}" method="POST">
            @csrf
            <div class="card-header d-flex align-items-center justify-content-between position-sticky top-0">
                <h5>{{___('Translate').' ' . $language->name}}</h5>
                <div>
                    <button class="btn btn-primary ms-2">{{ ___('Save Changes') }}</button>
                </div>
            </div>
            <div class="card-body my-1">
                <input class="form-control form-control-lg mb-3" type="search" placeholder="{{ ___('Search') }}" id="search-field" autofocus>
                <table class="table">
                    <tr>
                        <th>{{ ___('Key') }}</th>
                        <th>{{ ___('Value') }}</th>
                    </tr>
                    <tbody id="translations">
                    @foreach ($defaultLanguage as $file => $trans)
                        @foreach ($trans as $key1 => $value1)
                            @if (is_array($value1))
                                @foreach ($value1 as $key2 => $value2)
                                    @if (is_array($value2)) @continue @endif

                                    <tr data-key="{{ $value2 }} {{ @$translates[$file][$key1][$key2] }}">
                                        <td><textarea class="form-control bg-label-secondary"
                                                      readonly>{{ $value2 }}</textarea></td>
                                        <td><textarea name="translates[{{ $file }}][{{ $key1 }}][{{ $key2 }}]"
                                                      class="form-control"
                                                      placeholder="{{ $value2 }}">{{ @$translates[$file][$key1][$key2] }}</textarea></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr data-key="{{ $value1 }} {{ @$translates[$file][$key1] }}">
                                    <td><textarea class="form-control bg-label-secondary"
                                                  readonly>{{ $value1 }}</textarea></td>
                                    <td><textarea name="translates[{{ $file }}][{{ $key1 }}]" class="form-control"
                                                  placeholder="{{ $value1 }}">{{ @$translates[$file][$key1] }}</textarea></td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    @push('scripts_at_top')
        <script>
            "use strict";
            var QuickMenu = {"page": "languages"};
        </script>
    @endpush

    @push('scripts_at_bottom')
    <script>
        $('#search-field').on('keyup search', function (){
            var searchTerm = $(this).val().toLowerCase();
            $('#translations').find('tr').each(function () {
                if ($(this).filter(function() {
                    return $(this).attr('data-key').toLowerCase().indexOf(searchTerm) > -1;
                }).length > 0 || searchTerm.length < 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    </script>
    @endpush
@endsection
