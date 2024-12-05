@extends($activeTheme.'layouts.app')
@section('title', ___('Dashboard'))
@section('content')
    <!-- Fun Facts Container -->
    <div class="fun-facts-container">
        <div class="fun-fact" data-fun-fact-color="#36bd78">
            <div class="fun-fact-text">
                <span>{{ ___('Total Restaurants') }}</span>
                <h4>{{ count(request()->user()->posts) }}</h4>
            </div>
            <div class="fun-fact-icon"><i class="far fa-utensils"></i></div>
        </div>
        <div class="fun-fact" data-fun-fact-color="#efa80f">
            <div class="fun-fact-text">
                <span>{{ ___('Membership') }}</span>
                <h4>{{ request()->user()->plan()->name }}</h4>
            </div>
            <div class="fun-fact-icon"><i class="icon-feather-gift"></i></div>
        </div>
    </div>

    <!-- Dashboard Box -->
    <div class="dashboard-box main-box-in-row">
        <div class="headline">
            <h3><i class="icon-feather-bar-chart-2"></i> {{ ___('This Month Scans') }}</h3>
        </div>
        <div class="content">
            <!-- Chart -->
            <div class="chart">
                <canvas id="chart" width="100" height="45"></canvas>
            </div>
        </div>
    </div>
    <!-- Dashboard Box / End -->

@endsection

@push('scripts_at_bottom')
    <script src="{{ asset($activeThemeAssets.'js/chart.min.js') }}"></script>
    <script>
        Chart.defaults.global.defaultFontFamily = "Nunito";
        Chart.defaults.global.defaultFontColor = '#888';
        Chart.defaults.global.defaultFontSize = '14';

        var ctx = document.getElementById('chart').getContext('2d');

        var chart = new Chart(ctx, {
            type: 'line',

            // The data for our dataset
            data: {
                labels: @json($days),
                // Information about the dataset
                datasets: [{
                    label: @json(___('Scans')),
                    backgroundColor: '{{ $settings->theme_color }}15',
                    borderColor: '{{ $settings->theme_color }}',
                    borderWidth: "3",
                    data: @json($scans),
                    pointRadius: 5,
                    pointHoverRadius:5,
                    pointHitRadius: 10,
                    pointBackgroundColor: "#fff",
                    pointHoverBackgroundColor: "#fff",
                    pointBorderWidth: "2",
                }]
            },

            // Configuration options
            options: {
                layout: {
                    padding: 10,
                },
                legend: { display: false },
                title:  { display: false },
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: false
                        },
                        gridLines: {
                            borderDash: [6, 10],
                            color: "#d8d8d8",
                            lineWidth: 1,
                        },
                        ticks: {
                            beginAtZero:true
                        }
                    }],
                    xAxes: [{
                        scaleLabel: { display: false },
                        gridLines:  { display: false },
                    }],
                },
                tooltips: {
                    backgroundColor: '#333',
                    titleFontSize: 13,
                    titleFontColor: '#fff',
                    bodyFontColor: '#fff',
                    bodyFontSize: 13,
                    displayColors: false,
                    xPadding: 10,
                    yPadding: 10,
                    intersect: false
                }
            },
        });

    </script>
@endpush
