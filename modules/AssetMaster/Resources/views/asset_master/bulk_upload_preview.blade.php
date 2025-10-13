<!-- Put this once anywhere after Bootstrap is loaded -->
<style>
/* Make every header cell in your custom table pure white */
.table.custom-table thead th {
    background-color:#fff !important;   /* white header */
    color: #333 !important;     
}

#previewToggle:checked + label {
    color: #fff !important;
    border-color: #ccc; /* Optional: subtle border */
}
</style>

<x-app-layout>
    <div class="main-content">

             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.asset_master.bulk_upload')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Preview & Validate
                              </div>
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                                
                       <a href="{{route('admin.asset_management.asset_master.bulk_upload')}}" class="btn btn-dark btn-md">Back</a>
                            </div>
                        </div>

                    </div>
                   
                </div>
            </div>

            <div class="card mb-5">
                <div class="card-body">
                    <h5 class="card-title custom-dark">Upload Preview & Validation</h5>
                    <p class="text-muted custom-dark">Review uploaded files and validate data before sending for approval.</p>
            
                    <div class="border rounded p-3 mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 custom-dark">DB Format (1).xlsx</p>
                            <p class="mb-1 custom-dark">Uploaded by Alan on 6/23/2025</p>
                            <p class="mb-0 custom-dark">5 rows of data</p>
                        </div>
                        <div>
                            <input type="checkbox" class="btn-check" id="previewToggle" autocomplete="off">
                            <label class="btn btn-outline-primary d-flex align-items-center gap-1" for="previewToggle">
                                <i class="bi bi-eye"></i> Preview
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            

            
           <div class="card"  id="previewCard">
                <div class="card-body">
                    <h6 class="card-title custom-dark">Preview: DB Format (1).xlsx</h6>
                    <p class="text-muted custom-secondary">validate the data before submission for approval</p>
            
                    <div>
                        

  <div class="main-content mb-4">
        


        <div class="table-responsive">
                    <table class="table custom-table text-center" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                      <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn">
                                      <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                              <th scope="col" class="custom-dark">QC ID</th>
                              <th scope="col" class="custom-dark">Name</th>
                              <th scope="col" class="custom-dark">Location</th>
                              <th scope="col" class="custom-dark">Chassis No</th>
                              <th scope="col" class="custom-dark">Telematics No</th>
                              <th scope="col" class="custom-dark">Vehicle Type</th>
                              <th scope="col" class="custom-dark">Vehicle Model</th>
                              <th scope="col" class="custom-dark">QC Date and Time</th>
                            </tr>
                          </thead>

                          @php
                                $qcData = [
                                    ['QC1001', 'Technician 001', 'Chennai', 'CH100001', '90879827', '2 Wheeler', 'OLA', '11 May 2025, 10.00 AM', 'Pass'],
                                    ['QC1002', 'Technician 002', 'Bangalore', 'BL100002', '98765432', '4 Wheeler', 'Uber', '12 May 2025, 11.00 AM', 'Fail'],
                                    ['QC1003', 'Technician 003', 'Hyderabad', 'HY100003', '93456789', '2 Wheeler', 'Rapido', '13 May 2025, 9.30 AM', 'Pass'],
                                    ['QC1004', 'Technician 004', 'Mumbai', 'MU100004', '90123456', '3 Wheeler', 'Ola Auto', '14 May 2025, 2.15 PM', 'Fail'],
                                    ['QC1005', 'Technician 005', 'Delhi', 'DE100005', '91234567', '4 Wheeler', 'Uber', '15 May 2025, 4.00 PM', 'Pass'],
                                    ['QC1006', 'Technician 006', 'Pune', 'PU100006', '92345678', '2 Wheeler', 'Bounce', '16 May 2025, 12.30 PM', 'Fail'],
                                    ['QC1007', 'Technician 007', 'Kolkata', 'KO100007', '93456780', '3 Wheeler', 'Rapido', '17 May 2025, 5.00 PM', 'Pass'],
                                    ['QC1008', 'Technician 008', 'Ahmedabad', 'AH100008', '94567891', '4 Wheeler', 'OLA', '18 May 2025, 9.00 AM', 'Fail'],
                                    ['QC1009', 'Technician 009', 'Jaipur', 'JA100009', '95678902', '2 Wheeler', 'Uber Moto', '19 May 2025, 1.00 PM', 'Pass'],
                                    ['QC1010', 'Technician 010', 'Coimbatore', 'CB100010', '96789013', '4 Wheeler', 'Ola', '20 May 2025, 3.30 PM', 'Fail'],
                                ];
                            @endphp


                          
                        <tbody class="bg-white border border-white">
                                  
                      
                                   

                                   @foreach($qcData as $qc)
                                   <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="">
                                            </div>
                                        </td>
                                        <td>{{ $qc[0] }}</td>
                                        <td style="text-align:left;">{{ $qc[1] }}</td>
                                        <td style="text-align:left;">{{ $qc[2] }}</td>
                                        <td style="text-align:left;">{{ $qc[3] }}</td>
                                        <td style="text-align:left;">{{ $qc[4] }}</td>
                                        <td style="text-align:left;">{{ $qc[5] }}</td>
                                        <td style="text-align:left;">{{ $qc[6] }}</td>
                                        <td style="text-align:left;">{{ $qc[7] }}</td>
                                     
                                   </tr>

                                   @endforeach
                             
                        </tbody>
                        </table>
                </div>
    </div>
                
                
                        <div class="col-12 text-end d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-danger w-25">Reject</button>
                            <button type="submit" class="btn btn-success w-25">Approve</button>
                        </div>

                    </div>
                </div>
            </div>
            
            
    </div>
    
   
@section('script_js')
<script>
$(document).ready(function () {
    const $card = $('#previewCard');
    const $toggle = $('#previewToggle');

    // On page load
    if ($toggle.is(':checked')) {
        $card.show();
    } else {
        $card.hide();
    }

    // On change
    $toggle.on('change', function () {
        if ($(this).is(':checked')) {
            $card.show();
        } else {
            $card.hide();
        }
    });
});


</script>

@endsection
</x-app-layout>
