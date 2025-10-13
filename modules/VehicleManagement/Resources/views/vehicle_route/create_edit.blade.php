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
                        @localize('Route Name')
                        <span class="text-danger">*</span>
                    </label>
                </th>
                <td>
                    <input type="text" class="form-control" name="route_name" id="name"
                        value="{{ isset($item) ? $item->route_name : old('route_name') }}" placeholder="@localize('Route Name')" required>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="starting_point" class="">
                        @localize('Starting Point')
                        <span class="text-danger">*</span>
                    </label>
                </th>
                <td>
                    <input type="text" class="form-control" name="starting_point" id="starting_point" value="{{ isset($item) ? $item->starting_point : old('starting_point') }}" placeholder="@localize('Starting Point')" required>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="destination_point" class="">
                        @localize('Destination Point')
                        <span class="text-danger">*</span>
                    </label>
                </th>
                <td>
                    <input type="text" class="form-control" name="destination_point" id="destination_point" required value="{{ isset($item) ? $item->destination_point : old('destination_point') }}" placeholder="@localize('Destination Point')">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="description" class="">
                        @localize('description')
                        <span class="text-danger">*</span>
                    </label>
                </th>
                <td>
                    <textarea class="form-control" required name="description" id="description" placeholder="@localize('description')">{{ isset($item) ? $item->description : old('description') }}</textarea>
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


            <tr>
                <th>
                    <label for="create_pick_drop_point" class="">
                        @localize('Create Pick and Drop Point')
                    </label>
                </th>
                <td>
                    <select name="create_pick_drop_point" id="create_pick_drop_point" class="form-control">
                        <option value="1" @if (isset($item) && $item->create_pick_drop_point == 1) selected @endif>
                            @localize('Yes')
                        </option>
                        <option value="0" @if (isset($item) && $item->create_pick_drop_point == 0) selected @endif>
                            @localize('No')
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
