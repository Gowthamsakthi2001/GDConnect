<x-app-layout>
    <x-card>
        <x-slot name='actions'>

        </x-slot>
        <form
            action="{{ isset($item) ? route(config('theme.rprefix') . '.update', $item->id) : route(config('theme.rprefix') . '.store') }}"
            method="POST" class="needs-validation " novalidate="novalidate" enctype="multipart/form-data">
            @csrf
            @if (isset($item))
                @method('PUT')
            @endif
            <div>
                <div class="row">
                    <div class="col-md-2">
                        <label for="type_id" class="fw-bold">
                            {{ localize('Type') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="type_id" id="type_id" class="form-control" required>
                            @foreach (\Modules\Inventory\Entities\Expense::getTypes() as $id => $type)
                                <option value="{{ $id }}" @if (isset($item) && $item->type == $id) selected @endif>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="employee_id" class="fw-bold">
                            {{ localize('By Whom') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="employee_id" id="employee_id" class="form-control"
                            data-ajax-url="{{ route(config('theme.rprefix') . '.get-employee') }}" required>
                            @isset($item)
                                <option value="{{ $item->employee_id }}" selected>
                                    {{ ucfirst($item->employee->name ?? 'N/A') }}
                                </option>
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="vendor_id" class="fw-bold">
                            {{ localize('vendor') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="vendor_id" id="vendor_id" class="form-control"
                            data-ajax-url="{{ route(config('theme.rprefix') . '.get-vendor') }}" required>
                            @isset($item)
                                <option value="{{ $item->vendor_id }}" selected>
                                    {{ $item->vendor->name ?? 'N/A' }}
                                </option>
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="vehicle_id" class="fw-bold">
                            {{ localize('Vehicle') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="vehicle_id" id="vehicle_id" class="form-control"
                            data-ajax-url="{{ route(config('theme.rprefix') . '.get-vehicle') }}" required>
                            @isset($item)
                                <option value="{{ $item->vehicle_id }}" selected>
                                    {{ $item->vehicle->name ?? 'N/A' }}
                                </option>
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="trip_type_id" class="fw-bold">
                            {{ localize('Trip Type') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="trip_type_id" id="trip_type_id" class="form-control"
                            data-ajax-url="{{ route(config('theme.rprefix') . '.get-trip-type') }}" required>
                            @isset($item)
                                <option value="{{ $item->trip_type_id }}" selected>
                                    {{ $item->tripType->name ?? 'N/A' }}
                                </option>
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="trip_number" class="fw-bold">
                            {{ localize('Trip No') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control arrow-hidden" id="trip_number" name="trip_number"
                            min="0" onclick="selectAll(this)" placeholder="@localize('trip_number')"
                            value="{{ isset($item) ? $item->trip_number : old('trip_number', '00') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="odometer_millage" class="fw-bold">
                            {{ localize('Odometer_Mileage') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control arrow-hidden" id="odometer_millage"
                            name="odometer_millage" onclick="selectAll(this)" min="0"
                            placeholder="@localize('Odometer_Mileage')"
                            value="{{ isset($item) ? $item->odometer_millage : old('odometer_millage', '0.00') }}"
                            required>
                    </div>
                    <div class="col-md-2">
                        <label for="vehicle_rent" class="fw-bold">
                            {{ localize('Vehicle Rent') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control arrow-hidden" id="vehicle_rent" name="vehicle_rent"
                            min="0" onclick="selectAll(this)" step=".01" placeholder="@localize('vehicle_rent')"
                            value="{{ isset($item) ? $item->vehicle_rent : old('vehicle_rent', '0.00') }}" required>
                    </div>

                    <div class="col-md-2">
                        <label for="date" class="fw-bold">
                            {{ localize('Date') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="date" name="date"
                            placeholder="@localize('Date')"
                            value="{{ isset($item) ? $item->date : old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="remarks" class="fw-bold">
                            {{ localize('remarks') }}
                        </label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="1" placeholder="@localize('remarks')">{{ isset($item) ? $item->remarks : old('remarks') }}</textarea>
                    </div>


                </div>
                <div class="row my-5">
                    <div class="col-md-12">
                        <table class="table table-borderless table-striped" id="purchase-table"
                            data-type-url="{{ route(config('theme.rprefix') . '.get-type') }}">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="30%">
                                        @localize('Expense Type')
                                    </th>
                                    <th width="20%">
                                        @localize('Quantity')
                                        <span class="text-danger">*</span>
                                    </th>
                                    <th width="20%">
                                        @localize('Unit Price')
                                        <span class="text-danger">*</span>
                                    </th>
                                    <th width="20%">
                                        @localize('Total Price')
                                    </th>
                                    <th>
                                    </th>
                                </tr>
                            </thead>
                            <tbody data-details="{{ json_encode(isset($item) ? $item->details : []) }}"></tbody>
                        </table>
                    </div>
                </div>

                <div class="card-body footer-info fixed-bottom bg-light py-3 z-index-1">
                    <ul class="nav
                align-items-center justify-content-end">
                        <li class="nav-item text-end pe-2">
                            <b class="">@localize('Net total')</b>
                            <input type="number" step="0.01" class="form-control text-end gross_total"
                                value="0.00" readonly autocomplete="off">
                        </li>
                        <li class="nav-item pe-2">
                            <b class="text-white">...</b>
                            <button type="submit"
                                class="form-control btn btn-sm btn-success align-bottom bg-success">@localize('Save')
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </x-card>

    @push('lib-styles')
        <link href="{{ admin_asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush
    @push('lib-scripts')
        <script src="{{ admin_asset('vendor/select2/dist/js/select2.min.js') }}"></script>
    @endpush
    @push('js')
        <script src="{{ module_asset('Inventory/js/expense/create_edit.min.js') }}"></script>
    @endpush
</x-app-layout>
