<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{___('Add Allergy')}}</h2>
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
        <form action="{{ route('admin.allergies.store') }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ ___('Image') }}</label>
                <div class="d-flex align-items-start gap-4">
                    <img src="{{ asset('storage/allergies/default.png') }}" alt=""
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
            <x-admin.translated-input id="title" :title="___('Title')" />
            <div class="mb-3">
                {{ quick_switch(___('Enable/Disable'), 'active', true) }}
            </div>
        </form>
    </div>
</div>
