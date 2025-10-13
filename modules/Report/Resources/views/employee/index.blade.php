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
                    <label for="emp_type"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Employee Type')
                    </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="employee_type" id="emp_types" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach (config('employee.payroll_types') as $key => $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="blood"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Blood Group') </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="blood_group" id="shbloodg" tabindex="-1"
                            aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach (config('employee.blood_groups') as $key => $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
            <div class="col-sm-12 col-xl-4">
                <div class="form-group row mb-1">
                    <label for="department"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Department') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single required" name="department" id="departmentsh"
                            tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($departments as $key => $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="designation"
                        class="col-sm-5 col-form-label justify-content-start text-left">@localize('Designation') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single required" name="designation" id="designationsh"
                            tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($positions as $key => $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
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
            <div id="page-axios-data" data-table-id="#employee-table"></div>
        </div>
    </x-card>

</x-app-layout>
