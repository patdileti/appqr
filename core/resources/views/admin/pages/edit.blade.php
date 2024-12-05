<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Edit Page') }}</h2>
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
        <form action="{{ route('admin.pages.update', $page->id) }}" method="post" id="sidePanel_form">
            @csrf
            @method('PUT')
            <div class="mb-2">
                {{quick_switch(___('Active'), 'active', $page->active)}}
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Language') }} *</label>
                <select name="translation_lang" class="form-select" required>
                    <option value="" @selected( $page->translation_lang == null )>{{ ___('All') }}</option>
                    @foreach ($admin_languages as $adminLanguage)
                        <option value="{{ $adminLanguage->code }}" @selected( $page->translation_lang == $adminLanguage->code )>{{ $adminLanguage->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">{{ ___('Type') }} *</label>
                <select name="type" class="form-select" required>
                    <option value="0" @selected( $page->type == "0" )>{{ ___('Public') }}</option>
                    <option value="1" @selected( $page->type == "1" )>{{ ___('Private') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Title') }} *</label>
                <input type="text" name="page_title" class="form-control" value="{{ $page->title }}"/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Slug') }}</label>
                <input type="text" name="slug" class="form-control" value="{{ $page->slug }}" />
            </div>

            <div class="mb-3">
                <label class="form-label">{{ ___('Content') }} *</label>
                <textarea name="page_content" rows="10" class="form-control tiny-editor">{{ $page->content }}</textarea>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('assets/admin/js/quicklara.js') }}"></script>