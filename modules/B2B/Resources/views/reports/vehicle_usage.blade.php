@extends('layouts.b2b')

@section('css')
<style>
    .filters-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
        margin-bottom: 20px;
    }
    .filters-container select, 
    .filters-container input {
        min-width: 180px;
    }
    .filters-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .table thead th {
        font-size: 14px;
        font-weight: 600;
        background-color: #FAFBFC; /* neutral gray */
        color: #1A1A1A; /* dark text */
        border-bottom: 2px solid #dee2e6;
    }
    .table tbody td {
        font-size: 14px;
        font-weight:400;
        vertical-align: middle;
        color:#1A1A1A;
        border-top: none;   /* remove top borders */
        border-left: none;  /* remove left borders */
        border-right: none; /* remove right borders */
        border-bottom: 1px solid #dee2e6;
    }
    
    .td-text{
        opacity:52%;
    }
    .search-container {
        position: relative;
        display: flex;
        width: 100%;
        max-width: 600px; /* Optional: You can remove this if you want full width without max constraint */
    }
    
    .search-container i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
    }
    
    .search-container input {
        padding-left: 40px;
        border-radius: 4px 0 0 4px;
        height: 35px;
        width: 100%;
    }
    
    table thead th {
        background: white !important;
        color: #4b5563 !important;
    }
    .table tbody td {
        border: none !important; /* remove all borders from tbody cells */
    }
    .table-responsive {
    margin-top: 30px; /* adjust value as needed */
}
    
   
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0" style="font-size:16px;font-weight:600">Vehicle Usage</h5>
        <div class="d-flex gap-2 d-flex justify-content-end">
        <!--<div class="search-container mb-2 mt-2">-->
        <!--    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3"></i>-->
        <!--    <input type="text" style="height:40px; max-width:300px;" class="form-control"  id="vehicleSearch">-->
        <!--</div>-->
        <div class="align-content-center">
            <button class="btn btn-outline-secondary" style="height:40px;" ><i class="bi bi-download"></i>  Export</button>
        </div>
    </div>
    </div>

    <!-- Filters -->
    <div class="filters-container mb-4 d-flex justify-content-between">
        <div>
        <select class="form-select">
            <option>Yesterday</option>
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
        </select>
        </div>
        <div>
        <select class="form-select">
            <option>Select Vehicle Type</option>
            <option>2 Wheeler</option>
            <option>3 Wheeler</option>
            <option>4 Wheeler</option>
        </select>
        </div>
        <div>
        <select class="form-select">
            <option>Select City</option>
            <option>Chennai</option>
            <option>Bangalore</option>
            <option>Mumbai</option>
            <option>Pune</option>
            <option>Delhi</option>
        </select>
        </div>
        <div>
        <select class="form-select">
            <option>Select Vehicles</option>
            <option>AB-01-CD-1234</option>
            <option>AB-02-HG-5485</option>
        </select>
        </div>
        <div class="filters-actions ms-auto">
            <button class="btn btn-danger">Cancel</button>
            <button class="btn btn-primary">Apply</button>
        </div>
    </div>

       <div class="table-responsive ">
        <table id="ColorMasterTable_List" class="table text-left" style="width: 100%;">
            <thead class="bg-white rounded " style="background:white !important; color:black !important;">
                <tr>
                    <th scope="col" class="custom-dark">
                        <div class="form-check">
                            <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                            <label class="form-check-label" for="CSelectAllBtn"></label>
                        </div>
                    </th>
                    <th scope="col" class="custom-dark">Vehicle No </th>
                    <th scope="col" class="custom-dark">Vehicle Type</th>
                    <th scope="col" class="custom-dark">City</th>
                    <th scope="col" class="custom-dark">17-08-2025(Dist.in km)</th>
                    <th scope="col" class="custom-dark">18-08-2025(Dist.in km)</th>
                    <th scope="col" class="custom-dark">19-08-2025(Dist.in km)</th>
                    <th scope="col" class="custom-dark">20-08-2025(Dist.in km)</th>
                    <th scope="col" class="custom-dark">21-08-2025(Dist.in km)</th>
                    <th scope="col" class="custom-dark">22-08-2025(Dist.in km)</th>
                    <th scope="col" class="custom-dark">23-08-2025(Dist.in km)</th>
                    <th scope="col" class="custom-dark">Total Distance(in km)</th>
                    
                </tr>
            </thead>
            
            <tbody class="bg-white border border-white">
                <tr>
                    <td><div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox"></div></td>
                    <td>AB-01-CD-1234</td>
                    <td>2 Wheeler</td>
                    <td>Chennai</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>10.00</td>
                    <td>25.0</td>
                    <td>100.0</td>
                    <td>50.0</td>
                    <td>25.0</td>
                    <td>210.0</td>
                </tr>
                <tr>
                    <td><div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="VH5549"></div></td>
                    <td>AB-02-HG-5485</td>
                    <td>4 Wheeler</td>
                    <td>Bangalore</td>
                    <td>5.00</td>
                    <td>12.00</td>
                    <td>8.00</td>
                    <td>15.0</td>
                    <td>20.0</td>
                    <td>30.0</td>
                    <td>10.0</td>
                    <td>100.0</td>
                </tr>
                <tr>
                    <td><div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox"></div></td>
                    <td>AB-01-CD-1234</td>
                    <td>2 Wheeler</td>
                    <td>Chennai</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>10.00</td>
                    <td>25.0</td>
                    <td>100.0</td>
                    <td>50.0</td>
                    <td>25.0</td>
                    <td>210.0</td>
                </tr>
                <tr>
                    <td><div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="VH5549"></div></td>
                    <td>AB-02-HG-5485</td>
                    <td>4 Wheeler</td>
                    <td>Bangalore</td>
                    <td>5.00</td>
                    <td>12.00</td>
                    <td>8.00</td>
                    <td>15.0</td>
                    <td>20.0</td>
                    <td>30.0</td>
                    <td>10.0</td>
                    <td>100.0</td>
                </tr>
                <tr>
                    <td><div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox"></div></td>
                    <td>AB-01-CD-1234</td>
                    <td>2 Wheeler</td>
                    <td>Chennai</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>10.00</td>
                    <td>25.0</td>
                    <td>100.0</td>
                    <td>50.0</td>
                    <td>25.0</td>
                    <td>210.0</td>
                </tr>
                <tr>
                    <td><div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="VH5549"></div></td>
                    <td>AB-02-HG-5485</td>
                    <td>4 Wheeler</td>
                    <td>Bangalore</td>
                    <td>5.00</td>
                    <td>12.00</td>
                    <td>8.00</td>
                    <td>15.0</td>
                    <td>20.0</td>
                    <td>30.0</td>
                    <td>10.0</td>
                    <td>100.0</td>
                </tr>
            </tbody>
    
    
        
        </table>
       </div>
    
</div>
@endsection

@section('js')
<script>
    console.log("Vehicle Usage Page Loaded");
</script>

<script>
    $(document).ready(function () {
        $('#ColorMasterTable_List').DataTable({
            lengthMenu: [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"] ],
            responsive: false,
            scrollX: true,
            columnDefs: [
                { orderable: false, targets: '_all' }
            ]
        });
    });

    $(document).ready(function () {
        $('#CSelectAllBtn').on('change', function () {
            $('.sr_checkbox').prop('checked', this.checked);
        });

        $('.sr_checkbox').on('change', function () {
            if (!this.checked) {
                $('#CSelectAllBtn').prop('checked', false);
            } else if ($('.sr_checkbox:checked').length === $('.sr_checkbox').length) {
                $('#CSelectAllBtn').prop('checked', true);
            }
        });
    });
</script>
@endsection
