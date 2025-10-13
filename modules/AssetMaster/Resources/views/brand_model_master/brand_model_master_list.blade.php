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



</style>


    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="card-title h5 custom-dark m-0"><a href="{{ route('admin.asset_management.asset_master.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>Brand Model Master <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{ $brands->count() }}</span></div>
                            
                           
                          
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                           
                            <div class="text-center d-flex gap-2">
                                 <div class="m-2 bg-white p-2 px-3 border-gray">
                                     <!--<a href="{{route('admin.asset_management.brand_model_master.export_brand_model_master', ['status' => $status, 'from_date' => $from_date, 'to_date' => $to_date])}}" class=" bg-white text-dark"><i class="bi bi-download fs-17 me-1"></i> Export</a>-->
                                     
                                    <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                          <i class="bi bi-download fs-17 me-1"></i> Export
                                    </button>
                                    
                                 </div>
                                 
                                 
                                 
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                                <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                    style="cursor: pointer;" onclick="window.location.href='{{route('admin.asset_management.brand_model_master.create_brand_model_master')}}'">
                                    <i class="bi bi-plus-lg me-2 fs-6"></i> Create
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- End Page Header -->
        



        <div class="table-responsive">
                    <table  id="BrandModelMasterTable_List" class="table text-left" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                      <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                                      <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                              <th scope="col" class="custom-dark">Brand Model Name</th>
                              <th scope="col" class="custom-dark">Created At</th>
                              <th scope="col" class="custom-dark">Status</th>
                              <th scope="col" class="custom-dark">Active/In Active</th>
                              <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>

                         


                          
                        <tbody class="bg-white border border-white">
                                  
                      
                                   
                            @if(isset($brands))
                                @foreach($brands as $index => $brand)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="{{ $brand->id }}">
                                    </div>
                                </td>
                                <td>{{ $brand->brand_name ?? '' }}</td>
                                <td > {{ $brand->created_at ? \Carbon\Carbon::parse($brand->created_at)->format('d M Y, h:i A') : '' }}</td>
                            
                                {{-- Status Badge --}}
                                <td>
                                    @php
                                        $status = $brand->status == 1 ? 'active' : 'in active';
                                        $colorClass = match ($status) {
                                            'active' => 'text-success',
                                            'in active' => 'text-danger',
                                            default => 'text-secondary',
                                        };
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-circle-fill {{ $colorClass }}"></i>
                                        <span class="text-capitalize">{{ $status ?? '' }}</span>
                                    </div>
                                </td>
                            
                                {{-- Toggle Switch --}}
                                <td>
                                    <div class="form-check form-switch d-flex justify-content-center align-items-center m-0 p-0">
                                        <input class="form-check-input toggle-status"  data-id="{{ $brand->id }}" type="checkbox" role="switch" id="toggleSwitch{{ $loop->index }}" {{ $brand->status == 1 ? 'checked' : '' }}>
                                    </div>
                                </td>
                            
                                {{-- Action --}}
                                <td class="text-start">
                                    <a href="{{ route('admin.asset_management.brand_model_master.update_brand_model_master', ['id' => $brand->id]) }}" class="text-success" style="font-size: 1.2rem;">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                             
                        </tbody>
                        </table>
                </div>
    </div>
    
      <div class="modal fade" id="export_select_fields_modal" tabindex="-1" aria-labelledby="export_select_fields_modalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <form>
              <div class="modal-content rounded-4">
                <div class="modal-header border-0 d-flex justify-content-between">
                  <div>
                    <h1 class="h3 fs-5 text-center custom-dark" id="export_select_fields_modalLabel">Select Fields</h1>
                  </div>
                  <div>
                      <button class="btn text-white" style="background:#26c360;">Download</button>
                  </div>
                </div>
                <div class="modal-body p-md-3">
                  <div class="row p-4">
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field1">Select All</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field1">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field2">First Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field2">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Last Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field3">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Email ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field4">
                        </div>
                      </div>
                    </div>
                                        
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Gender</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field5">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field6">
                        </div>
                      </div>
                    </div>
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field7">House No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field7">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Street Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field8">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">City</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field9">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field10">Area</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field10">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">Pincode</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field11">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Alternative No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field12">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field13">Role</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field13">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field14">Account Holder Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field14">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field15">Bank Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field15">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field16">IFSC Code</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field16">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field16">Bank Account No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field16">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field17">DOB</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field17">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field18">Present Address</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field18">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field19">Premanent Address</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field19">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field20">Rider ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field20">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field21">Past Experience</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field21">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field22">Father/ Mother/ Guardian</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field22">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field23">Father/ Mother/ Guardian Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field23">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field24">Reference Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field24">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field25">Reference Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field25">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field26">Rerence Relationship</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field26">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field27">Spouse Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field27">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field28">Spouse Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field28">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field29">Blood Group</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field29">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field30">Social Media Link</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field30">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field31">Rider Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field31">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field32">Vehicle Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field32">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field33">Aadhaar No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field33">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field34">Aadhaar Front</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field34">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field35">Aadhaar Back</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field35">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field36">Pan No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field36">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field37">Pan Card</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field37">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field38">Driving license No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field38">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field39">Driving license Front</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field39">
                        </div>
                      </div>
                    </div>
                     
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Driving license Back</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field40">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Bank Details</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field40">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Profile Photo</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field40">
                        </div>
                      </div>
                    </div>
                   
                    
                  </div>
                </div>

              
              </div>
            </form>
          </div>
        </div>
        
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Brand Model Master</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearBrandModelFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyBrandModelFilter()">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Status</h6></div>
               </div>
               <div class="card-body">
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status" value="all"  {{ request('status', 'all') == 'all' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status">
                       All
                      </label>
                    </div>
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status1" value="1" {{ request('status') === '1' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status1">
                       Active
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status2" value="0" {{ request('status') === '0' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status2">
                        Inactive
                      </label>
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$from_date}}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$to_date}}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearBrandModelFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyBrandModelFilter()">Apply</button>
            </div>
            
          </div>
        </div>
    

