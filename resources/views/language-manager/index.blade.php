@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Language Manager</h3>
            <a href="{{ route('language-manager.create', ['currentLocale' => $currentLocale ?? app()->getLocale()]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Language
            </a>
        </div>
    </div>
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Native</th>
                        <th class="text-center">Default</th>
                        <th>Validation Rules</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($languages as $code => $language)
                        <tr>
                            <td><span class="badge bg-primary">{{ strtoupper($code) }}</span></td>
                            <td>{{ $language['name'] }}</td>
                            <td>{{ $language['native'] }}</td>
                            <td class="text-center">
                                @if(isset($language['is_default']) && $language['is_default'])
                                    <span class="badge bg-success rounded-pill">Yes</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">No</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($language['validation']))
                                    <div class="small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span><i class="bi bi-globe"></i> Country:</span>
                                            <span class="badge bg-info text-dark">{{ $language['validation']['country_description_min'] ?? 0 }} - {{ $language['validation']['country_description_max'] ?? 0 }} words</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span><i class="bi bi-building"></i> City:</span>
                                            <span class="badge bg-info text-dark">{{ $language['validation']['city_description_min'] ?? 0 }} - {{ $language['validation']['city_description_max'] ?? 0 }} words</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span><i class="bi bi-geo-alt"></i> Geo Point:</span>
                                            <span class="badge bg-info text-dark">{{ $language['validation']['geo_point_description_min'] ?? 0 }} - {{ $language['validation']['geo_point_description_max'] ?? 0 }} words</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">No validation rules</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <x-action-buttons
                                    :model="['id' => $code]"
                                    :editRoute="route('language-manager.edit', ['code' => $code, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                    :deleteRoute="route('language-manager.destroy', ['code' => $code, 'currentLocale' => $currentLocale ?? app()->getLocale()])"
                                    deleteMessage="Are you sure you want to delete this language? This will remove all language fields from the database."
                                    :showDeleteButton="!(isset($language['is_default']) && $language['is_default'])"
                                />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete confirmations with SweetAlert2
        const deleteButtons = document.querySelectorAll('.delete-confirmation');

        deleteButtons.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const confirmMessage = this.querySelector('button[data-confirm]').getAttribute('data-confirm');

                Swal.fire({
                    title: 'Are you sure?',
                    text: confirmMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
