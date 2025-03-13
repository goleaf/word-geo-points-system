@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}</h3>
            <div>
                <a href="{{ route('cities.index', ['country_id' => $city->country_id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
                <a href="{{ route('cities.edit', ['city' => $city, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <h5><i class="bi bi-globe me-1"></i> Country</h5>
                <p><a href="{{ route('countries.show', ['country' => $city->country, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}">{{ $city->country->{"name_" . ($currentLocale ?? app()->getLocale())} }}</a></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h5><i class="bi bi-card-text me-1"></i> Description</h5>
                <p>{{ $city->{"city_description_" . ($currentLocale ?? app()->getLocale())} }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Map of {{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}</h3>
        </div>
    </div>
    <div class="card-body">
        <div id="map" style="height: 400px;"></div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Geo Points in {{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}</h3>
            <a href="{{ route('cities.geo-points.create', ['city' => $city->id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Geo Point
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($geoPoints->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="bi bi-tag me-1"></i> Name</th>
                            <th><i class="bi bi-geo me-1"></i> Coordinates</th>
                            <th class="text-end"><i class="bi bi-gear me-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($geoPoints as $geoPoint)
                            <tr>
                                <td>{{ $geoPoint->{"name_" . ($currentLocale ?? app()->getLocale())} }}</td>
                                <td>{{ $geoPoint->lat }}, {{ $geoPoint->long }}</td>
                                <td>
                                    <x-action-buttons
                                        :model="$geoPoint"
                                        :showRoute="route('geo-points.show', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                        :editRoute="route('geo-points.edit', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                        :deleteRoute="route('geo-points.destroy', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                        deleteMessage="Are you sure you want to delete this geo point?"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $geoPoints->appends(['currentLocale' => $currentLocale ?? app()->getLocale()])->links('pagination::bootstrap-5') }}
        @else
            <div class="alert alert-info">
                No geo points found for this city. <a href="{{ route('cities.geo-points.create', ['city' => $city->id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}">Add a geo point</a>.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare geo points data for the map
        const points = [
            @foreach($geoPoints as $geoPoint)
            {
                lat: {{ $geoPoint->lat }},
                lng: {{ $geoPoint->long }},
                name: "{{ $geoPoint->{"name_" . ($currentLocale ?? app()->getLocale())} }}",
                description: "{{ Str::limit($geoPoint->{"description_" . ($currentLocale ?? app()->getLocale())}, 100) }}",
                cityName: "{{ $city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}",
                countryName: "{{ $city->country->{"name_" . ($currentLocale ?? app()->getLocale())} }}",
                url: "{{ route('geo-points.show', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}"
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ];

        // Initialize map with all geo points
        initMultiPointMap('map', points);
    });
</script>
@endpush
