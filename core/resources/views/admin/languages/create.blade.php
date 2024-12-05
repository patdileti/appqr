<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Add language') }}</h2>
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
        <form action="{{ route('admin.languages.store') }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ ___('Name') }} *</label>
                <input type="text" name="name" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Code') }} (ISO 639 Set 1) *</label>
                <input type="text" name="code" class="form-control" required>
                <small class="form-text"><a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes</a></small>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Direction') }} *</label>
                <select name="direction" class="form-control">
                    <option value="ltr">{{ ___('LTR') }}</option>
                    <option value="rtl">{{ ___('RTL') }}</option>
                </select>
            </div>
            <div class="mb-3">
                {{quick_switch(___('Set Default language'), 'is_default')}}
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('assets/admin/js/quicklara.js') }}"></script>
