<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Add tax') }}</h2>
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
        <form action="{{ route('admin.taxes.store') }}" method="post" id="sidePanel_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ ___('Internal name') }} *</label>
                <input type="text" name="internal_name" class="form-control" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Name') }} *</label>
                <input type="text" name="name" class="form-control" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Description') }} *</label>
                <input type="text" name="description" class="form-control" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Tax Value') }} *</label>
                <input type="text" name="value" class="form-control" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Value Type') }} *</label>
                <select class="form-select" name="value_type">
                    <option value="percentage">{{ ___('Percentage') }}</option>
                    <option value="fixed">{{ ___('Fixed') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Type') }} *</label>
                <select class="form-select" name="type">
                    <option value="inclusive">{{ ___('Inclusive') }}</option>
                    <option value="exclusive">{{ ___('Exclusive') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Billing for') }} *</label>
                <select class="form-select" name="billing_type">
                    <option value="personal">{{ ___('Personal') }}</option>
                    <option value="business">{{ ___('Business') }}</option>
                    <option value="both">{{ ___('Both') }}</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ ___('Country') }}</label>
                <select id="countries" class="form-control select2" name="countries[]" multiple>
                    @foreach (countries() as $country)
                        <option value="{{ $country->code }}">{{ $country->name }}</option>
                    @endforeach
                </select>
                <span class="form-text text-muted">{{ ___('Leave empty for all countries.') }}</span>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('assets/admin/js/quicklara.js') }}"></script>
