<x-app-layout>
    <style>
    .add-new {
        background-image: linear-gradient(310deg, #7928ca, #ff0080) !important;
    }
    
    .add-new:hover {
        background-image: linear-gradient(310deg, #ff0080, #7928ca) !important;
    }

    @media screen and (max-width: 476px) {
        .icons-cls a i {
            font-size: 12px;    
        }
    }
</style>

<div class="main-content">
    <div class="page-header mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <h2 class="page-header-title">
                <img src="{{asset('admin-assets/icons/custom/list_of_adhoc.png')}}" class="img-fluid rounded"><span class="ps-2">Adhoc Log List</span>
            </h2>
            <button class="btn btn-success" onclick="exportToExcel()"><i class="bi bi-download"></i> Download</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Logs
                        <span class="badge bg-dark ml-2" id="itemCount">{{ count($reports)}}</span>
                    </h5>
                    <input id="searchInput" type="search" class="form-control w-25" placeholder="Search Adhoc" aria-label="Search">
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">SL</th>
                                <th class="border-0">Name</th>
                                <th class="border-0">Client Name</th>
                                <th class="border-0">Hub Name</th>
                                <th class="border-0">Total Online Hours</th>
                                <th class="border-0 ">Status</th>
                                <th class="border-0 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="set-rows">
                            @if(!empty($reports) && count($reports) > 0)
                                    @foreach($reports as $index => $data)
                                        <?php
                                            $client = \Modules\Clients\Entities\Client::where('id', $data->client_id)->first();
                                        ?>
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <p style="font-weight: bold;">{{ $data->first_name ?? 'N/A' }} {{ $data->last_name ?? 'N/A' }}</p>
                                            </td>
                                            <td>{{ $client->client_name ?? 'N/A' }}</td>
                                            <td>{{ $client->hub_name ?? 'N/A' }}</td>
                                            <td>{{ $data->total_time ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge text-white {{ isset($data->rider_status) && $data->rider_status === 0 ? 'bg-danger' : 'bg-success' }}">
                                                    {{ isset($data->rider_status) && $data->rider_status === 0 ? 'Offline' : 'Online' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-outline-warning mx-1" href="{{ route('admin.Green-Drive-Ev.adhocmanagement.show_adhoc_log_report', [$data->user_id ?? '']) }}" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">No reports available</td>
                                    </tr>
                                @endif

                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script>
    // JavaScript functions for handling status change and table filtering
    function status_change_alert(url, message, e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: message,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = url;
            }
        });
    }
    
    function route_alert(route, message, title = "Are you sure?") {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = route;
            }
        });
    }

    let typingTimer;
    const debounceTime = 300; // Delay in milliseconds
    
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(filterTable, debounceTime);
    });
    
    function filterTable() {
        var searchValue = document.getElementById('searchInput').value.toLowerCase();
        var table = document.getElementById('datatable');
        var rows = table.getElementsByTagName('tr');

        if (searchValue === '') {
            for (var i = 1; i < rows.length; i++) {
                rows[i].style.display = '';
            }
            return;
        }

        for (var i = 1; i < rows.length; i++) {
            var cells = rows[i].getElementsByTagName('td');
            var rowContainsSearchValue = false;

            for (var j = 0; j < cells.length; j++) {
                if (cells[j].innerText.toLowerCase().includes(searchValue)) {
                    rowContainsSearchValue = true;
                    break;
                }
            }

            rows[i].style.display = rowContainsSearchValue ? '' : 'none';
        }
    }
    
    document.querySelector('.location-reload-to-base').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        var rows = document.getElementById('datatable').getElementsByTagName('tr');
        
        for (var i = 1; i < rows.length; i++) {
            rows[i].style.display = '';
        }
    });

   function exportToExcel() {
        const table = document.getElementById("datatable");
        const clonedTable = table.cloneNode(true);
        const rows = clonedTable.rows;
        for (let i = 0; i < rows.length; i++) {
            rows[i].deleteCell(-1); 
        }
    
        const ws = XLSX.utils.table_to_sheet(clonedTable);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws);
    
        const excelFileName = 'Adhoc_log_list_' + getCurrentDate() + '.xlsx';
        XLSX.writeFile(wb, excelFileName);
    }
    function getCurrentDate() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        return dd + '-' + mm + '-' + yyyy;
    }

</script>
</x-app-layout>