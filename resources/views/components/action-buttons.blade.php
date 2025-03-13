@props(['model', 'showRoute', 'editRoute', 'deleteRoute', 'deleteMessage', 'extraButtons' => null])

<div class="action-buttons-container">
    <div class="btn-group action-buttons" role="group">
        @if(isset($showRoute))
        <a href="{{ $showRoute }}" class="btn btn-sm btn-action btn-view" data-bs-toggle="tooltip" title="View Details">
            <i class="bi bi-eye-fill"></i>
        </a>
        @endif
        
        @if(isset($editRoute))
        <a href="{{ $editRoute }}" class="btn btn-sm btn-action btn-edit" data-bs-toggle="tooltip" title="Edit">
            <i class="bi bi-pencil-fill"></i>
        </a>
        @endif
        
        @if(isset($deleteRoute))
        <form action="{{ $deleteRoute }}" method="POST" class="delete-confirmation d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-action btn-delete" data-confirm="{{ $deleteMessage ?? 'Are you sure you want to delete this item?' }}" data-bs-toggle="tooltip" title="Delete">
                <i class="bi bi-trash-fill"></i>
            </button>
        </form>
        @endif
        
        {{ $extraButtons ?? '' }}
    </div>
</div>

@push('styles')
<style>
    .action-buttons-container {
        display: flex;
        justify-content: flex-end;
    }
    
    .action-buttons {
        background: #f8f9fa;
        border-radius: 20px;
        padding: 2px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .action-buttons:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    
    .btn-action {
        border-radius: 50%;
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        transition: all 0.2s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .btn-action::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s ease, height 0.3s ease;
    }
    
    .btn-action:hover::before {
        width: 120%;
        height: 120%;
    }
    
    .btn-action i {
        position: relative;
        z-index: 2;
    }
    
    .btn-view {
        background-color: #17a2b8;
        color: white;
    }
    
    .btn-view:hover {
        background-color: #138496;
        color: white;
    }
    
    .btn-edit {
        background-color: #007bff;
        color: white;
    }
    
    .btn-edit:hover {
        background-color: #0069d9;
        color: white;
    }
    
    .btn-delete {
        background-color: #dc3545;
        color: white;
    }
    
    .btn-delete:hover {
        background-color: #c82333;
        color: white;
    }
    
    /* Animation for button clicks */
    .btn-action:active {
        transform: scale(0.9);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-action {
            width: 36px;
            height: 36px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'top',
                delay: { show: 300, hide: 100 }
            });
        });
        
        // Prevent tooltip from showing on mobile devices
        if (window.innerWidth < 768) {
            tooltipList.forEach(tooltip => tooltip.disable());
        }
        
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
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: '<i class="bi bi-x-circle"></i> Cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: true
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