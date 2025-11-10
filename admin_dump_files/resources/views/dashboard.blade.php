<x-app-layout>
    
<style>
    .equal-card-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem; 
}

.equal-card {
    flex: 1 1 48%; 
    display: flex;
    flex-direction: column;
}

.equal-card .card {
    flex: 1; 
    height: 100%;
}

</style>
    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    @php
        // Count records for each model
        $deliCount = \Modules\Deliveryman\Entities\Deliveryman::count();
        $citycount = \Modules\City\Entities\City::count();
        $ridertype = \Modules\RiderType\Entities\RiderType::count();
        $Areacount = \Modules\City\Entities\Area::count();
        $zoneCount = \Modules\Zones\Entities\Zones::count();
        $leadsource = \Modules\LeadSource\Entities\LeadSource::count();
        $usercount = App\Models\User::count();

        // asset master details
        $amsLocationCount = \Modules\AssetMaster\Entities\AmsLocationMaster::count();
        $assetInsuranceCount = \Modules\AssetMaster\Entities\AssetInsuranceDetails::count();
        $assetBatteryCount = \Modules\AssetMaster\Entities\AssetMasterBattery::count();
        $assetChargerCount = \Modules\AssetMaster\Entities\AssetMasterCharger::count();
        $assetVehicleCount = \Modules\AssetMaster\Entities\AssetMasterVehicle::count();
        $manufacturerCount = \Modules\AssetMaster\Entities\ManufacturerMaster::count();
        $modalVehicleCount = \Modules\AssetMaster\Entities\ModalMasterVechile::count();
        $modelBatteryCount = \Modules\AssetMaster\Entities\ModelMasterBattery::count();
        $modelChargerCount = \Modules\AssetMaster\Entities\ModelMasterCharger::count();
        $poTableCount = \Modules\AssetMaster\Entities\PoTable::count();

        // Bar chart code
        $deliverymanCount = \Modules\Deliveryman\Entities\Deliveryman::selectRaw(
            'MONTH(created_at) as month, COUNT(*) as count',
        )
            ->groupBy('month')
            ->pluck('count', 'month');

        $january = $deliverymanCount->get(1, 0); // January
        $february = $deliverymanCount->get(2, 0); // February
        $march = $deliverymanCount->get(3, 0); // March
        $april = $deliverymanCount->get(4, 0); // April
        $may = $deliverymanCount->get(5, 0); // May
        $june = $deliverymanCount->get(6, 0); // June
        $july = $deliverymanCount->get(7, 0); // July
        $august = $deliverymanCount->get(8, 0); // August
        $september = $deliverymanCount->get(9, 0); // September
        $october = $deliverymanCount->get(10, 0); // October
        $november = $deliverymanCount->get(11, 0); // November
        $december = $deliverymanCount->get(12, 0); // December

        $monthlyData = [
            $january,
            $february,
            $march,
            $april,
            $may,
            $june,
            $july,
            $august,
            $september,
            $october,
            $november,
            $december,
        ];

        // kyc column geting details
        $kycVerifiedCount = \Modules\Deliveryman\Entities\Deliveryman::where('kyc_verify', 1)->count();
        $kycNotVerifiedCount = \Modules\Deliveryman\Entities\Deliveryman::where('kyc_verify', 0)->count();

        $verified = $kycVerifiedCount;
        $notVerified = $kycNotVerifiedCount;

        // active and inactive riders count
        $active_riders = \Modules\Deliveryman\Entities\Deliveryman::where('rider_status', 1)->count();
        $inactive_riders = \Modules\Deliveryman\Entities\Deliveryman::where('rider_status', 0)->count();

        $riderThisMonthCount = \Modules\Deliveryman\Entities\Deliveryman::whereMonth(
            'created_at',
            \Carbon\Carbon::now()->month,)
            ->whereYear('created_at', \Carbon\Carbon::now()->year)
            ->count();

    @endphp
    <style>
        @media screen and (min-width:768px) {
            .four-card {
                font-size: 18px;
            }
        }
    </style>

    <?php
    // Role Verification
    $db = \Illuminate\Support\Facades\DB::table('model_has_roles')
        ->where('model_id', auth()->user()->id)
        ->first();

    $roles = DB::table('roles')
        ->where('id', $db->role_id)
        ->first();
    ?>

    @if ($roles->name == 'Telecaller')
        @php
            $statuses = [
                'New' => 'New Lead',
                'Contacted' => 'Contacting',
                'Call_Back' => 'Call Back Request',
                'Onboarded' => 'Onboarded',
                'DeadLead' => 'Dead Lead',
            ];

            $icons = [
                'New' => 'bi-briefcase',
                'Contacted' => 'bi-telephone',
                'Call_Back' => 'bi-arrow-clockwise',
                'Onboarded' => 'bi-person-check',
                'DeadLead' => 'bi-x-circle',
            ];

            $colors = [
                'New' => '#17c653',
                'Contacted' => '#f1c40f',
                'Call_Back' => '#3498db',
                'Onboarded' => '#2ecc71',
                'DeadLead' => '#e74c3c',
            ];

            $leadCounts = [];
            $totalCount = \Illuminate\Support\Facades\DB::table('ev_tbl_leads')
                ->where('Assigned', auth()->user()->id)
                ->count();
            $leadsource = \Modules\LeadSource\Entities\LeadSource::count();

            foreach ($statuses as $key => $status) {
                $leadCounts[$key] = \Illuminate\Support\Facades\DB::table('ev_tbl_leads')
                    ->where('telecaller_status', $key)
                    ->where('Assigned', auth()->user()->id)
                    ->count();
            }


        @endphp
        
        
       
        
        
        

        <!-- Cards Section -->
        <div class="row mb-4">
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card border border-3" style="border-radius:10px;">
                    <div class="card-body px-2">
                        <div class="row gx-1 justify-content-center align-items-center">
                            <div class="col-4 text-center mt-2">
                                <!-- Updated Icon with New Color -->
                                <i class="bi bi-graph-up-arrow fs-4 p-3"
                                    style="color: #ffffff; background: #28a745; border-radius: 25%;"></i>
                            </div>
                            <div class="col-8">
                                <h5 style="color:#28a745;" class="four-card">Total Leads Source</h5>
                                <p>No of: {{ $leadsource }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card border border-3" style="border-radius:10px;">
                    <div class="card-body px-2">
                        <div class="row gx-1 justify-content-center align-items-center">
                            <div class="col-4 text-center mt-2">
                                <i class="bi bi-collection fs-4 p-3"
                                    style="color: #fff; background: #6c757d; border-radius: 25%;"></i>
                            </div>
                            <div class="col-8">
                                <h5 style="color:#6c757d;" class="four-card">Total Leads</h5>
                                <p>No of: {{ $totalCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($statuses as $key => $status)
                <div class="col-md-6 col-xl-3 mb-4">
                    <div class="card border border-3" style="border-radius:10px;">
                        <div class="card-body px-2">
                            <div class="row gx-1 justify-content-center align-items-center">
                                <div class="col-4 text-center mt-2">
                                    <i class="bi {{ $icons[$key] }} fs-4 p-3"
                                        style="color: #fff; background: {{ $colors[$key] }}; border-radius: 25%;"></i>
                                </div>
                                <div class="col-8">
                                    <h5 style="color:{{ $colors[$key] }};" class="four-card">{{ $status }}</h5>
                                    <p>No of: {{ $leadCounts[$key] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-5">
            <!-- Bar Chart -->
            <div class="col-lg-7 col-md-12 mb-4">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-header bg-white text-center py-2">
                        <h6 class="m-0 text-uppercase" style="color: #555; font-weight: bold;">Leads Overview (Bar
                            Chart)</h6>
                    </div>
                    <div class="card-body p-3" style="height: 350px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Doughnut Chart -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-header bg-white text-center py-2">
                        <h6 class="m-0 text-uppercase" style="color: #555; font-weight: bold;">Leads Breakdown (Doughnut
                            Chart)</h6>
                    </div>
                    <div class="card-body p-3" style="height: 350px;">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
         <!-- Include Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Data Preparation
                const labels = @json(array_values($statuses));
                const dataCounts = @json(array_values($leadCounts));
                const backgroundColors = @json(array_values($colors));

                // Bar Chart
                const ctxBar = document.getElementById('barChart').getContext('2d');
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Lead Count',
                            data: dataCounts,
                            backgroundColor: backgroundColors,
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                        animation: {
                            duration: 1000
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#666'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#ddd'
                                },
                                ticks: {
                                    color: '#666'
                                }
                            }
                        }
                    }
                });

                // Doughnut Chart
                const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
                new Chart(ctxDoughnut, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataCounts,
                            backgroundColor: backgroundColors,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#333',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                        animation: {
                            duration: 1000
                        }
                    }
                });
            });
        </script>
        
        <div class="container mt-5">
        <!-- Filter Buttons -->
        <div class="mb-4 d-flex justify-content-between">
            <div>
                <button class="btn btn-primary me-2" onclick="filterTable('today')">Today</button>
                <button class="btn btn-secondary me-2" onclick="filterTable('week')">This Week</button>
                <button class="btn btn-success" onclick="filterTable('month')">This Month</button>
            </div>
            <div>
                <button class="btn btn-success" onclick="downloadexcel('Telecaller')">Excel</button>
            </div>
        </div>
        
        
    
        <!-- Table -->
        <table class="table table-striped" id="dataTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>New</th>
                <th>Call Back</th>
                <th>Contacted</th>
                <th>Dead Lead</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <!-- Rows will be populated dynamically -->
            </tbody>
        </table>
    </div>
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        filterTable('today');
    });
        function filterTable(filter) {
            let telecaller = {{auth()->user()->id}};
            // console.log(telecaller)
            $.ajax({
                url: "{{ route('filter.data') }}",  // Adjust with your actual route
                type: "POST",  // Use POST method
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"  // Include CSRF token in the headers
                },
                data: { filter: filter ,telecaller:telecaller},
                success: function (data) {
                    let tbody = $('#dataTable tbody');
                    tbody.empty();
                    if (data.length > 0) {
                        data.forEach((row, index) => {  
                            tbody.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${row.name}</td>
                                    <td>${row.New}</td>
                                    <td>${row.Call_Back}</td>
                                    <td>${row.Contacted}</td>
                                    <td>${row.DeadLead}</td>
                                    <td>${row.count}</td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr><td colspan="7" class="text-center">No data available</td></tr>');
                    }
                },
                error: function () {
                    alert('An error occurred while fetching data.');
                }
            });
        }
    </script>
       
    @endif

    @if ($roles->name == 'HR')
        @php
            $statuses = [
                'New' => 'New Lead',
                'Contacted' => 'Contacting',
                'Call_Back' => 'Call Back Request',
                'Onboarded' => 'Onboarded',
                'DeadLead' => 'Dead Lead',
            ];

            $icons = [
                'New' => 'bi-briefcase',
                'Contacted' => 'bi-telephone',
                'Call_Back' => 'bi-arrow-clockwise',
                'Onboarded' => 'bi-person-check',
                'DeadLead' => 'bi-x-circle',
            ];

            $colors = [
                'New' => '#17c653',
                'Contacted' => '#f1c40f',
                'Call_Back' => '#3498db',
                'Onboarded' => '#2ecc71',
                'DeadLead' => '#e74c3c',
            ];


            $leadCounts = [];

            $totalCount = \Illuminate\Support\Facades\DB::table('ev_tbl_leads')->where('Assigned', auth()->user()->id) ->count();

            $leadsource = \Modules\LeadSource\Entities\LeadSource::count();

          foreach ($statuses as $key => $status) {
                $leadCounts[$key] = \Illuminate\Support\Facades\DB::table('ev_tbl_leads')
                    ->where('telecaller_status', $key)
                    ->count();
            }


            // two diagram
            $statuses1 = [
                'active_riders' => 'Active Riders',
                'inactive_riders' => 'Inactive Riders',
                'rider_this_month' => 'Rider This Month',
                'total_riders' => 'Total Riders',
                // 'DeadLead'  => 'Dead Lead'
            ];

            $icons1 = [
                'active_riders' => 'bi-briefcase',
                'inactive_riders' => 'bi-telephone',
                'rider_this_month' => 'bi-arrow-clockwise',
                'total_riders' => 'bi-person-check',
                // 'DeadLead'  => 'bi-x-circle'
            ];

            $colors1 = [
                'active_riders' => '#17c653',
                'inactive_riders' => '#e74c3c',
                'rider_this_month' => ' #f1c40f',
                'active_riders' => '#3498db'
            ];

            $leadCounts1 = [
                'active_riders' => $active_riders,
                'inactive_riders' => $inactive_riders,
                'rider_this_month' => $riderThisMonthCount,
                'total_riders' => $deliCount,
            ];
            // active and inactive riders count
            $active_riders = \Modules\Deliveryman\Entities\Deliveryman::where('rider_status', 1)->count();
            $inactive_riders = \Modules\Deliveryman\Entities\Deliveryman::where('rider_status', 0)->count();

            $riderThisMonthCount = \Modules\Deliveryman\Entities\Deliveryman::whereMonth(
                'created_at',
                \Carbon\Carbon::now()->month,
            )
                ->whereYear('created_at', \Carbon\Carbon::now()->year)
                ->count();
            $deliCount = \Modules\Deliveryman\Entities\Deliveryman::count();

        @endphp

        <!-- Cards Section -->
        <div class="row mb-4">

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card border border-3" style="border-radius:10px;">
                    <div class="card-body px-2">
                        <div class="row gx-1 justify-content-center align-items-center">
                            <div class="col-4 text-center mt-2">
                                <!-- Updated Icon with New Color -->
                                <i class="bi bi-graph-up-arrow fs-4 p-3"
                                    style="color: #ffffff; background: #28a745; border-radius: 25%;"></i>
                            </div>
                            <div class="col-8">
                                <h5 style="color:#28a745;" class="four-card">Total Riders </h5>
                                <p>No of: {{ $deliCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card border border-3" style="border-radius:10px;">
                    <div class="card-body px-2">
                        <div class="row gx-1 justify-content-center align-items-center">
                            <div class="col-4 text-center mt-2">
                                <i class="bi bi-briefcase fs-4 p-3"
                                    style="color: #fff; background: #f1c40f; border-radius: 25%;"></i>
                            </div>
                            <div class="col-8">
                                <h5 style="color:#f1c40f;" class="four-card">This Month Riders</h5>
                                <p>No of: {{ $riderThisMonthCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card border border-3" style="border-radius:10px;">
                    <div class="card-body px-2">
                        <div class="row gx-1 justify-content-center align-items-center">
                            <div class="col-4 text-center mt-2">
                                <i class="bi bi-person-check fs-4 p-3"
                                    style="color: #fff; background: #3498db; border-radius: 25%;"></i>
                            </div>
                            <div class="col-8">
                                <h5 style="color:#3498db;" class="four-card">Active Riders</h5>
                                <p>No of: {{ $active_riders }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card border border-3" style="border-radius:10px;">
                    <div class="card-body px-2">
                        <div class="row gx-1 justify-content-center align-items-center">
                            <div class="col-4 text-center mt-2">
                                <i class="bi bi-x-circle fs-4 p-3"
                                    style="color: #fff; background: #e74c3c; border-radius: 25%;"></i>
                            </div>
                            <div class="col-8">
                                <h5 style="color:#e74c3c;" class="four-card">InActive Riders</h5>
                                <p>No of: {{ $inactive_riders }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- @foreach ($statuses as $key => $status)
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border border-3" style="border-radius:10px;">
                <div class="card-body px-2">
                    <div class="row gx-1 justify-content-center align-items-center">
                        <div class="col-4 text-center mt-2">
                            <i class="bi {{ $icons[$key] }} fs-4 p-3"
                                style="color: #fff; background: {{ $colors[$key] }}; border-radius: 25%;"></i>
                        </div>
                        <div class="col-8">
                            <h5 style="color:{{ $colors[$key] }};" class="four-card">{{ $status }}</h5>
                            <p>No of: {{ $leadCounts[$key] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach --}}

        </div>

        <div class="row mt-5">
            <!-- Bar Chart -->
            <div class="col-lg-7 col-md-12 mb-4">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-header bg-white text-center py-2">
                        <h6 class="m-0 text-uppercase" style="color: #555; font-weight: bold;">Leads Overview (Bar
                            Chart)</h6>
                    </div>
                    <div class="card-body p-3" style="height: 350px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Doughnut Chart -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-header bg-white text-center py-2">
                        <h6 class="m-0 text-uppercase" style="color: #555; font-weight: bold;">Riders Information
                            (Doughnut Chart)</h6>
                    </div>
                    <div class="card-body p-3" style="height: 350px;">
                        <canvas id="doughnutChart1"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container mt-5">
    <!-- Filter Buttons -->
    <div class="mb-4 d-flex justify-content-between">
            <div>
                <button class="btn btn-primary me-2" onclick="filterTable('today')">Today</button>
                <button class="btn btn-secondary me-2" onclick="filterTable('week')">This Week</button>
                <button class="btn btn-success" onclick="filterTable('month')">This Month</button>
            </div>
            <div>
                <!--<button class="btn btn-success" onclick="downloadexcel('Hr')">Excel</button>-->
            </div>
        </div>

    <!-- Table -->
    <table class="table table-striped" id="dataTable">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>New</th>
            <th>Call Back</th>
            <th>Contacted</th>
            <th>Dead Lead</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <!-- Rows will be populated dynamically -->
        </tbody>
    </table>
</div>

<script>
    function filterTable(filter) {
        $.ajax({
            url: "{{ route('filter.hrdata') }}",  // Adjust with your actual route
            type: "POST",  // Use POST method
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"  // Include CSRF token in the headers
            },
            data: { filter: filter },
            success: function (data) {
                console.log(data)
                let tbody = $('#dataTable tbody');
                tbody.empty();
                if (data.length > 0) {
                    data.forEach((row, index) => {  
                        tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${row.name}</td>
                                <td>${row.New}</td>
                                <td>${row.Call_Back}</td>
                                <td>${row.Contacted}</td>
                                <td>${row.DeadLead}</td>
                                <td>${row.count}</td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="7" class="text-center">No data available</td></tr>');
                }
            },
            error: function () {
                alert('An error occurred while fetching data.');
            }
        });
    }
</script>

        <!-- Include Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Data Preparation
                const labels = @json(array_values($statuses));
                const dataCounts = @json(array_values($leadCounts));
                const backgroundColors = @json(array_values($colors));

                const labels1 = @json(array_values($statuses1));
                const dataCounts1 = @json(array_values($leadCounts1));
                const backgroundColors1 = @json(array_values($colors1));

                // Bar Chart
                const ctxBar = document.getElementById('barChart').getContext('2d');
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Rider Count',
                            data: dataCounts,
                            backgroundColor: backgroundColors,
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                        animation: {
                            duration: 1000
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#666'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#ddd'
                                },
                                ticks: {
                                    color: '#666'
                                }
                            }
                        }
                    }
                });

                // Doughnut Chart
                const ctxDoughnut = document.getElementById('doughnutChart1').getContext('2d');
                new Chart(ctxDoughnut, {
                    type: 'doughnut',
                    data: {
                        labels: labels1,
                        datasets: [{
                            data: dataCounts1,
                            backgroundColor: backgroundColors1,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#333',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                        animation: {
                            duration: 1000
                        }
                    }
                });
            });
        </script>
    @endif
    
    @if(isset($login_user_role) && ($login_user_role == 1 || $login_user_role == 13))

    <!--Super admin code start or admin-->
    <div class="row mb-4">
        
            <!--first row icon and dashboard label-->
        <div class="row my-3  d-flex justify-content-between align-items-center">
                    <?php
                      $role = \Illuminate\Support\Facades\DB::table('roles')->where('id',auth()->user()->role)->first();
                    ?>
                    <div class="col-12 col-xl-9 col-md-6">      
                        <h4>Dashboard </h4>
                        <p> Welcome Back, {{$role->name ?? 'Admin'}} </p>
                    </div>
                    
                    <div class="col-12 col-xl-3 col-md-6 ">      
                            
                            <div class='card p-3'> 
                                <div class="row my-3  d-flex justify-content-between align-items-center">
                                             <div class="col-4 col-md-4">      
                                                    <i class="bi bi-calendar p-3"  style="background: #3BB54A12; border-radius:25%;"></i>
                                              </div>
                    
                                            <div class="col-8 col-md-8 ">                        
                                                    <span> Chennai </span>
                                            </div>
                            </div>
                            
                            
                    </div>
                </div>
        </div>
        
        <!--second row for charts-->
        <div class="row my-5  d-flex justify-content-between ">
            
                    <div class="col-12 col-xl-5 col-md-6">      
                
                        
                        <div class="row  d-flex justify-content-between align-items-center">
                           
                           
                
                        <div class="col-12 col-xl-6 col-md-6 mb-3">      
                          <a href="{{ route('admin.Green-Drive-Ev.employee_management.employee_list') }}" class="text-dark">
                              <div class='card p-3'> 
                                <span class="mb-2">Total No of Employees</span>
                                <h4>{{ $total_employee_count }}</h4>
                            
                                <div class="card-body">
                                  <div>
                                    <canvas id="linechart_1"></canvas>
                                  </div>
                                </div>
                              </div>
                             </a>
                        </div>
                                                   
                         <div class="col-12 col-xl-6 col-md-6 mb-3">   
                            <a href="{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" class="text-dark">
                              <div class='card p-3'> 
                                <span class="mb-2">Total No of Riders</span>
                                <h4>{{ $total_rider_count }}</h4>
                            
                                <div class="card-body">
                                  <div>
                                    <canvas id="linechart_2"></canvas>
                                  </div>
                                </div>
                              </div>
                            </a>
                        </div>
                            
                            
                          <div class="col-12 col-xl-6 col-md-6 mb-3"> 
                            <a href="{{ route('admin.Green-Drive-Ev.adhocmanagement.list_of_adhoc') }}" class="text-dark">
                                  <div class='card p-3'> 
                                    <span class="mb-2">Total No of Adhoc</span>
                                    <h4>{{ $total_adhoc_count }}</h4>
                                
                                    <div class="card-body">
                                      <div>
                                        <canvas id="linechart_3"></canvas>
                                      </div>
                                    </div>
                                  </div>
                            </a>
                        </div>
                            
                            
                            
                        <div class="col-12 col-xl-6 col-md-6 mb-3">  
                            <a href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_list') }}" class="text-dark">
                              <div class='card p-3'> 
                                    <span class="mb-2">Total No of Ev Bikes</span>
                                    <h4>{{$total_vehicle_count}}</h4>
                                
                                    <div class="card-body">
                                      <div>
                                        <canvas id="linechart_4"></canvas>
                                      </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                           
                        </div>
                    
                    </div>
                    
                    <div class="col-12 col-xl-7 col-md-6 ">      
                            
                            <div class='card p-3'>
                                <div class='card-header'>
                                    
                                <div class="row d-flex justify-content-between align-items-center"> 
                                          <div class="col-8 col-xl-8 col-md-6 ">  
                                                   <p>  New Employees List </p>
                                               </div>
                                               
                                             <div class="col-4 col-xl-4 col-md-6 text-end ">
                                                        <div class="dropdown mb-3">
                                                              <button class="btn  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                 Month
                                                              </button>
                                                              <ul class="dropdown-menu" id="customMonthDropdown">
                                                              
                                                                <li><a class="dropdown-item" data-month="0" href="#">January</a></li>
                                                                <li><a class="dropdown-item" data-month="1" href="#">February</a></li>
                                                                <li><a class="dropdown-item" data-month="2" href="#">March</a></li>
                                                                <li><a class="dropdown-item" data-month="3" href="#">April</a></li>
                                                                <li><a class="dropdown-item" data-month="4" href="#">May</a></li>
                                                                <li><a class="dropdown-item" data-month="5" href="#">June</a></li>
                                                                <li><a class="dropdown-item" data-month="6" href="#">July</a></li>
                                                                <li><a class="dropdown-item" data-month="7" href="#">August</a></li>
                                                                <li><a class="dropdown-item" data-month="8" href="#">September</a></li>
                                                                <li><a class="dropdown-item" data-month="9" href="#">October</a></li>
                                                                <li><a class="dropdown-item" data-month="10" href="#">November</a></li>
                                                                <li><a class="dropdown-item" data-month="11" href="#">December</a></li>
                                                              </ul>
                                                            </div>
                                             
                                               </div>
                                </div>
                                    
                                    </div>
                       
                                <div class='card-body'>
                                    
                                       <div class="custom-chart-box">
                                     
                                            
                     
                                    <canvas id="customEmployeeChart"></canvas>
                                 </div>
                                    
                                </div>
                            
                              
                                                        
                            </div>
                </div>
        </div>
        
         <div class="equal-card-wrapper my-5">
            <div class="equal-card">      
                            
                            <div class='card p-3'>
                                <div class='card-header'>
                                    
                                <div class="row d-flex justify-content-between align-items-center"> 
                                          <div class="col-8 col-xl-8 col-md-6 ">  
                                                   <p>  New Employees List </p>
                                          </div>
                                               
                                             <div class="col-4 col-xl-4 col-md-6 text-end ">
                                                        <div class="dropdown mb-3">
                                                              <button class="btn  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                 Month
                                                              </button>
                                                              <ul class="dropdown-menu" id="customMonthDropdown">
                                                              
                                                                <li><a class="dropdown-item" data-month="0" href="#">January</a></li>
                                                                <li><a class="dropdown-item" data-month="1" href="#">February</a></li>
                                                                <li><a class="dropdown-item" data-month="2" href="#">March</a></li>
                                                                <li><a class="dropdown-item" data-month="3" href="#">April</a></li>
                                                                <li><a class="dropdown-item" data-month="4" href="#">May</a></li>
                                                                <li><a class="dropdown-item" data-month="5" href="#">June</a></li>
                                                                <li><a class="dropdown-item" data-month="6" href="#">July</a></li>
                                                                <li><a class="dropdown-item" data-month="7" href="#">August</a></li>
                                                                <li><a class="dropdown-item" data-month="8" href="#">September</a></li>
                                                                <li><a class="dropdown-item" data-month="9" href="#">October</a></li>
                                                                <li><a class="dropdown-item" data-month="10" href="#">November</a></li>
                                                                <li><a class="dropdown-item" data-month="11" href="#">December</a></li>
                                                              </ul>
                                                            </div>
                                             
                                               </div>
                                </div>
                                    
                                    </div>
                       
                                <div class='card-body'>
                                    
                                    <div class="chart-container">
                                      <h2>Leads</h2>
                                      <canvas id="leadsChart"></canvas>
                                    </div>
                                    
                                </div>
                            </div>
                </div>
         
                 <div class="equal-card">      
                    <div class="card p-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <p class="mb-0">Rider onboard count list</p>
                            <div class="onboard_filter">
                                <label for="onboard_category">Select Category: </label>
                                <select id="onboard_category" onchange="onboard_updateChart()" style="border:none;">
                                    <option value="rider">Rider</option>
                                    <option value="adhoc">Adhoc</option>
                                    <option value="helper">Helper</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container onboard-chart-container" style="position: relative; height: 300px; width: 100%;">
                                <canvas id="OnboardpieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx1 = document.getElementById('linechart_1').getContext('2d');
  const gradient1 = ctx1.createLinearGradient(0, 0, 0, 80);
  gradient1.addColorStop(0, 'rgba(0, 200, 83, 0.2)');
  gradient1.addColorStop(1, 'rgba(0, 200, 83, 0)');

  new Chart(ctx1, {
    type: 'line',
    data: {
      labels: Array(10).fill(''),
      datasets: [{
        data: [1, 2, 3, 6, 5, 6, 7, 6, 9],
        borderColor: '#00C853',
        backgroundColor: gradient1,
        fill: true,
        tension: 0.4,
        pointRadius: 0
      }]
    },
    options: {
      animation: false,
      plugins: { legend: { display: false } },
      scales: { x: { display: false }, y: { display: false } },
      responsive: true,
    }
  });

  const ctx2 = document.getElementById('linechart_2').getContext('2d');
  const gradient2 = ctx2.createLinearGradient(0, 0, 0, 80);
  gradient2.addColorStop(0, 'rgba(0, 200, 83, 0.2)');
  gradient2.addColorStop(1, 'rgba(0, 200, 83, 0)');

  new Chart(ctx2, {
    type: 'line',
    data: {
      labels: Array(10).fill(''),
      datasets: [{
        data: [1, 2, 3, 6, 5, 6, 7, 6, 9],
        borderColor: '#00C853',
        backgroundColor: gradient2,
        fill: true,
        tension: 0.4,
        pointRadius: 0
      }]
    },
    options: {
      animation: false,
      plugins: { legend: { display: false } },
      scales: { x: { display: false }, y: { display: false } },
      responsive: true,
    }
  });

  const ctx3 = document.getElementById('linechart_3').getContext('2d');
  const gradient3 = ctx3.createLinearGradient(0, 0, 0, 80);
  gradient3.addColorStop(0, 'rgba(0, 200, 83, 0.2)');
  gradient3.addColorStop(1, 'rgba(0, 200, 83, 0)');

  new Chart(ctx3, {
    type: 'line',
    data: {
      labels: Array(10).fill(''),
      datasets: [{
        data: [1, 2, 3, 6, 5, 6, 7, 6, 9],
        borderColor: '#00C853',
        backgroundColor: gradient3,
        fill: true,
        tension: 0.4,
        pointRadius: 0
      }]
    },
    options: {
      animation: false,
      plugins: { legend: { display: false } },
      scales: { x: { display: false }, y: { display: false } },
      responsive: true,
    }
  });

  const ctx4 = document.getElementById('linechart_4').getContext('2d');
  const gradient4 = ctx4.createLinearGradient(0, 0, 0, 80);
  gradient4.addColorStop(0, 'rgba(0, 200, 83, 0.2)');
  gradient4.addColorStop(1, 'rgba(0, 200, 83, 0)');

  new Chart(ctx4, {
    type: 'line',
    data: {
      labels: Array(10).fill(''),
      datasets: [{
        data: [1, 2, 3, 6, 5, 6, 7, 6, 9],
        borderColor: '#00C853',
        backgroundColor: gradient4,
        fill: true,
        tension: 0.4,
        pointRadius: 0
      }]
    },
    options: {
      animation: false,
      plugins: { legend: { display: false } },
      scales: { x: { display: false }, y: { display: false } },
      responsive: true,
    }
  });
</script>

<script>
    const customCtx = document.getElementById('customEmployeeChart').getContext('2d');

    const customGradient = customCtx.createLinearGradient(0, 0, 0, 400);
    customGradient.addColorStop(0, 'rgba(0, 200, 83, 1)');
    customGradient.addColorStop(1, 'rgba(0, 200, 83, 0.2)');

    const monthLabels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
    const newEmployeesData = [100, 140, 150, 250, 270, 200, 240, 100, 260, 320, 360, 400];
    const targetData = Array(12).fill(400);

    const customChart = new Chart(customCtx, {
      type: 'bar',
      data: {
        labels: monthLabels,
        datasets: [
          {
            label: 'Target',
            data: targetData,
            backgroundColor: 'rgba(220, 220, 220, 0.2)',
            borderRadius: 10,
            barPercentage: 0.5,
            categoryPercentage: 0.6
          },
          {
            label: 'New Employees',
            data: newEmployeesData,
            backgroundColor: customGradient,
            borderRadius: 10,
            barPercentage: 0.5,
            categoryPercentage: 0.6
          }
        ]
      },
      options: {
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: '#888', font: { weight: 'bold' } }
          },
          y: {
            grid: { display: false }, // ✅ Removes background grid lines
            ticks: {
              stepSize: 100,
              color: '#aaa'
            }
          }
        }
      }
    });
    
    

  // Data variables (can be dynamically updated)
  const lead_labels = ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];
  const lead_data = [1600, 2000, 4500, 7000, 1500, 2500, 6500, 9500];

  const lead_ctx = document.getElementById('leadsChart').getContext('2d');

  new Chart(lead_ctx, {
    type: 'line',
    data: {
      labels: lead_labels,
      datasets: [{
        label: 'Leads',
        data: lead_data,
        borderColor: 'green',
        borderWidth: 2,
        fill: true,
        backgroundColor: 'rgba(0, 255, 0, 0.1)',
        borderDash: [5, 5], // Dotted line
        tension: 0.3,       // Smooth curve
        pointRadius: 0      // No dots
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 10000,
          ticks: {
            callback: function(value) {
              return value / 1000 + 'K';
            }
          }
        }
      }
    }
  });

  </script>


  <script>
    // const data = {
    //     rider: {
    //         labels: {!! json_encode($onboarding_data->pluck('name')) !!},
    //         values: {!! json_encode($onboarding_data->pluck('cust_count')) !!}
    //     }
    // };

    // let pieChart;

    // function Onboard_createChart(category) {
    //     const ctx = document.getElementById('OnboardpieChart').getContext('2d');
    //     if (pieChart) {
    //         pieChart.destroy();
    //     }

    //     pieChart = new Chart(ctx, {
    //         type: 'pie',
    //         data: {
    //             labels: data[category].labels,
    //             datasets: [{
    //                 label: `Assigned Count - ${category}`,
    //                 data: data[category].values,
    //                 backgroundColor: ['#4e79a7', '#f28e2b', '#e15759', '#76b7b2', '#59a14f', '#edc949', '#af7aa1'],
    //                 borderColor: '#fff',
    //                 borderWidth: 2
    //             }]
    //         },
    //         options: {
    //             responsive: true,
    //             plugins: {
    //                 legend: {
    //                     position: 'bottom'
    //                 }
    //             }
    //         }
    //     });
    // }

    // function onboard_updateChart() {
    //     const selectedCategory = document.getElementById('onboard_category').value;

    //     // AJAX call to fetch updated data
    //     fetch(`/dashboard/onboard-category-data?category=${selectedCategory}`)
    //         .then(response => response.json())
    //         .then(json => {
    //             data[selectedCategory] = {
    //                 labels: json.labels,
    //                 values: json.values
    //             };
    //             Onboard_createChart(selectedCategory);
    //         });
    // }

    // // Initial render
    // Onboard_createChart('rider');
