<div class="card-header my-3 p-2 border-bottom">
    <h4>{{ config('theme.title') }}</h4>
</div>
<div class="modal-body px-5">
    <div class="row pb-5">
        <div class="col-md-6">
            <strong>@localize('')</strong>
            {{ $item->vehicle->name ?? 'N/A' }}
        </div>
        <div class="col-md-6 text-end">
            <strong>Date:</strong>
            {{ $item->date }}
        </div>
        <div class="col-md-6">
            <strong>Invoice:</strong>
            <span class="">{{ $item->code }}</span>
        </div>
        <div class="col-md-6  text-end">
            <strong class="mt-1">Status:</strong>
            @if ($item->status == 'pending')
                <span class="text-capitalize badge bg-warning text-dark">{{ $item->status }}</span>
            @elseif ($item->status == 'approved')
                <span class="text-capitalize badge bg-success">{{ $item->status }}</span>
            @elseif ($item->status == 'rejected')
                <span class="text-capitalize badge bg-danger">{{ $item->status }}</span>
            @endif
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            @localize('Category')
                        </th>
                        <th>
                            @localize('Item')
                            <span class="text-danger">*</span>
                        </th>
                        <th class="text-end">
                            @localize('Quantity')
                            <span class="text-danger">*</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item->details as $d)
                        <tr>
                            <td>
                                {{ $d->category->name }}
                            </td>
                            <td>
                                {{ $d->parts->name }}
                            </td>
                            <td class="text-end">
                                {{ $d->qty }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@localize('Close')</button>
    </div>
