<x-app-layout>
    <x-card>
        <x-slot name='actions'>
            <a href="{{ route(config('theme.rprefix') . '.index') }}" class="btn btn-success btn-sm"><i
                    class="fa fa-list"></i>&nbsp;{{ localize('User List') }}</a>
        </x-slot>
        <div class="row">
            <div class="col-sm-12">
                <form enctype="multipart/form-data"
                    action="{{ isset($item) ? route(config('theme.rprefix') . '.update', $item->id) : route(config('theme.rprefix') . '.store') }}"
                    method="POST"  enctype="multipart/form-data">
                    @csrf
                    @isset($item)
                        @method('PUT')
                    @endisset
                    <fieldset class="mb-5 py-3 px-4 ">
                        <legend>{{ localize('Personal Info') }}:</legend>
                        <div class=" row">
                            <div class="col-md-6">
                                <div class="form-group pt-1 pb-1">
                                    <label for="name" class="font-black mb-2">{{ localize('Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="{{ localize('Enter Name') }}"
                                        value="{{ isset($item) ? $item->name : old('name') }}" >
                                    @error('name')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pt-1 pb-1">
                                    <label for="email" class="font-black mb-2">{{ localize('Email') }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="{{ localize('Enter Email') }}"
                                        value="{{ isset($item) ? $item->email : old('email') }}" >
                                    @error('email')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pt-1 pb-1">
                                    <label for="phone" class="font-black mb-2">{{ localize('Phone') }} <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           oninput="sanitizeAndValidatePhone(this)" 
                                           class="form-control arrow-hidden" 
                                           name="phone" 
                                           id="phone" 
                                           placeholder="{{ localize('Ex : +917865465430') }}" 
                                           value="{{ isset($item) ? $item->phone : old('phone') }}" 
                                           maxlength="13">
                                    @error('phone')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group pt-1 pb-1">
                                    <label for="gender" class="font-black mb-2">{{ localize('Gender') }}</label>
                                    <select class="form-control show-tick" name="gender" id="gender">
                                        <option value="">--{{ localize('Select Gender') }}--</option>
                                        @foreach (App\Models\User::genderList() as $gender)
                                            <option 
                                                @isset($item)
                                                    @selected($item->gender == $gender)
                                                @endisset
                                                value="{{ $gender }}" 
                                                {{ old('gender', isset($item) ? $item->gender : null) == $gender ? 'selected' : '' }}>
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
                                    <label for="age" class="font-black mb-2">{{ localize('Age') }}</label>
                                    <input type="number" class="form-control arrow-hidden" name="age"
                                        id="age" placeholder="{{ localize('Enter your age') }}"
                                        value="{{ isset($item) ? $item->age : old('age') }}">
                                    @error('age')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 py-1">
                                <div class="form-group pt-1 pb-1">
                                    <label for="address" class="font-black mb-2">{{ localize('Address') }}</label>
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
                            <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="role" class="font-black mb-2">{{ localize('User Role') }} <span class="text-danger">*</span></label>
                                    <select class="form-control show-tick" name="role" id="role">
                                        <option value="">--{{ localize('Select User Role') }}--</option>
                                        @foreach (Modules\Role\Entities\Role::all() as $role)
                                            <option 
                                                @isset($item)
                                                    @selected($item->roles()->pluck('id')->first() == $role->id)
                                                @endisset
                                                value="{{ $role->id }}" 
                                                {{ old('role', isset($item) ? $item->roles()->pluck('id')->first() : null) == $role->id ? 'selected' : '' }}>
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
                                    <label for="status" class="font-black mb-2">@localize('Account Status')</label>
                                    <select class="form-control show-tick" name="status" id="status">
                                        @foreach (App\Models\User::statusList() as $status)
                                            <option 
                                                @isset($item)
                                                    @selected($item->status == $status)
                                                @endisset
                                                value="{{ $status }}" 
                                                {{ old('status', isset($item) ? $item->status : null) == $status ? 'selected' : '' }}>
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
                                    <label for="password" class="font-black mb-2">{{ localize('Password') }} <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="{{ localize('Enter Password') }}" value="{{old('password')}}"
                                        {{ isset($item) ? '' : '' }} autocomplete="new-password">
                                    @error('password')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="password_confirmation"
                                        class="font-black mb-2">{{ localize('Confirm Password') }} <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        id="password_confirmation" placeholder="{{ localize('Retype Password') }}"
                                        {{ isset($item) ? '' : '' }} value="{{old('password_confirmation')}}" autocomplete="new-password">
                                    @error('password_confirmation')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 pt-1 pb-1">
                                <div class="form-group">
                                    <label for="avatar" class="font-black mb-2">{{ localize('Avatar') }}</label>
                                    <input type="file" class="form-control" name="avatar" id="avatar"
                                        onchange="get_img_url(this, '#avatar_image');"
                                        placeholder="{{ localize('Select avatar image') }}">
                                    <img id="avatar_image" src="{{ isset($item) ? asset('public/admin-assets/users/'.$item->profile_photo_path) : '' }}"
                                        width="120px" class="mt-1">
                                    @error('avatar')
                                        <p class="text-danger pt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group pt-1 pb-1 d-flex justify-content-end">
                                    @if (isset($item))
                                        <!-- Render an anchor tag if the condition is true -->
                                        <a href="{{ url('/') }}/admin/user" class="btn btn-info btn-round px-4 text-white">{{ localize('Cancel') }}</a>
                                        <!-- Submit button -->
                                        <button type="submit" class="btn btn-success btn-round ms-2">{{ localize('Update User') }}</button>
                                    @else
                                        <!-- Render a reset button if the condition is false -->
                                        <button type="reset" class="btn btn-info btn-round px-4 text-white">{{ localize('Reset') }}</button>
                                        <!-- Submit button -->
                                        <button type="submit" class="btn btn-success btn-round ms-2">{{ localize('Create User') }}</button>
                                    @endif
                                    
                                </div>
                            </div>


                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        
   <script>
    function sanitizeAndValidatePhone(input) {
        // Allow only `+` at the beginning and digits in the input
        input.value = input.value.replace(/(?!^\+)\D/g, '');

        // Limit the input length to 13 characters
        if (input.value.length > 13) {
            input.value = input.value.substring(0, 13);
        }
    }
</script>

    </x-card>
    @push('css')
        <link href="{{ module_asset('User/css/user.min.css') }}" rel="stylesheet">
    @endpush
</x-app-layout>
