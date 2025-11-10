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
        <table class="table table-hover table-striped">
            <tr>
                <th>
                    <label for="name" class="">
                        @localize('Name')
                        <span class="text-danger">*</span>
                    </label>
                </th>
                <td>
                    <input type="text" class="form-control" name="name" id="name"
                        value="{{ isset($item) ? $item->name : old('name') }}" placeholder="@localize('Name')" required>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="is_active" class="">
                        @localize('Status')
                    </label>
                </th>
                <td>
                    <select name="is_active" id="is_active" class="form-control">
                        <option value="1" @if (isset($item) && $item->is_active == 1) selected @endif>
                            @localize('Active')
                        </option>
                        <option value="0" @if (isset($item) && $item->is_active == 0) selected @endif>
                            @localize('Inactive')
                        </option>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@localize('Close')</button>
        <button class="btn btn-success" type="submit">@localize('Save')</button>
    </div>
</form>
