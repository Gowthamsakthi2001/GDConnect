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
                            {{ localize('Requisition Type') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="type_id" id="type_id" class="form-control" required>
                            @foreach (\Modules\VehicleMaintenance\Entities\VehicleMaintenance::getTypes() as $id => $type)
                                <option value="{{ $id }}" @if (isset($item) && $item->type == $id) selected @endif>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="priority" class="fw-bold">
                            {{ localize('Priority') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="priority" id="priority" class="form-control" required>
                            @foreach (\Modules\VehicleMaintenance\Entities\VehicleMaintenance::getPriorities() as $i => $p)
                                <option value="{{ $i }}" @if (isset($item) && $item->p == $i) selected @endif>
                                    {{ $p }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="employee_id" class="fw-bold">
                            {{ localize('Requisition For') }}
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
                        <label for="maintenance_type_id" class="fw-bold">
                            {{ localize('Maintenance Type') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="maintenance_type_id" id="maintenance_type_id" class="form-control"
                            data-ajax-url="{{ route(config('theme.rprefix') . '.get-maintenance-type') }}" required>
                            @isset($item)
                                <option value="{{ $item->maintenance_type_id }}" selected>
                                    {{ $item->maintenanceType->name ?? 'N/A' }}
                                </option>
                            @endisset
                        </select>
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
                        <label for="title" class="fw-bold">
                            {{ localize('Service title') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title"
                            placeholder="@localize('title')" value="{{ isset($item) ? $item->title : old('title') }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label for="charge_bear_by" class="fw-bold">
                            {{ localize('Charge Bear By') }}
                        </label>
                        <input type="text" class="form-control" id="charge_bear_by" name="charge_bear_by"
                            placeholder="@localize('charge_bear_by')"
                            value="{{ isset($item) ? $item->charge_bear_by : old('charge_bear_by') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="charge" class="fw-bold">
                            {{ localize('charge') }}
                        </label>
                        <input type="number" class="form-control" id="charge" name="charge" min="0"
                            step=".01" placeholder="@localize('charge')"
                            value="{{ isset($item) ? $item->charge : old('charge', '0.00') }}">
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
                            data-inventory-category-url="{{ route(config('theme.rprefix') . '.get-inventory-category') }}"
                            data-parts-url="{{ route(config('theme.rprefix') . '.get-parts') }}">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="27%">
                                        @localize('Category')
                                        <span class="text-danger">*</span>
                                    </th>
                                    <th width="27%">
                                        @localize('Item')
                                        <span class="text-danger">*</span>
                                    </th>
                                    <th width="15%">
                                        @localize('Quantity')
                                        <span class="text-danger">*</span>
                                    </th>
                                    <th width="15%">
                                        @localize('Unit Price')
                                        <span class="text-danger">*</span>
                                    </th>
                                    <th width="15%">
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
        <script src="{{ module_asset('VehicleMaintenance/js/create_edit.min.js') }}"></script>
    @endpush
</x-app-layout>
