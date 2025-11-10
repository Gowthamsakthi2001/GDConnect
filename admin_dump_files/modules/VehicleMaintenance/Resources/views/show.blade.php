<div class="card-header my-3 p-2 border-bottom">
    <h4>{{ config('theme.title') }}</h4>
</div>
<div class="modal-body px-5">

    <div class="row pb-5">
        <div class="col-md-6">
            <table class="table table-borderless table-hover">
                <tr>
                    <td class="fw-bold">@localize('Service title')</td>
                    <td>:</td>
                    <td> {{ $item->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Requisition type')</td>
                    <td>:</td>
                    <td> {{ ucfirst($item->type) ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Requisition for')</td>
                    <td>:</td>
                    <td> {{ $item->employee->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Vehicle')</td>
                    <td>:</td>
                    <td> {{ $item->vehicle->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Maintenance type')</td>
                    <td>:</td>
                    <td> {{ $item->maintenanceType->name ?? 'N/A' }}</td>
                </tr>

                <tr>
                    <td class="fw-bold">@localize('Charge bear by')</td>
                    <td>:</td>
                    <td> {{ $item->charge_bear_by ?? 'N/A' }}</td>
                </tr>

                <tr>
                    <td class="fw-bold">@localize('Charge')</td>
                    <td>:</td>
                    <td> {{ $item->charge ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6 text-end">
            <table class="table table-borderless table-hover">
                <tr>
                    <td class="fw-bold">@localize('Date')</td>
                    <td>:</td>
                    <td> {{ $item->date }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Priority')</td>
                    <td>:</td>
                    <td> {{ ucfirst($item->priority) ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Code')</td>
                    <td>:</td>
                    <td>{{ $item->code }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Status')</td>
                    <td>:</td>
                    <td>
                        @if ($item->status == 'pending')
                            <span class="text-capitalize badge bg-warning text-dark">{{ $item->status }}</span>
                        @elseif ($item->status == 'approved')
                            <span class="text-capitalize badge bg-success">{{ $item->status }}</span>
                        @elseif ($item->status == 'rejected')
                            <span class="text-capitalize badge bg-danger">{{ $item->status }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Remarks')</td>
                    <td>:</td>
                    <td> {{ $item->remarks ?? '---' }}</td>
                </tr>
            </table>
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
