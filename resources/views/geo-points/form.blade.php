@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ isset($geoPoint) ? 'Edit Geo Point' : 'Add Geo Point' }}</h3>
            <div>
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#locationDataModal">
                    <i class="bi bi-magic"></i> Generate Location Data
                </button>
                <a href="{{ route('geo-points.index', ['city_id' => $cityId ?? (isset($geoPoint) ? $geoPoint->city_id : ''), 'currentLocale' => $currentLocale]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(isset($selectedCity))
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About {{ $selectedCity->{"city_name_" . $currentLocale} }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $selectedCity->{"city_description_" . $currentLocale} }}</p>
                    <div class="mt-2 text-muted">
                        <small><i class="bi bi-globe me-1"></i> {{ $selectedCity->country->{"name_" . $currentLocale} }}</small>
                    </div>
                </div>
            </div>
        @elseif(isset($cityId) && !isset($geoPoint))
            @php
                $city = \App\Models\City::with('country')->find($cityId);
            @endphp
            @if($city)
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
        @endif

        <form action="{{ isset($geoPoint) ? route('geo-points.update', ['geo_point' => $geoPoint, 'currentLocale' => $currentLocale]) : route('geo-points.store', ['currentLocale' => $currentLocale]) }}" method="POST">
            @csrf
            @if(isset($geoPoint))
                @method('PUT')
            @endif

            <input type="hidden" id="currentLocale" value="{{ $currentLocale }}">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="city_id" class="form-label"><i class="bi bi-building me-1"></i> City</label>
                    <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required {{ isset($cityId) && !isset($geoPoint) ? 'disabled' : '' }}>
                        <option value="">Select City</option>
                        @php
                            $citiesByCountry = $cities->groupBy(function($city) use ($currentLocale) {
                                return $city->country->{"name_" . $currentLocale};
                            });
                        @endphp

                        @foreach($citiesByCountry as $countryName => $citiesGroup)
                            <optgroup label="{{ $countryName }}">
                                @foreach($citiesGroup->sortBy("city_name_" . $currentLocale) as $city)
                                    <option value="{{ $city->id }}" {{ (old('city_id', isset($geoPoint) ? $geoPoint->city_id : $cityId) == $city->id) ? 'selected' : '' }}>
                                        {{ $city->{"city_name_" . $currentLocale} }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @if(isset($cityId) && !isset($geoPoint))
                        <input type="hidden" name="city_id" value="{{ $cityId }}">
                    @endif
                    @error('city_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <h4>{{ strtoupper($currentLocale) }} Content</h4>

                <div class="mb-3">
                    <label for="name_{{ $currentLocale }}" class="form-label"><i class="bi bi-tag me-1"></i> Name ({{ strtoupper($currentLocale) }})</label>
                    <div class="input-group">
                        <input type="text"
                               class="form-control @error('name_'.($currentLocale)) is-invalid @enderror"
                               id="name_{{ $currentLocale }}"
                               name="name_{{ $currentLocale }}"
                               value="{{ old('name_'.($currentLocale), isset($geoPoint) ? $geoPoint->{'name_'.($currentLocale)} : '') }}"
                               required>
                        @if(isset($geoPoint))
                            <button type="button"
                                    class="btn btn-outline-success geocode-button"
                                    data-geo-point-id="{{ $geoPoint->id }}"
                                    data-locale="{{ $currentLocale }}">
                                <i class="bi bi-geo"></i> Get Coordinates
                            </button>
                        @endif
                    </div>
                    @error('name_'.($currentLocale))
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($geoPoint))
                        <small class="text-muted">
                            Click "Get Coordinates" to automatically fill latitude and longitude based on the name.
                        </small>

                        <div class="mt-2">
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="nameTranslationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-translate"></i> Translate Name
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="nameTranslationDropdown">
                                    @foreach($availableLocales as $locale)
                                        @if($locale !== $currentLocale)
                                            <li>
                                                <button type="button"
                                                        class="dropdown-item translate-name-button"
                                                        data-entity-type="geo_point"
                                                        data-entity-id="{{ $geoPoint->id }}"
                                                        data-source-locale="{{ $currentLocale }}"
                                                        data-target-locale="{{ $locale }}">
                                                    <i class="bi bi-arrow-right"></i> Translate to {{ strtoupper($locale) }} ({{ config('languages.languages.'.$locale.'.native') }})
                                                </button>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>

                            <button type="button"
                                    class="btn btn-sm btn-outline-info translate-all-names-button ms-2"
                                    data-entity-type="geo_point"
                                    data-entity-id="{{ $geoPoint->id }}"
                                    data-source-locale="{{ $currentLocale }}">
                                <i class="bi bi-globe"></i> Translate to All Languages
                            </button>
                        </div>
                    @endif
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="lat" class="form-label"><i class="bi bi-geo-alt me-1"></i> Latitude</label>
                        <input type="text" class="form-control @error('lat') is-invalid @enderror" id="lat" name="lat" value="{{ old('lat', isset($geoPoint) ? $geoPoint->lat : '') }}" required>
                        @error('lat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="long" class="form-label"><i class="bi bi-geo-alt me-1"></i> Longitude</label>
                        <input type="text" class="form-control @error('long') is-invalid @enderror" id="long" name="long" value="{{ old('long', isset($geoPoint) ? $geoPoint->long : '') }}" required>
                        @error('long')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description_{{ $currentLocale }}" class="form-label"><i class="bi bi-card-text me-1"></i> Description ({{ strtoupper($currentLocale) }})</label>

                    @if(isset($geoPoint))
                        <div class="mb-2">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="translationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-translate"></i> Translate Description
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="translationDropdown">
                                    @foreach($availableLocales as $locale)
                                        @if($locale !== $currentLocale)
                                            <li>
                                                <button type="button"
                                                        class="dropdown-item translate-button"
                                                        data-entity-type="geo_point"
                                                        data-entity-id="{{ $geoPoint->id }}"
                                                        data-source-locale="{{ $currentLocale }}"
                                                        data-target-locale="{{ $locale }}">
                                                    <i class="bi bi-arrow-right"></i> Translate to {{ strtoupper($locale) }} ({{ config('languages.languages.'.$locale.'.native') }})
                                                </button>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <textarea class="form-control @error('description_'.($currentLocale)) is-invalid @enderror"
                              id="description_{{ $currentLocale }}"
                              name="description_{{ $currentLocale }}"
                              rows="5">{{ old('description_'.($currentLocale), isset($geoPoint) ? $geoPoint->{'description_'.($currentLocale)} : '') }}</textarea>
                    @error('description_'.($currentLocale))
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if(isset($validationRules[$currentLocale]))
                        <small class="text-muted">
                            Required: {{ $validationRules[$currentLocale]['geo_point_description_min'] }} - {{ $validationRules[$currentLocale]['geo_point_description_max'] }} words
                        </small>
                    @endif
                </div>
            </div>

            <!-- Hidden inputs for other languages -->
            @foreach($availableLocales as $locale)
                @if($locale !== ($currentLocale))
                    <input type="hidden"
                           name="name_{{ $locale }}"
                           value="{{ old('name_'.$locale, isset($geoPoint) ? $geoPoint->{'name_'.$locale} : '') }}">
                    <input type="hidden"
                           name="description_{{ $locale }}"
                           value="{{ old('description_'.$locale, isset($geoPoint) ? $geoPoint->{'description_'.$locale} : '') }}">
                @endif
            @endforeach

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ isset($geoPoint) ? 'Update Geo Point' : 'Create Geo Point' }}
                </button>
                <a href="{{ route('geo-points.index', ['city_id' => $cityId ?? (isset($geoPoint) ? $geoPoint->city_id : ''), 'currentLocale' => $currentLocale]) }}" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Location Data Modal -->