</script>

<script>
     const chartData = {
        rider: {
            labels: {!! json_encode($onboarding_data->pluck('name')) !!},
            values: {!! json_encode($onboarding_data->pluck('cust_count')) !!},
            cities: {!! json_encode($onboarding_data->pluck('city_count')) !!},
            hubs: {!! json_encode($onboarding_data->pluck('hub_count')) !!}
        }
    };

    let pieChart;

    function Onboard_createChart(category) {
        const ctx = document.getElementById('OnboardpieChart').getContext('2d');

        // Null/empty check
        if (!chartData[category] || !chartData[category].labels || chartData[category].labels.length === 0) {
            document.getElementById('OnboardpieChart').parentElement.innerHTML = '<p style="text-align:center;color:#888;">No data available</p>';
            return;
        }

        if (pieChart) {
            pieChart.destroy();
        }

        pieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartData[category].labels,
                    datasets: [{
                        label: `Assigned Count - ${category}`,
                        data: chartData[category].values,
                        backgroundColor: [
                            '#4e79a7', '#f28e2b', '#e15759',
                            '#76b7b2', '#59a14f', '#edc949',
                            '#af7aa1', '#ff9da7', '#9c755f'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            align: 'center',
                            labels: {
                                padding: 20,
                                boxWidth: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const index = context.dataIndex;
                                    const label = chartData[category].labels[index];
                                    const value = chartData[category].values[index];
                                    const city = chartData[category].cities[index];
                                    const hub = chartData[category].hubs[index];
            
                                    return `${label}: ${value}\nTotal Cities: ${city}\nTotal Hubs: ${hub}`;
                                }
                            }
                        }
                    },
                    layout: {
                        padding: { bottom: 20 }
                    }
                }
            });

    }

    function onboard_updateChart() {
        const selectedCategory = document.getElementById('onboard_category').value;

        $.ajax({
            url: "{{ route('RiderOnboardfilter.data') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: { category: selectedCategory },
            success: function (response) {
                    if (response && Array.isArray(response.labels) && response.labels.length > 0) {
                        chartData[selectedCategory] = {
                            labels: response.labels,
                            values: response.values,
                            cities: response.cities,
                            hubs: response.hubs
                        };
                
                        if (!document.getElementById('OnboardpieChart')) {
                            $('.onboard-chart-container').html('<canvas id="OnboardpieChart" style="width:100%;height:100%;"></canvas>');
                        }
                
                        // Wait a moment for canvas to appear before rendering
                        setTimeout(() => {
                            Onboard_createChart(selectedCategory);
                        }, 50);
                    } else {
                        chartData[selectedCategory] = {
                            labels: [],
                            values: [],
                            cities: [],
                            hubs: []
                        };
                
                        setTimeout(() => {
                            Onboard_createChart(selectedCategory);
                        }, 50);
                    }
                },

            error: function () {
                alert('An error occurred while fetching chart data.');
            }
        });
    }

    // Initial chart render
    Onboard_createChart('rider');
