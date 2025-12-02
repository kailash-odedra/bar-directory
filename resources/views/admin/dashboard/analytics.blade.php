@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/src/apex/apexcharts.css')}}">
@vite(['resources/scss/light/assets/dashboard/dash_1.scss'])
@vite(['resources/scss/dark/assets/dashboard/dash_1.scss'])
<style>
    .widget-one_hybrid a {
        text-decoration: none !important;
        color: inherit !important;
    }
    .widget-one_hybrid:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    .layout-spacing {
        margin-bottom: 20px;
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--bs-heading-color);
    }
    .dashboard-section {
        margin-top: 2rem;
    }
    .bar-card {
        border-left: 3px solid #00b894;
        padding-left: 1rem;
        margin-bottom: 0.75rem;
    }
    .bar-card:hover {
        background-color: rgba(0, 184, 148, 0.05);
        transition: background-color 0.2s ease;
    }
    .city-item, .tag-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 0.375rem;
        background-color: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
    }
    .city-item:hover, .tag-item:hover {
        background-color: rgba(0, 123, 255, 0.05);
        transition: background-color 0.2s ease;
    }
</style>
@endsection

@section('content')
<div class="row layout-top-spacing">

    <!-- Dashboard Overview Statistics -->
    <!-- Total Bars -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 layout-spacing">
        <a href="{{ route('admin.bar.index') }}" class="text-decoration-none" style="color: inherit;">
            <div class="widget widget-one_hybrid widget-followers">
                <div class="widget-heading">
                    <div class="w-title">
                        <div class="w-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                        <div class="">
                            <p class="w-value">{{ number_format($totalBars) }}</p>
                            <h5 class="">Total Bars</h5>
                        </div>
                    </div>
                </div>
                <div class="widget-content">    
                    <div class="w-chart">
                        <div id="total-bars-chart"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Claimed Bars -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 layout-spacing">
        <a href="{{ route('admin.bar.index', ['claimed' => 1]) }}" class="text-decoration-none" style="color: inherit;">
            <div class="widget widget-one_hybrid widget-referral">
                <div class="widget-heading">
                    <div class="w-title">
                        <div class="w-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="">
                            <p class="w-value">{{ number_format($claimedBars) }}</p>
                            <h5 class="">Claimed Bars</h5>
                        </div>
                    </div>
                </div>
                <div class="widget-content">    
                    <div class="w-chart">
                        <div id="claimed-bars-chart"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Pending Claims -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 layout-spacing">
        <a href="{{ route('admin.claims.index', ['status' => 'pending']) }}" class="text-decoration-none" style="color: inherit;">
            <div class="widget widget-one_hybrid widget-engagement">
                <div class="widget-heading">
                    <div class="w-title">
                        <div class="w-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="">
                            <p class="w-value">{{ number_format($pendingClaims) }}</p>
                            <h5 class="">Pending Claims</h5>
                        </div>
                    </div>
                </div>
                <div class="widget-content">    
                    <div class="w-chart">
                        <div id="pending-claims-chart"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Active Reviews -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 layout-spacing">
        <a href="{{ route('admin.bar-reviews.index', ['status' => 1]) }}" class="text-decoration-none" style="color: inherit;">
            <div class="widget widget-one_hybrid widget-followers">
                <div class="widget-heading">
                    <div class="w-title">
                        <div class="w-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        <div class="">
                            <p class="w-value">{{ number_format($activeReviews) }}</p>
                            <h5 class="">Active Reviews</h5>
                        </div>
                    </div>
                </div>
                <div class="widget-content">    
                    <div class="w-chart">
                        <div id="active-reviews-chart"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Featured Bars -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 layout-spacing">
        <a href="{{ route('admin.bar.index', ['featured' => 1]) }}" class="text-decoration-none" style="color: inherit;">
            <div class="widget widget-one_hybrid widget-referral">
                <div class="widget-heading">
                    <div class="w-title">
                        <div class="w-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-award">
                                <circle cx="12" cy="8" r="7"></circle>
                                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                            </svg>
                        </div>
                        <div class="">
                            <p class="w-value">{{ number_format($featuredBars) }}</p>
                            <h5 class="">Featured Bars</h5>
                        </div>
                    </div>
                </div>
                <div class="widget-content">    
                    <div class="w-chart">
                        <div id="featured-bars-chart"></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Top Rated Bars Section -->
    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing dashboard-section">
        <div class="widget widget-chart-three">
            <div class="widget-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star me-2" style="color: #fdcb6e;">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        Top Rated Bars (4+ Stars)
                    </h5>
                    <a href="{{ route('admin.bar.index') }}" class="text-primary small">View All</a>
                </div>
            </div>
            <div class="widget-content">
                @if($topRatedBars->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Bar Name</th>
                                    <th>Location</th>
                                    <th class="text-end">Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topRatedBars->take(10) as $bar)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.bar.edit', $bar) }}" class="text-decoration-none fw-semibold">
                                            {{ $bar->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $bar->location->city ?? 'N/A' }}{{ $bar->location->state ? ', ' . $bar->location->state->name : '' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success">
                                            {{ number_format($bar->avg_rating, 1) }} ⭐
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star text-muted mb-3">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <p class="text-muted mb-0">No bars with 4+ star rating yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Most Searched Cities Section -->
    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing dashboard-section">
        <div class="widget widget-chart-three">
            <div class="widget-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin me-2" style="color: #00b894;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Most Searched Cities
                    </h5>
                </div>
            </div>
            <div class="widget-content">
                @if($mostSearchedCities->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($mostSearchedCities->take(10) as $city)
                        <div class="city-item">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary rounded-pill me-3">{{ $loop->iteration }}</span>
                                <strong>{{ $city->city }}</strong>
                            </div>
                            <span class="badge bg-info">{{ $city->bar_count }} {{ $city->bar_count == 1 ? 'bar' : 'bars' }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin text-muted mb-3">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <p class="text-muted mb-0">No city data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Trending Tags / Categories Section -->
    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing dashboard-section">
        <div class="widget widget-chart-three">
            <div class="widget-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag me-2" style="color: #6c5ce7;">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        Trending Tags / Categories
                    </h5>
                    <a href="{{ route('admin.bar-tags.index') }}" class="text-primary small">View All</a>
                </div>
            </div>
            <div class="widget-content">
                @if($trendingTags->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($trendingTags->take(10) as $tag)
                        <div class="tag-item">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-3">{{ $tag->name }}</span>
                            </div>
                            <strong class="text-primary">{{ $tag->bars_count }} {{ $tag->bars_count == 1 ? 'bar' : 'bars' }}</strong>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag text-muted mb-3">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        <p class="text-muted mb-0">No tags available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reviews by Status Section -->
    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing dashboard-section">
        <div class="widget widget-chart-three">
            <div class="widget-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle me-2" style="color: #e17055;">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                        Reviews by Status
                    </h5>
                    <a href="{{ route('admin.bar-reviews.index') }}" class="text-primary small">View All</a>
                </div>
            </div>
            <div class="widget-content">
                <div id="reviewsStatusChart" style="min-height: 350px;"></div>
                @if($reviewsByStatus->count() > 0)
                    <div class="mt-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-3 border rounded">
                                    <div class="h4 mb-1 text-success">{{ $reviewsByStatus->get('approved', 0) }}</div>
                                    <small class="text-muted">Approved</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 border rounded">
                                    <div class="h4 mb-1 text-warning">{{ $reviewsByStatus->get('pending', 0) }}</div>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 border rounded">
                                    <div class="h4 mb-1 text-danger">{{ $reviewsByStatus->get('hidden', 0) }}</div>
                                    <small class="text-muted">Hidden</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="{{asset('plugins/src/apex/apexcharts.min.js')}}"></script>
@vite(['resources/js/dashboard/dash_1.js'])

<script>
// Reviews by Status Chart
document.addEventListener('DOMContentLoaded', function() {
    const reviewsData = @json($reviewsByStatus);
    
    const reviewsStatusChart = {
        series: [
            reviewsData.approved || 0,
            reviewsData.pending || 0,
            reviewsData.hidden || 0
        ],
        chart: {
            type: 'donut',
            height: 350,
            toolbar: {
                show: false
            }
        },
        labels: ['Approved', 'Pending', 'Hidden'],
        colors: ['#00b894', '#fdcb6e', '#e17055'],
        legend: {
            position: 'bottom',
            fontSize: '14px',
            fontFamily: 'Nunito, sans-serif'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '16px',
                            fontFamily: 'Nunito, sans-serif',
                            fontWeight: 600
                        },
                        value: {
                            show: true,
                            fontSize: '20px',
                            fontFamily: 'Nunito, sans-serif',
                            fontWeight: 700
                        },
                        total: {
                            show: true,
                            label: 'Total Reviews',
                            fontSize: '14px',
                            fontFamily: 'Nunito, sans-serif',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b
                                }, 0)
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.config.series[opts.seriesIndex] + ' (' + val.toFixed(1) + '%)'
            },
            style: {
                fontSize: '12px',
                fontFamily: 'Nunito, sans-serif'
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + ' reviews'
                }
            }
        }
    };

    const reviewsChart = new ApexCharts(document.querySelector("#reviewsStatusChart"), reviewsStatusChart);
    reviewsChart.render();
});
</script>

@endsection
