<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{___('Add Plan Options')}}</h2>
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
        <form action="{{ route('admin.planoption.store') }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf
            <x-admin.translated-input id="title" :title="___('Title')" />
            <div class="mb-3">
                {{ quick_switch(___('Enable/Disable'), 'active', true) }}
            </div>
        </form>
    </div>
</div>
<script>
    // translate picker
    $(document).off('change', ".translate-picker select").on('change', ".translate-picker select", function (e) {
        $('.translate-fields').hide();
        $('.translate-fields-' + $(this).val()).show();
        $('.translate-picker select').val($(this).val());
    });
</script>
