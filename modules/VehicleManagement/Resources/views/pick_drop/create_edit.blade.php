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
        <table class="table table-hover table-striped">
            <tr>
                <th>
                    <label for="route_id" class="col-form-label">@localize('Route') <i class="text-danger">*</i></label>
                </th>
                <td>
                    <select class="form-control basic-single" name="route_id" id="route_id" tabindex="-1"
                        aria-hidden="true">
                        <option value="">@localize('Please Select One')</option>
                        @foreach ($vehicle_routes as $vehicle_route)
                            <option value="{{ $vehicle_route->id }}"
                                {{ isset($item) ? ($item->route_id == $vehicle_route->id ? 'selected' : '') : '' }}>
                                {{ $vehicle_route->route_name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="start_point" class="col-form-label">@localize('Start Point') <i class="text-danger">*</i>
                    </label>
                </th>
                <td>
                    <input readonly name="start_point" class="form-control " type="text"
                        placeholder="@localize('Start Point')" id="start_point"
                        value="{{ isset($item) ? $item->start_point : old('start_point') }}">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="end_point" class="col-form-label">@localize('End Point') <i class="text-danger">*</i>
                    </label>
                </th>
                <td>
                    <input readonly name="end_point" class="form-control " type="text"
                        placeholder="@localize('End Point')" id="end_point"
                        value="{{ isset($item) ? $item->end_point : old('end_point') }}">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="employee" class="col-form-label">@localize('Requisition For') <i class="text-danger">*</i>
                </th>
                <td>
                    <select class="form-control basic-single" name="employee_id" id="employee" tabindex="-1"
                        aria-hidden="true">
                        <option value="">@localize('Please Select One')</option>
                        @foreach ($employees as $key => $value)
                            <option value="{{ $value->id }}"
                                {{ isset($item) ? ($item->employee_id == $value->id ? 'selected' : '') : '' }}>
                                {{ $value->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="request_type" class="col-form-label">@localize('Request Type') <i class="text-danger">*</i>
                    </label>
                </th>
                <td>
                    <select name="request_type" id="request_type" class="form-control">
                        <option value="">@localize('Please Select One')</option>
                        <option value="1" @if (isset($item) && $item->request_type == 1) selected @endif>
                            @localize('Specific Day')
                        </option>
                        <option value="0" @if (isset($item) && $item->request_type == 0) selected @endif>
                            @localize('Regular')
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="type" class="col-form-label">@localize('Type') <i class="text-danger">*</i>
                    </label>
                </th>
                <td>
                    <select name="type" id="type" class="form-control">
                        <option value="">@localize('Please Select One')</option>
                        <option value="Pickup" @if (isset($item) && $item->type == 'Pickup') selected @endif>
                            @localize('Pick up')
                        </option>
                        <option value="Drop" @if (isset($item) && $item->type == 'Drop') selected @endif>
                            @localize('Drop off')
                        </option>

                        <option value="PickDrop" @if (isset($item) && $item->type == 'PickDrop') selected @endif>
                            @localize('Pickup & Drop off')
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="date" class="col-form-label">@localize('Effective Date') </label>
                </th>
                <td>
                    <input name="effective_date" class="form-control" type="date" placeholder="@localize('Effective Date')"
                        id="date" value="{{ isset($item) ? $item->effective_date : old('effective_date') }}">
                </td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@localize('Close')</button>
        <button class="btn btn-success" type="submit">@localize('Save')</button>
    </div>
</form>
