<x-app-layout>
    <x-card>
        <x-slot name='actions'>
            <div class="accordion-header d-flex justify-content-end align-items-center" id="flush-headingOne">

                <button type="button" class="btn btn-success btn-sm mx-2" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne"> <i
                        class="fas fa-filter"></i> @localize('Filter')</button>
            </div>
        </x-slot>
        <x-filter-layout>
            <div class="row my-2">
                <div class="col-md-3">
                    <label for="category_id" class="fw-bold">
                        @localize('Category')
                    </label>
                    <select class="form-control select2-ajax"
                        data-ajax-url="{{ route(config('theme.rprefix') . '.get-category') }}" name="category_id"
                        id="category_id">
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="parts_id" class="fw-bold">
                        @localize('Parts')
                    </label>
                    <select class="form-control select2-ajax"
                        data-ajax-url="{{ route(config('theme.rprefix') . '.get-parts') }}" name="parts_id"
                        id="parts_id">
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="from_date" class="fw-bold">
                        @localize('Date From')
                    </label>
                    <input name="from_date" autocomplete="off" class="form-control  w-100" type="date"
                        placeholder="@localize('Joining Date From')" id="from_date"">
                </div>
                <div class="col-md-3">
                    <label for="to_date" class="fw-bold">
                        @localize(' Date To')
                    </label>
                    <input name="to_date" autocomplete="off" class="form-control w-100" type="date"
                        placeholder="@localize('Joining Date To')" id="to_date">
                </div>
            </div>
        </x-filter-layout>


        <div>
            <x-data-table :dataTable="$dataTable" />
            <div id="page-axios-data" data-table-id="#inventory-stock-table"></div>
        </div>
    </x-card>
    @push('lib-styles')
        <link href="{{ admin_asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush
    @push('lib-scripts')
        <script src="{{ admin_asset('vendor/select2/dist/js/select2.min.js') }}"></script>
    @endpush
    @push('js')
        <script src="{{ module_asset('Inventory/js/stock/index.min.js') }}"></script>
    @endpush
</x-app-layout>
