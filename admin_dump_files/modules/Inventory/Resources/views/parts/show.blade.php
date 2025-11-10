<div class="card-header my-3 p-2 border-bottom">
    <h4>{{ config('theme.title') }}</h4>
</div>
<div class="modal-body">
    <table class="table table-hover table-striped">
        <tr>
            <th>
                <label for="name" class="">
                    @localize('Name')
                </label>
            </th>
            <td>
                {{ $item->name }}
            </td>
        </tr>
        <tr>
            <th>
                <label for="category_id" class="">
                    @localize('Category Name')
                </label>
            </th>
            <td>
                {{ $item->category->name ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <th>
                <label for="location_id" class="">
                    @localize('location')
                </label>
            </th>
            <td>
                {{ $item->location->name ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <th>
                <label for="qty" class="">
                    @localize('quantity')
                </label>
            </th>
            <td>
                {{ $item->qty }}
            </td>
        </tr>
        <tr>
            <th>
                <label for="description" class="">
                    @localize('description')
                </label>
            </th>
            <td>
                {{ $item->description ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <th>
                <label for="remarks" class="">
                    @localize('remarks')
                </label>
            </th>
            <td>
                {{ $item->remarks ?? 'N/A' }}
            </td>
        </tr>

        <tr>
            <th>
                <label for="is_active" class="">
                    @localize('Status')
                </label>
            </th>
            <td>
                {!! $item->is_active
                    ? '<span class="badge bg-success">' . localize('Active') . '</span>'
                    : '<span class="badge bg-danger">' . localize('Inactive') . '</span>' !!}
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@localize('Close')</button>
</div>
