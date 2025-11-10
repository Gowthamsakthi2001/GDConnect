<x-app-layout>
    <x-card>
        <x-slot name='actions'>
            <div class="accordion-header d-flex justify-content-end align-items-center" id="flush-headingOne">
                <a class="btn btn-success btn-sm" href="javascript:void(0);"
                    onclick="axiosModal('{{ route(\config('theme.rprefix') . '.create') }}')">
                    <i class="fa fa-plus"></i>
                    {{ localize('Pick And Drop Requisition') }}
                </a>

                <button type="button" class="btn btn-success btn-sm mx-2" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne"> <i
                        class="fas fa-filter"></i> @localize('Filter')</button>
            </div>
        </x-slot>
        <x-filter-layout>
            <div class="col-sm-12 col-xl-4">
                <div class="form-group row mb-1">
                    <label for="route"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Route') </label>
                    </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="route_id" id="route" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($vehicle_routes as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->route_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-1">
                    <label for="type"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Type') </label>
                    <div class="col-sm-7">
                        <select class="form-control" name="type" id="type" tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            <option value="Pickup">@localize('Pick Up')</option>
                            <option value="Drop">@localize('Drop Off')</option>
                            <option value="PickDrop">@localize('Pick and Drop')</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-xl-4">
                <div class="form-group row mb-1">
                    <label for="request_type"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Request Type') </label>
                    <div class="col-sm-7">
                        <select class="form-control" name="request_type" id="request_type" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            <option value="0">@localize('Regular')</option>
                            <option value="1">@localize('Specific Day')</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-12">
                    <div class="form-group row mb-1">
                        <label for="date"
                            class="col-sm-5 col-form-label justify-content-start text-left">@localize('Requisition Date')
                        </label>
                        <div class="col-sm-7">
                            <input name="date" autocomplete="off" class="form-control w-100" type="date"
                                id="date">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-xl-4">
                <div class="form-group row mb-1">
                    <label for="status"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Status') </label>
                    <div class="col-sm-7">
                        <select class="form-control" name="status" id="status" tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            <option value="0">@localize('Pending')</option>
                            <option value="1">@localize('Release')</option>
                        </select>
                    </div>
                </div>
            </div>


        </x-filter-layout>

        <div>
            <x-data-table :dataTable="$dataTable" />
            <div id="page-axios-data" data-table-id="#pick-drop-table"></div>
        </div>
    </x-card>

    @push('js')
        <script src="{{ module_asset('VehicleManagement/js/create_edit.min.js') }}"></script>
    @endpush

</x-app-layout>
