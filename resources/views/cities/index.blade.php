@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                @if(isset($country))
                    Cities in {{ $country->{"name_" . ($currentLocale ?? app()->getLocale())} }}
                @else
                    All Cities
                @endif
            </h3>
            <div>
                @if(isset($country))
                    <a href="{{ route('countries.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Countries
                    </a>
                    <a href="{{ route('countries.cities.create', ['country' => $country->id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add City
                    </a>
                @else
                    <a href="{{ route('cities.create', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add City
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(isset($country))
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About {{ $country->{"name_" . ($currentLocale ?? app()->getLocale())} }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $country->{"description_" . ($currentLocale ?? app()->getLocale())} }}</p>
                </div>
            </div>
        @endif

        <div class="mb-3">
            <form action="{{ route('cities.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search cities...">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Search
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('cities.index', ['country_id' => $country->id ?? null, 'currentLocale' => $currentLocale]) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="input-group me-2">
                                <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                <select name="country_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Countries</option>
                                    @foreach($countries->sortBy("name_" . $currentLocale) as $countryOption)
                                        <option value="{{ $countryOption->id }}" {{ (isset($country) && $country->id == $countryOption->id) ? 'selected' : '' }}>
                                            {{ $countryOption->{"name_" . $currentLocale} }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group me-2">
                                <span class="input-group-text"><i class="bi bi-sort-down"></i></span>
                                <select name="sortField" class="form-select" onchange="this.form.submit()">
                                    <option value="city_name" {{ $sortField === 'city_name' ? 'selected' : '' }}>Sort by City Name</option>
                                    <option value="country_id" {{ $sortField === 'country_id' ? 'selected' : '' }}>Sort by Country</option>
                                    <option value="geo_points_count" {{ $sortField === 'geo_points_count' ? 'selected' : '' }}>Sort by Geo Points</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-sort-alpha-down"></i></span>
                                <select name="sortDirection" class="form-select" onchange="this.form.submit()">
                                    <option value="asc" {{ $sortDirection === 'asc' ? 'selected' : '' }}>A-Z</option>
                                    <option value="desc" {{ $sortDirection === 'desc' ? 'selected' : '' }}>Z-A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="currentLocale" value="{{ $currentLocale }}">
            </form>
        </div>

        <!-- Map Section -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-map me-2"></i>
                    @if(isset($country))
                        Map of Cities in {{ $country->{"name_" . ($currentLocale ?? app()->getLocale())} }}
                    @else
                        Map of All Cities
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div id="map" style="height: 500px;"></div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prepare cities data for the map
                const cities = [
                    @foreach($cities as $city)
                    {
                        name: "{{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}",
                        countryName: "{{ $city->country->{"name_" . ($currentLocale ?? app()->getLocale())} }}",
                        geoPointsCount: {{ $city->geo_points_count ?? 0 }},
                        url: "{{ route('cities.show', ['city' => $city, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}"
                    },
                    @endforeach
                ];

                // Initialize the map with all cities
                initCityGroupedMap('map', cities);
            });
        </script>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        @if(!isset($country))
                        <th class="text-nowrap">
                            <a href="{{ route('cities.index', ['sortField' => 'country_id', 'sortDirection' => $sortField === 'country_id' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => $search, 'currentLocale' => $currentLocale]) }}" class="text-white text-decoration-none">
                                <i class="bi bi-globe me-1"></i> Country
                                @if($sortField === 'country_id')
                                    @if($sortDirection === 'asc')
                                        <i class="bi bi-arrow-up"></i>
                                    @else
                                        <i class="bi bi-arrow-down"></i>
                                    @endif
                                @endif
                            </a>
                        </th>
                        @endif
                        <th class="text-nowrap">
                            <a href="{{ route('cities.index', ['country_id' => $country->id ?? null, 'sortField' => 'city_name', 'sortDirection' => $sortField === 'city_name' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => $search, 'currentLocale' => $currentLocale]) }}" class="text-white text-decoration-none">
                                <i class="bi bi-building me-1"></i> City Name
                                @if($sortField === 'city_name')
                                    @if($sortDirection === 'asc')
                                        <i class="bi bi-arrow-up"></i>
                                    @else
                                        <i class="bi bi-arrow-down"></i>
                                    @endif
                                @endif
                            </a>
                        </th>
                        <th><i class="bi bi-card-text me-1"></i> Description</th>
                        <th class="text-center text-nowrap">
                            <a href="{{ route('cities.index', ['country_id' => $country->id ?? null, 'sortField' => 'geo_points_count', 'sortDirection' => $sortField === 'geo_points_count' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => $search, 'currentLocale' => $currentLocale]) }}" class="text-white text-decoration-none">
                                <i class="bi bi-geo-alt me-1"></i> Geo Points
                                @if($sortField === 'geo_points_count')
                                    @if($sortDirection === 'asc')
                                        <i class="bi bi-arrow-up"></i>
                                    @else
                                        <i class="bi bi-arrow-down"></i>
                                    @endif
                                @endif
                            </a>
                        </th>
                        <th class="text-end text-nowrap"><i class="bi bi-gear me-1"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cities as $city)
                        <tr>
                            @if(!isset($country))
                            <td>{{ $city->country->{"name_" . ($currentLocale ?? app()->getLocale())} }}</td>
                            @endif
                            <td>{{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}</td>
                            <td>{{ Str::limit($city->{"city_description_" . ($currentLocale ?? app()->getLocale())}, 50) }}</td>
                            <td class="text-center">
                                @if($city->geo_points_count > 0)
                                    <a href="{{ route('cities.geo-points.index', ['city' => $city->id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}"
                                       class="badge bg-success rounded-pill text-decoration-none"
                                       data-bs-toggle="tooltip"
                                       title="View Geo Points for {{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}">
                                        {{ $city->geo_points_count }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary rounded-pill">0</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <x-action-buttons
                                    :model="$city"
                                    :showRoute="route('cities.show', ['city' => $city, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                    :editRoute="route('cities.edit', ['city' => $city, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                    :deleteRoute="route('cities.destroy', ['city' => $city, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                    deleteMessage="Are you sure you want to delete this city? This will also delete all related geo points."
                                >
                                </x-action-buttons>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ isset($country) ? 4 : 5 }}" class="text-center">No cities found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $cities->appends(['country_id' => $country->id ?? null, 'search' => $search, 'sortField' => $sortField, 'sortDirection' => $sortDirection, 'currentLocale' => $currentLocale ?? app()->getLocale()])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
