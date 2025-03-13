@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ isset($country) ? 'Edit Country' : 'Add Country' }}</h3>
            <a href="{{ route('countries.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ isset($country) ? route('countries.update', ['country' => $country, 'currentLocale' => $currentLocale ?? app()->getLocale()]) : route('countries.store', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" method="POST">
            @csrf
            @if(isset($country))
                @method('PUT')
            @endif

            <div class="mb-4">
                <h4>{{ strtoupper($currentLocale) }} Content</h4>

                <div class="mb-3">
                    <label for="name_{{ $currentLocale }}" class="form-label"><i class="bi bi-globe me-1"></i> Name ({{ strtoupper($currentLocale) }})</label>
                    <input type="text"
                           class="form-control @error('name_'.$currentLocale) is-invalid @enderror"
                           id="name_{{ $currentLocale }}"
                           name="name_{{ $currentLocale }}"
                           value="{{ old('name_'.$currentLocale, isset($country) ? $country->{'name_'.$currentLocale} : '') }}"
                           required>
                    @error('name_'.$currentLocale)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description_{{ $currentLocale }}" class="form-label"><i class="bi bi-card-text me-1"></i> Description ({{ strtoupper($currentLocale) }})</label>

                    @if(isset($country))
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
                                                        data-entity-type="country"
                                                        data-entity-id="{{ $country->id }}"
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

                    <textarea class="form-control @error('description_'.$currentLocale) is-invalid @enderror"
                              id="description_{{ $currentLocale }}"
                              name="description_{{ $currentLocale }}"
                              rows="5">{{ old('description_'.$currentLocale, isset($country) ? $country->{'description_'.$currentLocale} : '') }}</textarea>
                    @error('description_'.$currentLocale)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if(isset($validationRules[$currentLocale]))
                        <small class="text-muted">
                            Required: {{ $validationRules[$currentLocale]['country_description_min'] }} - {{ $validationRules[$currentLocale]['country_description_max'] }} words
                        </small>
                    @endif
                </div>
            </div>

            <!-- Hidden inputs for other languages -->
            @foreach($availableLocales as $locale)
                @if($locale !== $currentLocale)
                    <input type="hidden"
                           name="name_{{ $locale }}"
                           value="{{ old('name_'.$locale, isset($country) ? $country->{'name_'.$locale} : '') }}">
                    <input type="hidden"
                           name="description_{{ $locale }}"
                           value="{{ old('description_'.$locale, isset($country) ? $country->{'description_'.$locale} : '') }}">
                @endif
            @endforeach

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ isset($country) ? 'Update Country' : 'Create Country' }}
                </button>
                <a href="{{ route('countries.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
