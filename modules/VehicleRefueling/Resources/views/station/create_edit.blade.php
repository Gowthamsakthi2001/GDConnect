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
            <div class="col-md-6 my-1">
                <label for="vendor_id" class="fw-bold">
                    @localize('Vendor Name')
                    <span class="text-danger">*</span>
                </label>
                <select name="vendor_id" id="vendor_id" class="form-control select2-ajax"
                    data-ajax-url="{{ route(config('theme.rprefix') . '.vendor-list') }}"
                    data-placeholder="@localize('Select Vendor')" required>
                    @if (isset($item) && !empty($item->vendor_id))
                        <option value="{{ $item->vendor_id }}" selected>{{ $item->vendor->name }}</option>
                    @endif
                </select>
                <label class="error" for="vendor_id"></label>
            </div>
            <div class="col-md-6 my-1">
                <label for="name" class="fw-bold">
                    @localize('Station Name')
                    <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" name="name" id="name"
                    value="{{ isset($item) ? $item->name : old('name') }}" placeholder="@localize('name')" required>
            </div>
            <div class="col-md-6 my-1">
                <label for="contact_person" class="fw-bold">
                    @localize('contact_person')
                </label>
                <input type="text" class="form-control" name="contact_person" id="contact_person"
                    value="{{ isset($item) ? $item->contact_person : old('contact_person') }}"
                    placeholder="@localize('contact_person')">
            </div>
            <div class="col-md-6 my-1">
                <label for="contact_number" class="fw-bold">
                    @localize('contact_number')
                </label>
                <input type="text" class="form-control" name="contact_number" id="contact_number"
                    value="{{ isset($item) ? $item->contact_number : old('contact_number') }}"
                    placeholder="@localize('contact_number')">
            </div>
            <div class="col-md-6 my-1">
                <label for="address" class="fw-bold">
                    @localize('address')
                </label>
                <textarea class="form-control" rows="1" name="address" id="address" placeholder="@localize('address')">{{ isset($item) ? $item->address : old('address') }}</textarea>
            </div>
            <div class="col-md-6 my-1">
                <label for="is_active" class="fw-bold">
                    @localize('Status')
                    <span class="text-danger">*</span>
                </label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" @if (isset($item) && $item->is_active == 1) selected @endif>
                        @localize('Active')
                    </option>
                    <option value="0" @if (isset($item) && $item->is_active == 0) selected @endif>
                        @localize('Inactive')
                    </option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@localize('Close')</button>
        <button class="btn btn-success" type="submit">@localize('Save')</button>
    </div>
</form>
