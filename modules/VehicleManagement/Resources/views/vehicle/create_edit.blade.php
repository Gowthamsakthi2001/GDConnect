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
                    <label for="name" class="col-sm-5 col-form-label">@localize('Vehicle Name') <i
                            class="text-danger">*</i></label> </label>
                    <div class="col-sm-7">
                        <input name="name" class="form-control" type="text" placeholder="@localize('Vehicle Name')"
                            id="name" value="{{ isset($item) ? $item->name : old('name') }}" required>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="department_id" class="col-sm-5 col-form-label">@localize('Department') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="department_id" id="department_id" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ isset($item) ? ($item->department_id == $department->id ? 'selected' : '') : '' }}>
                                    {{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="registration_date" class="col-sm-5 col-form-label">@localize('Registration Date') <i
                            class="text-danger">*</i></label> </label>
                    <div class="col-sm-7">
                        <input name="registration_date" autocomplete="off" required class="form-control" type="date"
                            placeholder="@localize('Requisition Date')" id="registration_date"
                            value="{{ isset($item) ? $item->registration_date : old('registration_date') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="license_plate" class="col-sm-5 col-form-label">@localize('License Plate') <i
                            class="text-danger">*</i></label> </label>
                    <div class="col-sm-7">
                        <input name="license_plate" class="form-control" type="text" placeholder="@localize('License Plate')"
                            id="license_plate" value="{{ isset($item) ? $item->license_plate : old('license_plate') }}"
                            required>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="alert_cell_no" class="col-sm-5 col-form-label">@localize('Alert Cell Number') <i
                            class="text-danger">*</i></label> </label>
                    <div class="col-sm-7">
                        <input name="alert_cell_no" class="form-control" type="number" placeholder="@localize('Alert Cell Number')"
                            id="alert_cell_no" value="{{ isset($item) ? $item->alert_cell_no : old('alert_cell_no') }}"
                            required>
                    </div>
                </div>

                <div class="form-group row mb-1">
                    <label for="ownership" class="col-sm-5 col-form-label">@localize('Ownership') <i
                            class="text-danger">*</i></label> </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="ownership_id" id="ownership" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($ownerships as $key => $value)
                                <option value="{{ $value->id }}"
                                    {{ isset($item) ? ($item->ownership_id == $value->id ? 'selected' : '') : '' }}>
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="col-md-12 col-lg-6">

                <div class="form-group row mb-1">
                    <label for="vehicle_type" class="col-sm-5 col-form-label">@localize('Vehicle Type') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="vehicle_type_id" id="vehicle_type"
                            tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($vehicle_types as $key => $value)
                                <option value="{{ $value->id }}"
                                    {{ isset($item) ? ($item->vehicle_type_id == $value->id ? 'selected' : '') : '' }}>
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="rta_office" class="col-sm-5 col-form-label">@localize('RTA Office') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="rta_circle_office_id" id="rta_office"
                            tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($circle_offices as $rta_office)
                                <option value="{{ $rta_office->id }}"
                                    {{ isset($item) ? ($item->rta_circle_office_id == $rta_office->id ? 'selected' : '') : '' }}>
                                    {{ $rta_office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="driver_id" class="col-sm-5 col-form-label">@localize('Driver') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="driver_id" id="driver_id" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}"
                                    {{ isset($item) ? ($item->driver_id == $driver->id ? 'selected' : '') : '' }}>
                                    {{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-1">
                    <label for="vendor" class="col-sm-5 col-form-label">@localize('Vendor') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="vendor_id" id="vendor" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($vendors as $key => $value)
                                <option value="{{ $value->id }}"
                                    {{ isset($item) ? ($item->vendor_id == $value->id ? 'selected' : '') : '' }}>
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="seat_capacity" class="col-sm-5 col-form-label">@localize('Seat Capacity with Driver') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="seat_capacity" class="form-control" type="number"
                            placeholder="@localize('Seat Capacity with Driver')" id="seat_capacity"
                            value="{{ isset($item) ? $item->seat_capacity : old('seat_capacity') }}" required>
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
