<div class="card-header my-3 p-2 border-bottom">
    <h4>{{ config('theme.title') }}</h4>
</div>
<div class="modal-body px-5">

    <div class="row pb-5">
        <div class="col-md-6">
            <table class="table table-borderless table-hover">
                <tr>
                    <td class="fw-bold">@localize('Type')</td>
                    <td>:</td>
                    <td> {{ ucfirst($item->type) ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('By Whom')</td>
                    <td>:</td>
                    <td> {{ $item->employee->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('vendor')</td>
                    <td>:</td>
                    <td> {{ $item->vendor->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Vehicle')</td>
                    <td>:</td>
                    <td> {{ $item->vehicle->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Trip Type')</td>
                    <td>:</td>
                    <td> {{ $item->tripType->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('Trip No')</td>
                    <td>:</td>
                    <td> {{ $item->trip_number ?? 'N/A' }}</td>
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
                    <td class="fw-bold">@localize('odometer_millage')</td>
                    <td>:</td>
                    <td> {{ $item->odometer_millage ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">@localize('vehicle_rent')</td>
                    <td>:</td>
                    <td>{{ $item->vehicle_rent }}</td>
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
                        <th width="30%">
                            @localize('Expense type')
                        </th>
                        <th width="20%" class="text-end">
                            @localize('Quantity')
                            <span class="text-danger">*</span>
                        </th>
                        <th width="20%" class="text-end">
                            @localize('Unit Price')
                            <span class="text-danger">*</span>
                        </th>
                        <th width="20%" class="text-end">
                            @localize('Total Price')
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item->details as $d)
                        <tr>
                            <td>
                                {{ $d->type->name }}
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
                        <td colspan="3" class="text-end">
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
