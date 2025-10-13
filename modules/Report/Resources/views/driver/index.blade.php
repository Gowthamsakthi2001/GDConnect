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
            <div class="col-sm-12 col-xl-4">
                <div class="form-group row mb-1">
                    <label for="driver"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Driver')
                    </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="driver_id" id="driver" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($drivers as $key => $value)
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
                <div class="form-group row mb-1">
                    <label for="mobile"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Mobile Number')
                    </label>
                    <div class="col-sm-7">
                        <input name="mobile" autocomplete="off" class="form-control w-100" type="text"
                            placeholder="@localize('Mobile Number')" id="mobile">
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="leave_status"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Leave Status')</label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single required" name="leave_status" id="leave_status"
                            tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            <option value="0">@localize('Yes')</option>
                            <option value="1">@localize('No')</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-xl-4">
                <div class="row">
                    <div class="col-sm-12 col-xl-12">
                        <div class="form-group row mb-1">
                            <label for="join_datefrsh"
                                class="col-sm-5 col-form-label justify-content-start text-left">@localize('Joining Date From')
                            </label>
                            <div class="col-sm-7">
                                <input name="join_date_from" autocomplete="off" class="form-control  w-100"
                                    type="date" placeholder="@localize('Joining Date From')" id="join_datefrsh">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-12">
                        <div class="form-group row mb-1">
                            <label for="joining_d_to"
                                class="col-sm-5 col-form-label justify-content-start text-left">@localize('Joining Date To')
                            </label>
                            <div class="col-sm-7">
                                <input name="join_date_to" autocomplete="off" class="form-control w-100" type="date"
                                    placeholder="@localize('Joining Date To')" id="joining_d_to">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-filter-layout>

        <div>
            <x-data-table :dataTable="$dataTable" />
            <div id="page-axios-data" data-table-id="#driver-table"></div>
        </div>
    </x-card>

</x-app-layout>
