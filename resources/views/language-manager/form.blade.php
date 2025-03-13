@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ isset($language) ? 'Edit Language: ' . $language['name'] : 'Add New Language' }}</h3>
            <a href="{{ route('language-manager.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ isset($language) ? route('language-manager.update', ['code' => $language['code'], 'currentLocale' => $currentLocale ?? app()->getLocale()]) : route('language-manager.store', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" method="POST">
            @csrf
            @if(isset($language))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <h4 class="mb-3 border-bottom pb-2">Language Information</h4>

                    <div class="mb-3">
                        <label for="code" class="form-label">Language Code (2 letters)</label>
                        @if(isset($language))
                            <input type="text" name="code" id="code" value="{{ old('code', $language['code'] ?? '') }}"
                                class="form-control-plaintext" readonly>
                            <div class="form-text text-muted">Language code cannot be changed</div>
                        @else
                            <input type="text" name="code" id="code" value="{{ old('code', '') }}"
                                class="form-control @error('code') is-invalid @enderror"
                                maxlength="2" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Language Name (in English)</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $language['name'] ?? '') }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="native" class="form-label">Native Name</label>
                        <input type="text" name="native" id="native" value="{{ old('native', $language['native'] ?? '') }}"
                               class="form-control @error('native') is-invalid @enderror" required>
                        @error('native')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h4 class="mb-3 border-bottom pb-2">Validation Rules (Word Count)</h4>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Country Description</label>
                        <div class="row">
                            <div class="col-6">
                                <label for="country_description_min" class="form-label small">Min Words</label>
                                <input type="number" name="country_description_min" id="country_description_min"
                                       value="{{ old('country_description_min', isset($language['validation']) ? $language['validation']['country_description_min'] : 10) }}"
                                       min="0" class="form-control @error('country_description_min') is-invalid @enderror" required>
                                @error('country_description_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="country_description_max" class="form-label small">Max Words</label>
                                <input type="number" name="country_description_max" id="country_description_max"
                                       value="{{ old('country_description_max', isset($language['validation']) ? $language['validation']['country_description_max'] : 500) }}"
                                       min="1" class="form-control @error('country_description_max') is-invalid @enderror" required>
                                @error('country_description_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">City Description</label>
                        <div class="row">
                            <div class="col-6">
                                <label for="city_description_min" class="form-label small">Min Words</label>
                                <input type="number" name="city_description_min" id="city_description_min"
                                       value="{{ old('city_description_min', isset($language['validation']) ? $language['validation']['city_description_min'] : 5) }}"
                                       min="0" class="form-control @error('city_description_min') is-invalid @enderror" required>
                                @error('city_description_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="city_description_max" class="form-label small">Max Words</label>
                                <input type="number" name="city_description_max" id="city_description_max"
                                       value="{{ old('city_description_max', isset($language['validation']) ? $language['validation']['city_description_max'] : 300) }}"
                                       min="1" class="form-control @error('city_description_max') is-invalid @enderror" required>
                                @error('city_description_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Geo Point Description</label>
                        <div class="row">
                            <div class="col-6">
                                <label for="geo_point_description_min" class="form-label small">Min Words</label>
                                <input type="number" name="geo_point_description_min" id="geo_point_description_min"
                                       value="{{ old('geo_point_description_min', isset($language['validation']) ? $language['validation']['geo_point_description_min'] : 3) }}"
                                       min="0" class="form-control @error('geo_point_description_min') is-invalid @enderror" required>
                                @error('geo_point_description_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="geo_point_description_max" class="form-label small">Max Words</label>
                                <input type="number" name="geo_point_description_max" id="geo_point_description_max"
                                       value="{{ old('geo_point_description_max', isset($language['validation']) ? $language['validation']['geo_point_description_max'] : 200) }}"
                                       min="1" class="form-control @error('geo_point_description_max') is-invalid @enderror" required>
                                @error('geo_point_description_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ isset($language) ? 'Update Language' : 'Add Language' }}
                </button>
                <a href="{{ route('language-manager.index', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
