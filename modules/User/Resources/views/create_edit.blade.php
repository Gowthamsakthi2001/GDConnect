<x-app-layout>
    <x-card>
        <x-slot name='actions'>
            <a href="{{ route(config('theme.rprefix') . '.index') }}" class="btn btn-success btn-sm px-4"><i
                    class="fa fa-list"></i>&nbsp;{{ localize('Staff List') }}</a>
                    
           <?php
                if(isset($item->id) && $item->id != ""){
                    $approve_users = \Illuminate\Support\Facades\DB::table('model_has_roles')
                    ->join('users', 'model_has_roles.model_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->select('users.id as user_id', 'users.name as user_name', 'roles.name as role_name')   
                    ->where('roles.id', 3) // Filter for role_id = 3 (Telecaller)
                    ->get();
                    $update_user_id = $item->id; 
                    $get_approve_ids = [];
                    $getUser_updateData = [];
                
                    foreach ($approve_users as $user) {
                        $get_approve_ids[] = $user->user_id;
                        if ($user->user_id == $item->id) {
                            $getUser_updateData = [
                                'id' => $user->user_id,
                                'name' => $user->user_name,
                                'role' => $user->role_name,
                            ];
                            break; 
                        }
                    }
                }
            ?>
            @if(isset($get_approve_ids) && !empty($get_approve_ids) && in_array($update_user_id,$get_approve_ids))
              <a href="{{route('admin.user.staff_export',['id'=>$item->id,'user_role'=>$getUser_updateData['role']])}}" class="btn btn-round me-1 btn-sm px-4 btn-primary"><i class="bi bi-download"></i>&nbsp;{{ localize('Onboard Export') }}</a>
            @endif
        </x-slot>
        <div class="row">
            <div class="col-sm-12">
                <form id="StaffCreateorEditForm" enctype="multipart/form-data"
                    action="javascript:void(0);"
                    method="POST" class="needs-validation" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="ActionUrl" value="{{ isset($item) ? route(config('theme.rprefix') . '.update', $item->id) : route(config('theme.rprefix') . '.store') }}"> 
                    @isset($item)
                        @method('PUT')
                    @endisset
                    <fieldset class="mb-5 py-3 px-4 ">
                        <legend>{{ localize('Personal Info') }}:</legend>
                        <div class=" row">
                            <div class="col-md-6">
                                <div class="form-group pt-1 pb-1">
                                    <label for="name" class="font-black">{{ localize('Name') }} <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="{{ localize('Enter Name') }}"
                                        value="{{ isset($item) ? $item->name : old('name') }}" required>
                                    @error('name')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pt-1 pb-1">
                                    <label for="email" class="font-black">{{ localize('Email') }} <span class="text-danger fw-bold">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="{{ localize('Enter Email') }}"
                                        value="{{ isset($item) ? $item->email : old('email') }}" required>
                                    @error('email')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pt-1 pb-1">
                                    <label for="phone" class="font-black">{{ localize('Phone') }} <span class="text-danger fw-bold">*</span></label>
                                    <!--<input type="text" class="form-control arrow-hidden" name="phone"-->
                                    <!--    id="phone" placeholder="{{ localize('Enter phone') }}"-->
                                    <!--    value="{{ isset($item) ? $item->phone : old('phone') }}"-->
                                    <!--    onkeyup="validatePhoneNumber()">-->
                                        <input type="tel" class="form-control arrow-hidden" oninput="sanitizeAndValidatePhone(this)" id="phone" name="phone" placeholder="{{ localize('Enter phone') }}" value="{{ isset($item) ? $item->phone : old('phone') }}">
                                    <p id="phone-error" class="text-danger pt-2" style="display: none;"></p>
                                    @error('phone')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pt-1 pb-1">
                                    <label for="gender" class="font-black">{{ localize('Gender') }} <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-control show-tick" name="gender" id="gender">
                                        <option selected disabled>--{{ localize('Select Gender') }}--</option>
                                        @foreach (App\Models\User::genderList() as $gender)
                                            <option {{ isset($item) ? selected($item->gender, $gender) : null }}>
                                                {{ $gender }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('gender')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pt-1 pb-1">
                                    <label for="age" class="font-black">{{ localize('Age') }}</label>
                                    <input type="number" class="form-control arrow-hidden" name="age"
                                        id="age" placeholder="{{ localize('Enter your age') }}"
                                        value="{{ isset($item) ? $item->age : old('age') }}">
                                    @error('age')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
             
                            <div class="col-12 py-1">
                                <div class="form-group pt-1 pb-1">
                                    <label for="address" class="font-black">{{ localize('Address') }}</label>
                                    <textarea name="address" id="address" class="form-control" placeholder="{{ localize('Enter your address') }}">{{ isset($item) ? $item->address : old('address') }}</textarea>
                                    @error('address')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="mb-5 py-3 px-4 ">
                        <legend>{{ localize('Account Info') }}:</legend>
                        <div class="row">
                            <?php
                             $cities = \Modules\City\Entities\City::where('status',1)->get();
                             $city_id = isset($item) ? $item->city_id : old('city');
                             $login_type = isset($item) ? $item->login_type : '';
                             $zone_id = isset($item) ? $item->zone_id : '';
                            ?>
                            <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="role" class="font-black">Login Type <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-control show-tick" name="login_type" id="login_type" required onchange="GetLogin_Type(this.value)">
                                        <option value="">--{{ localize('Select') }}--</option>
                                        <option value="1" {{$login_type == 1 ? 'selected' : ''}}>Master</option>
                                        <option value="2" {{$login_type == 2 ? 'selected' : ''}}>Zone</option>
                                    </select>
                                    @error('login_type')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="role" class="font-black">Assign City <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-control show-tick" name="city" id="city" required onchange="getZones(this.value)">
                                        <option value="">--{{ localize('Select City') }}--</option>
                                        @foreach ($cities as $city)
                                           <option value="{{ $city->id }}" {{ $city_id == $city->id ? 'selected' : '' }}>
                                                {{ $city->city_name }}
                                            </option>

                                        @endforeach
                                    </select>
                                    @error('city')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 pt-1 pb-1" id="ToggleZone" style="display:{{$login_type == 2 ? 'block' : 'none'}};">
                                <div class="form-group">
                                    <label for="role" class="font-black">Assign Zone <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-control show-tick" name="zone" id="zone">
                                        <option value="">--{{ localize('Select Zone') }}--</option>
                                        
                                        @if(isset($item->zone_id) && $zones)
                                            @foreach ($zones as $zone)
                                                <option value="{{ $zone->id }}" {{ $zone_id == $zone->id ? 'selected' : '' }}>
                                                    {{ $zone->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                        
                                    </select>
                                    @error('zone')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            
                             <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="role" class="font-black">User Role <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-control show-tick" name="role" id="role" required>
                                        <option selected disabled>--{{ localize('Select User Role') }}--</option>
                                        @foreach (Modules\Role\Entities\Role::all() as $role)
                                            <option
                                                @isset($item) @selected($item->roles()->pluck('id')->first() == $role->id)
                                            @endisset
                                                value="{{ $role->id }}">
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="status" class="font-black">Account Status <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-control show-tick" name="status" id="status" required>
                                        <option selected disabled>--{{ localize('Select Account Status') }}--</option>
                                        @foreach (App\Models\User::statusList() as $status)
                                            <option {{ isset($item) ? selected($item->status, $status) : null }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_status_id')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="password" class="font-black">{{ localize('Password') }} <span class="text-danger fw-bold">*</span></label>
                                    <div class="position-relative">
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="{{ localize('Enter Password') }}"
                                        {{ isset($item) ? '' : 'required' }} autocomplete="new-password">
                                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 " 
                                          style="cursor:pointer;" 
                                          onclick="togglePassword()">
                                        <i id="eyeIcon" class="bi bi-eye"></i>
                                    </span>
                                    </div>
                                    @error('password')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="password_confirmation" class="font-black">Confirm Password<span class="text-danger fw-bold">*</span></label>
                                    <div class="position-relative">
                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                                        placeholder="{{ localize('Retype Password') }}"
                                        {{ isset($item) ? '' : 'required' }} autocomplete="new-password">
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 " 
                                          style="cursor:pointer;" 
                                          onclick="toggleConfirmPassword()">
                                        <i id="eyeIcon1" class="bi bi-eye"></i>
                                    </span>
                                    </div>
                                    @error('password_confirmation')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="avatar" class="font-black">{{ localize('Profile') }} <span class="text-danger fw-bold">*</span></label>
                                    <input type="file" class="form-control" name="avatar" id="avatar"
                                        onchange="get_img_url(this, '#avatar_image');"
                                        placeholder="{{ localize('Select avatar image') }}" accept="image/jpeg,image/png,image/jpg,image/gif">
                                    @if(isset($item->profile_photo_path) && $item->profile_photo_path != "")
                                        <img id="avatar_image" 
                                             src="{{ asset(isset($item) && $item->profile_photo_path ? 'uploads/users/' . $item->profile_photo_path : '') }}" 
                                             width="120px" 
                                             class="mt-1">
                                    @else
                                      <img id="avatar_image" 
                                         src="" 
                                         width="120px" 
                                         class="mt-1">
                                    @endif

                                    @error('avatar')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!--<div class="col-md-12 pt-1 pb-1">-->
                            <!--    <div>-->
                            <!--        <h4 class="border-bottom  py-1 mx-1 mb-0 font-medium-2 font-black">-->
                            <!--            <i class="feather icon-lock mr-50 "></i>-->
                            <!--            {{ localize('Permission') }}-->
                            <!--        </h4>-->
                            <!--        <div class="row mt-1">-->
                            <!--            @forelse (Modules\Permission\Entities\Permission::groups() as $gName=>$g)-->
                            <!--                <div class="col-md-12">-->
                            <!--                    <fieldset>-->
                            <!--                        <legend>-->
                            <!--                            {{ $gName }}-->
                            <!--                        </legend>-->
                            <!--                        <div class="row py-3">-->
                            <!--                            @forelse ($g as $p)-->
                            <!--                                <div class="col-md-4 form-group">-->
                            <!--                                    <div class="form-check form-switch">-->
                            <!--                                        <input class="form-check-input" type="checkbox"-->
                            <!--                                            role="switch" id="{{ $p->name }}"-->
                            <!--                                            name="permissions[{{ $p->id }}]"-->
                            <!--                                            {{ isset($item) ? (permission_check($item->permissions, $p->id) ? 'checked' : '') : '' }}-->
                            <!--                                            value="{{ $p->id }}">-->
                            <!--                                        <label class="form-check-label"-->
                            <!--                                            for="{{ $p->name }}">-->
                            <!--                                            {{ permission_key_to_name($p->name) }}-->
                            <!--                                        </label>-->
                            <!--                                    </div>-->
                            <!--                                </div>-->
                            <!--                            @empty-->
                            <!--                                <div class="col-md-12 text-center p-5">-->
                            <!--                                    <p class="text-danger">-->
                            <!--                                        {{ localize('No Permission Found') }}</p>-->
                            <!--                                </div>-->
                            <!--                            @endforelse-->
                            <!--                        </div>-->

                            <!--                    </fieldset>-->
                            <!--                </div>-->
                            <!--            @empty-->
                            <!--                <div class="col-md-12 text-center p-5">-->
                            <!--                    <p class="text-danger">{{ localize('No Permission Group') }}</p>-->
                            <!--                </div>-->
                            <!--            @endforelse-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->

                            <div class="col-md-12 d-flex justify-content-end">
                                
                              
                                    <button type="reset" id="StaffResetBtn"
                                        class="btn btn-secondary btn-round me-2">{{ localize('Reset') }}</button>
                          
                                    <button type="submit" id="StaffActionBtn"
                                        class="btn btn-success btn-round">{{ localize('Save') }}</button>
                                
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </x-card>
    @push('css')
        <link href="{{ module_asset('User/css/user.min.css') }}" rel="stylesheet">
    @endpush

@section('script_js')

   <script>
    // function validatePhoneNumber() {
    //     const phoneInput = document.getElementById('phone');
    //     const phoneError = document.getElementById('phone-error');
    //     const phoneValue = phoneInput.value.trim();

    //     // Regular expression for +91 followed by exactly 10 digits
    //     const phoneRegex = /^\+91\d{10}$/;

    //     // Check if the input matches the format
    //     if (!phoneValue.match(phoneRegex)) {
    //         if (!phoneValue.startsWith('+91')) {
    //             phoneError.textContent = "Phone number must start with +91.";
    //         } else if (phoneValue.length > 13) {
    //             phoneError.textContent = "Phone number must be a maximum of 13 characters, including +91.";
    //         } else if (phoneValue.length < 13) {
    //             phoneError.textContent = "Phone number must be 10 digits after +91.";
    //         } else {
    //             phoneError.textContent = "Invalid phone number format.";
    //         }
    //         phoneError.style.display = "block";
    //     } else {
    //         phoneError.style.display = "none";
    //     }
    // }
    
    function sanitizeAndValidatePhone(input) {
            // Ensure the input starts with '+91'
            if (!input.value.startsWith('+91')) {
                input.value = '+91' + input.value.replace(/^\+?91/, ''); // Keep "+91" at the beginning
            }

            // Allow only digits after '+91'
            input.value = input.value.replace(/[^\d+]/g, ''); // Remove any non-digit, non-plus characters

            // Limit the length to 13 characters (including '+91')
            if (input.value.length > 13) {
                input.value = input.value.substring(0, 13);
           }
        }
        
    function GetLogin_Type(login_type){
      let ZoneWrapper = $('#ToggleZone');
      if(login_type == 1){
          ZoneWrapper.find('select').prop('required', false);
          $("#zone").val('');
          ZoneWrapper.hide();
          
      }else if(login_type == 2){
          ZoneWrapper.find('select').prop('required', true);
          $("#zone").val('');
          ZoneWrapper.show();
      }else{
          ZoneWrapper.find('select').prop('required', false);
          $("#zone").val('');
          ZoneWrapper.hide();
      }
    }
        
     function getZones(CityID) {
        let ZoneDropdown = $('#zone');
        // let ZoneWrapper = $('#ToggleZone');
    
        ZoneDropdown.empty().append('<option value="">Loading...</option>');
        // ZoneWrapper.show();
    
        if (CityID) {
            $.ajax({
                url: "{{ route('global.get_zones', ':CityID') }}".replace(':CityID', CityID),
                type: "GET",
                success: function (response) {
                    ZoneDropdown.empty().append('<option value="">--Select Zone--</option>');
    
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (key, zone) {
                            ZoneDropdown.append('<option value="' + zone.id + '">' + zone.name + '</option>');
                        });
                    } else {
                        ZoneDropdown.append('<option value="">No Zones available for this City</option>');
                    }
                },
                error: function () {
                    ZoneDropdown.empty().append('<option value="">Error loading zones</option>');
                }
            });
        } else {
            ZoneDropdown.empty().append('<option value="">Select a city first</option>');
            // ZoneWrapper.hide();
        }
    }

        
</script>

<script>

    $("#StaffCreateorEditForm").submit(function(e) {
    e.preventDefault();

    var form = $(this)[0];
    var formData = new FormData(form);
    formData.append("_token", "{{ csrf_token() }}");

    var $submitBtn = $("#StaffActionBtn");
    var originalText = $submitBtn.html();
    $submitBtn.prop("disabled", true).html("⏳ Submitting...");
   
    var url = $("#ActionUrl").val();
    var RedirectUrl = "{{route('admin.user.index')}}";
    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {

            $submitBtn.prop("disabled", false).html(originalText);

            if (response.success) {
            
                Swal.fire({
                    icon: 'success',
                    title: 'Created!',
                    text: response.message,
                    confirmButtonText: 'OK'
                });
                
                setTimeout(function(){
                    window.location.href = RedirectUrl;
                },2000);
    
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: response.message,
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr, status, error) {
            $submitBtn.prop("disabled", false).html(originalText);
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    toastr.error(value[0]);
                });
            } else {
                toastr.error("Please try again.");
            }
        }
    });
});
</script>

<script>
    function togglePassword() {
    const passwordField = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.remove("bi-eye");
        eyeIcon.classList.add("bi-eye-slash");
    } else {
        passwordField.type = "password";
        eyeIcon.classList.remove("bi-eye-slash");
        eyeIcon.classList.add("bi-eye");
    }
}

function toggleConfirmPassword() {
    const passwordField = document.getElementById("password_confirmation");
    const eyeIcon1 = document.getElementById("eyeIcon1");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon1.classList.remove("bi-eye");
        eyeIcon1.classList.add("bi-eye-slash");
    } else {
        passwordField.type = "password";
        eyeIcon1.classList.remove("bi-eye-slash");
        eyeIcon1.classList.add("bi-eye");
    }
}
</script>
@endsection
</x-app-layout>
