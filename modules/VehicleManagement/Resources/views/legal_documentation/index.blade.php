<x-app-layout>
    <x-card>
        <x-slot name='actions'>
            <div class="accordion-header d-flex justify-content-end align-items-center" id="flush-headingOne">
                <a class="btn btn-success btn-sm" href="javascript:void(0);"
                    onclick="axiosModal('{{ route(\config('theme.rprefix') . '.create') }}')">
                    <i class="fa fa-plus"></i>
                    {{ localize('Add Legal Documentation') }}
                </a>

                <button type="button" class="btn btn-success btn-sm mx-2" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne"> <i
                        class="fas fa-filter"></i> @localize('Filter')</button>
            </div>
        </x-slot>
        <x-filter-layout>
            <div class="col-sm-12 col-xl-4">
                <div class="form-group row mb-1">
                    <label for="Document_type"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Document Type') </label>
                    </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="document_type_id" id="Document_type" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($document_types as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="vehicle"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Vehicle') </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="vehicle_id" id="vehicle" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($vehicles as $key => $value)
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
                                class="col-sm-5 col-form-label justify-content-start text-left">@localize('Expire Date From')
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
                                class="col-sm-5 col-form-label justify-content-start text-left">@localize('Expire Date To')
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
            <div id="page-axios-data" data-table-id="#legal-documentation-table"></div>
        </div>
    </x-card>

</x-app-layout>
