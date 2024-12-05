<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Add FAQ') }}</h2>
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
        <form action="{{ route('admin.faqs.store') }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-2">
                {{quick_switch(___('Active'), 'active', true)}}
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Language') }} *</label>
                <select name="translation_lang" class="form-select" required>
                    <option value="">{{ ___('All') }}</option>
                    @foreach ($admin_languages as $adminLanguage)
                        <option value="{{ $adminLanguage->code }}">
                            {{ $adminLanguage->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Title') }} *</label>
                <input type="text" name="faq_title" class="form-control" value="" required />
            </div>
            <div class="mb-2">
                <label class="form-label">{{ ___('Content') }} *</label>
                <textarea name="faq_content" class="tiny-editor"></textarea>
            </div>

        </form>
    </div>
</div>

<script src="{{ asset('assets/admin/js/quicklara.js') }}"></script>
