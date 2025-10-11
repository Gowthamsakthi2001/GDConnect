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
            <div class="col-md-6 my-1">
                <label for="vehicle_id" class="fw-bold">
                    @localize('vehicle')
                    <span class="text-danger">*</span>
                </label>
                <select name="vehicle_id" id="vehicle_id" class="form-control select2-ajax"
                    data-ajax-url="{{ route(config('theme.rprefix') . '.get-vehicle') }}"
                    data-placeholder="@localize('Select vehicle')" required>
                    @if (isset($item) && !empty($item->vehicle_id))
                        <option value="{{ $item->vehicle_id }}" selected>{{ $item->vehicle->name }}</option>
                    @endif
                </select>
                <label class="error" for="vehicle_id"></label>
            </div>
            <div class="col-md-6 my-1">
                <label for="station_id" class="fw-bold">
                    @localize('fuel station')
                    <span class="text-danger">*</span>
                </label>
                <select name="station_id" id="station_id" class="form-control select2-ajax"
                    data-ajax-url="{{ route(config('theme.rprefix') . '.get-station') }}"
                    data-placeholder="@localize('Select station')" required>
                    @if (isset($item) && !empty($item->station_id))
                        <option value="{{ $item->station_id }}" selected>{{ $item->station->name }}</option>
                    @endif
                </select>
                <label class="error" for="station_id"></label>
            </div>

            <div class="col-md-6 my-1">
                <label for="type_id" class="fw-bold">
                    @localize('Fule Type')
                    <span class="text-danger">*</span>
                </label>
                <select name="type_id" id="type_id" class="form-control select2-ajax"
                    data-ajax-url="{{ route(config('theme.rprefix') . '.get-type') }}"
                    data-placeholder="@localize('Select type')" required>
                    @if (isset($item) && !empty($item->type_id))
                        <option value="{{ $item->type_id }}" selected>{{ $item->type->name }}</option>
                    @endif
                </select>
                <label class="error" for="type_id"></label>
            </div>

            <div class="col-md-6 my-1">
                <label for="date" class="fw-bold">
                    @localize('Date')
                    <span class="text-danger">*</span>
                </label>
                <input type="date" class="form-control" name="date" id="date"
                    value="{{ isset($item) ? $item->date : old('date', date('Y-m-d')) }}"
                    placeholder="@localize('date')" required>
            </div>
            <div class="col-md-6 my-1">
                <label for="date" class="fw-bold">
                    @localize('Current Odometer')
                    <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control arrow-hidden" name="current_qty" id="current_qty"
                    step="0.01" min="0" onclick="this.select()"
                    value="{{ isset($item) ? $item->current_qty : old('current_qty', '0.00') }}"
                    placeholder="@localize('current_qty')" required>
            </div>
            <div class="col-md-6 my-1">
                <label for="qty" class="fw-bold">
                    @localize('Quantity')
                    <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control arrow-hidden" name="qty" id="qty" step="0.01"
                    min="0" onclick="this.select()" value="{{ isset($item) ? $item->qty : old('qty', '0.00') }}"
                    placeholder="@localize('qty')" required>
            </div>


        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@localize('Close')</button>
        <button class="btn btn-success" type="submit">@localize('Save')</button>
    </div>
</form>
