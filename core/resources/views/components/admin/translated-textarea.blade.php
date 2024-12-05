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
                    <option
                        value="{{ $language->code }}" @selected($language->code == env('DEFAULT_LANGUAGE'))>{{ $language->name }}</option>
                @endforeach
            </select>
        </div>
    </label>
    <div class="translate-fields translate-fields-default" style="display: none">
        <textarea name="{{ $id }}" rows="3" class="form-control"
                  required>{{ $value }}</textarea>
    </div>

    @foreach ($admin_languages as $language)
        <div class="translate-fields translate-fields-{{ $language->code }}"
             @if($language->code != env('DEFAULT_LANGUAGE')) style="display: none" @endif>
            <textarea name="translations[{{ $language->code }}][{{ $id }}]"
                      rows="3"
                      class="form-control @if($language->code == env('DEFAULT_LANGUAGE')) translation-input-default @endif"
                      required>{{ !empty($translations->{$language->code}->{$id})
                        ? $translations->{$language->code}->{$id}
                        : $value }}</textarea>
        </div>
    @endforeach
</div>
