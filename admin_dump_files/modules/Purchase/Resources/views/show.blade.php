<div class="card-header my-3 p-2 border-bottom">
    <h4>{{ config('theme.title') }}</h4>
</div>
<div class="modal-body px-5">
    <div class="row pb-5">
        <div class="col-md-6">
            <strong>Vendor:</strong>
            {{ $item->vendor->name ?? 'N/A' }}
        </div>
        <div class="col-md-6 text-end">
            <strong>Date:</strong>
            {{ $item->date }}
        </div>
        <div class="col-md-6">
            <strong>Invoice:</strong>
            <span class="">{{ $item->code }}</span>
            <br>
            <strong class="mt-1">Status:</strong>
            @if ($item->status == 'pending')
                <span class="text-capitalize badge bg-warning text-dark">{{ $item->status }}</span>
            @elseif ($item->status == 'approved')
                <span class="text-capitalize badge bg-success">{{ $item->status }}</span>
            @elseif ($item->status == 'rejected')
                <span class="text-capitalize badge bg-danger">{{ $item->status }}</span>
            @endif
        </div>
        <div class="col-md-6  text-end">
            @if ($item->req_img_path)
                <a class="text-muted fw-bold" href="{{ storage_path($item->req_img_path) }}" target="_blank">
                    @localize('Work Order')
                </a>
            @endif
            @if ($item->req_img_path && $item->order_path)
                |
            @endif
            @if ($item->order_path)
                <a class="text-muted fw-bold" href="{{ storage_path($item->order_path) }}" target="_blank">
                    @localize('Manual Requisition')
                </a>
            @endif

        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th width="27%">
                            @localize('Category')
                        </th>
                        <th width="27%">
                            @localize('Item')
                            <span class="text-danger">*</span>
                        </th>
                        <th width="15%" class="text-end">
                            @localize('Quantity')
                            <span class="text-danger">*</span>
                        </th>
                        <th width="15%" class="text-end">
                            @localize('Unit Price')
                            <span class="text-danger">*</span>
                        </th>
                        <th width="15%" class="text-end">
                            @localize('Total Price')
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
                            <td class="text-end">
                                {{ $d->price }}
                            </td>
                            <td class="text-end">
                                {{ $d->total }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end">
                            <strong>
                                @localize('Total') :
                            </strong>
                        </td>
                        <td class="text-end">
                            {{ $item->total }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@localize('Close')</button>
    </div>
