<x-app-layout>

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
               <div class="d-flex justify-content-between">
                    <div>
                        <img src="{{asset('admin-assets/icons/custom/list_of_adhoc.png')}}" class="img-fluid rounded"><span class="ps-2">Edit Adhoc</span>
                    </div>
                    
               </div>
            </h2>
        </div>
  
        <div class="tile">
            <div class="card mb-4">
                    <div class="card-header">
                        Work status update
                    </div>
                    <div class="card-body">
                      <form action="{{route('admin.Green-Drive-Ev.adhocmanagement.update_work_status')}}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <input type="hidden" name="work_status_dm_id" value="{{$dm->id}}">
                                <label for="work_status" class="form-label"> Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="work_status" name="work_status">
                                  <option value="">Select</option>
                                  <option value="1" {{$dm->work_status == 1 ? 'selected' : ''}}>Adhoc Helper</option>
                                  <option value="2" {{$dm->work_status == 2 ? 'selected' : ''}}>Adhoc Driver</option>
                                </select>
                            </div>
                        
                            <div class="text-end">
                                <button type="submit" class="btn custom-btn-primary btn-sm">Update</button>
                            </div>
                        </form>

                    </div>
                </div>
            
            <div class="card mb-4">
                    <div class="card-header">
                        Update Active Date 
                    </div>
                    <div class="card-body">
                      <form action="{{route('admin.Green-Drive-Ev.adhocmanagement.update_active_date')}}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <input type="hidden" name="active_date_dm_id" value="{{$dm->id}}">
                                <label for="active_date" class="form-label">Active Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="active_date" name="active_date" value="">
                               <div class="form-text">
                                    {{ $dm->active_date ? 'Last Update at ' . date('d-m-Y h:i:s A', strtotime($dm->active_date)) : 'Currently no updates' }}.
                                </div>

                            </div>
                        
                            <div class="text-end">
                                <button type="submit" class="btn custom-btn-primary btn-sm">Update</button>
                            </div>
                        </form>

                    </div>
                </div>
        </div>
@section('script_js')
<script>
    // document.querySelectorAll('#zone_id, #client_id, #current_city_id').forEach(function(filter) {
    //         filter.addEventListener('change', function() {
    //             let filterName = filter.id;
    //             let filterValue = filter.value;
        
    //             // Clear other fields based on the selected filter
    //             if (filterName === 'zone_id') {
    //                 // If zone is selected, clear client_id and current_city_id
    //                 if (filterValue !== '') {
    //                     document.getElementById('client_id').value = '';
    //                     document.getElementById('current_city_id').value = '';
    //                 }
    //             } else if (filterName === 'client_id') {
    //                 // If client is selected, clear zone_id and current_city_id
    //                 if (filterValue !== '') {
    //                     document.getElementById('zone_id').value = '';
    //                     document.getElementById('current_city_id').value = '';
    //                 }
    //             } else if (filterName === 'current_city_id') {
    //                 // If current city is selected, clear zone_id and client_id
    //                 if (filterValue !== '') {
    //                     document.getElementById('zone_id').value = '';
    //                     document.getElementById('client_id').value = '';
    //                 }
    //             }
        
    //             // Apply the filter based on the selected value
    //             applyFilter(filterName, filterValue);
    //         });
    //     });


        
        // function applyFilter(filterName, filterValue) {
        //     // Reload the DataTable with the corresponding filter applied
        //     let url = "{{ route('admin.Green-Drive-Ev.adhocmanagement.list_of_adhoc') }}?" + filterName + "=" + filterValue;
        //     $('#supervisor-list-table').DataTable().ajax.url(url).load();
        // }
    
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
    //                     const remarks = result.value;
    //                     $.ajax({
    //                         url: route,
    //                         type: "POST",
    //                         data: {
    //                             id: id,
    //                             status: status,
    //                             remarks: remarks,
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
        
        //  function status_change_alert(url, message, e) {
        //     e.preventDefault();
        //     Swal.fire({
        //         title: "Are you sure?",
        //         text: message,
        //         icon: 'warning',
        //         showCancelButton: true,
        //         cancelButtonColor: 'default',
        //         confirmButtonColor: '#FC6A57',
        //         cancelButtonText: "No",
        //         confirmButtonText: "Yes",
        //         reverseButtons: true
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             location.href = url;
        //         }
        //     });
        // }

</script>
@endsection
</x-app-layout>
