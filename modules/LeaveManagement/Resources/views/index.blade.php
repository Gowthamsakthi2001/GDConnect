<x-app-layout>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
                <img src="{{asset('admin-assets/icons/custom/leave-icon-vector.jpg')}}" class="img-fluid rounded"><span class="ps-2">Types of Leave</span>
            </h2>
        </div>
        <!-- End Page Header -->
        
        
           <x-card>
            <x-slot name='actions'>
                <a href="javascript:void(0);" onclick="AddorEditLeaveFunction(0)" class="btn btn-success btn-sm">
                    <i class="fa fa-plus-circle"></i>&nbsp;
                    {{ localize('Add Leave') }}
                </a>
            </x-slot>
    
             <div>
                <x-data-table :dataTable="$dataTable" />
            </div>
        </x-card>
        
        <!-- Modal start -->
        <div class="modal fade" id="AddOrEditLeave" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="AddOrEditLeaveLabel" aria-hidden="true">
            <form id="AddorUpdateLeaveForm" method="POST">
                @method('POST')
                @csrf
                <input type="hidden" id="Store_Url" value="{{ route('admin.Green-Drive-Ev.leavemanagement.add_or_update') }}">
                <input type="hidden" name="leave_id" id="EditLeaveId" value="">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="AddOrEditLeaveLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-12">
                                <label class="input-label mb-2 ms-1" for="leave_name">{{ __('Leave Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="leave_name" id="leave_name" class="form-control" placeholder="{{ __('Leave Name') }}" value="{{ old('leave_name') }}" maxlength="191">
                                <div class="text-danger text-danger1 leave_name_err"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="input-label mb-2 ms-1" for="short_name">{{ __('Short Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="short_name" id="short_name" class="form-control" placeholder="{{ __('Short Name') }}" value="{{ old('short_name') }}" maxlength="191">
                                <div class="text-danger text-danger1 short_name_err"></div>
                            </div>
                             <div class="col-md-12">
                                   <label class="input-label mb-2 ms-1" for="leave_type">{{ __('Leave Type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control basic-single" id="leave_type" name="leave_type" onchange="CheckType(this.value)">
                                        <option value="">Select Type</option>
                                        <option value="day" >Day</option>
                                        <option value="hour" >Hour</option>
                                    </select>
                                    <div class="text-danger text-danger1 leave_type_err"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="input-label mb-2 ms-1" for="days"><span id="day_or_hourLabel">{{ __('Days') }}</span> <span class="text-danger">*</span></label>
                                <input type="text" name="days" id="days" class="form-control" placeholder="{{ __('Days') }}" value="0"  onkeypress="return isNumberKeyNew(event)">
                                <div class="text-danger text-danger1 days_err"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success submitBtn" id="submitBtnLeave">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Modal end -->
    </div>

<script>
       function route_leave_alert(route, message, title = "Are you sure?") {
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
                    location.href = route;
                }
            });
        }
    
   

   function CheckType(value) {
        if (value == 'hour') {
            document.getElementById("day_or_hourLabel").innerHTML = "Hour (Per Day)";
        } else {
            document.getElementById("day_or_hourLabel").innerHTML = "Days";
        }
    }

</script>
</x-app-layout>