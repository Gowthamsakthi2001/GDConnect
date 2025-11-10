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
                    <label for="vehicle" class="col-sm-5 col-form-label">@localize('Vehicle') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" required name="vehicle_id" id="vehicle"
                            data-select2-id="vehicle" tabindex="-1" aria-hidden="true">
                            <option value="" selected="selected">@localize('Please Select One')</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                    {{ isset($item) ? ($item->vehicle_id == $vehicle->id ? 'selected' : '') : '' }}>
                                    {{ $vehicle->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="fuel_type" class="col-sm-5 col-form-label">@localize('Fuel Type') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" required name="fuel_type_id" id="fuel_type"
                            data-select2-id="fuel_type" tabindex="-1" aria-hidden="true">
                            <option value="" selected="selected">@localize('Please Select One')</option>
                            @foreach ($fuel_types as $fuel_type)
                                <option value="{{ $fuel_type->id }}"
                                    {{ isset($item) ? ($item->fuel_type_id == $fuel_type->id ? 'selected' : '') : '' }}>
                                    {{ $fuel_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="fuel_station" class="col-sm-5 col-form-label">@localize('Fuel Station') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" required name="fuel_station_id" id="fuel_station"
                            data-select2-id="fuel_station" tabindex="-1" aria-hidden="true">
                            <option value="" selected="selected">@localize('Please Select One')</option>
                            @foreach ($fuel_stations as $fuel_station)
                                <option value="{{ $fuel_station->id }}"
                                    {{ isset($item) ? ($item->fuel_station_id == $fuel_station->id ? 'selected' : '') : '' }}>
                                    {{ $fuel_station->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="budget" class="col-sm-5 col-form-label">@localize('Budget') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="budget" required class="form-control" type="number"
                            placeholder="@localize('Budget')" id="budget"
                            value="{{ isset($item) ? $item->budget : old('budget', 0) }}" step="0.01"
                            min="0">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="place" class="col-sm-5 col-form-label">@localize('Place') <i class="text-danger">*</i>
                    </label>
                    <div class="col-sm-7">
                        <input name="place" class="form-control" type="text" placeholder="@localize('Place')"
                            id="place" value="{{ isset($item) ? $item->place : old('place') }}" required>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="km_per_unit" class="col-sm-5 col-form-label">@localize('Kilometer Per Unit') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="km_per_unit" class="form-control" type="number" placeholder="@localize('Kilometer Per Unit')"
                            id="km_per_unit" value="{{ isset($item) ? $item->km_per_unit : old('km_per_unit', 0) }}"
                            step="0.01" min="0" required>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="last_reading" class="col-sm-5 col-form-label">@localize('Last Reading') </label>
                    <div class="col-sm-7">
                        <input name="last_reading" class="form-control" type="number" placeholder="@localize('Last Reading')"
                            min="0" id="last_reading"
                            value="{{ isset($item) ? $item->last_reading : old('last_reading', 0) }}" step="0.01">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="last_unit" class="col-sm-5 col-form-label">@localize('Last Unit') </label>
                    <div class="col-sm-7">
                        <input name="last_unit" class="form-control" type="number" placeholder="@localize('Last Unit')"
                            id="last_unit" value="{{ isset($item) ? $item->last_unit : old('last_unit', 0) }}"
                            step="0.01" min="0">
                    </div>
                </div>
            </div>


            <div class="col-md-12 col-lg-6">

                <div class="form-group row my-2">
                    <label for="driver" class="col-sm-5 col-form-label">@localize('Driver') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" required name="driver_id" id="driver"
                            tabindex="-1" aria-hidden="true">
                            <option value="" selected="selected">@localize('Please Select One')
                            </option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}"
                                    {{ isset($item) ? ($item->driver_id == $driver->id ? 'selected' : '') : '' }}>
                                    {{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="refuel_limit" class="col-sm-5 col-form-label">@localize('Refuel Limit') </label>
                    <div class="col-sm-7">
                        <input name="refuel_limit" class="form-control" type="number" step="0.01"
                            min="0" placeholder="@localize('Refuel Limit Type')" id="refuel_limit"
                            value="{{ isset($item) ? $item->refuel_limit : old('refuel_limit', 0) }}">
                    </div>
                </div>


                <div class="form-group row my-2">
                    <label for="max_unit" class="col-sm-5 col-form-label">@localize('Max Unit') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="max_unit" class="form-control" type="number" step="0.01" min="0"
                            placeholder="@localize('Max Unit')" id="max_unit"
                            value="{{ isset($item) ? $item->max_unit : old('max_unit', 0) }}" required>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="consumption_percent" class="col-sm-5 col-form-label">@localize('Consumption Percent') </label>
                    <div class="col-sm-7">
                        <input name="consumption_percent" class="form-control" type="number" step="0.01"
                            min="0" placeholder="@localize('Consumption Percent')" id="consumption_percent"
                            value="{{ isset($item) ? $item->consumption_percent : old('consumption_percent', 0) }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="odometer_day_end" class="col-sm-5 col-form-label">@localize('Odometer KM after day end stop') </label>
                    <div class="col-sm-7">
                        <input name="odometer_day_end" class="form-control" type="text"
                            placeholder="@localize('Odometer KM after day end stop')" id="odometer_day_end"
                            value="{{ isset($item) ? $item->odometer_day_end : old('odometer_day_end') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="odometer_refuel_time" class="col-sm-5 col-form-label">@localize('Odometer at time of refuelin') </label>
                    <div class="col-sm-7">
                        <input name="odometer_refuel_time" class="form-control" type="text"
                            placeholder="@localize('Odometer at time of refuelin')" id="odometer_refuel_time"
                            value="{{ isset($item) ? $item->odometer_refuel_time : old('odometer_refuel_time') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="unit_taken" class="col-sm-5 col-form-label">@localize('Unit Taken') </label>
                    <div class="col-sm-7">
                        <input name="unit_taken" class="form-control" type="number" step="0.01" min="0"
                            placeholder="@localize('Unit Taken')" id="unit_taken"
                            value="{{ isset($item) ? $item->unit_taken : old('unit_taken', 0) }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="fuel_slip" class="col-sm-5 col-form-label">@localize('Fuel Slip Scan Copy') </label>
                    <div class="col-sm-7">
                        <input type="file" accept="image/*" name="fuel_slip"
                            onchange="get_img_url(this, '#document_image');">

                        <img id="document_image" src="{{ isset($item) ? $item->slip_url : '' }}" width="120px"
                            class="mt-1">
                    </div>
                </div>

                <div class="form-group row m-0">
                    <label for="strict_policy" class="col-sm-5 col-form-label">&nbsp; </label>
                    <div class="col-sm-7 checkbox checkbox-primary">
                        <input id="checkbox2" type="checkbox" name="strict_policy" value="1"
                            {{ isset($item) ? ($item->strict_policy == 1 ? 'checked' : '') : '' }}>
                        <label for="checkbox2">@localize('Strict Consumption Apply')</label>
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
