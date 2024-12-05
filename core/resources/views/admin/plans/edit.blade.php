<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{___('Edit Plan')}}</h2>
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
        <form action="{{ route('admin.plans.update', $plan->id) }}" method="post" enctype="multipart/form-data"
              id="sidePanel_form">
            @csrf
            @method('PUT')

            <x-admin.translated-input id="name" :title="___('Plan Name')" :value="$plan->name" :translations="@$plan->translations" />
            <x-admin.translated-textarea id="description" :title="___('Description')" :value="@$plan->description" :translations="@$plan->translations" />

            <div class="mb-3">
                <label class="form-label">{{ ___('Status') }} *</label>
                <select class="form-select" name="status">
                    <option value="1" {{$plan->status == 1 ? 'selected' : ''}}>{{ ___('Active') }}</option>
                    <option value="0" {{$plan->status == 0 ? 'selected' : ''}}>{{ ___('Inactive') }}</option>
                    <option value="2" {{$plan->status == 2 ? 'selected' : ''}}>{{ ___('Hidden') }}</option>
                </select>
            </div>

            @if($plan->id != 'free' && $plan->id != 'trial')
                <div class="mb-3">
                    <label class="form-label">{{ ___('Monthly Price') }} *</label>
                    <div class="custom-input-group input-group">
                        <input type="text" name="monthly_price" class="form-control"
                               value="{{ $plan->monthly_price }}" required/>
                        <span class="input-group-text"><strong>{{ $settings->currency_code }}</strong></span>
                    </div>
                    <small class="form-text">{{ ___('Set 0 to disable it.') }}</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ ___('Annual Price') }} *</label>
                    <div class="custom-input-group input-group">
                        <input type="text" name="annual_price" class="form-control"
                               value="{{ $plan->annual_price }}" required/>
                        <span class="input-group-text"><strong>{{ $settings->currency_code }}</strong></span>
                    </div>
                    <small class="form-text">{{ ___('Set 0 to disable it.') }}</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ ___('Lifetime Price') }} *</label>
                    <div class="custom-input-group input-group">
                        <input type="text" name="lifetime_price" class="form-control"
                               value="{{ $plan->lifetime_price }}" required/>
                        <span class="input-group-text"><strong>{{ $settings->currency_code }}</strong></span>
                    </div>
                    <small class="form-text">{{ ___('Set 0 to disable it.') }}</small>
                </div>
                <div class="mb-3">
                    {{ quick_switch(___('Recommended'), 'recommended', $plan->recommended == 'yes') }}
                </div>
            @endif

            @if ($plan->id  == 'trial')
                <div class="mb-3">
                    <label class="form-label" for="days">{{ ___('Days') }} *</label>
                    <input name="days" type="number" class="form-control" id="days"
                           value="{{ $plan->days }}" min="1">
                    <span
                        class="form-text text-muted">{{ ___('The number of days, the trial plan can be used.') }}</span>
                </div>
            @endif

            <h5 class="m-t-35">{{ ___('Plan Settings') }}</h5>
            <hr>
            <div class="mb-3">
                <label class="form-label" for="field_limit">{{ ___('Menu Category Limit') }} *</label>
                <input name="category_limit" type="number" class="form-control" id="category_limit"
                       value="{{ @$plan->settings->category_limit }}">
                <span class="form-text text-muted">{{ ___('For unlimited, enter 999.') }}</span>
            </div>
            <div class="mb-3">
                <label class="form-label" for="field_limit">{{ ___('Menu Items Limit Per Category') }} *</label>
                <input name="menu_limit" type="number" class="form-control" id="menu_limit"
                       value="{{ @$plan->settings->menu_limit }}">
                <span class="form-text text-muted">{{ ___('For unlimited, enter 999.') }}</span>
            </div>
            <div class="mb-3">
                <label class="form-label" for="scan_limit">{{ ___('Scans Limit Per Month') }} *</label>
                <input name="scan_limit" type="number" class="form-control" id="scan_limit"
                       value="{{ @$plan->settings->scan_limit }}">
                <span class="form-text text-muted">{{ ___('For unlimited, enter 999.') }}</span>
            </div>
            <div class="mb-3">
                {{ quick_switch(___('Allow ordering'), 'allow_ordering', @$plan->settings->allow_ordering == '1', ___('Allow restaurants to accept orders.')) }}
            </div>
            <div class="mb-3">
                {{ quick_switch(___('Hide Branding'), 'hide_branding', @$plan->settings->hide_branding == '1') }}
            </div>
            <div class="mb-3">
                {{quick_switch(___('Show advertisements'), 'advertisements', @$plan->settings->advertisements == '1')}}
            </div>

            @if($PlanOption->count())
                <h5 class="m-t-35">{{ ___('Custom Settings') }}</h5>
                <hr>
                @foreach ($PlanOption as $planoption)
                    @php
                        $planoption_id = $planoption['id'];
                    @endphp
                    <div class="mb-3">
                        {{ quick_switch($planoption['title'], "planoptions[$planoption_id]", (isset($plan->settings->custom_features->$planoption_id) && $plan->settings->custom_features->$planoption_id == '1')) }}
                    </div>
                @endforeach
            @endif

            @if($plan->id != 'free' && $plan->id != 'trial')
                @if($taxes->count())
                    <h5 class="m-t-35">{{ ___('Taxes') }}</h5>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">{{ ___('Select Taxes') }}</label>
                        <select class="form-control select2" name="taxes[]" multiple>
                            @php
                                $plan_taxes = explode(',', $plan->taxes_ids);
                            @endphp
                            @foreach ($taxes as $tax)
                                @php
                                    $value = ($tax->value_type == 'percentage' ? (float) $tax->value .'%' : price_format($tax->value));
                                @endphp

                                <option value="{{ $tax->id }}"
                                    {{ in_array($tax->id, $plan_taxes) ? 'selected' : '' }}>
                                    {{ $tax->name }} ({{ $value }})
                                </option>

                            @endforeach
                        </select>
                        <span class="form-text text-muted">{{ ___('Select taxes for this plan.') }}</span>
                    </div>
                @endif
            @endif
        </form>
    </div>
</div>
<script src="{{ asset('assets/admin/js/quicklara.js') }}"></script>
