<x-app-layout>

<style>
    .form-check-input:checked { background-color:#0f62fe!important; border-color:#0f62fe!important; }
    table thead th{ background:white!important; color:#4b5563!important; }
    .custom-dropdown-toggle::after { display:none!important; }
    .form-check-input[type="checkbox"] { width:2.3rem; height:1.2rem; }
</style>

<div class="main-content">

    <div class="card bg-transparent my-4">
        <div class="card-header" style="background:#fbfbfb;">
            <div class="row g-3">
                <div class="col-md-6 d-flex align-items-center">
                    <div class="card-title h5 custom-dark m-0">
                        Recovery Updates Master
                        <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">
                            {{ $data->count() }}
                        </span>
                    </div>
                </div>

                <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                    <div class="text-center d-flex gap-2">
                        <div class="m-2 bg-white p-2 px-3 border-gray">
                            <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                <i class="bi bi-download fs-17 me-1"></i> Export
                            </button>
                        </div>

                        <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()">
                            <i class="bi bi-filter fs-17"></i> Filter
                        </div>

                        <div class="m-2 btn btn-success d-flex align-items-center px-3"
                             style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#AddRecoveryUpdate">
                            <i class="bi bi-plus-lg me-2 fs-6"></i> Create
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table id="RecoveryUpdatesTable" class="table text-left" style="width:100%;">
            <thead class="bg-white rounded">
            <tr>
                <th scope="col" class="custom-dark">
                    <div class="form-check">
                        <input class="form-check-input" style="width:25px;height:25px;" type="checkbox" id="RUSelectAllBtn"
                               title="Note: To select all rows, first choose 'All' in the table page-size dropdown.">
                        <label class="form-check-label" for="RUSelectAllBtn"></label>
                    </div>
                </th>
                <th scope="col" class="custom-dark">Label Name</th>
                <th scope="col" class="custom-dark">Created At</th>
                <th scope="col" class="custom-dark">Status</th>
                <th scope="col" class="custom-dark">Active / Inactive</th>
                <th scope="col" class="custom-dark">Action</th>
            </tr>
            </thead>

            <tbody class="bg-white border border-white">
            @foreach($data as $val)
                @php
                    // expected columns: id, key, label_name, status, created_at
                    $status = (int) ($val->status ?? 0);
                    $colorClass = match ($status) { 1 => 'text-success', 0 => 'text-danger', default => 'text-secondary' };
                    $statusName = $status === 1 ? 'Active' : 'Inactive';
                @endphp
                <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input ru_checkbox" style="width:25px;height:25px;" type="checkbox" value="{{ $val->id }}">
                        </div>
                    </td>
                    <td>{{ $val->label_name ?? '' }}</td>
                    <td>{{ $val->created_at ? date('d M Y h:i:s A', strtotime($val->created_at)) : '' }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2 justify-content-start">
                            <i class="bi bi-circle-fill {{ $colorClass }}"></i>
                            <span class="text-capitalize">{{ $statusName }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="form-check form-switch d-flex justify-content-center align-items-center m-0 p-0">
                            <input class="form-check-input toggle-status" data-id="{{ $val->id }}" type="checkbox"
                                   role="switch" id="toggleSwitch{{ $loop->index }}" {{ $status === 1 ? 'checked' : '' }}>
                        </div>
                    </td>
                    <td class="text-start">
                        <a href="javascript:void(0);" class="text-success editRUbtn"
                           data-id="{{ $val->id }}"
                           data-key="{{ $val->key }}"
                           data-name="{{ $val->label_name }}"
                           data-status="{{ (int)$val->status }}"
                           style="font-size:1.2rem;">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>
    </div>
</div>

{{-- Offcanvas Filter --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightRU" aria-labelledby="offcanvasRightRULabel">
    <div class="offcanvas-header">
        <h5 class="custom-dark" id="offcanvasRightRULabel">Recovery Updates Master</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <div class="d-flex gap-2 mb-3">
            <button class="btn btn-outline-secondary w-50" onclick="clearRUFilter()">Clear All</button>
            <button class="btn btn-success w-50" onclick="applyRUFilter()">Apply</button>
        </div>

        <div class="card mb-3">
            <div class="card-header p-2"><h6 class="custom-dark m-0">Select Status</h6></div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="status_value" id="ru_status_all" value="all"
                           {{ request('status','all') == 'all' ? 'checked' : '' }}>
                    <label class="form-check-label" for="ru_status_all">All</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="status_value" id="ru_status_active" value="1"
                           {{ request('status') === '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="ru_status_active">Active</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="status_value" id="ru_status_inactive" value="0"
                           {{ request('status') === '0' ? 'checked' : '' }}>
                    <label class="form-check-label" for="ru_status_inactive">Inactive</label>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header p-2"><h6 class="custom-dark m-0">Date Between</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="RUFromDate">From Date</label>
                    <input type="date" name="from_date" id="RUFromDate" class="form-control" max="{{ date('Y-m-d') }}" value="{{ $from_date }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="RUToDate">To Date</label>
                    <input type="date" name="to_date" id="RUToDate" class="form-control" max="{{ date('Y-m-d') }}" value="{{ $to_date }}">
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mb-3">
            <button class="btn btn-outline-secondary w-50" onclick="clearRUFilter()">Clear All</button>
            <button class="btn btn-success w-50" onclick="applyRUFilter()">Apply</button>
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="AddRecoveryUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="AddRecoveryUpdate" aria-hidden="true">
    <form id="AddRUForm" action="javascript:void(0);" method="POST">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Recovery Updates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="col-12 mb-3">
                            <label class="mb-2 ms-1">Label Name <span class="text-danger">*</span></label>
                            <input class="form-control" name="label_name" placeholder="e.g. Recovery In Progress" required>
                        </div>

                        <div class="col-12">
                            <label class="mb-2 ms-1">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success submitBtn">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="EditRecoveryUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="EditRecoveryUpdate" aria-hidden="true">
    <form id="EditRUForm" action="javascript:void(0);" method="POST">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Recovery Updates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id" id="ru_edit_id">

                        <div class="col-12 mb-3">
                            <label class="mb-2 ms-1">Label Name <span class="text-danger">*</span></label>
                            <input class="form-control" name="label_name" id="ru_name" required>
                        </div>

                        <div class="col-12">
                            <label class="mb-2 ms-1">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="ru_edit_status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success updateBtn">Update</button>
                </div>
            </div>
        </div>
    </form>
</div>

@section('script_js')
<script>
    function applyRUFilter() {
        const selectedStatus = document.querySelector('input[name="status_value"]:checked');
        const status = selectedStatus ? selectedStatus.value : 'all';
        const from_date = document.getElementById('RUFromDate').value;
        const to_date   = document.getElementById('RUToDate').value;

        if ((from_date && !to_date) || (!from_date && to_date)) {
            toastr.error("From Date and To Date are both required");
            return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        url.searchParams.set('from_date', from_date);
        url.searchParams.set('to_date', to_date);
        window.location.href = url.toString();
    }

    function clearRUFilter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('status');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        window.location.href = url.toString();
    }

    // select all
    $(document).ready(function () {
        $('#RUSelectAllBtn').on('change', function () {
            $('.ru_checkbox').prop('checked', this.checked);
        });

        $('.ru_checkbox').on('change', function () {
            if (!this.checked) {
                $('#RUSelectAllBtn').prop('checked', false);
            } else if ($('.ru_checkbox:checked').length === $('.ru_checkbox').length) {
                $('#RUSelectAllBtn').prop('checked', true);
            }
        });
    });
</script>

<script>
    // DataTable (no column ordering like your Color Master)
    $(document).ready(function () {
        $('#RecoveryUpdatesTable').DataTable({
            columnDefs: [{ orderable: false, targets: '_all' }],
            lengthMenu: [[10,25,50,100,250,-1], [10,25,50,100,250,"All"]],
            responsive: false,
            scrollX: true,
        });
    });

    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightRU');
        bsOffcanvas.show();
    }
</script>

<script>
    // Create
    $("#AddRUForm").submit(function (e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        formData.append("_token","{{ csrf_token() }}");

        const redirect = "{{ route('admin.Green-Drive-Ev.master_management.recovery_updates_master.index') }}";
        const $btn = $(this).find('.submitBtn').prop('disabled',true).html(
            '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...'
        );

        $.ajax({
            url: "{{ route('admin.Green-Drive-Ev.master_management.recovery_updates_master.store') }}",
            type: "POST",
            data: formData,
            contentType:false,
            processData:false,
            success: function (resp) {
                $btn.prop('disabled',false).html('Submit');
                if (resp.success) {
                    toastr.success(resp.message || 'Created');
                    $("#AddRecoveryUpdate").modal('hide');
                    form.reset();
                    setTimeout(()=>window.location.href=redirect, 800);
                } else {
                    toastr.error(resp.message || 'Unexpected error');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled',false).html('Submit');
                if (xhr.status === 422) {
                    const errs = xhr.responseJSON.errors || {};
                    Object.values(errs).forEach(arr => toastr.error(arr[0]));
                } else {
                    toastr.error("Something went wrong. Please try again.");
                }
            }
        });
    });

    // Open Edit modal
    $(document).on('click', '.editRUbtn', function () {
        $('#ru_edit_id').val($(this).data('id'));
        $('#ru_key').val($(this).data('key'));
        $('#ru_name').val($(this).data('name'));
        $('#ru_edit_status').val(String($(this).data('status'))).trigger('change');
        $('#EditRecoveryUpdate').modal('show');
    });

    // Update
    $("#EditRUForm").submit(function (e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        formData.append("_token","{{ csrf_token() }}");

        const $btn = $(this).find('.updateBtn').prop('disabled',true).html(
            '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Submitting...'
        );

        $.ajax({
            url: "{{ route('admin.Green-Drive-Ev.master_management.recovery_updates_master.update') }}",
            type: "POST",
            data: formData,
            contentType:false,
            processData:false,
            success: function (resp) {
                $btn.prop('disabled',false).html('Update');
                if (resp.success) {
                    toastr.success(resp.message || 'Updated');
                    $('#EditRecoveryUpdate').modal('hide');
                    form.reset();
                    setTimeout(()=>window.location.reload(), 800);
                } else {
                    toastr.error(resp.message || 'Something went wrong.');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled',false).html('Update');
                if (xhr.status === 422) {
                    const errs = xhr.responseJSON.errors || {};
                    Object.values(errs).forEach(arr => toastr.error(arr[0]));
                } else {
                    toastr.error("Unexpected error. Try again.");
                }
            }
        });
    });

    // Toggle Status
    $(document).ready(function () {
        $('.toggle-status').change(function (e) {
            e.preventDefault();

            const checkbox = $(this);
            const id = checkbox.data('id');
            const intendedStatus = checkbox.is(':checked') ? 1 : 0;

            // revert until confirmed
            checkbox.prop('checked', !intendedStatus);

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to ${intendedStatus ? 'activate' : 'deactivate'} this item.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    checkbox.prop('checked', intendedStatus);

                    $.ajax({
                        url: "{{ route('admin.Green-Drive-Ev.master_management.recovery_updates_master.status_update') }}",
                        type: "POST",
                        data: { _token:"{{ csrf_token() }}", id, status:intendedStatus },
                        success: function (resp) {
                            if (resp.success) {
                                Swal.fire({ icon:'success', title:'Updated!', text:resp.message, timer:1200, showConfirmButton:false })
                                    .then(()=> location.reload());
                            } else {
                                Swal.fire({ icon:'error', title:'Failed!', text:resp.message || 'Update failed' });
                            }
                        },
                        error: function () {
                            Swal.fire({ icon:'error', title:'Oops!', text:'Server error occurred.' });
                        }
                    });
                } else {
                    checkbox.prop('checked', !intendedStatus);
                }
            });
        });
    });

    // Export selected/filtered
    document.getElementById('exportBtn').addEventListener('click', function () {
        const selected = [];
        document.querySelectorAll('.ru_checkbox:checked').forEach(cb => selected.push(cb.value));

        const params = new URLSearchParams();
        params.append('status', '{{ request()->status }}');
        params.append('from_date', '{{ $from_date }}');
        params.append('to_date', '{{ $to_date }}');
        if (selected.length > 0) params.append('selected_ids', JSON.stringify(selected));

        const url = `{{ route('admin.Green-Drive-Ev.master_management.recovery_updates_master.export') }}?${params.toString()}`;
        window.location.href = url;
    });
</script>
@endsection

</x-app-layout>
