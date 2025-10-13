<x-app-layout>
    <style>
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 25px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-switch-label {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 50px;
            transition: background-color 0.3s ease;
        }

        .toggle-switch-indicator {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 16px;
            height: 16px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        input:checked + .toggle-switch-label {
            background-color: #4CAF50; /* Green when active */
        }

        input:checked + .toggle-switch-label .toggle-switch-indicator {
            transform: translateX(26px); /* Move the indicator to the right */
        }

    </style>
    <x-card>
        <x-slot name='actions'>
            <a href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_index') }}" class="btn btn-success btn-sm">
                <i class="fa fa-plus-circle"></i>&nbsp;
                {{ localize('Add Asset Master Vechile') }}
            </a>
        </x-slot>
           <div class="row d-flex justify-content-end align-items-center">
                <div class="col-auto mb-3">
                    <a href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_importverify') }}" class="btn btn-round custom-btn me-2 mb-2">
                        Import Data's Verify
                    </a>
                    <a href="{{ asset('public/EV/AssetVehile.xlsx') }}" download="AssetVehile.xlsx" class="btn btn-round  custom-btn me-2 mb-2">
                        <i class="bi bi-download"></i> Demo Excel
                    </a>
                </div>
            
                <div class="col-auto mb-3">
                    <form class="form-inline" action="{{ route('admin.Green-Drive-Ev.asset-master.import-excel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="file" class="form-control form-control-sm" id="excel_file" name="excel_file" accept=".pdf,.doc,.docx,.txt,.jpg,.png,.csv,.xls,.xlsx">
                            <button type="submit" class="btn btn-round btn-sm custom-btn ms-2">
                                <i class="bi bi-upload"></i> Import Asset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <div>
            <x-data-table :dataTable="$dataTable" />
        </div>
    </x-card>
    <script>
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
</script>
</x-app-layout>
