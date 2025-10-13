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
                    <label for="employee_id" class="col-sm-5 col-form-label">@localize('Requisition For') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="employee_id" id="employee_id" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ isset($item) ? ($item->employee_id == $employee->id ? 'selected' : '') : '' }}>
                                    {{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-1">
                    <label for="vehicle_type" class="col-sm-5 col-form-label">@localize('Vehicle Type') <i
                        class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="vehicle_type_id" id="vehicle_type"
                            tabindex="-1" aria-hidden="true" required>
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
                    <label for="where_from" class="col-sm-5 col-form-label">@localize('Where From') <i
                        class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="where_from" class="form-control" type="text"
                            placeholder="@localize('Where From')" id="where_from"
                            value="{{ isset($item) ? $item->where_from : old('where_from') }}" required>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label for="where_to" class="col-sm-5 col-form-label">@localize('Where To') <i
                        class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="where_to" class="form-control" type="text"
                            placeholder="@localize('Where To')" id="where_to"
                            value="{{ isset($item) ? $item->where_to : old('where_to') }}" required>
                    </div>
                </div>


                <div class="form-group row my-2">
                    <label for="pick_up" class="col-sm-5 col-form-label">@localize('Pick Up') </label>
                    <div class="col-sm-7">
                        <input name="pickup" class="form-control" type="text" placeholder="@localize('Pick Up')"
                            id="pick_up" value="{{ isset($item) ? $item->pickup : old('pickup') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="requisition_date" class="col-sm-5 col-form-label">@localize('Requisition Date') </label>
                    <div class="col-sm-7">
                        <input name="requisition_date" autocomplete="off" class="form-control" type="date"
                            placeholder="@localize('Requisition Date')" id="requisition_date"
                            value="{{ isset($item) ? $item->requisition_date : old('requisition_date') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="time_from" class="col-sm-5 col-form-label">@localize('Time From') </label>
                    <div class="col-sm-7">
                        <input name="time_from" class="form-control" type="time" placeholder="@localize('Time From')"
                            id="time_from" value="{{ isset($item) ? $item->time_from : old('time_from') }}">
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-6">

                <div class="form-group row my-2">
                    <label for="time_to" class="col-sm-5 col-form-label">@localize('Time To') </label>
                    <div class="col-sm-7">
                        <input name="time_to" class="form-control" type="time" placeholder="@localize('Time From')"
                            id="time_to" value="{{ isset($item) ? $item->time_to : old('time_to') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="tolerance" class="col-sm-5 col-form-label">@localize('Tolerance Duration')
                        <i class="text-danger">*</i></label>
                    </label>

                    <div class="col-sm-7">
                        <input name="tolerance" required class="form-control" type="text"
                            placeholder="@localize('Tolarance Duration')" id="tolerance_duration"
                            value="{{ isset($item) ? $item->tolerance : old('tolerance') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="no_of_passenger" class="col-sm-5 col-form-label">@localize('Number of Passenger') </label>
                    <div class="col-sm-7">
                        <input name="number_of_passenger" class="form-control" type="number"
                            placeholder="@localize('Number of Passenger')" id="no_of_passenger"
                            value="{{ isset($item) ? $item->no_of_passenger : old('no_of_passenger') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="driver_id" class="col-sm-5 col-form-label">@localize('Driven By') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="driver_id" id="driver_id" tabindex="-1"
                            aria-hidden="true" required>
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
                    <label for="purpose" class="col-sm-5 col-form-label">@localize('Purpose') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="purpose" id="purpose" tabindex="-1"
                            aria-hidden="true" required>
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($purposes as $key => $value)
                                <option value="{{ $value->id }}"
                                    {{ isset($item) ? ($item->purpose == $value->id ? 'selected' : '') : '' }}>
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="details" class="col-sm-5 col-form-label">@localize('Details') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <textarea name="details" id="details" cols="30" rows="4" required>{{ isset($item) ? $item->details : old('details') }}</textarea>
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
