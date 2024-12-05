<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Edit Allergy') }}</h2>
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
        <form action="{{ route('admin.allergies.update', $allergy->id) }}" method="post" id="sidePanel_form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ ___('Image') }}</label>
                <div class="d-flex align-items-start gap-4">
                    <img src="{{ str($allergy->image)->isUrl() ? $allergy->image : asset('storage/allergies/'.$allergy->image) }}" alt=""
                         class="d-block rounded" width="32" id="uploadedImage">
                    <div>
                        <label for="upload" class="btn btn-primary mb-2" tabindex="0">
                            <i class="fas fa-upload"></i>
                            <span class="d-none d-sm-block ms-2">{{ ___('Upload Image') }}</span>
                            <input name="image" type="file" id="upload" hidden
                                   onchange="readURL(this,'uploadedImage')"
                                   accept="image/png, image/jpeg" required>
                        </label>
                        <p class="form-text mb-0">{{ ___('Allowed JPG, JPEG or PNG.') }}</p>
                    </div>
                </div>
            </div>
            <x-admin.translated-input id="title" :title="___('Title')"  :value="$allergy->title" :translations="$allergy->translations" />
            <div class="mb-3">
                {{ quick_switch(___('Enable/Disable'), 'active', $allergy->active) }}
            </div>
        </form>
    </div>
</div>
