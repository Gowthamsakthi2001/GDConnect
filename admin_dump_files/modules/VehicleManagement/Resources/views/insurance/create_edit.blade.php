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
                    <label for="company_id" class="col-sm-5 col-form-label">@localize('Company Name') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="company_id" id="company_id" tabindex="-1"
                            aria-hidden="true" required>
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}"
                                    {{ isset($item) ? ($item->company_id == $company->id ? 'selected' : '') : '' }}>
                                    {{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="policy_no" class="col-sm-5 col-form-label">@localize('Policy Number') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="policy_number" class="form-control" type="text" placeholder="@localize('Policy Number')"
                            id="policy_no" value="{{ isset($item) ? $item->policy_number : old('policy_number') }}"
                            required>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="start_date" class="col-sm-5 col-form-label">@localize('Start Date') </label>
                    <div class="col-sm-7">
                        <input name="start_date" class="form-control " type="date" placeholder="@localize('Start Date')"
                            id="start_date" value="{{ isset($item) ? $item->start_date : old('start_date') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="recurring_period" class="col-sm-5 col-form-label">@localize('Recurring Period') <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="recurring_period_id" id="recurring_period"
                            tabindex="-1" aria-hidden="true" required>
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($recurring_periods as $recurring_period)
                                <option value="{{ $recurring_period->id }}"
                                    {{ isset($item) ? ($item->recurring_period_id == $recurring_period->id ? 'selected' : '') : '' }}>
                                    {{ $recurring_period->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="add_reminder" class="col-sm-5 col-form-label">@localize('Add Reminder') </label>
                    <div class="col-sm-7">
                        <select name="add_reminder" id="add_reminder" class="form-control">
                            <option value="1" @if (isset($item) && $item->add_reminder == 1) selected @endif>
                                @localize('Yes')
                            </option>
                            <option value="0" @if (isset($item) && $item->add_reminder == 0) selected @endif>
                                @localize('No')
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="status" class="col-sm-5 col-form-label">@localize('Status') </label>
                    <div class="col-sm-7">
                        <select name="status" id="status" class="form-control">
                            <option value="1" @if (isset($item) && $item->status == 1) selected @endif>
                                @localize('Active')
                            </option>
                            <option value="0" @if (isset($item) && $item->status == 0) selected @endif>
                                @localize('Inactive')
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="remarks" class="col-sm-5 col-form-label">@localize('Remarks') </label>
                    <div class="col-sm-7">
                        <textarea class="form-control" name="remarks" id="remarks" cols="30" rows="3">{{ isset($item) ? $item->remarks : old('remarks') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-6">

                <div class="form-group row mb-1">
                    <label for="vehicle" class="col-sm-5 col-form-label">@localize('Vehicle') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="vehicle_id" id="vehicle" tabindex="-1"
                            aria-hidden="true" required>
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($vehicles as $key => $value)
                                <option value="{{ $value->id }}"
                                    {{ isset($item) ? ($item->vehicle_id == $value->id ? 'selected' : '') : '' }}>
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="charge_payable" class="col-sm-5 col-form-label">@localize('Charge Payable') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="charge_payable" class="form-control" type="number"
                            step="any
                            placeholder="@localize('Charge Payable')" id="charge_payable"
                            value="{{ isset($item) ? $item->charge_payable : old('charge_payable') }}" required>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="end_date" class="col-sm-5 col-form-label">@localize('End Date') </label>
                    <div class="col-sm-7">
                        <input name="end_date" class="form-control " type="date" placeholder="@localize('End Date')"
                            id="end_date" value="{{ isset($item) ? $item->end_date : old('end_date') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="recurring_date" class="col-sm-5 col-form-label">@localize('Recurring Date') </label>
                    <div class="col-sm-7">
                        <input name="recurring_date" class="form-control " type="date"
                            placeholder="@localize('Recurring Date')" id="recurring_date"
                            value="{{ isset($item) ? $item->recurring_date : old('recurring_date') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="deductible" class="col-sm-5 col-form-label">@localize('Deductible')
                        <i class="text-danger">*</i></label>
                    </label>

                    <div class="col-sm-7">
                        <input name="deductible" required class="form-control" type="number" step="any"
                            placeholder="@localize('Deductible')" id="deductible"
                            value="{{ isset($item) ? $item->deductible : old('deductible') }}" required>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="policy_document" class="col-sm-5 col-form-label">@localize('Policy Document')

                        @if (!isset($item))
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <div class="col-sm-7">
                        <input type="file" accept="image/*" name="policy_document" id="policy_document"
                            @if (!isset($item)) required @endif
                            onchange="get_img_url(this, '#document_image');">

                        <img id="document_image" src="{{ isset($item) ? $item->policy_document_url : '' }}"
                            width="120px" class="mt-1">
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
