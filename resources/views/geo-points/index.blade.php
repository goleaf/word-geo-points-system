@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                @if(isset($city))
                    Geo Points in {{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}
                @else
                    All Geo Points
                @endif
            </h3>
            <div>
                @if(isset($city))
                    <a href="{{ route('cities.index', ['country_id' => $city->country_id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Cities
                    </a>
                    <a href="{{ route('cities.geo-points.create', ['city' => $city->id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Geo Point
                    </a>
                @else
                    <a href="{{ route('geo-points.create', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Geo Point
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('geo-points.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search geo points...">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Search
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('geo-points.index', ['city_id' => $city->id ?? null, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="input-group me-2">
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
                <input type="hidden" name="sortField" value="{{ $sortField }}">
                <input type="hidden" name="currentLocale" value="{{ $currentLocale }}">
            </form>
        </div>

        @if(isset($city))
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About {{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $city->{"city_description_" . ($currentLocale ?? app()->getLocale())} }}</p>
                    <div class="mt-2 text-muted">
                        <small><i class="bi bi-globe me-1"></i> {{ $city->country->{"name_" . ($currentLocale ?? app()->getLocale())} }}</small>
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
                        Map of Geo Points in {{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}
                    @else
                        Map of All Geo Points
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="map-container">
                    <div id="map-loading" class="map-loading">
                        <div class="spinner-border text-primary map-loading-spinner" role="status">
                            <span class="sr-only">Loading map...</span>
                        </div>
                    </div>
                    <div id="map" class="h-100"></div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prepare geo points data for the map
                const geoPoints = [
                    @foreach($geoPoints as $geoPoint)
                    {
                        lat: {{ $geoPoint->lat }},
                        lng: {{ $geoPoint->long }},
                        name: "{{ $geoPoint->{"name_" . $currentLocale} }}",
                        description: "{{ Str::limit($geoPoint->{"description_" . $currentLocale}, 100) }}",
                        cityName: "{{ $geoPoint->city->{"city_name_" . $currentLocale} }}",
                        countryName: "{{ $geoPoint->city->country->{"name_" . $currentLocale} }}",
                        url: "{{ route('geo-points.show', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale]) }}"
                    },
                    @endforeach
                ];

                // Initialize the map with all geo points
                initMultiPointMap('map', geoPoints);

                // Hide loading indicator after map is initialized
                document.getElementById('map-loading').style.display = 'none';
            });
        </script>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        @if(!isset($city))
                        <th class="text-nowrap">
                            <a href="{{ route('geo-points.index', ['sortField' => 'city_id', 'sortDirection' => $sortField === 'city_id' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => $search, 'currentLocale' => $currentLocale]) }}" class="text-white text-decoration-none">
                                <i class="bi bi-building me-1"></i> City
                                @if($sortField === 'city_id')
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
                            <a href="{{ route('geo-points.index', ['city_id' => $city->id ?? null, 'sortField' => 'name', 'sortDirection' => $sortField === 'name' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => $search, 'currentLocale' => $currentLocale]) }}" class="text-white text-decoration-none">
                                <i class="bi bi-tag me-1"></i> Name
                                @if($sortField === 'name')
                                    @if($sortDirection === 'asc')
                                        <i class="bi bi-arrow-up"></i>
                                    @else
                                        <i class="bi bi-arrow-down"></i>
                                    @endif
                                @endif
                            </a>
                        </th>
                        <th class="text-nowrap"><i class="bi bi-geo me-1"></i> Coordinates</th>
                        <th><i class="bi bi-card-text me-1"></i> Description</th>
                        <th class="text-end text-nowrap"><i class="bi bi-gear me-1"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($geoPoints as $geoPoint)
                        <tr>
                            @if(!isset($city))
                            <td>{{ $geoPoint->city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }} ({{ $geoPoint->city->country->{"name_" . ($currentLocale ?? app()->getLocale())} }})</td>
                            @endif
                            <td>{{ $geoPoint->{"name_" . ($currentLocale ?? app()->getLocale())} }}</td>
                            <td>{{ $geoPoint->lat }}, {{ $geoPoint->long }}</td>
                            <td>{{ Str::limit($geoPoint->{"description_" . ($currentLocale ?? app()->getLocale())}, 50) }}</td>
                            <td class="text-end">
                                <x-action-buttons
                                    :model="$geoPoint"
                                    :showRoute="route('geo-points.show', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                    :editRoute="route('geo-points.edit', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                    :deleteRoute="route('geo-points.destroy', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                    deleteMessage="Are you sure you want to delete this geo point?"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ isset($city) ? 4 : 5 }}" class="text-center">No geo points found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $geoPoints->appends(['city_id' => $city->id ?? null, 'search' => $search, 'sortField' => $sortField, 'sortDirection' => $sortDirection, 'currentLocale' => $currentLocale ?? app()->getLocale()])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
