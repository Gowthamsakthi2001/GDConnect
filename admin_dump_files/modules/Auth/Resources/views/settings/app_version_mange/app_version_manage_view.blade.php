<x-app-layout>
    <x-card>
        <x-auth::setting active_tab="{{ $active_tab }}">
            <div>
                <h3>App Version Management Settings</h3>
                <!--<p>{{ localize('Add additional security to your account using sms authenticate.') }}</p>-->
                <hr>
            </div>
            <div class="mt-0">
                <form id="AppVersionUpdateForm" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">
                       <!-- App Live Version -->
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_live_version">
                                    App Live Version <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <input 
                                        class="form-control input-py me-2" 
                                        type="text" 
                                        name="app_live_version" 
                                        id="app_live_version" 
                                        placeholder="EX : 1.4.6+42" 
                                        required 
                                        value="{{ old('app_live_version', $app_live_version) }}" 
                                    >
                                    
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Test Version -->
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_test_version">
                                    App Test Version <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <input 
                                        class="form-control input-py me-2" 
                                        type="text" 
                                        name="app_test_version" 
                                        id="app_test_version" 
                                        placeholder="EX : 1.4.5+41+UAT" 
                                        required 
                                        value="{{ old('app_test_version', $app_test_version) }}"
                                    >
                                  
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Live Download URL -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_live_download_url">
                                    Live Download URL <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <textarea 
                                        class="form-control me-2" 
                                        name="app_live_download_url" 
                                        id="app_live_download_url" 
                                        required>{{ old('app_live_download_url', $live_latest_apk_url) }}</textarea>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary btn-sm align-self-start mt-1" 
                                        onclick="copyToClipboard('app_live_download_url', 'Live Download URL')" 
                                        title="Copy"
                                    >
                                        📋
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Test Download URL -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_test_download_url">
                                    Test Download URL <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <textarea 
                                        class="form-control me-2" 
                                        name="app_test_download_url" 
                                        id="app_test_download_url" 
                                        required>{{ old('test_latest_apk_url', $test_latest_apk_url) }}</textarea>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary btn-sm align-self-start mt-1" 
                                        onclick="copyToClipboard('app_test_download_url', 'Test Download URL')" 
                                        title="Copy"
                                    >
                                        📋
                                    </button>
                                </div>
                            </div>
                        </div>

                         <div class="col-12 pt-2 text-end">
                            <button type="button" class="btn btn-success input-py" onclick="Auth_Confirm_Update()">Save Changes</button>
                        </div>
                    </div>
                </form>  
             </div>
            
            <div class="mt-2">
                <h3>Rider App Version Management Settings</h3>
                <!--<p>{{ localize('Add additional security to your account using sms authenticate.') }}</p>-->
                <hr>
            </div>
            <div class="mt-0">
                <form id="RiderAppVersionUpdateForm" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">
                       <!-- App Live Version -->
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_live_version">
                                   Rider App Live Version <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <input 
                                        class="form-control input-py me-2" 
                                        type="text" 
                                        name="b2b_rider_app_live_version" 
                                        id="b2b_rider_app_live_version" 
                                        placeholder="EX : 1.4.6+42" 
                                        required 
                                        value="{{ old('rider_app_live_version', $rider_app_live_version) }}" 
                                    >
                                    
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Test Version -->
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_test_version">
                                   Rider App Test Version <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <input 
                                        class="form-control input-py me-2" 
                                        type="text" 
                                        name="b2b_rider_app_test_version" 
                                        id="b2b_rider_app_test_version" 
                                        placeholder="EX : 1.4.5+41+UAT" 
                                        required 
                                        value="{{ old('rider_app_test_version', $rider_app_test_version) }}"
                                    >
                                  
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Live Download URL -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_live_download_url">
                                   Rider App Live Download URL <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <textarea 
                                        class="form-control me-2" 
                                        name="b2b_rider_live_latest_apk_url" 
                                        id="b2b_rider_live_latest_apk_url" 
                                        required>{{ old('rider_app_live_download_url', $rider_live_latest_apk_url) }}</textarea>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary btn-sm align-self-start mt-1" 
                                        onclick="copyToClipboard('b2b_rider_live_latest_apk_url', 'Live Download URL')" 
                                        title="Copy"
                                    >
                                        ðŸ“‹
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Test Download URL -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_test_download_url">
                                   Rider App Test Download URL <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <textarea 
                                        class="form-control me-2" 
                                        name="b2b_rider_test_latest_apk_url" 
                                        id="b2b_rider_test_latest_apk_url" 
                                        required>{{ old('rider_test_latest_apk_url', $rider_test_latest_apk_url) }}</textarea>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary btn-sm align-self-start mt-1" 
                                        onclick="copyToClipboard('b2b_rider_live_latest_apk_url', 'Test Download URL')" 
                                        title="Copy"
                                    >
                                        ðŸ“‹
                                    </button>
                                </div>
                            </div>
                        </div>

                         <div class="col-12 pt-2 text-end">
                            <button type="button" class="btn btn-success input-py" onclick="B2b_Auth_Confirm_Update('RiderAppVersionUpdateForm', '{{ route('app_version_manage.settings.rider-update') }}', '{{ $b2b_app_password }}')">Save Changes</button>
                        </div>
                    </div>
                </form>  
             </div>
             
             <div class="mt-2">
                <h3>Agent App Version Management Settings</h3>
                <!--<p>{{ localize('Add additional security to your account using sms authenticate.') }}</p>-->
                <hr>
            </div>
            <div class="mt-0">
                <form id="AgentAppVersionUpdateForm" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">
                       <!-- App Live Version -->
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_live_version">
                                   Agent App Live Version <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <input 
                                        class="form-control input-py me-2" 
                                        type="text" 
                                        name="b2b_agent_app_live_version" 
                                        id="b2b_agent_app_live_version" 
                                        placeholder="EX : 1.4.6+42" 
                                        required 
                                        value="{{ old('agent_app_live_version', $agent_app_live_version) }}" 
                                    >
                                    
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Test Version -->
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_test_version">
                                   Agent App Test Version <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <input 
                                        class="form-control input-py me-2" 
                                        type="text" 
                                        name="b2b_agent_app_test_version" 
                                        id="b2b_agent_app_test_version" 
                                        placeholder="EX : 1.4.5+41+UAT" 
                                        required 
                                        value="{{ old('agent_app_test_version', $agent_app_test_version) }}"
                                    >
                                  
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Live Download URL -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_live_download_url">
                                   Agent App Live Download URL <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <textarea 
                                        class="form-control me-2" 
                                        name="b2b_agent_live_latest_apk_url" 
                                        id="b2b_agent_live_latest_apk_url" 
                                        required>{{ old('agent_app_live_download_url', $agent_live_latest_apk_url) }}</textarea>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary btn-sm align-self-start mt-1" 
                                        onclick="copyToClipboard('b2b_agent_live_latest_apk_url', 'Live Download URL')" 
                                        title="Copy"
                                    >
                                        ðŸ“‹
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- App Test Download URL -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="app_test_download_url">
                                   Agent App Test Download URL <span class="text-danger">*</span>
                                </label>
                                <div class="form-input mb-3 position-relative d-flex align-items-center">
                                    <textarea 
                                        class="form-control me-2" 
                                        name="b2b_agent_test_latest_apk_url" 
                                        id="b2b_agent_test_latest_apk_url" 
                                        required>{{ old('agent_test_latest_apk_url', $agent_test_latest_apk_url) }}</textarea>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary btn-sm align-self-start mt-1" 
                                        onclick="copyToClipboard('b2b_agent_test_latest_apk_url', 'Test Download URL')" 
                                        title="Copy"
                                    >
                                        ðŸ“‹
                                    </button>
                                </div>
                            </div>
                        </div>
                    
                         <div class="col-12 pt-2 text-end">
                            <button type="button" class="btn btn-success input-py" onclick="B2b_Auth_Confirm_Update('AgentAppVersionUpdateForm', '{{ route('app_version_manage.settings.agent-update') }}', '{{ $b2b_app_password }}')">Save Changes</button>
                        </div>
                    </div>
                </form>  
             </div>
           
        </x-auth::setting>
    </x-card>
 @section('script_js')
<script>
    function copyToClipboard(elementId, label) {
        const element = document.getElementById(elementId);

        if (element) {
            element.select();
            element.setSelectionRange(0, 99999); 
            document.execCommand("copy");

            toastr.success(label + ' copied successfully!');
        }
    }
    function Auth_Confirm_Update() {
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

            let form = $("#AppVersionUpdateForm")[0];
            let formData = new FormData(form);
            formData.append('password', password);

            $.ajax({
                url: "{{ route('app_version_manage.settings.update') }}",
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
                        Swal.fire('Access Denied!', response.message ?? 'You do not have permission to update this setting.', 'warning');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong.', 'error');
                }
            });
        }
    });
}

function B2b_Auth_Confirm_Update(formId, url, correctPassword) {
    Swal.fire({
        title: 'Enter The Password',
        html: `
            <div class="input-group">
                <input id="swal-password-1" type="password" class="form-control" placeholder="Type your password here...">
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
            const password = document.getElementById('swal-password-1').value;
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
            const passwordInput = document.getElementById('swal-password-1');
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
</x-app-layout>
