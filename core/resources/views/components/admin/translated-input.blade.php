@props([
    'id',
    'title',
    'value' => null,
    'translations' => null
])

<div class="mb-3 form-group">
    <label class="d-flex align-items-end m-b-5" for="name">
        {{ $title }} *
        <div class="d-flex align-items-center translate-picker">
            <i class="fa fa-language"></i>
            <select class="custom-select custom-select-sm ml-1">
                @foreach ($admin_languages as $language)
                    <option value="{{ $language->code }}" @selected($language->code == env('DEFAULT_LANGUAGE'))>{{ $language->name }}</option>
                @endforeach
            </select>
        </div>
    </label>
    <div class="translate-fields translate-fields-default" style="display: none">
        <input name="{{ $id }}" id="{{ $id }}" type="text" class="form-control" value="{{$value}}" required>
    </div>
    @foreach ($admin_languages as $language)
        <div class="translate-fields translate-fields-{{ $language->code }}"
             @if($language->code != env('DEFAULT_LANGUAGE')) style="display: none" @endif>
            <input type="text"
                   class="form-control @if($language->code == env('DEFAULT_LANGUAGE')) translation-input-default @endif"
                   name="translations[{{ $language->code }}][{{ $id }}]"
                   value="{{ !empty($translations->{$language->code}->{$id})
                        ? $translations->{$language->code}->{$id}
                        : $value }}">
        </div>
    @endforeach
</div>
