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
                    <div class="col-md-3">
                        <label for="vendor_id" class="fw-bold">
                            {{ localize('Vendor Name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="vendor_id" id="vendor_id" class="form-control"
                            data-ajax-url="{{ route('admin.purchase.get-vendor') }}" required>
                            @isset($item)
                                <option value="{{ $item->vendor_id }}" selected>
                                    {{ $item->vendor->name }}
                                </option>
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date" class="fw-bold">
                            {{ localize('Date') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="date" name="date"
                            placeholder="@localize('Date')"
                            value="{{ isset($item) ? $item->date : old('date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="req_img_path" class="fw-bold"> {{ localize('Manual Requisition Image') }}</label>
                        <input type="file" class="form-control" id="req_img_path" name="req_img_path"
                            placeholder="@localize('req_img_path')" accept=".jpg,.png,.jpeg">
                    </div>
                    <div class="col-md-3">
                        <label for="order_path" class="fw-bold"> {{ localize('Work Order Image') }}</label>
                        <input type="file" class="form-control" id="order_path" name="order_path"
                            accept=".jpg,.png,.jpeg">
                    </div>
                </div>
                <div class="row my-5">
                    <div class="col-md-12">
                        <table class="table table-borderless table-striped" id="purchase-table"
                            data-inventory-category-url="{{ route('admin.purchase.get-inventory-category') }}"
                            data-parts-url="{{ route('admin.purchase.get-parts') }}">
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
        <script src="{{ module_asset('Purchase/js/app.min.js') }}"></script>
    @endpush
</x-app-layout>
