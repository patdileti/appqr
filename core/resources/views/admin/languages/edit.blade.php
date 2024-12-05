<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Edit Language') }}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{ ___('Save') }}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-default btn-icon slidePanel-close" title="{{ ___('Close') }}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form action="{{ route('admin.languages.update', $language->id) }}" method="post" id="sidePanel_form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ ___('Name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ $language->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Direction') }} : <span class="red">*</span></label>
                <select name="direction" class="form-select">
                    <option value="ltr" {{ $language->direction == 'ltr' ? 'selected' : '' }}>{{ ___('LTR') }}</option>
                    <option value="rtl" {{ $language->direction == 'rtl' ? 'selected' : '' }}>{{ ___('RTL') }}</option>
                </select>
            </div>
            <div class="mb-3">
                {{quick_switch(___('Active'), 'active', $language->active)}}
            </div>
            <div class="mb-3">
                {{quick_switch(___('Set Default language'), 'is_default', env('DEFAULT_LANGUAGE') == $language->code )}}
            </div>
        </form>
    </div>
</div>
