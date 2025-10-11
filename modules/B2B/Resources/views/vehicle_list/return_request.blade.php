@extends('layouts.b2b')
@section('css')
@endsection


@section('content')
<div class="main-content">
   
        <!-- Header Section -->
        <div class="mb-4">
            <div class="p-3 rounded" style="background:#fbfbfb;">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <!-- Title -->
                    <h5 class="m-0 text-truncate custom-dark">
                        Return Request
                    </h5>
                    
        
                    <!-- Back Button -->
                    <a href="{{ route('b2b.vehiclelist') }}" 
                       class="btn btn-dark btn-md mt-2 mt-md-0">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    
             
            
            <div class="card">
                <div class="card-body">
        <form id="returnRequestForm" action="{{ route('b2b.return_request_functionality') }}" method="POST">
            @csrf
                    <div class="row">
                         
                         <input type="hidden" class="form-control bg-white" name="id" id="id" value="{{ $data['id']}}">
                         <input type="hidden" class="form-control bg-white" name="rider_id" id="rider_id" value="{{ $data['rider']['id']}}">
                         <input type="hidden" class="form-control bg-white" name="rider_mobile_no" id="rider_mobile_no" value="{{ $data['rider']['mobile_no']}}">
                         
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="return_reason">Reason for return</label>
                                <select class="form-select custom-select2-field form-control-sm" id="return_reason" name="return_reason">
                                    <option value="Select Reason for Return">Select Reason for Return</option>
                                    <option value="Contract End">Contract End</option>
                                    <option value="Performance Issue">Performance Issue</option>
                                    <option value="Vehicle Issue">Vehicle Issue</option>
                                    <option value="No Longer Needed">No Longer Needed</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <!--  <div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="vehicle_number">Vehicle Id</label>-->
                        <!--        <input type="text" class="form-control bg-white" name="vehicle_id" id="vehicle_id"  placeholder="Enter Vehicle Id">-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="register_number">Vehicle Register No</label>
                                <input type="text" class="form-control bg-white" name="register_number" id="register_number" value="{{ $data['vehicle']['permanent_reg_number']}}"  placeholder="Enter Vehicle Register No" readonly>
                            </div>
                        </div>
                        

                          <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number</label>
                                <input type="text" class="form-control bg-white"  name="chassis_number" id="chassis_number" value="{{ $data['vehicle']['chassis_number']}}"  placeholder="Enter Chassis No" readonly>
                            </div>
                        </div>
                        
                        
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="rider_name">Rider Name</label>
                                <input type="text" class="form-control bg-white"   name="rider_name" id="rider_name" value="{{ $data['rider']['name']}}"  placeholder="Enter Rider Name" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="client_business_name">Client Business Name</label>
                                <input type="text" class="form-control bg-white"  name="client_business_name" id="client_business_name"  value="{{ $data['rider']['customerLogin']['customer_relation']['name']}}" placeholder="Enter Client Business Name" readonly>
                            </div>
                        </div>
                   
                        
                      <!--  <div class="col-md-6 mb-3">-->
                      <!--      <div class="form-group">-->
                      <!--          <label class="input-label mb-2 ms-1" for="contact_person_name">Contact Person Name</label>-->
                      <!--          <input type="text" class="form-control bg-white" name="contact_person_name" id="contact_person_name"  placeholder="Enter Contact Person Name">-->
                      <!--      </div>-->
                      <!--  </div>-->
                        
                        
                        <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="contact_no">Contact No</label>
                                    <input type="text" class="form-control bg-white phone-input" name="contact_no" id="contact_no" value="{{ $data['rider']['customerLogin']['customer_relation']['phone'] }}"  placeholder="Enter Contact No" readonly>
                                </div>
                        </div>
  
                      <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="contact_email">Contact Email</label>
                                    <input type="text" class="form-control bg-white" name="contact_email" id="contact_email" value="{{ $data['rider']['customerLogin']['customer_relation']['email'] }}" placeholder="Enter Contact Email" readonly>
                                </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="description">Description</label>
                                <textarea class="form-control bg-white" name="description" id="description" rows="6" placeholder="Enter Description"></textarea>
                            </div>
                        </div>
                        
                        <!-- Button -->
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                                <button type="button" class="btn btn-danger btn-back me-2 ">Reset</button>
                                <button type="submit" id="submitBtn" class="btn btn-primary ">Submit</button> 
                        </div>
                    </div>
                    
                    </div>
                    
        </form>    
            </div>
            </div>
            


</div>
@endsection

@section('js')

<script>

$(document).ready(function () {

    $('#returnRequestForm').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);
        let btn = $('#submitBtn');
        let selected_reason = $("#return_reason option:selected").text();
        formData.append('selected_reason',selected_reason);
        // Change button text & disable
        btn.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Return request submitted successfully!',
                    showConfirmButton: false,
                    timer: 3000
                });

                form.trigger("reset");

                setTimeout(() => {
                    window.location.href = "{{ route('b2b.vehiclelist') }}";
                }, 1500);
            },
            error: function (xhr) {
                let errors = xhr.responseJSON?.errors;
                let message = "Something went wrong!";
                if (errors) {
                    message = Object.values(errors).flat().join('<br>');
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: message,
                    showConfirmButton: false,
                    timer: 4000
                });
            },
            complete: function () {
                // Reset button text & enable
                btn.prop('disabled', false).text('Submit');
            }
        });
    });
});

function sanitizeAndValidatePhone(input) {
    // Ensure the input starts with '+91'
    if (!input.value.startsWith('+91')) {
        input.value = '+91' + input.value.replace(/^\+?91/, '');
    }

    // Allow only digits after '+91'
    input.value = '+91' + input.value.substring(3).replace(/[^\d]/g, '');

    // Limit the total length to 13 characters (including '+91')
    if (input.value.length > 13) {
        input.value = input.value.substring(0, 13);
    }
}

// Apply to all inputs with class "phone-input"
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".phone-input").forEach(function(input) {
        input.addEventListener("input", function() {
            sanitizeAndValidatePhone(this);
        });
    });
});
</script>


@endsection