</script>



        
       
    </div>
    
    <!--Super admin code end-->
    @endif
    
    
     <!--@can('maintenance_report')-->
        <!--    <div class="col-md-6  col-xl-3 mb-4">-->
        <!--        <div class="card  border border-3" style="border-radius:10px;">-->
        <!--            <div class="card-body px-2">-->
        <!--                <a href="{{ route('admin.Green-Drive-Ev.City.list') }}">-->
        <!--                    <div class="row gx-1 justify-content-center aligh-items-center">-->
        <!--                        <div class="col-4 text-center mt-2">-->
        <!--                            <i class="bi bi-buildings fs-4 p-3"-->
        <!--                                style="color: #fff; background: #17c653; border-radius:25%;"></i>-->
        <!--                        </div>-->
        <!--                        <div class="col-8 ">-->
        <!--                            <h5 style="color:#17c653;" class="four-card">City</h5>-->
        <!--                            <p>No of: <?php echo $citycount; ?></p>-->

        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--    <div class="col-md-6 col-xl-3 mb-4">-->
        <!--        <div class="card  border border-3" style="border-radius:10px;">-->
        <!--            <div class="card-body px-2">-->
        <!--                <a href="{{ route('admin.Green-Drive-Ev.Area.list') }}">-->
        <!--                    <div class="row gx-1 justify-content-center aligh-items-center">-->
        <!--                        <div class="col-4 text-center mt-2">-->
        <!--                            <i class="bi bi-stoplights fs-4 p-3"-->
        <!--                                style="color: #fff; background: #17c653; border-radius:25%;"></i>-->
        <!--                        </div>-->
        <!--                        <div class="col-8 ">-->
        <!--                            <h5 style="color:#17c653;" class="four-card">Area</h5>-->
        <!--                            <p>No of: <?php echo $Areacount; ?></p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--    <div class="col-md-6  col-xl-3 mb-4">-->
        <!--        <div class="card  border border-3" style="border-radius:10px;">-->
        <!--            <div class="card-body px-2">-->
        <!--                <a href="{{ route('admin.Green-Drive-Ev.rider-type.list') }}">-->
        <!--                    <div class="row gx-1 justify-content-center aligh-items-center">-->
        <!--                        <div class="col-4 text-center mt-2">-->
        <!--                            <i class="bi bi-scooter fs-4 p-3"-->
        <!--                                style="color: #fff; background: #17c653; border-radius:25%;"></i>-->
        <!--                        </div>-->
        <!--                        <div class="col-8 ">-->
        <!--                            <h5 style="color:#17c653;" class="four-card">Rider Category</h5>-->
        <!--                            <p>No of: <?php echo $ridertype; ?></p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--    <div class="col-md-6 mb-4 col-xl-3">-->
        <!--        <div class="card  border border-3" style="border-radius:10px;">-->
        <!--            <div class="card-body px-2">-->
        <!--                <a href="{{ route('admin.Green-Drive-Ev.delivery-man.list') }}">-->
        <!--                    <div class="row gx-1 justify-content-center aligh-items-center">-->
        <!--                        <div class="col-4 text-center mt-2">-->
        <!--                            <i class="bi bi-truck fs-4 p-3"-->
        <!--                                style="color: #fff; background: #17c653; border-radius:25%;"></i>-->

        <!--                        </div>-->
        <!--                        <div class="col-8 ">-->
        <!--                            <h5 style="color:#17c653;" class="four-card">Delivery Man</h5>-->
        <!--                            <p>No of: <?php echo $deliCount; ?></p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->

        <!--    <div class="col-md-6 mb-4 col-xl-3">-->
        <!--        <div class="card  border border-3" style="border-radius:10px;">-->
        <!--            <div class="card-body px-2">-->
        <!--                <a href="{{ route('admin.Green-Drive-Ev.lead-source.list') }}">-->
        <!--                    <div class="row gx-1 justify-content-center aligh-items-center">-->
        <!--                        <div class="col-4 text-center mt-2">-->
        <!--                            <i class="bi bi-card-list fs-4 p-3"-->
        <!--                                style="color: #fff; background: #17c653; border-radius:25%;"></i>-->

        <!--                        </div>-->
        <!--                        <div class="col-8 ">-->
        <!--                            <h5 style="color:#17c653;" class="four-card">Lead source</h5>-->
        <!--                            <p>No of: <?php echo $leadsource; ?></p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--    <div class="col-md-6 mb-4 col-xl-3">-->
        <!--        <div class="card  border border-3" style="border-radius:10px;">-->
        <!--            <div class="card-body px-2">-->
        <!--                <a href="{{ route('admin.Green-Drive-Ev.zone.zone') }}">-->
        <!--                    <div class="row gx-1 justify-content-center aligh-items-center">-->
        <!--                        <div class="col-4 text-center mt-2">-->
        <!--                            <i class="bi bi-geo-alt fs-4 p-3"-->
        <!--                                style="color: #fff; background: #17c653; border-radius:25%;"></i>-->

        <!--                        </div>-->
        <!--                        <div class="col-8 ">-->
        <!--                            <h5 style="color:#17c653;" class="four-card">Zones</h5>-->
        <!--                            <p>No of: <?php echo $zoneCount; ?></p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--    <div class="col-md-6 mb-4 col-xl-3">-->
        <!--        <div class="card  border border-3" style="border-radius:10px;">-->
        <!--            <div class="card-body px-2">-->
        <!--                <a href="{{ route('admin.user.index') }}">-->
        <!--                    <div class="row gx-1 justify-content-center aligh-items-center">-->
        <!--                        <div class="col-4 text-center mt-2">-->
        <!--                            <i class="bi bi-person-gear fs-4 p-3"-->
        <!--                                style="color: #fff; background: #17c653; border-radius:25%;"></i>-->

        <!--                        </div>-->
        <!--                        <div class="col-8 ">-->
        <!--                            <h5 style="color:#17c653;" class="four-card">User</h5>-->
        <!--                            <p>No of: <?php echo $usercount; ?></p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--@endcan-->




    <!--<div class="row mb-4">-->
    <!--    @can('maintenance_report')-->
    <!--        <div class="col-xl-12 mb-4 mb-xl-0">-->
    <!--            <div class="card rounded-0">-->
    <!--                <div class="card-header">-->
    <!--                    <div class="d-lg-flex justify-content-between align-items-center">-->
    <!--                        <h5 class="fw-bold fs-17 mb-0" style="text-transform:capitalize;">@localize('Last 12 Month Deliveryman Details:')</h5>-->
    <!--                        </h6>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <div class="card-body px-2">-->
    <!--                    <div>-->
    <!--                        <canvas id="myChart"></canvas>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    @endcan-->
    <!--</div>-->


    <!-- 2nd  -->
    <!--@can('vehicle_requisition_report')-->
        <!--<div class="row mb-4">-->
        <!--    <div class="col-xl-6 mb-4 mb-xl-0">-->
        <!--        <div class="card rounded-0">-->
        <!--            <div class="card-header card_header px-3">-->
        <!--                <div class="d-flex justify-content-between align-items-center">-->
        <!--                    <h6 class="fs-16 fw-bold mb-0">KYC Verification</h6>-->
        <!--                    </h6>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div class="card-body w-100  p-5">-->
        <!--                <canvas id="myChart1"></canvas>-->
        <!--            </div>-->

        <!--        </div>-->
        <!--    </div>-->
        <!--    <div class="col-xl-6">-->
        <!--        <div class="card rounded">-->
        <!--            {{-- <div class="card-header card_header px-3">-->
        <!--                <h4> New Card</h4>-->
        <!--            </div> --}}-->
        <!--            <div class="card-body">-->
        <!--                <div class="table-responsive">-->
        <!--                    <table class="table table-striped table-borderless table-hover rounded-3 table-light">-->
        <!--                        <thead>-->
        <!--                            <tr class="text-center">-->
        <!--                                <th class="py-3">Sno</th>-->
        <!--                                <th class="py-3">Asset Master</th>-->
        <!--                                <th class="py-3">Count</th>-->

        <!--                            </tr>-->
        <!--                        </thead>-->
        <!--                        <tbody class="text-center">-->

        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.list') }}';"-->
        <!--                                style="cursor: pointer;">-->

        <!--                                <td class="py-3">1</td>-->
        <!--                                <td class="py-3">Model Master Vehicle</td>-->
        <!--                                <td class="py-3"> <?php echo $modalVehicleCount; ?> </td>-->
        <!--                            </tr>-->


        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.modal_master_battery_list') }}';"-->
        <!--                                style="cursor: pointer;">-->
        <!--                                <td class="py-3">2</td>-->
        <!--                                <td class="py-3">Model Master Battery</td>-->
        <!--                                <td class="py-3"> <?php echo $modelBatteryCount; ?> </td>-->
        <!--                            </tr>-->

        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.model_master_charger_list') }}';"-->
        <!--                                style="cursor: pointer;">-->
        <!--                                <td class="py-3">3</td>-->
        <!--                                <td class="py-3">Model Master Charger</td>-->
        <!--                                <td class="py-3"> <?php echo $modelChargerCount; ?> </td>-->
        <!--                            </tr>-->

        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.manufacturer_master_list') }}';"-->
        <!--                                style="cursor: pointer;">-->
        <!--                                <td class="py-3">4</td>-->
        <!--                                <td class="py-3">Manufactuarar Master </td>-->
        <!--                                <td class="py-3"> <?php echo $manufacturerCount; ?> </td>-->
        <!--                            </tr>-->

        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.po_table_list') }}';"-->
        <!--                                style="cursor: pointer;">-->
        <!--                                <td class="py-3">5</td>-->
        <!--                                <td class="py-3">PO Table </td>-->
        <!--                                <td class="py-3"> <?php echo $poTableCount; ?> </td>-->
        <!--                            </tr>-->

        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.ams_location_master_list') }}';"-->
        <!--                                style="cursor: pointer;">-->
        <!--                                <td class="py-3">6</td>-->
        <!--                                <td class="py-3">AMS Location Master </td>-->
        <!--                                <td class="py-3"> <?php echo $amsLocationCount; ?> </td>-->
        <!--                            </tr>-->

        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.asset_insurance_details_list') }}';"-->
        <!--                                style="cursor: pointer;">-->
        <!--                                <td class="py-3">7</td>-->
        <!--                                <td class="py-3">Asset Insurance Details </td>-->
        <!--                                <td class="py-3"> <?php echo $assetInsuranceCount; ?> </td>-->
        <!--                            </tr>-->

        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_list') }}';"-->
        <!--                                style="cursor: pointer;">-->
        <!--                                <td class="py-3">8</td>-->
        <!--                                <td class="py-3">Asset Master Vehicle </td>-->
        <!--                                <td class="py-3"> <?php echo $assetVehicleCount; ?> </td>-->
        <!--                            </tr>-->

        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.asset_master_battery_list') }}';"-->
        <!--                                style="cursor: pointer;">-->
        <!--                                <td class="py-3">9</td>-->
        <!--                                <td class="py-3">Asset Master Battery</td>-->
        <!--                                <td class="py-3"> <?php echo $assetBatteryCount; ?> </td>-->
        <!--                            </tr>-->

        <!--                            <tr onclick="window.location.href='{{ route('admin.Green-Drive-Ev.asset-master.asset_master_charger_list') }}';"-->
        <!--                                style="cursor: pointer;">-->
        <!--                                <td class="py-3">10</td>-->
        <!--                                <td class="py-3">Asset Master charger</td>-->
        <!--                                <td class="py-3"> <?php echo $assetChargerCount; ?> </td>-->
        <!--                            </tr>-->

        <!--                        </tbody>-->
        <!--                    </table>-->
        <!--                </div>-->

        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
    <!--@endcan-->

    <!-- new chart.js End -->
    {{-- @can('legal_document_management')
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-17 font-weight-600 mb-0">@localize('Reminder')</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-borderless table-hover rounded-3 table-light">
                        <thead>
                            <tr>
                                <th class="py-3">@localize('Vehicle No.')</th>
                                <th class="py-3">@localize('Document name')</th>
                                <th class="py-3">@localize('Expiration Date')</th>
                                <th class="py-3">@localize('Renewal Date')</th>
                                <th class="py-3 text-center">@localize('Current Status')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reminders as $doc)
                                <tr>
                                    <td class="py-3">{{ $doc->vehicle?->name }}</td>
                                    <td class="py-3">{{ $doc->document_type?->name }}</td>
                                    <td class="py-3">{{ $doc->expiry_date }}</td>
                                    <td class="py-3">{{ $doc->expiry_date }}</td>
                                    <td class="py-3 text-center">{!! $doc->current_status !!}</td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">@localize('No data found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $reminders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endcan --}}


    @push('css')
        <link rel="stylesheet" href="{{ admin_asset('css/dashboard.min.css') }}">
        <style>
        </style>
    @endpush
    @push('js')
        <script src="{{ admin_asset('vendor/amcharts5/index.min.js') }}"></script>
        <script src="{{ admin_asset('vendor/amcharts5/venn.js') }}"></script>
        <script src="{{ admin_asset('vendor/amcharts5/percent.min.js') }}"></script>
        <script src="{{ admin_asset('vendor/amcharts5/percent.min.js') }}"></script>
        <script src="{{ admin_asset('vendor/amcharts5/themes/Animated.min.js') }}"></script>
        <script src="{{ admin_asset('vendor/amcharts5/xy.min.js') }}"></script>
        <script src="{{ admin_asset('js/dashboard.min.js') }}"></script>
        <script src="{{ admin_asset('js/chart.js') }}"></script>


        <script>
            // Bar Chart
            const ctx = document.getElementById('myChart').getContext('2d');
            // Define 12 labels
            const labels = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            // Fetch monthly data from PHP
            const monthlyData = <?php echo json_encode($monthlyData); ?>;
            const barChartData = {
                labels: labels,
                datasets: [{
                    label: 'Months',
                    data: monthlyData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.4)',
                        'rgba(255, 159, 64, 0.4)',
                        'rgba(255, 205, 86, 0.4)',
                        'rgba(75, 192, 192, 0.4)',
                        'rgba(54, 162, 235, 0.4)',
                        'rgba(153, 102, 255, 0.4)',
                        'rgba(201, 203, 207, 0.4)',
                        'rgba(123, 239, 178, 0.4)',
                        'rgba(250, 128, 114, 0.4)',
                        'rgba(100, 149, 237, 0.4)',
                        'rgba(218, 112, 214, 0.4)',
                        'rgba(144, 238, 144, 0.4)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)',
                        'rgb(123, 239, 178)',
                        'rgb(250, 128, 114)',
                        'rgb(100, 149, 237)',
                        'rgb(218, 112, 214)',
                        'rgb(144, 238, 144)'
                    ],
                    borderWidth: 2
                }]
            };

            const barChart = new Chart(ctx, {
                type: 'bar', // Chart type
                data: barChartData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });



            // Doughnut Chart

            const verifiedCount = <?php echo json_encode($verified); ?>;
            const notVerifiedCount = <?php echo json_encode($notVerified); ?>;



            const ctx1 = document.getElementById('myChart1').getContext('2d');
            const doughnutChartData = {
                labels: ['Verified', 'Pending'],
                datasets: [{
                    label: 'KYC',
                    data: [verifiedCount, notVerifiedCount],
                    backgroundColor: [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)'
                    ],
                    hoverOffset: 4
                }]
            };

            const doughnutChart = new Chart(ctx1, {
                type: 'doughnut',
                data: doughnutChartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });
            
            function downloadexcel(title) {
                let table = document.getElementById("dataTable"); 
                let wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" }); 
                XLSX.writeFile(wb, title+".xlsx");
            }
        </script>
    @endpush
</x-app-layout>
