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
            <div class="col-md-12 col-lg-6">
                <div class="form-group row my-2">
                    <label for="driver_id" class="col-sm-5 col-form-label">@localize('Driver Name') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" required name="driver_id" id="driver_id"
                            tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')
                            </option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}"
                                    {{ isset($item) ? ($item->driver_id == $driver->id ? 'selected' : '') : '' }}>
                                    {{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="over_time_status" class="col-sm-5 col-form-label">@localize('Over Time Status') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control" required name="over_time_status" id="over_time_status"
                            tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')
                            </option>
                            <option value="1">@localize('Yes')</option>
                            <option value="0">@localize('No')</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="salary_status" class="col-sm-5 col-form-label">@localize('Salary Status') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="statusyes" name="salary_status" class="custom-control-input"
                                value="1" @checked(isset($item) ? $item->salary_status : false)>
                            <label class="custom-control-label" for="statusyes">@localize('Yes')</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="statusno" name="salary_status" class="custom-control-input"
                                value="0" @checked(isset($item) ? ($item->salary_status == 0 ? true : false) : true)>
                            <label class="custom-control-label" for="statusno">@localize('No')</label>
                        </div>
                    </div>
                </div>


                <div class="form-group row my-2">
                    <label for="overtime_payment" class="col-sm-5 col-form-label">@localize('Overtime Payment') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="ot_payment" required class="form-control" type="number" step="any"
                            placeholder="@localize('Overtime Payment')" id="overtime_payment"
                            value="{{ isset($item) ? $item->overtime_payment : old('overtime_payment') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="performance_bonus" class="col-sm-5 col-form-label">@localize('Performance Bonus') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="performance_bonus" required class="form-control" type="number" step="any"
                            placeholder="@localize('Performance Bonus')" id="performance_bonus"
                            value="{{ isset($item) ? $item->performance_bonus : old('performance_bonus') }}">
                    </div>
                </div>


            </div>
            <div class="col-md-12 col-lg-6">

                <div class="form-group row my-2">
                    <label for="penalty_amount" class="col-sm-5 col-form-label">@localize('Penalty Amount') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <input name="penalty_amount" required class="form-control" type="number" step="any"
                            placeholder="@localize('Penalty Amount')" id="penalty_amount"
                            value="{{ isset($item) ? $item->penalty_amount : old('penalty_amount') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="penalty_reason" class="col-sm-5 col-form-label">@localize('Penalty Reason') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <textarea name="penalty_reason" required id="penalty_reason" cols="30" rows="3" class="form-control">{{ isset($item) ? $item->penalty_reason : old('penalty_reason') }}</textarea>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="penalty_date" class="col-sm-5 col-form-label">@localize('Penalty Date') </label>
                    <div class="col-sm-7">
                        <input name="penalty_date" autocomplete="off" class="form-control" type="date"
                            placeholder="@localize('Penalty Date')" id="penalty_date"
                            value="{{ isset($item) ? $item->penalty_date : old('penalty_date') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@localize('Close')</button>

        <button class="btn btn-success" type="submit">@localize('Save')</button>
    </div>
</form>
