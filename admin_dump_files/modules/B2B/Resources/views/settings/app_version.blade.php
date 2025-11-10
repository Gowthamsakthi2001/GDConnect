@extends('layouts.b2b')

@section('content')

<div class="">
    <div class="p-3 rounded" style="background:#fbfbfb;">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <!-- Title -->
            <h5 class="m-0 text-truncate custom-dark" style="font-size: clamp(1rem, 2vw, 1rem);">
               App Version Management Settings
            </h5>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs" id="appVersionTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="rider-tab" data-bs-toggle="tab" data-bs-target="#rider" type="button" role="tab">
            Rider App
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="agent-tab" data-bs-toggle="tab" data-bs-target="#agent" type="button" role="tab">
            Agent App
        </button>
    </li>
</ul>

<div class="tab-content mt-3" id="appVersionTabContent">

    <!-- Rider Tab -->
    <div class="tab-pane fade show active" id="rider" role="tabpanel">
        <form id="RiderAppVersionUpdateForm" action="javascript:void(0);" method="POST">
            @csrf
            <div class="row">
                <!-- App Live Version -->
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label class="col-form-label">Rider App Live Version <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="b2b_rider_app_live_version" 
                               value="{{ old('b2b_rider_app_live_version', $rider_app_live_version) }}" required>
                    </div>
                </div>
                <!-- App Test Version -->
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label class="col-form-label">Rider App Test Version <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="b2b_rider_app_test_version"
                               value="{{ old('b2b_rider_app_test_version', $rider_app_test_version) }}" required>
                    </div>
                </div>
                <!-- Live URL -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="col-form-label">Rider Live Download URL <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                        <textarea id="rider_live_url" class="form-control" name="b2b_rider_live_latest_apk_url" required>{{ old('b2b_rider_live_latest_apk_url', $rider_live_latest_apk_url) }}</textarea>
                        <button type="button" onclick="copyToClipboard('rider_live_url', 'Live Rider App Url')">📋</button>  
                        </div>
                    </div>
                </div>
                <!-- Test URL -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="col-form-label">Rider Test Download URL <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                        <textarea id="rider_test_url" class="form-control" name="b2b_rider_test_latest_apk_url" required>{{ old('b2b_rider_test_latest_apk_url', $rider_test_latest_apk_url) }}</textarea>
                        <button type="button" onclick="copyToClipboard('rider_test_url', 'Test Rider App Url')">📋</button>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <button type="button" class="btn btn-success" onclick="Auth_Confirm_Update('RiderAppVersionUpdateForm', '{{ route('b2b.settings.app_version_manage.update_rider') }}', '{{ $b2b_app_password }}')">Save Changes</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Agent Tab -->
    <div class="tab-pane fade" id="agent" role="tabpanel">
        <form id="AgentAppVersionUpdateForm" action="javascript:void(0);" method="POST">
            @csrf
            <div class="row">
                <!-- App Live Version -->
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label class="col-form-label">Agent App Live Version <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="b2b_agent_app_live_version"
                               value="{{ old('b2b_agent_app_live_version', $agent_app_live_version) }}" required>
                    </div>
                </div>
                <!-- App Test Version -->
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label class="col-form-label">Agent App Test Version <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="b2b_agent_app_test_version"
                               value="{{ old('b2b_agent_app_test_version', $agent_app_test_version) }}" required>
                    </div>
                </div>
                <!-- Live URL -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="col-form-label">Agent Live Download URL <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                        <textarea id="agent_live_url" class="form-control" name="b2b_agent_live_latest_apk_url" required>{{ old('b2b_agent_live_latest_apk_url', $agent_live_latest_apk_url) }}</textarea>
                         <button type="button" onclick="copyToClipboard('agent_live_url', 'Live Agent App Url')">📋</button>
                        </div>
                    </div>
                </div>
                <!-- Test URL -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="col-form-label">Agent Test Download URL <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                        <textarea id="agent_test_url" class="form-control" name="b2b_agent_test_latest_apk_url" required>{{ old('b2b_agent_test_latest_apk_url', $agent_test_latest_apk_url) }}</textarea>
                         <button type="button" onclick="copyToClipboard('agent_test_url', 'Test Agent App Url')">📋</button>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <button type="button" class="btn btn-success" onclick="Auth_Confirm_Update('AgentAppVersionUpdateForm', '{{ route('b2b.settings.app_version_manage.update_agent') }}', '{{ $b2b_app_password }}')">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection



@section('js')
<script>

function copyToClipboard(elementId, label) {
    const element = document.getElementById(elementId);

    if (element) {
        // Copy text
        element.select();
        element.setSelectionRange(0, 99999); 
        document.execCommand("copy");

        // SweetAlert Toast
        Swal.fire({
            icon: 'success',
            title: label + ' copied successfully!',
            showConfirmButton: false,
            timer: 3000,   // 3 seconds
            timerProgressBar: true,
            position: 'top-end',
            toast: true
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Element not found!',
            showConfirmButton: false,
            timer: 3000,
            toast: true,
            position: 'top-end'
        });
    }
}

function Auth_Confirm_Update(formId, url, correctPassword) {
    Swal.fire({
        title: 'Enter The Password',
        html: `
            <div class="input-group">
                <input id="swal-password" type="password" class="form-control" placeholder="Type your password here...">
                <button class="btn btn-outline-secondary" type="button" id="toggle-password">Show</button>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#6c757d',
        confirmButtonColor: '#FC6A57',
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Confirm',
        preConfirm: () => {
            const password = document.getElementById('swal-password').value;
            if (!password) {
                Swal.showValidationMessage('Password cannot be empty!');
                return false;
            }
            if (password !== correctPassword) {
                Swal.showValidationMessage('Incorrect password!');
                return false;
            }
            return password;
        },
        didOpen: () => {
            const toggleBtn = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('swal-password');
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleBtn.textContent = isPassword ? 'Hide' : 'Show';
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const password = result.value;
            let form = $("#" + formId)[0];
            let formData = new FormData(form);
            formData.append('password', password);
            console.log($('meta[name="csrf-token"]').attr('content'));
            $.ajax({
                url: url,
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === true) {
                        Swal.fire('Updated!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Access Denied!', response.message ?? 'You do not have permission.', 'warning');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong.', 'error');
                }
            });
        }
    });
}

</script>
@endsection
