<x-app-layout>
      <style>
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 25px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-switch-label {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 50px;
            transition: background-color 0.3s ease;
        }

        .toggle-switch-indicator {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 16px;
            height: 16px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        input:checked + .toggle-switch-label {
            background-color: #4CAF50; /* Green when active */
        }

        input:checked + .toggle-switch-label .toggle-switch-indicator {
            transform: translateX(26px); /* Move the indicator to the right */
        }

    </style>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
                <img src="{{asset('admin-assets/icons/custom/ahaar_card_log.png')}}" class="img-fluid rounded"><span class="ps-2">List Of Asset Status</span>
            </h2>
        </div>
        <!-- End Page Header -->
        
        
           <x-card>
            <x-slot name='actions'>
                <a href="javascript:void(0);" onclick="AddorEditStatusModal(0)" class="btn btn-success btn-sm">
                    <i class="fa fa-plus-circle"></i>&nbsp;
                    {{ localize('Add Status') }}
                </a>
            </x-slot>
             <div>
                <x-data-table :dataTable="$dataTable" />
            </div>
        </x-card>
    </div>
    
    <div class="modal fade" id="AddorEditStatus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="AddorEditStatusLabel" aria-hidden="true">
        <form action="{{ route('admin.Green-Drive-Ev.asset-master.asset_status_store') }}" method="POST">
            @csrf
            <input type="hidden" name="status_id" id="EditStatusId" value="">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="AddorEditStatusLabel">Enter a Status Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="input-label mb-2 ms-1" for="status_name">{{ __('Name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="status_name" id="status_name" class="form-control"
                                    placeholder="{{ __('Enter a Status Name') }}" value="{{ old('status_name') }}"
                                    maxlength="191">
                                @error('status_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
    
                            <div class="col-12">
                                <label for="statusSelect" class="mb-2 ms-1">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-control basic-single" id="status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success submitBtn">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@section('script_js')
<script>
        function AddorEditStatusModal(id) {
            if (id == 0) {
                $('#AddorEditStatus form').trigger('reset');
                
            } else {
                $("#AddorEditStatusLabel").text("Update a Status Details");
                $(".submitBtn").text('Update');
                const url = "{{ route('admin.Green-Drive-Ev.asset-master.asset_get_status', ['id' => '__id__']) }}".replace('__id__', id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $("#EditStatusId").val(response.data.id);
                            $('#status_name').val(response.data.status_name);
                            $("#status").val(response.data.status);
                        } else {
                            toastr.error('error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
            $("#AddorEditStatus").modal('show');

        }
        
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
                    $.ajax({
                        url: route,
                        type: 'GET',
                        success: function (response) {
                            if (response.success) {
                               Swal.fire('Success!', response.message, 'success');
                               $('#asset-status-table').DataTable().ajax.reload(null, false); 
                              
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
    
                        },
                        error: function (xhr) {
                            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                        }
                    });
                }
            });
        }
        
        function status_change_alert(url, message, e) {
                e.preventDefault();
                Swal.fire({
                    title: "Are you sure?",
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
                        location.href = url;
                    }
                });   
        }
        
    </script>

@endsection
</x-app-layout>