@section('script_js')


<script>
    
        function applyBrandModelFilter() {
        const selectedStatus = document.querySelector('input[name="status"]:checked');
        const status = selectedStatus ? selectedStatus.value : 'all';
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
        
                if(from_date != "" || to_date != ""){
            if(to_date == "" || from_date == ""){
                toastr.error("From Date and To Date is must be required");
                return;
            }
            
        }
        
             
        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        url.searchParams.set('from_date', from_date);
        url.searchParams.set('to_date', to_date);
    
        window.location.href = url.toString();
    }


    
    function clearBrandModelFilter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('status');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        window.location.href = url.toString();
    }
    
    
    
    
    
    
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


<script>
      document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('field1');
        const checkboxes = document.querySelectorAll('.form-check-input');
    
        selectAll.addEventListener('change', function () {
          checkboxes.forEach(checkbox => {
            if (checkbox !== selectAll) {
              checkbox.checked = selectAll.checked;
            }
          });
        });
    
        // Optional: Update "Select All" if any individual checkbox is unchecked
        checkboxes.forEach(checkbox => {
          if (checkbox !== selectAll) {
            checkbox.addEventListener('change', function () {
              const allChecked = Array.from(checkboxes)
                .filter(cb => cb !== selectAll)
                .every(cb => cb.checked);
              selectAll.checked = allChecked;
            });
          }
        });
      });
</script>

<script>
    
        
        $(document).ready(function () {
       $('#BrandModelMasterTable_List').DataTable({
            // dom: 'Blfrtip',
            // dom: 'frtip',
            // buttons: ['excel', 'pdf', 'print'],
            // order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
            lengthMenu: [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"] ],
            responsive: false,
            scrollX: true,
        });
    });
    
    function SelectExportFields(){
        $("#export_select_fields_modal").modal('show');
    }
    
    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
    }
    
  
</script>


<script>
    $(document).ready(function () {
    $('.toggle-status').change(function (e) {
        e.preventDefault();

        var checkbox = $(this);
        var brandId = checkbox.data('id');
        var intendedStatus = checkbox.is(':checked') ? 1 : 0;

        // Temporarily revert the checkbox until confirmed
        checkbox.prop('checked', !intendedStatus);

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to ${intendedStatus ? 'activate' : 'deactivate'} this brand.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Now update checkbox state immediately after confirmation
                checkbox.prop('checked', intendedStatus);

                // Proceed with AJAX
                $.ajax({
                    url: "{{ route('admin.asset_management.brand_model_master.status_update') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: brandId,
                        status: intendedStatus
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Server error occurred.'
                        });
                    }
                });
            } else {
                // Cancelled - keep checkbox in original state
                checkbox.prop('checked', !intendedStatus);
            }
        });
    });
});

</script>



<script>
  document.getElementById('exportBtn').addEventListener('click', function () {
    const selected = [];
    document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
      selected.push(cb.value);
    });



    const params = new URLSearchParams();
    params.append('status', '{{ request()->status }}');
    params.append('from_date', '{{ $from_date }}');
    params.append('to_date', '{{ $to_date }}');
    
    if (selected.length > 0) {
      params.append('selected_ids', JSON.stringify(selected));
    }

    const url = `{{ route('admin.asset_management.brand_model_master.export_brand_model_master') }}?${params.toString()}`;
    window.location.href = url;
  });
</script>
@endsection
</x-app-layout>
