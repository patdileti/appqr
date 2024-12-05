<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{___('Add Plan')}}</h2>
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
        <form action="{{ route('admin.plans.store') }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf

            <x-admin.translated-input id="name" :title="___('Plan Name')" />
            <x-admin.translated-textarea id="description" :title="___('Description')" />

            <div class="mb-3">
                <label class="form-label">{{ ___('Status') }} *</label>
                <select class="form-select" name="status">
                    <option value="1">{{ ___('Active') }}</option>
                    <option value="0">{{ ___('Inactive') }}</option>
                    <option value="2">{{ ___('Hidden') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Monthly Price') }} *</label>
                <div class="custom-input-group input-group">
                    <input type="text" name="monthly_price" class="form-control"
                           value="0" required/>
                    <span class="input-group-text"><strong>{{ $settings->currency_code }}</strong></span>
                </div>
                <small class="form-text">{{ ___('Set 0 to disable it.') }}</small>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Annual Price') }} *</label>
                <div class="custom-input-group input-group">
                    <input type="text" name="annual_price" class="form-control"
                           value="0" required/>
                    <span class="input-group-text"><strong>{{ $settings->currency_code }}</strong></span>
                </div>
                <small class="form-text">{{ ___('Set 0 to disable it.') }}</small>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Lifetime Price') }} *</label>
                <div class="custom-input-group input-group">
                    <input type="text" name="lifetime_price" class="form-control"
                           value="0" required/>
                    <span class="input-group-text"><strong>{{ $settings->currency_code }}</strong></span>
                </div>
                <small class="form-text">{{ ___('Set 0 to disable it.') }}</small>
            </div>
            <div class="mb-3">
                {{ quick_switch(___('Recommended'), 'recommended') }}
            </div>
            <h5 class="m-t-35">{{ ___('Plan Settings') }}</h5>
            <hr>
            <div class="mb-3">
                <label class="form-label" for="field_limit">{{ ___('Menu Category Limit') }} *</label>
                <input name="category_limit" type="number" class="form-control" id="category_limit" value="10">
                <span class="form-text text-muted">{{ ___('For unlimited, enter 999.') }}</span>
            </div>
            <div class="mb-3">
                <label class="form-label" for="field_limit">{{ ___('Menu Items Limit Per Category') }} *</label>
                <input name="menu_limit" type="number" class="form-control" id="menu_limit" value="10">
                <span class="form-text text-muted">{{ ___('For unlimited, enter 999.') }}</span>
            </div>
            <div class="mb-3">
                <label class="form-label" for="scan_limit">{{ ___('Scans Limit Per Month') }} *</label>
                <input name="scan_limit" type="number" class="form-control" id="scan_limit" value="50">
                <span class="form-text text-muted">{{ ___('For unlimited, enter 999.') }}</span>
            </div>
            <div class="mb-3">
                {{ quick_switch(___('Allow ordering'), 'allow_ordering', false, ___('Allow restaurants to accept orders.')) }}
            </div>
            <div class="mb-3">
                {{ quick_switch(___('Hide Branding'), 'hide_branding', true) }}
            </div>
            <div class="mb-3">
                {{ quick_switch(___('Show advertisements'), 'advertisements') }}
            </div>

            @if($PlanOption->count())
                <h5 class="m-t-35">{{ ___('Custom Settings') }}</h5>
                <hr>
                @foreach ($PlanOption as $planoption)
                    <div class="mb-3">
                        {{quick_switch($planoption['title'], "planoptions[{$planoption['id']}]")}}
                    </div>
                @endforeach
            @endif

            @if($taxes->count())
            <h5 class="m-t-35">{{ ___('Taxes') }}</h5>
            <hr>
            <div class="mb-3">
                <label class="form-label">{{ ___('Select Taxes') }}</label>
                <select class="form-control select2" name="taxes[]" multiple>
                    @foreach ($taxes as $tax)
                        @php
                            $value = ($tax->value_type == 'percentage' ? (float) $tax->value .'%' : price_format($tax->value));
                        @endphp

                        <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $value }})</option>

                    @endforeach
                </select>
                <span class="form-text text-muted">{{ ___('Select taxes for this plan.') }}</span>
            </div>
            @endif
        </form>
    </div>
</div>
<script src="{{ asset('assets/admin/js/quicklara.js') }}"></script>
