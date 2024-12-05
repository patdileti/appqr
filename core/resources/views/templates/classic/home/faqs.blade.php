@extends($activeTheme.'layouts.main')
@section('title', ___('Frequently Asked Questions'))
@section('content')
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ ___('Frequently Asked Questions') }}</h2>
                    <span>{{ ___("Got Questions? We've Got Answers!") }}</span>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ ___('Home') }}</a></li>
                            <li>{{ ___('FAQs') }}</li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    {!! ads_on_top() !!}
    <div class="container">
        <div class="margin-bottom-50">

            <!-- Accordion -->
            <div class="accordion js-accordion">
            @foreach ($faqs as $faq)
                <!-- Accordion Item -->
                <div class="accordion__item js-accordion-item @if($loop->first) active @endif">
                    <div class="accordion-header js-accordion-header">{{ $faq->faq_title }}</div>

                    <!-- Accordtion Body -->
                    <div class="accordion-body js-accordion-body">

                        <!-- Accordion Content -->
                        <div class="accordion-body__contents">
                            {!! $faq->faq_content !!}
                        </div>

                    </div>
                    <!-- Accordion Body / End -->
                </div>
                <!-- Accordion Item / End -->
            @endforeach
            </div>
            <!-- Accordion / End -->
            {{ $faqs->links($activeTheme.'pagination/default') }}
        </div>
        <!-- faq-page -->
    </div>
    {!! ads_on_bottom() !!}
@endsection
