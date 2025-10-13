<x-app-layout>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
                <img src="{{asset('admin-assets/icons/custom/leave-icon-vector.jpg')}}" class="img-fluid rounded"><span class="ps-2">Permission Requests</span>
            </h2>
        </div>
        <!-- End Page Header -->
        
        
           <x-card>
            <x-slot name='actions'>
                <a href="{{ route('admin.Green-Drive-Ev.leavemanagement.index') }}" class="btn btn-success btn-sm">
                    <i class="fa fa-plus-circle"></i>&nbsp;
                    {{ localize('Back') }}
                </a>
            </x-slot>
             <div>
                <x-data-table :dataTable="$dataTable" />
            </div>
        </x-card>
    </div>
@section('script_js')
<script>
    function ApproveOrRejectStatus(route, id, message, status, title = "Are you sure?") {
    // Show initial popup immediately
    Swal.fire({
        title: title,
        html: `
            <div>${message}</div>
            <div id="leave-count-container" class="mt-3 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading leave data...</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'default',
        confirmButtonColor: '#FC6A57',
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        reverseButtons: true,
        showConfirmButton: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            // Disable the confirm button initially
            const confirmButton = Swal.getConfirmButton();
            confirmButton.disabled = true;
            confirmButton.style.opacity = '0.5';
            confirmButton.style.cursor = 'not-allowed';
            
            // Fetch leave counts when the modal opens
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.leavemanagement.get-leave-count') }}",
                type: "GET",
                data: { id: id },
                success: function(countResponse) {
                    if (!countResponse.success) {
                        document.getElementById('leave-count-container').innerHTML = 
                            '<div class="text-danger">Could not fetch leave counts</div>';
                        return;
                    }
                    
                    const leaveCounts = countResponse.data;
                    const leaveCountTable = `
                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-center">Leave Summary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Casual Leaves</td>
                                        <td colspan="2">${leaveCounts.casual_leave_count}/${leaveCounts.max_casual}</td>
                                    </tr>
                                    <tr>
                                        <td>Sick Leaves</td>
                                        <td colspan="2">${leaveCounts.sick_leave_count}/${leaveCounts.max_sick}</td>
                                    </tr>
                                    <tr>
                                        <td>Permission Leave</td>
                                        <td>Days: ${leaveCounts.permission_leave.days}</td>
                                        <td>Hours: ${leaveCounts.permission_leave.hours}</td>
                                    </tr>
                                    <tr>
                                        <td>Other Leaves</td>
                                        <td colspan="2">${leaveCounts.other_leave_count}</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td>Total Leaves</td>
                                        <td colspan="2">${leaveCounts.total_leaves}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    document.getElementById('leave-count-container').innerHTML = leaveCountTable;
                    
                    // Add additional fields based on status
                    if (status == 1) {
                        // For approval - add paid/unpaid selection
                        const paidStatusSelect = `
                            <div class="mt-3">
                                <label for="swal-paid-status" class="form-label">Leave Type:</label>
                                <select id="swal-paid-status" class="form-select">
                                    <option value="1">Paid</option>
                                    <option value="0">Unpaid</option>
                                </select>
                            </div>
                        `;
                        document.getElementById('leave-count-container').innerHTML += paidStatusSelect;
                    } else {
                        // For rejection - add remarks textarea
                        const rejectReason = `
                            <div class="mt-3">
                                <label for="swal-reject-reason" class="form-label">Reject Reason:</label>
                                <textarea id="swal-reject-reason" class="form-control" 
                                    placeholder="Enter remarks here..." rows="3" required></textarea>
                            </div>
                        `;
                        document.getElementById('leave-count-container').innerHTML += rejectReason;
                    }
                    
                    // Enable the confirm button after content is loaded
                    confirmButton.disabled = false;
                    confirmButton.style.opacity = '1';
                    confirmButton.style.cursor = 'pointer';
                },
                error: function() {
                    document.getElementById('leave-count-container').innerHTML = 
                        '<div class="text-danger">Could not fetch leave counts</div>';
                    // Keep the confirm button disabled on error
                }
            });
        },
        preConfirm: () => {
            // Validate and collect data
            if (status == 1) {
                const isPaid = document.getElementById('swal-paid-status').value;
                if (isPaid === undefined) {
                    Swal.showValidationMessage('Please select payment status');
                    return false;
                }
                return { isPaid };
            } else {
                const remarks = document.getElementById('swal-reject-reason').value;
                if (!remarks) {
                    Swal.showValidationMessage('Reject reason is required');
                    return false;
                }
                return { remarks };
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Prepare data for submission
            const data = {
                id: id,
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            // Add status-specific data
            if (status == 1) {
                data.is_paid = result.value.isPaid;
            } else {
                data.remarks = encodeURIComponent(result.value.remarks);
            }
            
            // Show loading state on the confirm button
            Swal.showLoading();
            
            // Make the final AJAX call
            $.ajax({
                url: route,
                type: "POST",
                data: data,
                success: function(response) {
                    if(response.success){
                        const actionText = status == 1 ? "Approved" : "Rejected";
                        Swal.fire({
                            icon: 'success',
                            title: actionText + '!',
                            text: response.message,
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire("Warning!", response.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                }
            });
        }
    });
}

    // function ApproveOrRejectStatus(route, id, message, status, title = "Are you sure?") {
    //         if (status == 1) {
    //             Swal.fire({
    //                 title: title,
    //                 text: message,
    //                 icon: 'warning',
    //                 showCancelButton: true,
    //                 cancelButtonColor: 'default',
    //                 confirmButtonColor: '#FC6A57',
    //                 cancelButtonText: "No",
    //                 confirmButtonText: "Yes",
    //                 reverseButtons: true
    //             }).then((result) => {
    //                 if (result.isConfirmed) {
    //                     $.ajax({
    //                         url: route,
    //                         type: "POST",
    //                         data: {
    //                             id: id,
    //                             status: status,
    //                             _token: $('meta[name="csrf-token"]').attr('content') 
    //                         },
    //                         success: function (response) {
    //                           if(response.success){
    //                                 Swal.fire("Approved!",response.message, "success");
    //                                 setTimeout(function(){
    //                                     location.reload(); 
    //                                 },1000);
    //                           }else{
    //                                 Swal.fire("Warning!",response.message, "error");
    //                           }
    //                         },
    //                         error: function () {
    //                             Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
    //                         }
    //                     });
    //                 }
    //             });
    //         } else {
    //             Swal.fire({
    //                 title: title,
    //                 text: message,
    //                 icon: 'warning',
    //                 input: 'textarea', 
    //                 inputPlaceholder: 'Enter remarks here...',
    //                 inputAttributes: {
    //                     rows: 4 
    //                 },
    //                 showCancelButton: true,
    //                 cancelButtonColor: 'default',
    //                 confirmButtonColor: '#FC6A57',
    //                 cancelButtonText: "No",
    //                 confirmButtonText: "Yes",
    //                 reverseButtons: true,
    //                 preConfirm: (remarks) => {
    //                     if (!remarks) {
    //                         Swal.showValidationMessage('Reject Reason are required');
    //                     }
    //                     return remarks;
    //                 }
    //             }).then((result) => {
    //                 if (result.isConfirmed) {
    //                     const encodedRemarks = encodeURIComponent(result.value);
    //                     $.ajax({
    //                         url: route,
    //                         type: "POST",
    //                         data: {
    //                             id: id,
    //                             status: status,
    //                             remarks: encodedRemarks,
    //                             _token: $('meta[name="csrf-token"]').attr('content')
    //                         },
    //                         success: function (response) {
    //                             if(response.success){
    //                                 Swal.fire("Rejected!",response.message, "success");
    //                                 setTimeout(function(){
    //                                     location.reload(); 
    //                                 },1000);
    //                           }else{
    //                                 Swal.fire("Warning!",response.message, "error");
    //                           }
    //                         },
    //                         error: function () {
    //                             Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
    //                         }
    //                     });
    //                 }
    //             });
    //         }
    //     }
</script>
@endsection
</x-app-layout>
