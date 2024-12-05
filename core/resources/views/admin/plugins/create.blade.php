<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{___('Upload Plugin')}}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{___('Save')}}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-icon btn-default slidePanel-close" title="{{___('Close')}}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form action="{{ route('admin.plugins.store') }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="plugin_zip">{{ ___('Plugin Zip File') }} *</label>
                <input type="file" name="plugin_zip" id="plugin_zip" class="form-control" accept=".zip" required>
            </div>
        </form>
    </div>
</div>
