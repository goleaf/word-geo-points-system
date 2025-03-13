@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ isset($city) ? 'Edit City' : 'Add City' }}</h3>
            <a href="{{ route('cities.index', ['country_id' => $countryId ?? (isset($city) ? $city->country_id : ''), 'currentLocale' => $currentLocale]) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(isset($selectedCountry))
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About {{ $selectedCountry->{"name_" . $currentLocale} }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $selectedCountry->{"description_" . $currentLocale} }}</p>
                </div>
            </div>
        @elseif(isset($countryId) && !isset($city))
            @php
                $country = \App\Models\Country::find($countryId);
            @endphp
            @if($country)
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About {{ $country->{"name_" . $currentLocale} }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $country->{"description_" . $currentLocale} }}</p>
                    </div>
                </div>
            @endif
        @endif

        <form action="{{ isset($city) ? route('cities.update', ['city' => $city, 'currentLocale' => $currentLocale]) : route('cities.store', ['currentLocale' => $currentLocale]) }}" method="POST">
            @csrf
            @if(isset($city))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="country_id" class="form-label"><i class="bi bi-globe me-1"></i> Country</label>
                <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id" required {{ isset($countryId) && !isset($city) ? 'disabled' : '' }}>
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ (old('country_id', isset($city) ? $city->country_id : $countryId) == $country->id) ? 'selected' : '' }}>
                            {{ $country->{"name_" . $currentLocale} }}
                        </option>
                    @endforeach
                </select>
                @if(isset($countryId) && !isset($city))
                    <input type="hidden" name="country_id" value="{{ $countryId }}">
                @endif
                @error('country_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <h4>{{ strtoupper($currentLocale ?? app()->getLocale()) }} Content</h4>

                <div class="mb-3">
                    <label for="city_name_{{ $currentLocale ?? app()->getLocale() }}" class="form-label"><i class="bi bi-building me-1"></i> City Name ({{ strtoupper($currentLocale ?? app()->getLocale()) }})</label>
                    <input type="text"
                           class="form-control @error('city_name_'.($currentLocale ?? app()->getLocale())) is-invalid @enderror"
                           id="city_name_{{ $currentLocale ?? app()->getLocale() }}"
                           name="city_name_{{ $currentLocale ?? app()->getLocale() }}"
                           value="{{ old('city_name_'.($currentLocale ?? app()->getLocale()), isset($city) ? $city->{'city_name_'.($currentLocale ?? app()->getLocale())} : '') }}"
                           required>
                    @error('city_name_'.($currentLocale ?? app()->getLocale()))
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="city_description_{{ $currentLocale ?? app()->getLocale() }}" class="form-label"><i class="bi bi-card-text me-1"></i> Description ({{ strtoupper($currentLocale ?? app()->getLocale()) }})</label>

                    @if(isset($city))
                        <div class="mb-2">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="translationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-translate"></i> Translate Description
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="translationDropdown">
                                    @foreach($availableLocales as $locale)
                                        @if($locale !== ($currentLocale ?? app()->getLocale()))
                                            <li>
                                                <button type="button"
                                                        class="dropdown-item translate-button"
                                                        data-entity-type="city"
                                                        data-entity-id="{{ $city->id }}"
                                                        data-source-locale="{{ $currentLocale ?? app()->getLocale() }}"
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

                    <textarea class="form-control @error('city_description_'.($currentLocale ?? app()->getLocale())) is-invalid @enderror"
                              id="city_description_{{ $currentLocale ?? app()->getLocale() }}"
                              name="city_description_{{ $currentLocale ?? app()->getLocale() }}"
                              rows="5">{{ old('city_description_'.($currentLocale ?? app()->getLocale()), isset($city) ? $city->{'city_description_'.($currentLocale ?? app()->getLocale())} : '') }}</textarea>
                    @error('city_description_'.($currentLocale ?? app()->getLocale()))
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if(isset($validationRules[$currentLocale ?? app()->getLocale()]))
                        <small class="text-muted">
                            Required: {{ $validationRules[$currentLocale ?? app()->getLocale()]['city_description_min'] }} - {{ $validationRules[$currentLocale ?? app()->getLocale()]['city_description_max'] }} words
                        </small>
                    @endif
                </div>
            </div>

            <!-- Hidden inputs for other languages -->
            @foreach($availableLocales as $locale)
                @if($locale !== ($currentLocale ?? app()->getLocale()))
                    <input type="hidden"
                           name="city_name_{{ $locale }}"
                           value="{{ old('city_name_'.$locale, isset($city) ? $city->{'city_name_'.$locale} : '') }}">
                    <input type="hidden"
                           name="city_description_{{ $locale }}"
                           value="{{ old('city_description_'.$locale, isset($city) ? $city->{'city_description_'.$locale} : '') }}">
                @endif
            @endforeach

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ isset($city) ? 'Update City' : 'Create City' }}
                </button>
                <a href="{{ route('cities.index', ['country_id' => $countryId ?? (isset($city) ? $city->country_id : ''), 'currentLocale' => $currentLocale]) }}" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
