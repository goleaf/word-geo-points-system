@extends('layouts.app')

@section('content')
<div class="card map-view">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                <i class="bi bi-map me-2"></i>
                @if(isset($city))
                    Geo Points Map for {{ $city->{"city_name_" . $currentLocale} }}
                @else
                    All Geo Points Map
                @endif
            </h3>
            <div>
                <a href="{{ route('geo-points.index', ['currentLocale' => $currentLocale]) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-table"></i> Table View
                </a>
                @if(isset($city))
                    <a href="{{ route('cities.geo-points.create', ['city' => $city->id, 'currentLocale' => $currentLocale]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Geo Point
                    </a>
                @else
                    <a href="{{ route('geo-points.create', ['currentLocale' => $currentLocale]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Geo Point
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('geo-points.map') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search geo points...">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Search
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('geo-points.map', ['city_id' => $city->id ?? null, 'currentLocale' => $currentLocale]) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <select name="city_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Cities</option>
                                @php
                                    $citiesByCountry = $cities->groupBy(function($city) use ($currentLocale) {
                                        return $city->country->{"name_" . $currentLocale};
                                    });
                                @endphp

                                @foreach($citiesByCountry as $countryName => $citiesGroup)
                                    <optgroup label="{{ $countryName }}">
                                        @foreach($citiesGroup->sortBy("city_name_" . $currentLocale) as $cityOption)
                                            <option value="{{ $cityOption->id }}" {{ (isset($city) && $city->id == $cityOption->id) ? 'selected' : '' }}>
                                                {{ $cityOption->{"city_name_" . $currentLocale} }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="currentLocale" value="{{ $currentLocale }}">
            </form>
        </div>

        @if(isset($city))
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About {{ $city->{"city_name_" . $currentLocale} }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $city->{"city_description_" . $currentLocale} }}</p>
                    <div class="mt-2 text-muted">
                        <small><i class="bi bi-globe me-1"></i> {{ $city->country->{"name_" . $currentLocale} }}</small>
                    </div>
                </div>
            </div>
        @endif

        <!-- Map Section -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-map me-2"></i>
                    @if(isset($city))
                        Interactive Map of {{ $city->{"city_name_" . $currentLocale} }}
                    @else
                        Interactive Map of All Geo Points
                    @endif
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="map-container">
                    <div id="map-loading" class="map-loading">
                        <div class="spinner-border text-primary map-loading-spinner" role="status">
                            <span class="sr-only">Loading map...</span>
                        </div>
                    </div>
                    <div id="map" style="height: 100%; width: 100%;"></div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-success h-100 stats-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Total Geo Points</h5>
                    </div>
                    <div class="card-body">
                        <h2 class="display-4">
                            {{ collect($geoPointsByCity)->sum(function($city) { return count($city['points']); }) }}
                        </h2>
                        <p class="text-muted">Points on the map</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-primary h-100 stats-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-building me-2"></i>Cities</h5>
                    </div>
                    <div class="card-body">
                        <h2 class="display-4">{{ count($geoPointsByCity) }}</h2>
                        <p class="text-muted">Cities with geo points</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info h-100 stats-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-globe me-2"></i>Countries</h5>
                    </div>
                    <div class="card-body">
                        <h2 class="display-4">
                            {{ collect($geoPointsByCity)->pluck('countryName')->unique()->count() }}
                        </h2>
                        <p class="text-muted">Countries represented</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- City List Section -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Cities with Geo Points</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($geoPointsByCity as $cityData)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-secondary city-card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bi bi-building me-2"></i>{{ $cityData['name'] }}
                                        <span class="badge bg-primary float-end">{{ count($cityData['points']) }} points</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><small class="text-muted"><i class="bi bi-globe me-1"></i>{{ $cityData['countryName'] }}</small></p>
                                    <ul class="list-group list-group-flush">
                                        @foreach(array_slice($cityData['points'], 0, 3) as $point)
                                            <li class="list-group-item px-0">
                                                <a href="{{ $point['url'] }}" class="text-decoration-none">
                                                    <i class="bi bi-geo-alt me-1"></i>{{ $point['name'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                        @if(count($cityData['points']) > 3)
                                            <li class="list-group-item px-0 text-center">
                                                <small class="text-muted">And {{ count($cityData['points']) - 3 }} more points</small>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data for the map
        const citiesData = @json($geoPointsByCity);

        // Initialize the city grouped map
        initCityGroupedMap('map', citiesData);

        // Hide loading indicator after map is initialized
        document.getElementById('map-loading').style.display = 'none';
    });
</script>
@endsection
