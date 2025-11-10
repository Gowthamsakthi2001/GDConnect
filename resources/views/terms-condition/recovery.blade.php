<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Recovery Request Closure</title>
<link href="{{ admin_asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<style>
:root { --base-color: #0d6efd; }
body { font-family: 'Poppins', sans-serif; background-color:#f8f9fa; color:#333; }
.container-box { max-width:800px; margin:50px auto; background:#fff; border-radius:12px; box-shadow:0 5px 20px rgba(0,0,0,0.08); padding:40px 30px; }
.header { text-align:center; margin-bottom:25px; }
.header h2 { font-weight:600; color: var(--base-color); }
ul { margin-top:15px; padding-left:1.2rem; }
ul li { margin-bottom:10px; line-height:1.7; font-size:15px; }
p { font-size:15px; line-height:1.7; }
.footer { margin-top:30px; color:#555; }
.action-buttons { margin-top:30px; text-align:center; }
.action-buttons button { margin:0 10px; padding:10px 25px; border:none; border-radius:5px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; }
.confirm { background:var(--base-color); color:#fff; }
.cancel { background:#dc3545; color:#fff; }
#response-message { margin-top:20px; font-weight:600; text-align:center; }
.request-info { margin-top:20px; border:1px solid #ddd; padding:15px; border-radius:8px; background:#f9f9f9; }
.request-info h5 { margin-bottom:5px; font-weight:600; }
.loading-icon { border:2px solid #f3f3f3; border-top:2px solid #fff; border-right:2px solid #fff; border-radius:50%; width:16px; height:16px; margin-right:8px; animation: spin 1s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
@media (max-width:576px) { .container-box { padding:25px 20px; margin:20px; } .header h2 { font-size:20px; } }
</style>
</head>
<body>
@if(isset($recovery) && $recovery->status === 'closed')
    <!-- ✅ Stylish Thank You / Already Closed Section -->
    <div class="container-box text-start" style="padding:20px 20px;">
        <div style="
            background: linear-gradient(135deg, #28a745 0%, #60d394 100%);
            color: #fff;
            border-radius: 12px;
            padding: 40px 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        ">
            <h1 style="font-size:34px; font-weight:700; margin-bottom:15px;">Recovery Request Closed</h1>
            <p style="font-size:17px; line-height:1.8; max-width:600px; margin:0 auto;">
                This recovery request has already been <strong>successfully closed</strong>.  
                Thank you for reviewing and confirming the completion of this recovery process.
            </p>
            <hr style="width:80px; border:2px solid #fff; margin:25px auto;">
            <p style="font-size:15px; color:#e9fcef;">
                For any assistance, please reach out to Admin Support.
                <br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team"
            </p>
        </div>

    </div>
@else
<div class="container-box">

    <div class="header">
        <h2 class="text-center">Recovery Request Closure Confirmation</h2>
        <p class="text-start">Please review and confirm before closing this recovery request.</p>
    </div>
    @php
        $reasonList = [
        1 => 'Breakdown',
        2 => 'Battery Drain',
        3 => 'Accident',
        4 => 'Rider Unavailable',
        5 => 'Other',
    ];

    $reasonText = $reasonList[$recovery->reason ?? 0] ?? 'Unknown';
    @endphp
    @if(isset($recovery))
        <!-- Recovery information -->
        <div class="request-info mb-2">
            <h5>Recovery Request Information</h5>
            <p><strong>Request ID:</strong> {{ $recovery->assignment->req_id }}</p>
            <p><strong>Vehicle Number:</strong> {{ $recovery->vehicle_number ?? 'N/A' }}</p>
            <p><strong>Chassis Number:</strong> {{ $recovery->chassis_number ?? 'N/A' }}</p>
            <p><strong>Reason:</strong> {{ $reasonText ?? 'N/A' }}</p>
            <p><strong>Description:</strong> {{ $recovery->description ?? '' }}</p>
            <p><strong>Requested By:</strong> {{ $recovery->rider->customerLogin->customer_relation->trade_name ?? 'Unknown' }}</p>
        </div>
    @endif

    <p>By proceeding with the closure of this recovery request, I acknowledge and confirm that:</p>
    <ul>
        <li>The vehicle recovery process has been completed successfully.</li>
        <li>I understand that this closure is final and cannot be undone.</li>
        <li>I confirm that I take full responsibility for the closure decision.</li>
    </ul>

    <div class="footer">
        <p>By continuing, you acknowledge that you have reviewed all details and wish to close this recovery request.</p>
    </div>

    @if(isset($recovery) && $recovery->close_status != 1 && $recovery->close_status != 2)
        <div class="action-buttons">
            <button class="confirm" data-response="confirm">Confirm Close</button>
            <!--<button class="cancel" data-response="reject">Reject</button>-->
        </div>
        <p id="response-message"></p>
    @endif

</div>
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(isset($recovery) && $recovery->close_status != 1)
<script>
$(document).ready(function() {
    $('.confirm, .cancel').click(function() {
        var $btn = $(this);
        var response = $btn.data('response'); // confirm or reject
        var recoveryId = "{{ encrypt($recovery->id ?? '') }}";
        var actionText = response === 'confirm' ? 'Confirm' : 'Reject';

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you really want to ${actionText.toLowerCase()} the closure of this request?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${actionText}!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if(result.isConfirmed) {
                $btn.html('<span class="loading-icon"></span>Submitting...').prop('disabled', true);
                $('.confirm, .cancel').not($btn).prop('disabled', true);

                $.ajax({
                    url: "{{ route('customer.close_request') }}", // Update this route name
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        recovery_id: recoveryId,
                        response: response
                    },
                    success: function(res) {
                        
                        $('.container-box').fadeOut(400, function() {
                                // Replace with thank-you message
                                $(this).html(`
                                    <div class="text-start" style="
                                        padding:20px 20px;
                                        background: linear-gradient(135deg, #28a745, #6edc82);
                                        color:#fff;
                                        border-radius:12px;
                                        box-shadow:0 8px 25px rgba(0,0,0,0.1);
                                    ">
                                        <h1 style="font-size:34px; font-weight:700;">Thank You!</h1>
                                        <p style="font-size:17px; margin-top:10px; line-height:1.8;">
                                            The recovery request has been <strong>successfully closed.</strong><br>
                                            We appreciate your prompt action and confirmation.
                                        </p>
                                        <hr style="width:80px; border:2px solid #fff; margin:25px auto;">
                                    </div>
                                `).fadeIn(500);
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

                        $btn.text(actionText).prop('disabled', false);
                        $('.confirm, .cancel').not($btn).prop('disabled', false);
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
