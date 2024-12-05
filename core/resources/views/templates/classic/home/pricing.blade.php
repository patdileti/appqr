@extends($activeTheme.'layouts.main')
@section('title', ___('Pricing'))
@section('content')
    <div id="titlebar" class="gradient">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ ___('Pricing') }}</h2>
                    <span>{{ ___("Membership Plans") }}</span>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li>{{ ___('Pricing') }}</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {!! ads_on_top() !!}
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <form action="{{route('checkout.index')}}" method="get">
                    <div class="billing-cycle-radios margin-bottom-70">
                        @if($total_monthly)
                            <div class="radio billed-monthly-radio">
                                <input id="radio-monthly" name="interval" type="radio" value="monthly" checked="">
                                <label for="radio-monthly"><span class="radio-label"></span> {{___('Monthly')}}
                                </label>
                            </div>
                        @endif
                        @if($total_annual)
                            <div class="radio billed-yearly-radio">
                                <input id="radio-yearly" name="interval" type="radio" value="yearly">
                                <label for="radio-yearly"><span class="radio-label"></span> {{___('Yearly')}}
                                </label>
                            </div>
                        @endif
                        @if($total_lifetime)
                            <div class="radio billed-lifetime-radio">
                                <input id="radio-lifetime" name="interval" type="radio" value="lifetime">
                                <label for="radio-lifetime"><span class="radio-label"></span> {{___('Lifetime')}}
                                </label>
                            </div>
                        @endif
                    </div>
                    <!-- Pricing Plans Container -->
                    <div class="pricing-plans-container">
                    @foreach ([$free_plan, $trial_plan] as $plan)
                        @include($activeTheme.'layouts.includes.pricing-table')
                    @endforeach

                    @foreach ($plans as $plan)
                        @include($activeTheme.'layouts.includes.pricing-table')
                    @endforeach
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="margin-top-80"></div>

    {!! ads_on_bottom() !!}
@endsection
