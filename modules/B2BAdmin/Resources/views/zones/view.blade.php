<x-app-layout>

@section('style_css')
<style>
   
    table thead th{
        background: white !important;
        color: #4b5563 !important;
    }
  
      .datatable-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
        .loading-spinner {
        width: 3rem;
        height: 3rem;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #0f62fe;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
        @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }


    /* ---------- Pagination container: force single-line flex layout ---------- */
    .dataTables_wrapper .dataTables_paginate {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: flex-end; /* change to center if you want it centered */
      flex-wrap: nowrap;
      white-space: nowrap;
      margin-top: 12px;
    }
    
    /* Style Previous / Next only */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 100px;   /* ðŸ”¹ make both equal width */
      height: 40px;
      padding: 8px 16px;  /* ðŸ”¹ more padding for balanced look */
      border-radius: 6px;
      border: none;
      color: #fff !important;
      background-color: #0d6efd;
      cursor: pointer;
      font-weight: 500;
    }
    
    /* Hover */
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
      background-color: #0b5ed7;
    }
    
    /* Disabled */
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
      background-color: #e9ecef;
      color: #6c757d !important;
      cursor: not-allowed;
    }
    
</style>
@endsection

    <div class="main-content">
       
        <div class="card my-4">
            <div class="card-header">
                
                <div class="row g-3 d-flex justify-content-between align-items-center">
                    
                    <div class="col-md-6 col-12 mb-2">
                        <div class="d-flex justify-content-start align-items-center">
                    
                    <div class="px-3 py-2 bg-white">
                        <!-- Client -->
                        <div class="h5 fw-bold custom-dark mb-2">
                            Client Name: {{$data->trade_name ?? ''}}
                        </div>
                    
                        <!-- Info row -->
                        <div class="d-flex flex-wrap align-items-center gap-4 small text-secondary">
                            <!-- Stats -->
                            <div class="d-flex align-items-center gap-2">
                                <span>Total No of Zones: <strong>{{$data->zone_logins_count ?? 0}}</strong></span>
                            </div>
                        </div>
                    </div>

                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                        
                         
                            <a href="{{route('b2b.admin.zone.zone_list')}}" class="btn btn-dark  px-5"><i class="bi bi-arrow-left me-2"></i> Back</a>
                            

                        </div>
                    </div>

                </div>
            </div>
        </div>
        
         <div class="card p-4">
                  <div id="loadingOverlay" class="datatable-loading-overlay">
                        <div class="loading-spinner"></div>
                    </div>
                   <div class="table-responsive">
                    <table id="ZoneList" class="table text-left" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                              <tr>
                                <th class="custom-dark">State</th>
                                <th class="custom-dark">City</th>
                                <th class="custom-dark">Zone</th>
                                <th class="custom-dark">Zone Status</th>
                                <th class="custom-dark">Agent</th>
                              </tr>
                            </thead>
                            
                            <tbody class="border border-white">
                                
                            </tbody>

                        </table>
                     </div>
             
        </div>

    </div>
    
    

   
@section('script_js')



<script>
     $(document).ready(function () {
        $('#loadingOverlay').show();
         table = $('#ZoneList').DataTable({
            pageLength: 25,
            pagingType: "simple",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('b2b.admin.zone.zone_view', $id) }}",
                type: 'GET',
                 data: function (d) {
                },
            
                beforeSend: function () {
                    $('#loadingOverlay').show();
                },
                complete: function () {
                    $('#loadingOverlay').hide();
                },
                error: function (xhr) {
                    $('#loadingOverlay').hide();
                    toastr.error(xhr.responseJSON?.error || 'Failed to load data. Please try again.');
                }
            },
            columns: [
            { data: 0 },
            { data: 1 }, 
            { data: 2 }, 
            { data: 3 }, 
            { data: 4 }, 
            ],
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            scrollX: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            initComplete: function () {
                $('#loadingOverlay').hide();
    
                // Checkbox handling
                $('#ZoneList').on('change', '.sr_checkbox', function () {
                    $('#CSelectAllBtn').prop('checked', $('.sr_checkbox:checked').length === $('.sr_checkbox').length);
                });
    
                $('#CSelectAllBtn').on('change', function () {
                    $('.sr_checkbox').prop('checked', this.checked);
                });
            }
        });
    
        // Error handling for DataTables
        $.fn.dataTable.ext.errMode = 'none';
        $('#ZoneList').on('error.dt', function (e, settings, techNote, message) {
            console.error('DataTables Error:', message);
            $('#loadingOverlay').hide();
            toastr.error('Error loading data. Please try again.');
        });
    
        // Show loading overlay during redraw
        $('#ZoneList').on('preDraw.dt', function () {
            $('#loadingOverlay').show();
        });
    
        $('#ZoneList').on('draw.dt', function () {
            $('#loadingOverlay').hide();
        });
    });

</script>





@endsection
</x-app-layout>
