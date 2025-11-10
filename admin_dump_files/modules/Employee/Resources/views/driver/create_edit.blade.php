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
                    <label for="driver_name" class="col-sm-5 col-form-label">@localize('Driver Name') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="name" required id="driver_name" class="form-control" type="text"
                            placeholder="@localize('Driver Name')" value="{{ isset($item) ? $item->name : old('name') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="license_number" class="col-sm-5 col-form-label">@localize('License Number') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="license_num" required id="license_number" class="form-control" type="text"
                            placeholder="@localize('License Number')"
                            value="{{ isset($item) ? $item->license_num : old('license_num') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="emp_nid" class="col-sm-5 col-form-label">@localize('NID') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="nid" required="" class="form-control" type="number"
                            placeholder="@localize('Employee NID')" id="emp_nid"
                            value="{{ isset($item) ? $item->nid : old('nid') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="working_time_slot" class="col-sm-5 col-form-label">@localize('Working Time Slot') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="working_time_slot" required class="form-control" type="text"
                            placeholder="10:00AM-6:00PM" id="working_time_slot"
                            value="{{ isset($item) ? $item->working_time_slot : old('working_time_slot') }}">
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
                    <label for="present_address" class="col-sm-5 col-form-label">@localize('Present Address') </label>
                    <div class="col-sm-7">
                        <input name="present_address" class="form-control" type="text"
                            placeholder="@localize('Present Address')" id="present_address"
                            value="{{ isset($item) ? $item->present_address : old('present_address') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="checkbox2" class="col-sm-5 col-form-label">&nbsp;</label>
                    <div class="col-sm-7 checkbox checkbox-primary">
                        <input id="checkbox2" type="checkbox" value="1" name="is_active"
                            @checked(isset($item) ? $item->is_active : false)>
                        <label for="checkbox2">@localize('Is Active')</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">

                <div class="form-group row my-2">
                    <label for="phone" class="col-sm-5 col-form-label">@localize('Mobile') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="phone" required class="form-control" type="number"
                            placeholder="@localize('Mobile')" id="phone"
                            value="{{ isset($item) ? $item->phone : old('phone') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="license_type_id" class="col-sm-5 col-form-label">@localize('License Type') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" required name="license_type_id"
                            id="license_type_id" tabindex="-1" aria-hidden="true">
                            <option value="" selected="selected">@localize('Please Select One')
                            </option>
                            @foreach ($license_types as $license_type)
                                <option value="{{ $license_type->id }}"
                                    {{ isset($item) ? ($item->license_type_id == $license_type->id ? 'selected' : '') : '' }}>
                                    {{ $license_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="license_issue_date" class="col-sm-5 col-form-label">@localize('License Issue Date') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="license_issue_date" required autocomplete="off" class="form-control"
                            type="date" placeholder="@localize('License Issue Date')" id="license_issue_date"
                            value="{{ isset($item) ? $item->license_issue_date : old('license_issue_date') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="join_date" class="col-sm-5 col-form-label">@localize('Joining Date') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="joining_date" required autocomplete="off" class="form-control" type="date"
                            placeholder="@localize('Joining Date')" id="join_date"
                            value="{{ isset($item) ? $item->joining_date : old('joining_date') }}">
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

                <div class="form-group row">
                    <label for="leavestatus" class="col-sm-5 col-form-label">@localize('Leave Status') </label>
                    <div class="col-sm-7">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="statusyes" name="leave_status" class="custom-control-input"
                                value="1" @checked(isset($item) ? $item->leave_status : false)>
                            <label class="custom-control-label" for="statusyes">@localize('Yes')</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="statusno" name="leave_status" class="custom-control-input"
                                value="0" @checked(isset($item) ? ($item->leave_status == 0 ? true : false) : true)>
                            <label class="custom-control-label" for="statusno">@localize('No')</label>
                        </div>
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
