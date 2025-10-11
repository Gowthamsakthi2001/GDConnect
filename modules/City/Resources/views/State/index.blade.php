<x-app-layout>
    <style>
        .form-check-input:checked {
            background-color: #0f62fe !important;
            border-color: #0f62fe !important;
        }
        table thead th{
            background: white !important;
            color: #4b5563 !important;
        }
        .custom-dropdown-toggle::after {
            display: none !important;
        }
        .form-check-input[type="checkbox"] {
            width: 2.3rem;
            height: 1.2rem;
        }
        .dropdown-menu {
            max-width: 250px;
            word-wrap: break-word;
        }
        body {
            overflow-x: hidden !important;
        }
    </style>

    <div class="main-content">
        <div class="card bg-transparent my-4">
            <div class="card-header" style="background:#fbfbfb;">
                <div class="row g-3">
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="card-title h5 custom-dark m-0">State <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{ $states->count() }}</span></div>
                    </div>

                    <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                        <div class="text-center d-flex gap-2">
                            <div class="m-2 bg-white p-2 px-3 border-gray" onclick="exportStates()"><i class="bi bi-download fs-17 me-1"></i> Export</div>
                            <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                            <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                onclick="AddorEditVTModal('0',this)"
                                style="cursor: pointer;">
                                <i class="bi bi-plus-lg me-2 fs-6"></i> Create
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless custom-table text-center" style="width: 100%;">
                <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                    <tr>
                        <th scope="col" class="custom-dark text-center">#</th>
                        <th scope="col" class="custom-dark text-center">Name</th>
                        <th scope="col" class="custom-dark text-center">Created At</th>
                        <th scope="col" class="custom-dark text-center">Status</th>
                        <th scope="col" class="custom-dark text-center">Active/In Active</th>
                        <th scope="col" class="custom-dark text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($states))
                        @foreach($states as $val)
                            <?php
                                $status = $val->status;
                                if ($status == 1) {
                                    $colorClass = 'text-success';
                                } elseif ($status == 0) {
                                    $colorClass = 'text-danger';
                                } else {
                                    $colorClass = 'text-secondary';
                                }
                                
                                $statusName = $status == 1 ? 'Active' : 'Inactive';
                            ?>
                    
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $val->state_name ?? '' }}</td>
                                <td>{{ date('d M Y h:i:s A', strtotime($val->created_at)) }}</td>
                    
                                <td>
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        <i class="bi bi-circle-fill {{ $colorClass }}"></i>
                                        <span class="text-capitalize">{{ $statusName }}</span>
                                    </div>
                                </td>
                    
                                <td>
                                    <div class="form-check form-switch d-flex justify-content-center align-items-center m-0 p-0">
                                        <input class="form-check-input toggle-status"
                                               data-id="{{ $val->id }}"
                                               type="checkbox"
                                               role="switch"
                                               id="toggleSwitch{{ $loop->index }}"
                                               {{ $status == 1 ? 'checked' : '' }}>
                                    </div>
                                </td>
                    
                                <td>
                                    <div class="dropdown">
                                        <button type="button"
                                                class="btn btn-sm dropdown-toggle custom-dropdown-toggle"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a href="javascript:void(0);"
                                                   data-name="{{ $val->state_name }}"
                                                   data-code="{{ $val->state_code }}"
                                                   data-status="{{ $val->status }}"
                                                   onclick="AddorEditVTModal('{{ $val->id }}',this)"
                                                   class="dropdown-item d-flex align-items-center">
                                                   <i class="bi bi-pencil-square me-2"></i> Edit
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- State Create/Edit Modal -->
        <div class="modal fade" id="stateModal" tabindex="-1" aria-labelledby="stateModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="stateModalLabel">Create State</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form id="stateForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="state_id" value="">
        
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="state_name" class="form-label">State Name</label>
                    <input type="text" class="form-control" name="state_name" id="state_name" required>
                  </div>
        
                  <div class="mb-3">
                    <label for="state_code" class="form-label">State Code</label>
                    <input type="text" class="form-control" name="state_code" id="state_code" required>
                  </div>
        
                  <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                      <option value="1">Active</option>
                      <option value="0">Inactive</option>
                    </select>
                  </div>
                </div>
        
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-success">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Filter Offcanvas Component -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
            <div class="offcanvas-header">
                <h5 class="custom-dark" id="offcanvasRightHR01Label">State Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearLocationFilter()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyLocationFilter()">Apply</button>
                </div>
             
                <div class="card mb-3">
                    <div class="card-header p-2">
                        <div><h6 class="custom-dark">Select Status</h6></div>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" {{isset($ch_status) && $ch_status == 'all' ? 'checked': ''}} type="radio" name="status" id="status" value="all" >
                            <label class="form-check-label" for="status">All</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="status" id="status1" value="1" {{isset($ch_status) && $ch_status == 1 ? 'checked': ''}}>
                            <label class="form-check-label" for="status1">Active</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="status" id="status2" value="0" {{isset($ch_status) && $ch_status == 0 ? 'checked': ''}}>
                            <label class="form-check-label" for="status2">Inactive</label>
                        </div>
                    </div>
                </div>
               
                <div class="card mb-3">
                    <div class="card-header p-2">
                        <div><h6 class="custom-dark">Date Between</h6></div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="FromDate">From Date</label>
                            <input type="date" name="from_date" id="FromDate" class="form-control" value="{{$from_date ?? ''}}" max="{{date('Y-m-d')}}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="ToDate">To Date</label>
                            <input type="date" name="to_date" id="ToDate" class="form-control" value="{{$to_date ?? ''}}" max="{{date('Y-m-d')}}">
                        </div>
                    </div>
                </div>
             
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearLocationFilter()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyLocationFilter()">Apply</button>
                </div>
            </div>
        </div>
    </div>
    
    
    @section('script_js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function AddorEditVTModal(id, el) {
        let form = document.getElementById("stateForm"); 
    
        if (id == 0) {
            // Create Mode
            form.reset();
            form.action = "{{ route('admin.Green-Drive-Ev.State.store') }}";
            document.getElementById("formMethod").value = "POST";
            document.getElementById("stateModalLabel").innerText = "Create State";
            document.getElementById("state_id").value = '';
        } else {
            // Edit Mode
            form.action = "{{ route('admin.Green-Drive-Ev.State.update', '') }}/" + id;
            document.getElementById("formMethod").value = "PUT";
            document.getElementById("stateModalLabel").innerText = "Edit State";
            document.getElementById("state_id").value = id;
    
            // Populate old data from table row
            let name = el.getAttribute("data-name");
            let code = el.getAttribute("data-code");
            let status = el.getAttribute("data-status");
    
            document.getElementById("state_name").value = name;
            document.getElementById("state_code").value = code;
            document.getElementById("status").value = status;
        }
    
        // finally open modal
        let modal = new bootstrap.Modal(document.getElementById("stateModal"));
        modal.show();
    }

    // Toggle status functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to all toggle switches
        document.querySelectorAll('.toggle-status').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                const stateId = this.getAttribute('data-id');
                const isActive = this.checked ? 1 : 0;
                const originalState = !this.checked;
                
                const statusUrl = "{{ route('admin.Green-Drive-Ev.State.status', ['id' => 'ID_PLACEHOLDER', 'status' => 'STATUS_PLACEHOLDER']) }}"
                    .replace('ID_PLACEHOLDER', stateId)
                    .replace('STATUS_PLACEHOLDER', isActive);
                
                fetch(statusUrl, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Status updated successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        const statusCell = this.closest('tr').querySelector('.d-flex.align-items-center.gap-2');
                        const statusIcon = statusCell.querySelector('i');
                        const statusText = statusCell.querySelector('span');
                        
                        if (isActive) {
                            statusIcon.className = 'bi bi-circle-fill text-success';
                            statusText.textContent = 'Active';
                        } else {
                            statusIcon.className = 'bi bi-circle-fill text-danger';
                            statusText.textContent = 'Inactive';
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Failed to update status'
                        });
                        this.checked = originalState;
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong: ' + error.message
                    });
                    this.checked = originalState;
                });
            });
        });

        // Show success message from server if exists
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    });
    
    // Filter functions
    function applyLocationFilter() {
        const selectedStatus = document.querySelector('input[name="status"]:checked');
        const status = selectedStatus ? selectedStatus.value : 'all';
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
        
        if(from_date !== "" || to_date !== ""){
            if(to_date === "" || from_date === ""){
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'From Date and To Date are both required'
                });
                return;
            }
        }

        // Build URL with query parameters
        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        if (from_date) url.searchParams.set('from_date', from_date);
        if (to_date) url.searchParams.set('to_date', to_date);
    
        window.location.href = url.toString();
    }

    function clearLocationFilter() {
        // Remove all filter parameters and reload
        const url = new URL(window.location.href);
        url.searchParams.delete('status');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        window.location.href = url.toString();
    }

    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
    }
    
    
    //Export code 
    //  function exportStates() 
    //  {
    //     // Get the current URL parameters
    //     const urlParams = new URLSearchParams(window.location.search);
        
    //     // Check if any filters are currently applied
    //     const hasStatusFilter = urlParams.has('status') && urlParams.get('status') !== 'all';
    //     const hasDateFilter = urlParams.has('from_date') || urlParams.has('to_date');
        
    //     // If no filters are applied, export all data without any filters
    //     if (!hasStatusFilter && !hasDateFilter) {
    //         window.location.href = "{{ route('admin.Green-Drive-Ev.State.export') }}";
    //         return;
    //     }
        
    //     // Otherwise, use the current filters
    //     let exportUrl = "{{ route('admin.Green-Drive-Ev.State.export') }}";
    //     let hasParams = false;
        
    //     // Add status filter if present and not 'all'
    //     const status = urlParams.get('status');
    //     if (status && status !== 'all') {
    //         exportUrl += `?status=${status}`;
    //         hasParams = true;
    //     }
        
    //     // Add date filters if present
    //     const fromDate = urlParams.get('from_date');
    //     const toDate = urlParams.get('to_date');
        
        
    //     if (fromDate) {
    //         exportUrl += `${hasParams ? '&' : '?'}from_date=${fromDate}`;
    //         hasParams = true;
    //     }
        
    //     if (toDate) {
    //         exportUrl += `${hasParams ? '&' : '?'}to_date=${toDate}`;
    //     }
        
    //     // Redirect to export URL
    //     window.location.href = exportUrl;
    // }
    
    function exportStates() {
    // Get the current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    // Create base export URL
    let exportUrl = "{{ route('admin.Green-Drive-Ev.State.export') }}";
    let params = [];
    
    // Add status filter if present and not 'all'
    const status = urlParams.get('status');
    if (status && status !== 'all') {
        params.push(`status=${status}`);
    }
    
    // Add date filters if present
    const fromDate = urlParams.get('from_date');
    const toDate = urlParams.get('to_date');
    
    if (fromDate) {
        params.push(`from_date=${fromDate}`);
    }
    
    if (toDate) {
        params.push(`to_date=${toDate}`);
    }
    
    // Append parameters to URL if any exist
    if (params.length > 0) {
        exportUrl += '?' + params.join('&');
    }
    
    // Redirect to export URL
    window.location.href = exportUrl;
}



    
   
    
    </script>
    @endsection
</x-app-layout>