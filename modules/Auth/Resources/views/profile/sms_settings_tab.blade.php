<x-app-layout>
    <x-card>
        <x-auth::setting active_tab="{{ $active_tab }}">
            <div>
                <h3>SMS Settings</h3>
                <p>{{ localize('Add additional security to your account using sms authenticate.') }}</p>
                <hr>
            </div>
            <div class="mt-0">
                <form action="{{route('sms_settings_view.update')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="sms_temp_id">Template ID<span class="text-danger">*</span></label>
                                <div class="form-input mb-3 position-relative">
                                    <input class="form-control input-py " type="text" name="sms_temp_id" id="sms_temp_id" placeholder="Template ID" required="" value="{{ old('sms_temp_id', $sms_temp_id) }}">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="sms_auth_id">Auth ID<span class="text-danger">*</span></label>
                                <div class="form-input mb-3 position-relative">
                                    <input class="form-control input-py " type="text" name="sms_auth_id" id="sms_auth_id" placeholder="Auth ID" required="" value="{{ old('sms_auth_id', $sms_auth_id) }}">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="col-form-label text-capitalize" for="sms_pe_registration_id">PE Registration ID </label>
                                <div class="form-input mb-3 position-relative">
                                    <input class="form-control input-py " type="text" name="sms_pe_registration_id" id="sms_pe_registration_id" placeholder="PE Registration ID" required="" value="{{ old('sms_pe_registration_id', $sms_pe_registration_id) }}">
                                     
                                </div>
                            </div>
                        </div>
                        <div class="col-12 pt-2 text-end">
                            <button class="btn btn-success input-py">Update</button>
                        </div>
                    </div>
            </form>
            </div>

           
        </x-auth::setting>
    </x-card>
 
</x-app-layout>
