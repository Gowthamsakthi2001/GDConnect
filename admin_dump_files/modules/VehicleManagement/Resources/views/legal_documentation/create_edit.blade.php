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
                    <label for="document_type_id" class="col-sm-5 col-form-label">@localize('Document Type') </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="document_type_id" id="document_type_id"
                            tabindex="-1" aria-hidden="true">
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($document_types as $document_type)
                                <option value="{{ $document_type->id }}"
                                    {{ isset($item) ? ($item->document_type_id == $document_type->id ? 'selected' : '') : '' }}>
                                    {{ $document_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-1">
                    <label for="vehicle" class="col-sm-5 col-form-label">@localize('Vehicle') <i class="text-danger">*</i>
                    </label>
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
                    <label for="issue_date" class="col-sm-5 col-form-label">@localize('Last Issue Date') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="issue_date" required class="form-control " type="date"
                            placeholder="@localize('Last Issue Date')" id="issue_date"
                            value="{{ isset($item) ? $item->issue_date : old('issue_date') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="expiry_date" class="col-sm-5 col-form-label">@localize('Expire Date') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="expiry_date" class="form-control " type="date" placeholder="@localize('Expire Date')"
                            required id="expiry_date"
                            value="{{ isset($item) ? $item->expiry_date : old('expiry_date') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="charge_paid" class="col-sm-5 col-form-label">@localize('Charge Paid') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <input name="charge_paid" class="form-control" type="number" step="any"
                            placeholder="@localize('Charge Paid')" id="charge_paid"
                            value="{{ isset($item) ? $item->charge_paid : old('charge_paid') }}" required>
                    </div>
                </div>


            </div>

            <div class="col-md-12 col-lg-6">

                <div class="form-group row mb-1">
                    <label for="vendor" class="col-sm-5 col-form-label">@localize('Vendor') <i
                            class="text-danger">*</i> </label>
                    <div class="col-sm-7">
                        <select class="form-control basic-single" name="vendor_id" id="vendor" tabindex="-1"
                            aria-hidden="true" required>
                            <option value="">@localize('Please Select One')</option>
                            @foreach ($vendors as $key => $value)
                                <option value="{{ $value->id }}"
                                    {{ isset($item) ? ($item->vendor_id == $value->id ? 'selected' : '') : '' }}>
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="commission" class="col-sm-5 col-form-label">@localize('Commission')
                        <i class="text-danger">*</i></label>
                    </label>

                    <div class="col-sm-7">
                        <input name="commission" required class="form-control" type="number" step="any"
                            placeholder="@localize('Commission')" id="commission"
                            value="{{ isset($item) ? $item->commission : old('commission') }}" required>
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="email" class="col-sm-5 col-form-label">@localize('Email')
                    </label>

                    <div class="col-sm-7">
                        <input name="email" class="form-control" type="email" placeholder="@localize('Email')"
                            id="email" value="{{ isset($item) ? $item->email : old('email') }}">
                    </div>
                </div>

                <div class="form-group row my-2">
                    <label for="legal_document_file" class="col-sm-5 col-form-label">@localize('Document Attachment') </label>
                    <div class="col-sm-7">
                        <input type="file" accept="image/*" name="legal_document_file" id="legal_document_file"
                            onchange="get_img_url(this, '#document_image');">

                        <img id="document_image" src="{{ isset($item) ? $item->document_file_url : '' }}"
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
