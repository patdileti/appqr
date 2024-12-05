<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Create New Category') }}</h2>
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
        <form action="{{ route('admin.blog.category.store') }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-2">
                {{quick_switch(___('Active'), 'active', true)}}
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Category title') }} *</label>
                <input type="text" name="title" class="form-control" value="" required autofocus />
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Slug') }}</label>
                <input type="text" name="slug" class="form-control" value="" required />
            </div>
        </form>
    </div>
</div>
