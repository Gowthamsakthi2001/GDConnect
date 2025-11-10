<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Terms and Conditions</title>
<link href="{{ admin_asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link rel="shortcut icon" href="{{ url('/') }}/storage/setting/ycsbDAa4bOn4ouFfSKkJ0o5C8prSzthSJEUHG078.png?v=1"> 
<style>
:root { --base-color: #0d6efd; }
body { font-family: 'Poppins', sans-serif; background-color:#f8f9fa; color:#333; }
.terms-container { max-width:800px; margin:50px auto; background:#fff; border-radius:12px; box-shadow:0 5px 20px rgba(0,0,0,0.08); padding:40px 30px; }
.terms-header { text-align:center; margin-bottom:25px; }
.terms-header h2 { font-weight:600; color: var(--base-color); }
ul { margin-top:15px; padding-left:1.2rem; }
ul li { margin-bottom:10px; line-height:1.7; font-size:15px; }
p { font-size:15px; line-height:1.7; }
.terms-footer { margin-top:30px; color:#555; }
.accept-reject { margin-top:30px; text-align:center; }
.accept-reject button { margin:0 10px; padding:10px 25px; border:none; border-radius:5px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; }
.accept { background:var(--base-color); color:#fff; }
.reject { background:#dc3545; color:#fff; }
#response-message { margin-top:20px; font-weight:600; text-align:center; }
.rider-info { margin-top:20px; border:1px solid #ddd; padding:15px; border-radius:8px; background:#f9f9f9; }
.rider-info h5 { margin-bottom:5px; font-weight:600; }
.loading-icon { border:2px solid #f3f3f3; border-top:2px solid #fff; border-right:2px solid #fff; border-radius:50%; width:16px; height:16px; margin-right:8px; animation: spin 1s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
@media (max-width:576px) { .terms-container { padding:25px 20px; margin:20px; } .terms-header h2 { font-size:20px; } }
</style>
</head>
<body>

<div class="terms-container">

    <div class="terms-header">
        <h2>Terms and Conditions for Rider Responsibility</h2>
        <p>Please read these terms carefully before proceeding.</p>
    </div>

    @if(isset($rider))
        @if($rider->terms_condition_status == 1)
            <p style="text-align:center; font-weight:600; color:green;">You have already accepted the Terms & Conditions.</p>
        @elseif($rider->terms_condition_status == 2)
            <p style="text-align:center; font-weight:600; color:red;">You have rejected the Terms & Conditions.</p>
        @endif

        <!-- Rider information for customer reference -->
        <div class="rider-info">
            <h5>Rider Information</h5>
            <p><strong>Name:</strong> {{ $rider->name }}</p>
            <p><strong>Phone:</strong> {{ $rider->mobile_no }}</p>
        </div>
    @endif

    <p>By registering as a rider on our platform, I acknowledge and confirm the following:</p>
    <ul>
        <li>I do <strong>not</strong> possess a valid driving license or Learner's License (LLR).</li>
        <li>I understand that operating a vehicle without a valid license is against the law.</li>
        <li>I take <strong>full responsibility</strong> for any accidents, damages, or legal consequences that may arise while performing deliveries.</li>
        <li>I agree to indemnify and hold harmless the company, its employees, and partners from any claims, losses, or liabilities resulting from my actions.</li>
        <li>I confirm that I will follow all safety guidelines and traffic rules to the best of my ability.</li>
    </ul>

    <div class="terms-footer">
        <p>By continuing, I acknowledge that I have read and understood these terms and accept the responsibility for all outcomes arising from my participation on the platform.</p>
    </div>

      @if(isset($rider) && $rider->terms_condition_status != 1 && $rider->terms_condition_status != 2)
            <div class="accept-reject">
                <button class="accept" data-response="accept">Accept</button>
                <button class="reject" data-response="reject">Reject</button>
            </div>
            <p id="response-message"></p>
        @endif

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(isset($rider) && $rider->terms_condition_status != 1)
<script>
$(document).ready(function() {
    $('.accept, .reject').click(function() {
        var $btn = $(this);
        var response = $btn.data('response'); // accept or reject
        var riderId = "{{ encrypt($rider->id ?? '') }}";
        var actionText = response === 'accept' ? 'Accept' : 'Reject';

        // Show confirmation popup
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you really want to ${actionText} the Terms & Conditions?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${actionText}!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if(result.isConfirmed) {
                // Show loading state
                $btn.html('<span class="loading-icon"></span>Submitting...').prop('disabled', true);
                $('.accept, .reject').not($btn).prop('disabled', true);

                $.ajax({
                    url: "{{ route('customer.respond') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        rider_id: riderId,
                        response: response
                    },
                    success: function(res) {
                        // Update UI with result
                        if(response === 'accept') {
                            $('.accept-reject').html('<p style="text-align:center; font-weight:600; color:green;">You have successfully accepted the Terms & Conditions.</p>');
                        } else if(response === 'reject') {
                            $('.accept-reject').html('<p style="text-align:center; font-weight:600; color:red;">You have rejected the Terms & Conditions.</p>');
                        }

                        // Show toast notification
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            icon: res.status === 'success' ? 'success' : (res.status === 'info' ? 'info' : 'error'),
                            title: res.message
                        });
                    },
                    error: function(err) {
                        let msg = err.responseJSON?.message || 'Something went wrong.';
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            icon: 'error',
                            title: msg
                        });

                        // Revert buttons if error occurs
                        $btn.text(actionText).prop('disabled', false);
                        $('.accept, .reject').not($btn).prop('disabled', false);
                    }
                });
            }
        });
    });
});
</script>

@endif

</body>
</html>
