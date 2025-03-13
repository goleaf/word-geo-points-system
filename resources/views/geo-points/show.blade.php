@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ $geoPoint->{"name_" . ($currentLocale ?? app()->getLocale())} }}</h3>
            <div>
                <a href="{{ route('cities.geo-points.index', ['city' => $geoPoint->city_id, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
                <a href="{{ route('geo-points.edit', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <h5><i class="bi bi-building me-1"></i> City</h5>
                <p><a href="{{ route('cities.show', ['city' => $geoPoint->city, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}">{{ $geoPoint->city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}</a></p>
            </div>
            <div class="col-md-6">
                <h5><i class="bi bi-globe me-1"></i> Country</h5>
                <p><a href="{{ route('countries.show', ['country' => $geoPoint->city->country, 'currentLocale' => $currentLocale ?? app()->getLocale()]) }}">{{ $geoPoint->city->country->{"name_" . ($currentLocale ?? app()->getLocale())} }}</a></p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <h5><i class="bi bi-geo-alt me-1"></i> Latitude</h5>
                <p>{{ $geoPoint->lat }}</p>
            </div>
            <div class="col-md-6">
                <h5><i class="bi bi-geo-alt me-1"></i> Longitude</h5>
                <p>{{ $geoPoint->long }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h5><i class="bi bi-card-text me-1"></i> Description</h5>
                <p>{{ $geoPoint->{"description_" . ($currentLocale ?? app()->getLocale())} }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Map</h3>
        </div>
    </div>
    <div class="card-body">
        <div id="map" style="height: 400px;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map with zoom level 14
        initSingleGeoPointMap(
            'map',
            {{ $geoPoint->lat }},
            {{ $geoPoint->long }},
            "{{ $geoPoint->{"name_" . ($currentLocale ?? app()->getLocale())} }}",
            "{{ Str::limit($geoPoint->{"description_" . ($currentLocale ?? app()->getLocale())}, 100) }}",
            "{{ $geoPoint->city->{"city_name_" . ($currentLocale ?? app()->getLocale())} }}",
            "{{ $geoPoint->city->country->{"name_" . ($currentLocale ?? app()->getLocale())} }}"
        );
    });
</script>
@endpush
