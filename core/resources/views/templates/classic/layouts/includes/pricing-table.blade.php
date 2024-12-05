@if($plan->status == 1)
    <div class="pricing-plan {{ @$plan->recommended == "yes" ? 'recommended' : '' }}">

        @if (@$plan->recommended == "yes")
            <div class="recommended-badge">{{ ___('Recommended') }}</div>
        @endif

        <h3>{{ !empty($plan->translations->{get_lang()}->name)
                        ? $plan->translations->{get_lang()}->name
                        : $plan->name }}</h3>

        @if ($plan->id == 'free' || $plan->id == 'trial')
            <div class="pricing-plan-label">
                <strong>{{ $plan->id == 'free' ? ___('Free') : ___('Trial') }}</strong>
            </div>
        @else
            @if($total_monthly != 0)
                <div class="pricing-plan-label billed-monthly-label">
                    <strong>{{ price_symbol_format($plan->monthly_price) }}</strong>/ {{ ___('Monthly') }}
                </div>
            @endif

            @if($total_annual != 0)
                <div class="pricing-plan-label billed-yearly-label">
                    <strong>{{ price_symbol_format($plan->annual_price) }}</strong>/ {{ ___('Yearly') }}
                </div>
            @endif

            @if($total_lifetime != 0)
                <div class="pricing-plan-label billed-lifetime-label">
                    <strong>{{ price_symbol_format($plan->lifetime_price) }}</strong>/ {{ ___('Lifetime') }}
                </div>
            @endif

        @endif

        <div class="pricing-plan-features">
            <strong>{{ ___('Features of :plan_name', [ 'plan_name' => !empty($plan->translations->{get_lang()}->name)
                        ? $plan->translations->{get_lang()}->name
                        : $plan->name ]) }}</strong>
            <ul>
                <li>
                    {!! ___(':plan_category_limit Menu Categories', ['plan_category_limit' => '<strong>' . ($plan->settings->category_limit == 999 ? ___('Unlimited') : number_format($plan->settings->category_limit)) . '</strong>']) !!}
                </li>
                <li>
                    {!! ___(':plan_menu_limit Menu Items Per Category', ['plan_menu_limit' => '<strong>' . ($plan->settings->menu_limit == 999 ? ___('Unlimited') : number_format($plan->settings->menu_limit)) . '</strong>']) !!}
                </li>
                <li>
                    {!! ___(':plan_scan_limit Scans Per Month', ['plan_scan_limit' => '<strong>' . ($plan->settings->scan_limit == 999 ? ___('Unlimited') : number_format($plan->settings->scan_limit)) . '</strong>']) !!}
                </li>
                <li>
                    @if ($plan->settings->allow_ordering)
                        <span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span>
                    @else
                        <span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span>
                    @endif
                    {{ ___('Allow restaurants to accept orders') }}
                </li>
                <li>
                    @if (@$plan->settings->hide_branding)
                        <span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span>
                    @else
                        <span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span>
                    @endif
                    {{ ___('Hide QuickVCard Branding') }}
                </li>
                <li>
                    @if (!@$plan->settings->advertisements)
                        <span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span>
                    @else
                        <span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span>
                    @endif
                    {{ ___('No Advertisements') }}
                </li>

                @if (!empty($plan->settings->custom_features))
                    @foreach ($plan->settings->custom_features as $key => $value)
                        @php $planoption = plan_option($key) @endphp
                        @if($planoption)
                            <li>
                                @if ($value)
                                    <span class="icon-text yes"><i
                                            class="icon-feather-check-circle margin-right-2"></i></span>
                                @else
                                    <span class="icon-text no"><i
                                            class="icon-feather-x-circle margin-right-2"></i></span>
                                @endif

                                {{ !empty($planoption->translations->{get_lang()}->title)
                                    ? $planoption->translations->{get_lang()}->title
                                    : $planoption->title }}
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </div>

        @if(auth()->check() && request()->user()->group_id == $plan->id)
            <button class="button full-width margin-top-20 ripple-effect disabled"
                    disabled>{{___('Current Plan')}}</button>
        @else
            <button type="submit" class="button full-width margin-top-20 ripple-effect" name="plan"
                    value="{{ $plan->id }}">{{___('Choose Plan')}}</button>
        @endif
    </div>
@endif
