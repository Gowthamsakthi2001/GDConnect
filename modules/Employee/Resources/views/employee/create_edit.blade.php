<form
    action="{{ isset($item) ? route(config('theme.rprefix') . '.update', $item->id) : route(config('theme.rprefix') . '.store') }}"
    method="POST" class="needs-validation modal-content" novalidate="novalidate" enctype="multipart/form-data"
    onsubmit="submitFormAxios(event)">
    @csrf
    @if (isset($item))
        @method('PUT')
    @endif
    <div class="card-header my-3 p-2 border-bottom">
        <h4>{{ config('theme.title') }}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12 col-lg-6">
                <div class="form-group row my-2">
                    <label for="emp_name" class="col-sm-5 col-form-label">@localize('Employee Name') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="name" required id="emp_name" class="form-control" type="text"
                            placeholder="@localize('Employee Name')" value="{{ isset($item) ? $item->name : old('name') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="pay_roll_type" class="col-sm-5 col-form-label">@localize('Pay Roll Type') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="payroll_type" id="pay_roll_type"
                            data-select2-id="pay_roll_type" tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach (config('employee.payroll_types') as $payroll_type)
                                <option value="{{ $payroll_type }}"
                                    {{ isset($item) ? ($item->payroll_type == $payroll_type ? 'selected' : '') : '' }}>
                                    {{ $payroll_type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="department" class="col-sm-5 col-form-label">@localize('Department') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" required name="department_id" id="department"
                            data-select2-id="department" tabindex="-1" aria-hidden="true">
                            <option value="" selected="selected">@localize('Please Select One')</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ isset($item) ? ($item->department_id == $department->id ? 'selected' : '') : '' }}>
                                    {{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="email" class="col-sm-5 col-form-label">@localize('Email') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="email" required class="form-control" type="email"
                            placeholder="@localize('Email')" id="email"
                            value="{{ isset($item) ? $item->email : old('email') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="email2" class="col-sm-5 col-form-label">@localize('Email Optional') </label>
                    <div class="col-sm-7">
                        <input name="email2" class="form-control" type="email" placeholder="@localize('Email Optional')"
                            id="email2" value="{{ isset($item) ? $item->email2 : old('email2') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="blood" class="col-sm-5 col-form-label">@localize('Blood Group') </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="blood" id="blood"
                            data-select2-id="blood_group" tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One') </option>
                            @foreach (config('employee.blood_groups') as $blood_group)
                                <option value="{{ $blood_group }}"
                                    {{ isset($item) ? ($item->blood_group == $blood_group ? 'selected' : '') : '' }}>
                                    {{ $blood_group }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="working_slot_from" class="col-sm-5 col-form-label">@localize('Working Slot From') </label>
                    <div class="col-sm-7">
                        <input name="working_slot_from" class="form-control ttimepicker" type="text"
                            placeholder="@localize('Working Slot From')" id="working_slot_from"
                            value="{{ isset($item) ? $item->working_slot_from : old('working_slot_from') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="father_name" class="col-sm-5 col-form-label">@localize('Father Name') </label>
                    <div class="col-sm-7">
                        <input name="father_name" class="form-control" type="text" placeholder="@localize('Father Name')"
                            id="father_name" value="{{ isset($item) ? $item->father_name : old('father_name') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="present_cont" class="col-sm-5 col-form-label">@localize('Present Contact Number') </label>
                    <div class="col-sm-7">
                        <input name="present_contact" class="form-control" type="number"
                            placeholder="@localize('Present Contact Number')" id="present_cont"
                            value="{{ isset($item) ? $item->present_contact : old('present_contact') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="present_address" class="col-sm-5 col-form-label">@localize('Present Address') </label>
                    <div class="col-sm-7">
                        <input name="present_address" class="form-control" type="text"
                            placeholder="@localize('Present Address')" id="present_address"
                            value="{{ isset($item) ? $item->present_address : old('present_address') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="present_city" class="col-sm-5 col-form-label">@localize('Present City') </label>
                    <div class="col-sm-7">
                        <input name="present_city" class="form-control" type="text"
                            placeholder="@localize('Present City')" id="present_city"
                            value="{{ isset($item) ? $item->present_city : old('present_city') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="referance" class="col-sm-5 col-form-label">@localize('Reference Name') </label>
                    <div class="col-sm-7">
                        <input name="reference_name" class="form-control" type="text"
                            placeholder="Reference Name" id="referance"
                            value="{{ isset($item) ? $item->reference_name : old('reference_name') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="ref_address" class="col-sm-5 col-form-label">@localize('Reference Address') </label>
                    <div class="col-sm-7">
                        <input name="reference_address" class="form-control" type="text"
                            placeholder="@localize('Reference Address')" id="ref_address"
                            value="{{ isset($item) ? $item->reference_address : old('reference_address') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="ref_email" class="col-sm-5 col-form-label">@localize('Reference Email') </label>
                    <div class="col-sm-7">
                        <input name="reference_email" class="form-control" type="email"
                            placeholder="@localize('Reference Email')" id="ref_email"
                            value="{{ isset($item) ? $item->reference_email : old('reference_email') }}">
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">
                <div class="form-group row my-2">
                    <label for="emp_nid" class="col-sm-5 col-form-label">@localize('Employee NID') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="nid" required="" class="form-control" type="number"
                            placeholder="@localize('Employee NID')" id="emp_nid"
                            value="{{ isset($item) ? $item->nid : old('nid') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="designation" class="col-sm-5 col-form-label">@localize('Designation') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" required name="position_id" id="designation"
                            data-select2-id="designation" tabindex="-1" aria-hidden="true">
                            <option value="" selected="selected">@localize('Please Select One')
                            </option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}"
                                    {{ isset($item) ? ($item->position_id == $position->id ? 'selected' : '') : '' }}>
                                    {{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="phone" class="col-sm-5 col-form-label">@localize('Employee Mobile') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="phone" required class="form-control" type="number"
                            placeholder="@localize('Employee Mobile')" id="phone"
                            value="{{ isset($item) ? $item->phone : old('phone') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="phone2" class="col-sm-5 col-form-label">@localize('Employee Mobile Optional') </label>
                    <div class="col-sm-7">
                        <input name="phone2" class="form-control" type="number" placeholder="@localize('Employee Mobile Optional')"
                            id="phone2" value="{{ isset($item) ? $item->phone2 : old('phone2') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="join_date" class="col-sm-5 col-form-label">@localize('Join Date') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="join_date" required autocomplete="off" class="form-control" type="date"
                            placeholder="@localize('Join Date')" id="join_date"
                            value="{{ isset($item) ? $item->join_date : old('join_date') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="dob" class="col-sm-5 col-form-label">@localize('Date of Birth') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="dob" required autocomplete="off" class="form-control" type="date"
                            placeholder="@localize('Date of Birth')" id="dob"
                            value="{{ isset($item) ? $item->dob : old('dob') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="working_slot_to" class="col-sm-5 col-form-label">@localize('Working Slot To') </label>
                    <div class="col-sm-7">
                        <input name="working_slot_to" class="form-control ttimepicker" type="text"
                            placeholder="@localize('Working Slot To')" id="working_slot_to"
                            value="{{ isset($item) ? $item->working_slot_to : old('working_slot_to') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="mother_name" class="col-sm-5 col-form-label">@localize('Mother Name') </label>
                    <div class="col-sm-7">
                        <input name="mother_name" class="form-control" type="text"
                            placeholder="@localize('Mother Name')" id="mother_name"
                            value="{{ isset($item) ? $item->mother_name : old('mother_name') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="permanent_contact" class="col-sm-5 col-form-label">@localize('Permanent Contact Number') </label>
                    <div class="col-sm-7">
                        <input name="permanent_contact" class="form-control" type="text"
                            placeholder="@localize('Permanent Contact Number')" id="permanent_contact"
                            value="{{ isset($item) ? $item->permanent_contact : old('permanent_contact') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="permanent_address" class="col-sm-5 col-form-label">@localize('Permanent Address') </label>
                    <div class="col-sm-7">
                        <input name="permanent_address" class="form-control" type="text"
                            placeholder="@localize('Permanent Address')" id="permanent_address"
                            value="{{ isset($item) ? $item->permanent_address : old('permanent_address') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="permanent_city" class="col-sm-5 col-form-label">@localize('Permanent City') </label>
                    <div class="col-sm-7">
                        <input name="permanent_city" class="form-control" type="text"
                            placeholder="@localize('Permanent City')" id="permanent_city"
                            value="{{ isset($item) ? $item->permanent_city : old('permanent_city') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="ref_mobile" class="col-sm-5 col-form-label">@localize('Reference Mobile') </label>
                    <div class="col-sm-7">
                        <input name="reference_mobile" class="form-control" type="number"
                            placeholder="@localize('Reference Mobile')" id="ref_mobile"
                            value="{{ isset($item) ? $item->reference_mobile : old('reference_mobile') }}">
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="picture" class="col-sm-5 col-form-label">@localize('Photograph') </label>
                    <div class="col-sm-7">
                        <input type="file" accept="image/*" name="picture"
                            onchange="get_img_url(this, '#avatar_image');">
                        <img id="avatar_image" src="{{ isset($item) ? $item->avatar_url : '' }}" width="120px"
                            class="mt-1">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@localize('Close')</button>

        <button class="btn btn-success" type="submit">@localize('Save')</button>
    </div>
</form>
