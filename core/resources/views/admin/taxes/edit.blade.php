<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ ___('Edit tax') }}</h2>
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
        <form action="{{ route('admin.taxes.update', $tax->id) }}" method="post" id="sidePanel_form">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ ___('Internal name') }} *</label>
                <input type="text" name="internal_name" class="form-control" value="{{ $tax->internal_name }}" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ $tax->name }}" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Description') }} *</label>
                <input type="text" name="description" class="form-control" value="{{ $tax->description }}" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Tax Value') }} *</label>
                <input type="text" name="value" class="form-control" value="{{ $tax->value }}" required/>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Value Type') }} *</label>
                <select class="form-select" name="value_type">
                    <option value="percentage" {{ $tax->value_type == "percentage" ? 'selected' : '' }}>{{ ___('Percentage') }}</option>
                    <option value="fixed" {{ $tax->value_type == "fixed" ? 'selected' : '' }}>{{ ___('Fixed') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Type') }} *</label>
                <select class="form-select" name="type">
                    <option value="inclusive" {{ $tax->type == "inclusive" ? 'selected' : '' }}>{{ ___('Inclusive') }}</option>
                    <option value="exclusive" {{ $tax->type == "exclusive" ? 'selected' : '' }}>{{ ___('Exclusive') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Billing for') }} *</label>
                <select class="form-select" name="billing_type">
                    <option value="personal" {{ $tax->billing_type == "personal" ? 'selected' : '' }}>{{ ___('Personal') }}</option>
                    <option value="business" {{ $tax->billing_type == "business" ? 'selected' : '' }}>{{ ___('Business') }}</option>
                    <option value="both" {{ $tax->billing_type == "both" ? 'selected' : '' }}>{{ ___('Both') }}</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ ___('Country') }}</label>
                <select id="countries" class="form-control select2" name="countries[]" multiple>
                    @php
                    $countries = explode(',', $tax->countries);
                    @endphp
                    @foreach (countries() as $country)
                        <option value="{{ $country->code }}"
                            {{ in_array($country->code, $countries) ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
                <span class="form-text text-muted">{{ ___('Leave empty for all countries.') }}</span>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('assets/admin/js/quicklara.js') }}"></script>
