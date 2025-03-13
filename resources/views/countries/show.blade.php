@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ $country->{"name_" . ($currentLocale ?? app()->getLocale())} }}</h3>
            <div>
                <a href="{{ route('countries.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
                <a href="{{ route('countries.edit', ['country' => $country, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h4 class="border-bottom pb-2"><i class="bi bi-card-text me-1"></i> Description</h4>
                <p>{{ $country->{"description_" . ($currentLocale ?? app()->getLocale())} }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Map of {{ $country->{"name_" . ($currentLocale ?? app()->getLocale())} }}</h3>
        </div>
    </div>
    <div class="card-body">
        <div id="map" style="height: 500px;"></div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Cities in {{ $country->{"name_" . ($currentLocale ?? app()->getLocale())} }}</h3>
            <a href="{{ route('countries.cities.create', ['country' => $country->id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add City
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('countries.show', ['country' => $country, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search cities...">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Search
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('countries.show', ['country' => $country, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-sort-alpha-down"></i></span>
                            <select name="sortDirection" class="form-select" onchange="this.form.submit()">
                                <option value="asc" {{ $sortDirection === 'asc' ? 'selected' : '' }}>A-Z</option>
                                <option value="desc" {{ $sortDirection === 'desc' ? 'selected' : '' }}>Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="sortField" value="{{ $sortField }}">
                <input type="hidden" name="currentLocale" value="{{ $currentLocale ?? app()->getLocale() }}">
            </form>
        </div>

        @if($cities->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-nowrap">
                                <a href="{{ route('countries.show', ['country' => $country, 'sortField' => 'city_name', 'sortDirection' => $sortField === 'city_name' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => $search, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="text-white text-decoration-none">
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
                                <a href="{{ route('countries.show', ['country' => $country, 'sortField' => 'geo_points_count', 'sortDirection' => $sortField === 'geo_points_count' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => $search, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="text-white text-decoration-none">
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
                        @foreach($cities as $city)
                            <tr>
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
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $cities->appends(['search' => $search, 'sortField' => $sortField, 'sortDirection' => $sortDirection, 'currentLocale' => $currentLocale ?? app()->getLocale()])->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="alert alert-info">
                @if(!empty($search))
                    No cities found matching "{{ $search }}". <a href="{{ route('countries.show', ['country' => $country, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}">Clear search</a> or <a href="{{ route('countries.cities.create', ['country' => $country->id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}">add a new city</a>.
                @else
                    No cities found for this country. <a href="{{ route('countries.cities.create', ['country' => $country->id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}">Add a city</a>.
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare cities data for the map
        const cities = [
            @foreach($cities as $city)
                @php
                    // Get geo points for this city
                    $cityGeoPoints = $city->geoPoints()->get();
                @endphp

                @if($cityGeoPoints->count() > 0)
                {
                    name: "{{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}",
                    countryName: "{{ $country->{"name_" . ($currentLocale ?? app()->getLocale())} }}",
                    points: [
                        @foreach($cityGeoPoints as $geoPoint)
                        {
                            lat: {{ $geoPoint->lat }},
                            lng: {{ $geoPoint->long }},
                            name: "{{ $geoPoint->{"name_" . ($currentLocale ?? app()->getLocale())} }}",
                            description: "{{ Str::limit($geoPoint->{"description_" . ($currentLocale ?? app()->getLocale())}, 100) }}",
                            url: "{{ route('geo-points.show', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}"
                        }{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ]
                }{{ !$loop->last ? ',' : '' }}
                @endif
            @endforeach
        ];

        // Initialize map with cities grouped by geo points
        initCityGroupedMap('map', cities);
    });
</script>
@endpush
