@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Countries</h3>
            <a href="{{ route('countries.create', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Country
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('countries.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search countries...">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('countries.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    @endif
                </div>
                <input type="hidden" name="sortField" value="{{ $sortField }}">
                <input type="hidden" name="sortDirection" value="{{ $sortDirection }}">
                <input type="hidden" name="currentLocale" value="{{ $currentLocale ?? app()->getLocale() }}">
            </form>
        </div>

        <!-- World Map Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-globe me-2"></i>World Map</h5>
            </div>
            <div class="card-body">
                <div id="map" style="height: 500px;"></div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prepare countries data for the map
                const countries = [
                    @foreach($countries as $country)
                    {
                        name: "{{ $country->{"name_" . $currentLocale} }}",
                        cities: [
                            @foreach($country->cities as $city)
                            {
                                name: "{{ $city->{"city_name_" . $currentLocale} }}",
                                geoPointsCount: {{ $city->geo_points_count ?? 0 }},
                                url: "{{ route('cities.show', ['city' => $city, 'currentLocale' => $currentLocale]) }}"
                            },
                            @endforeach
                        ],
                        url: "{{ route('countries.show', ['country' => $country, 'currentLocale' => $currentLocale]) }}"
                    },
                    @endforeach
                ];

                // Initialize the map with all countries
                initCityGroupedMap('map', countries);
            });
        </script>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th class="text-nowrap">
                            <a href="{{ route('countries.index', ['sortField' => 'name', 'sortDirection' => $sortField === 'name' && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => $search, 'currentLocale' => $currentLocale]) }}" class="text-white text-decoration-none">
                                <i class="bi bi-globe me-1"></i> Name
                                @if($sortField === 'name')
                                    @if($sortDirection === 'asc')
                                        <i class="bi bi-arrow-up"></i>
                                    @else
                                        <i class="bi bi-arrow-down"></i>
                                    @endif
                                @endif
                            </a>
                        </th>
                        <th class="text-center text-nowrap">
                            <i class="bi bi-building me-1"></i> Cities
                        </th>
                        <th class="text-end text-nowrap"><i class="bi bi-gear me-1"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $country)
                        <tr>
                            <td>{{ $country->{"name_" . $currentLocale} }}</td>
                            <td class="text-center">
                                @if($country->cities_count > 0)
                                    <a href="{{ route('cities.index', ['country_id' => $country->id, 'currentLocale' => $currentLocale]) }}"
                                       class="badge bg-primary rounded-pill text-decoration-none"
                                       data-bs-toggle="tooltip"
                                       title="View Cities in {{ $country->{"name_" . $currentLocale} }}">
                                        {{ $country->cities_count }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary rounded-pill">0</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <x-action-buttons
                                    :model="$country"
                                    :showRoute="route('countries.show', ['country' => $country, 'currentLocale' => $currentLocale])"
                                    :editRoute="route('countries.edit', ['country' => $country, 'currentLocale' => $currentLocale])"
                                    :deleteRoute="route('countries.destroy', ['country' => $country, 'currentLocale' => $currentLocale])"
                                    deleteMessage="Are you sure you want to delete this country? This will also delete all related cities and geo points."
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No countries found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $countries->appends(['search' => $search, 'sortField' => $sortField, 'sortDirection' => $sortDirection, 'currentLocale' => $currentLocale])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
