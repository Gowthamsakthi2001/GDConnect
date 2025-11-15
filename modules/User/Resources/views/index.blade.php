<x-app-layout>
    <x-card>
        <x-slot name='actions'>
            <a href="{{ route(config('theme.rprefix') . '.create') }}" class="btn btn-success btn-sm">
                <i class="fa fa-plus-circle"></i>&nbsp;
                {{ localize('Add Staff') }}
            </a>
        </x-slot>

        <div>
            <x-data-table :dataTable="$dataTable" />
        </div>
    </x-card>
 @section('script_js')
    <script>
         function route_alert(route, message, title = "Are you sure?") {
                    Swal.fire({
                        title: title,
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        cancelButtonColor: 'default',
                        confirmButtonColor: '#FC6A57',
                        cancelButtonText: "No",
                        confirmButtonText: "Yes",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                             var token = '{{csrf_token()}}';
                            $.ajax({
                                url: route,
                                type: 'DELETE',
                                data: {_token: token},
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire('Success!', response.message, 'success');
                                        setTimeout(function() {
                                            // location.reload(); 
                                            $('#user-table').DataTable().ajax.reload(null,
                                                false);
                                        }, 1000);
                                    } else {
                                        Swal.fire('Warning!', response.message, 'warning');
                                    }

                                },
                                error: function(xhr) {
                                    Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                                }
                            });
                        }
                    });
                }
    </script>
 @endsection
</x-app-layout>
