<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('countries.*') ? 'active' : '' }}" href="{{ route('countries.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}">
                            <i class="bi bi-globe"></i> Countries
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cities.*') ? 'active' : '' }}" href="{{ route('cities.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}">
                            <i class="bi bi-building"></i> Cities
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('geo-points.*') ? 'active' : '' }}" href="{{ route('geo-points.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}">
                            <i class="bi bi-geo-alt"></i> Geo Points
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('language-manager.*') ? 'active' : '' }}" href="{{ route('language-manager.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}">
                            <i class="bi bi-gear"></i> Language Manager
                        </a>
                    </li>

                    <!-- Language Switcher -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-translate"></i> {{ strtoupper(session('currentLocale', config('languages.default'))) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            @foreach(array_keys(config('languages.languages') ?? []) as $locale)
                                <li>
                                    <a class="dropdown-item {{ session('currentLocale') === $locale ? 'active' : '' }}"
                                       href="{{ route('locale.change', ['locale' => $locale, 'redirect' => url()->current()]) }}">
                                        {{ config('languages.languages.'.$locale.'.native') }} ({{ strtoupper($locale) }})
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container flex-grow-1">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-light py-4 mt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center mb-3">
                    <h5 class="mb-3 border-bottom pb-2">Database Statistics</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="card-title mb-0"><i class="bi bi-globe"></i> Countries</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold">Total:</span>
                                        <span class="badge bg-primary rounded-pill">{{ \App\Models\Country::count() }}</span>
                                    </div>
                                    @php
                                        $countryStats = \App\Models\Country::withCount('cities')->get();
                                        $maxCitiesCountry = $countryStats->sortByDesc('cities_count')->first();
                                        $avgCitiesPerCountry = $countryStats->avg('cities_count');
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                                        <span>Most cities:</span>
                                        <span class="text-primary">
                                            @if($maxCitiesCountry)
                                                {{ $maxCitiesCountry->{"name_" . app()->getLocale()} }} ({{ $maxCitiesCountry->cities_count }})
                                            @else
                                                None
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span>Avg cities:</span>
                                        <span class="text-primary">{{ number_format($avgCitiesPerCountry, 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0"><i class="bi bi-building"></i> Cities</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold">Total:</span>
                                        <span class="badge bg-info rounded-pill">{{ \App\Models\City::count() }}</span>
                                    </div>
                                    @php
                                        $cityStats = \App\Models\City::withCount('geoPoints')->get();
                                        $maxPointsCity = $cityStats->sortByDesc('geo_points_count')->first();
                                        $avgPointsPerCity = $cityStats->avg('geo_points_count');
                                        $emptyCities = $cityStats->where('geo_points_count', 0)->count();
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                                        <span>Most points:</span>
                                        <span class="text-info">
                                            @if($maxPointsCity)
                                                {{ $maxPointsCity->{"city_name_" . app()->getLocale()} }} ({{ $maxPointsCity->geo_points_count }})
                                            @else
                                                None
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span>Empty cities:</span>
                                        <span class="text-info">{{ $emptyCities }} (Avg: {{ number_format($avgPointsPerCity, 1) }})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0"><i class="bi bi-geo-alt"></i> Geo Points</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold">Total:</span>
                                        <span class="badge bg-success rounded-pill">{{ \App\Models\GeoPoint::count() }}</span>
                                    </div>
                                    @php
                                        $latestPoint = \App\Models\GeoPoint::latest()->first();
                                        $northernmost = \App\Models\GeoPoint::orderByDesc('lat')->first();
                                        $southernmost = \App\Models\GeoPoint::orderBy('lat')->first();
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                                        <span>Latest:</span>
                                        <span class="text-success">
                                            @if($latestPoint)
                                                {{ $latestPoint->{"name_" . app()->getLocale()} }}
                                            @else
                                                None
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span>Coordinates:</span>
                                        <span class="text-success">
                                            @if($northernmost && $southernmost)
                                                N: {{ number_format($northernmost->lat, 2) }} |
                                                S: {{ number_format($southernmost->lat, 2) }}
                                            @else
                                                No data
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <p class="mb-0 text-muted border-top pt-3">
                        <small>
                            <span class="me-3"><i class="bi bi-translate"></i> Languages: {{ count(array_keys(config('languages.languages') ?? [])) }}</span>
                            <span class="me-3"><i class="bi bi-check-circle"></i> Current: {{ strtoupper(app()->getLocale()) }}</span>
                            <span><i class="bi bi-clock"></i> Last updated: {{ now()->format('Y-m-d H:i') }}</span>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
