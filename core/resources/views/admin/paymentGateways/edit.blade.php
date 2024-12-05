<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Edit') .' '. $gateway->payment_folder }}</h2>
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
        <form action="{{ route('admin.gateways.update', $gateway->id) }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                {{quick_switch(___('Active'), 'active', $gateway->payment_install)}}
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Title') }} *</label>
                <input type="text" name="title" class="form-control" value="{{ $gateway->payment_title }}"
                       required>
            </div>

            <h5 class="mt-5">{{__('Credentials')}}</h5>
            <hr>
            @include('admin.paymentGateways.forms.'.$gateway->payment_folder)

        </form>
    </div>
</div>
