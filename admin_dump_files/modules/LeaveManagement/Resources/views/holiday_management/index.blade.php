<x-app-layout>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
                <img src="{{asset('admin-assets/icons/custom/leave-icon-vector.jpg')}}" class="img-fluid rounded"><span class="ps-2">Holiday Management</span>
            </h2>
        </div>
        <!-- End Page Header -->
        
        <x-card>
            <x-slot name='actions'>
                <a href="{{ route('admin.Green-Drive-Ev.leavemanagement.holidays.manage') }}" class="btn btn-success btn-sm">
                    <i class="fa fa-plus-circle"></i>&nbsp;
                    {{ localize('Add Holiday') }}
                </a>
            </x-slot>
            
            <div>
                 <x-data-table :dataTable="$dataTable" />
               
            </div>
        </x-card>
    </div>

@push('js')
<script>
$(document).ready(function() {
    // Consolidated delete button handler with SweetAlert2
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const holidayId = $(this).data('id');
        const isGroup = $(this).data('group') || false;
        
        Swal.fire({
            title: isGroup ? 'Delete Recurring Holiday Group?' : 'Delete Holiday?',
            text: isGroup ? 'This will delete ALL holidays in this recurring group!' : 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.leavemanagement.holidays.destroy') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: holidayId,
                        is_group: isGroup
                    },
                    beforeSend: function() {
                        Swal.showLoading();
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Holiday deleted successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Refresh DataTable without page reload
                                if (window.LaravelDataTables && window.LaravelDataTables['holiday-table']) {
                                    window.LaravelDataTables['holiday-table'].ajax.reload(null, false);
                                }
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to delete holiday',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while deleting';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire(
                            'Error!',
                            errorMsg,
                            'error'
                        );
                    }
                });
            }
        });
    });
});

// Remove the standalone deleteHoliday() function since we're using the delegated handler
</script>
@endpush
</x-app-layout>