<div class="modal fade" id="locationDataModal" tabindex="-1" aria-labelledby="locationDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="locationDataModalLabel"><i class="bi bi-magic"></i> Generate Location Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="locationDataForm">
                    <input type="hidden" name="locale" value="{{ $currentLocale }}">

                    <div class="mb-3">
                        <label for="place_name" class="form-label"><i class="bi bi-tag"></i> Place Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="place_name" name="place_name" required>
                            @if(isset($geoPoint))
                                <button type="button" class="btn btn-outline-secondary" id="fillFromExisting">
                                    <i class="bi bi-arrow-left-right"></i> Use Current
                                </button>
                            @endif
                        </div>
                        <small class="text-muted">Enter the name of the location (e.g., Eiffel Tower, Central Park)</small>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label"><i class="bi bi-geo-alt"></i> Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                        <small class="text-muted">Enter the address or location details</small>
                    </div>

                    <div class="mb-3">
                        <label for="modal_city_id" class="form-label"><i class="bi bi-building"></i> City</label>
                        <select class="form-select" id="modal_city_id" name="city_id" required>
                            <option value="">Select City</option>
                            @foreach($citiesByCountry as $countryName => $citiesGroup)
                                <optgroup label="{{ $countryName }}">
                                    @foreach($citiesGroup->sortBy("city_name_" . $currentLocale) as $city)
                                        <option value="{{ $city->id }}" {{ (old('city_id', isset($geoPoint) ? $geoPoint->city_id : $cityId) == $city->id) ? 'selected' : '' }}>
                                            {{ $city->{"city_name_" . $currentLocale} }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="card mb-3 border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-info-circle"></i> Language Requirements for {{ strtoupper($currentLocale) }}</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $requirements = config("languages.languages.{$currentLocale}.requirements") ?? [
                                    'min_words' => 20,
                                    'max_words' => 50,
                                    'tone' => 'informative',
                                    'focus' => 'historical and cultural significance',
                                    'language_name' => config("languages.languages.{$currentLocale}.name") ?? $currentLocale
                                ];
                            @endphp
                            <ul class="mb-0 ps-3">
                                <li><strong>Words:</strong> {{ $requirements['min_words'] }} - {{ $requirements['max_words'] }}</li>
                                <li><strong>Tone:</strong> {{ ucfirst($requirements['tone']) }}</li>
                                <li><strong>Focus:</strong> {{ ucfirst($requirements['focus']) }}</li>
                                <li><strong>Language:</strong> {{ config("languages.languages.{$currentLocale}.name") }}</li>
                            </ul>
                            <small class="text-muted mt-2 d-block">The generated description will adhere to these language-specific requirements.</small>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="generateLocationData">
                            <i class="bi bi-magic"></i> Generate Data
                        </button>
                    </div>
                </form>

                <div id="locationDataPreview" class="mt-4 d-none">
                    <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-info-circle"></i> Generated Data Preview</h5>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong><i class="bi bi-tag"></i> Name</strong>
                        </div>
                        <div class="card-body">
                            <p id="previewName" class="mb-0"></p>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong><i class="bi bi-geo-alt"></i> Coordinates</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-0"><strong>Latitude:</strong> <span id="previewLat"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0"><strong>Longitude:</strong> <span id="previewLong"></span></p>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div id="previewMap" style="height: 200px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong><i class="bi bi-card-text"></i> Description</strong>
                                <small class="text-muted"><span id="previewWordCount">0</span> words</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <p id="previewDescription" class="mb-0"></p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Click "Apply to Form" to use this data in your geo point.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="applyLocationData" disabled>
                    <i class="bi bi-check-circle"></i> Apply to Form
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the "Fill from existing" button
        const fillFromExistingBtn = document.getElementById('fillFromExisting');
        if (fillFromExistingBtn) {
            fillFromExistingBtn.addEventListener('click', function() {
                const currentName = document.getElementById('name_{{ $currentLocale }}').value;
                document.getElementById('place_name').value = currentName;
            });
        }

        // Initialize map preview
        let previewMap = null;
        let previewMarker = null;

        // Listen for when the preview is shown
        document.addEventListener('locationDataGenerated', function(e) {
            const data = e.detail;

            // Initialize map if not already done
            if (!previewMap) {
                previewMap = L.map('previewMap').setView([data.coordinates.lat, data.coordinates.long], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(previewMap);

                previewMarker = L.marker([data.coordinates.lat, data.coordinates.long]).addTo(previewMap);
            } else {
                // Update marker position
                previewMap.setView([data.coordinates.lat, data.coordinates.long], 13);
                previewMarker.setLatLng([data.coordinates.lat, data.coordinates.long]);
            }

            // Ensure map is properly sized
            setTimeout(function() {
                previewMap.invalidateSize();
            }, 100);
        });
    });
</script>
@endpush
@endsection
