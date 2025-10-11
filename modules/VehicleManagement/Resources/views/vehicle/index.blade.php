<x-app-layout>
    <x-card>
        <x-slot name='actions'>
            <div class="accordion-header d-flex justify-content-end align-items-center" id="flush-headingOne">
                <a class="btn btn-success btn-sm" href="javascript:void(0);"
                    onclick="axiosModal('{{ route(\config('theme.rprefix') . '.create') }}')">
                    <i class="fa fa-plus"></i>&nbsp;
                    {{ localize('Add Vehicle') }}
                </a>

                <button type="button" class="btn btn-success btn-sm mx-2" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne"> <i
                        class="fas fa-filter"></i> @localize('Filter')</button>
            </div>
        </x-slot>
        <x-filter-layout>
            <div class="col-sm-12 col-xl-4">
                <div class="form-group row mb-1">
                    <label for="department"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Department')
                    </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="department_id" id="department" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($departments as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="vehicle_type"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Vehicle Type') </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="vehicle_type_id" id="vehicle_type"
                            tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($vehicle_types as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-xl-4">
                <div class="form-group row mb-1">
                    <label for="ownership"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Ownership')
                    </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="ownership_id" id="ownership" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($ownerships as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="vendor"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Vendor') </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="vendor_id" id="vendor" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($vendors as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-xl-4">
                <div class="row">
                    <div class="col-sm-12 col-xl-12">
                        <div class="form-group row mb-1">
                            <label for="date_from"
                                class="col-sm-5 col-form-label justify-content-start text-left">@localize('Registration From')
                            </label>
                            <div class="col-sm-7">
                                <input name="date_from" autocomplete="off" class="form-control  w-100" type="date"
                                    placeholder="@localize('From')" id="date_from">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-12">
                        <div class="form-group row mb-1">
                            <label for="d_to"
                                class="col-sm-5 col-form-label justify-content-start text-left">@localize('Registration To')
                            </label>
                            <div class="col-sm-7">
                                <input name="date_to" autocomplete="off" class="form-control w-100" type="date"
                                    placeholder="@localize('To')" id="d_to">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </x-filter-layout>

        <div>
            <x-data-table :dataTable="$dataTable" />
            <div id="page-axios-data" data-table-id="#vehicle-table"></div>
        </div>
    </x-card>

</x-app-layout>
