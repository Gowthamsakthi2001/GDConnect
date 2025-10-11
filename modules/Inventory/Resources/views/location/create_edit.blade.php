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
                    <label for="room" class="">
                        @localize('room')
                        <span class="text-danger">*</span>
                    </label>
                </th>
                <td>
                    <input type="number" class="form-control" name="room" id="room"
                        value="{{ isset($item) ? $item->room : old('room', 0) }}" min="0"
                        placeholder="@localize('room')" required>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="self" class="">
                        @localize('self')
                    </label>
                </th>
                <td>
                    <input type="number" class="form-control" name="self" id="self"
                        value="{{ isset($item) ? $item->self : old('self', 0) }}" min="0"
                        placeholder="@localize('self')">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="drawer" class="">
                        @localize('drawer')
                    </label>
                </th>
                <td>
                    <input type="number" class="form-control" name="drawer" id="drawer"
                        value="{{ isset($item) ? $item->drawer : old('drawer', 0) }}" min="0"
                        placeholder="@localize('drawer')">
                </td>
            </tr>
            {{-- capacity --}}
            <tr>
                <th>
                    <label for="capacity" class="">
                        @localize('Capacity')
                    </label>
                </th>
                <td>
                    <input type="number" class="form-control" name="capacity" id="capacity"
                        value="{{ isset($item) ? $item->capacity : old('capacity', 0) }}" min="0"
                        placeholder="@localize('Capacity')">
                </td>
            </tr>
            {{-- dimension --}}
            <tr>
                <th>
                    <label for="dimension" class="">
                        @localize('Dimension')
                    </label>
                </th>
                <td>
                    <input type="number" class="form-control" name="dimension" id="dimension"
                        value="{{ isset($item) ? $item->dimension : old('dimension', 0) }}" min="0"
                        placeholder="@localize('Dimension')">
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
