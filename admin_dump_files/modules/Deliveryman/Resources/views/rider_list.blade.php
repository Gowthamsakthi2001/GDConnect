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
        
         /* updated by Gowtham Sakthi */
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


    </style>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
               <div class="d-flex justify-content-between">
                    <div>
                        <img src="{{asset('admin-assets/icons/custom/deliveryman.jpg')}}" class="img-fluid rounded"><span class="ps-2">List of Rider</span>
                    </div>
                    <a href="{{ route('admin.Green-Drive-Ev.delivery-man.create') }}" class="btn custom-btn-primary btn-sm">
                            <i class="fa fa-plus-circle"></i>&nbsp;
                            Add Rider
                        </a>
               </div>
            </h2>
        </div>
        <!-- End Page Header -->
        
        <div class="tile">
                <div class="card mb-4">
                    <div class="card-header p-0 m-0"></div>
                    <div class="card-body">
                        <div class="row mb-3">
                             <div class="col-md-4">
                                 <label>Select City</label>
                                <select id="current_city_id" class="form-control custom-select2-field" onchange="DmExportFiler()">
                                    <option value="">Select City</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" {{$city->id == $city_id ? 'selected' : ''}}>{{ $city->city_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                           <div class="col-md-4">
                               <label>Select Zone</label>
                                <select id="zone_id" class="form-control custom-select2-field" onchange="DmExportFiler()">
                                    <option value="">Select Zone</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}" {{$zone->id == $zone_id ? 'selected' : ''}}>{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        
                            <div class="col-md-4">
                                <label>Select Client</label>
                                <select id="client_id" class="form-control custom-select2-field" onchange="DmExportFiler()">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{$client->id == $client_id ? 'selected' : ''}}>{{ $client->client_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                           
                        </div>
                        <div class="row mb-3 ">
                            <div class="col-12 d-flex justify-content-end">
                                   <a class="btn btn-round me-1 btn-md px-4 btn-dark all-filter-export-btn"
                                       data-baseurl="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'all']) }}"
                                       href="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'all', 'city_id' => $city_id, 'zone_id' => $zone_id, 'client_id' => $client_id]) }}">
                                        <i class="bi bi-download"></i> All
                                    </a>
                                    
                                    <a class="btn btn-dark btn-round btn-md me-1 all-filter-export-btn"
                                       data-baseurl="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'pending']) }}"
                                       href="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'pending', 'city_id' => $city_id, 'zone_id' => $zone_id, 'client_id' => $client_id]) }}">
                                        <i class="bi bi-download"></i> Pending
                                    </a>
                                    
                                    <a class="btn btn-dark btn-round btn-md me-1 all-filter-export-btn"
                                       data-baseurl="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'approve']) }}"
                                       href="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'approve', 'city_id' => $city_id, 'zone_id' => $zone_id, 'client_id' => $client_id]) }}">
                                        <i class="bi bi-download"></i> Approved
                                    </a>
                                    
                                    <a class="btn btn-dark btn-round btn-md me-1 all-filter-export-btn"
                                       data-baseurl="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'deny']) }}"
                                       href="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'deny', 'city_id' => $city_id, 'zone_id' => $zone_id, 'client_id' => $client_id]) }}">
                                        <i class="bi bi-download"></i> Rejected
                                    </a>

                             </div>
                                  
                        </div>
                        
                        <div class="table-responsive table-container "> <!-- updated by Gowtham Sakthi -->
                                <div id="loadingOverlay" class="datatable-loading-overlay">
                                    <div class="loading-spinner"></div>
                                </div>
                
                                <table id="deliveryman-table" class="table custom-table text-center" style="width: 100%;">
                                    <thead class="bg-success rounded">
                                        <tr>
                                            <th scope="col" class="text-white">#</th>
                                            <th scope="col" class="text-white">Image</th>
                                            <th scope="col" class="text-white">Deliveryman Name</th>
                                            <th scope="col" class="text-white">GDM ID</th>
                                            <th scope="col" class="text-white">Email ID</th>
                                            <th scope="col" class="text-white">Mobile Number</th>
                                            <th scope="col" class="text-white">Role</th>
                                            <th scope="col" class="text-white">City</th>
                                            <th scope="col" class="text-white">Zone</th>
                                            <th scope="col" class="text-white">Client Name</th>
                                            <th scope="col" class="text-white">Hub Name</th>
                                            <th scope="col" class="text-white">Rider Status</th>
                                            <th scope="col" class="text-white">Last Login Date</th>
                                            <th scope="col" class="text-white">Job Status</th>
                                            <th scope="col" class="text-white">Adhar Verified</th>
                                            <th scope="col" class="text-white">Pan Verified</th>
                                            <th scope="col" class="text-white">Bank Verified</th>
                                            <th scope="col" class="text-white">License Verified</th>
                                            <th scope="col" class="text-white">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white border border-white">
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        
                    </div>
            </div>
        </div>
        
        <div class="modal fade" id="ProfileImage_srcModal" tabindex="-1" aria-labelledby="ProfileImage_srcModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md">
            <form>
              <div class="modal-content rounded-4">
                <div class="modal-header border-0 d-flex justify-content-end">
                  <button type="button" class="btn btn-dark btn-sm btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="d-flex justify-content-center">
                      <img class="img-fluid" id="profile_image_src" src="">
                  </div>
                </div>
               
              </div>
            </form>
          </div>
        </div>
@section('script_js')
    <script>
        
        //updated by Gowtham Sakthi
$(document).ready(function () {
  // Show loading overlay initially
  $("#loadingOverlay").show();

  var table = $("#deliveryman-table").DataTable({
    pageLength: 15,
    pagingType: "simple",
    destroy: true,
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('admin.Green-Drive-Ev.delivery-man.list') }}",
      type: "GET",
      data: function (d) {
        d.city_id = $("#current_city_id").val();
        d.zone_id = $("#zone_id").val();
        d.client_id = $("#client_id").val();
      },
      beforeSend: function () {
        $("#loadingOverlay").show();
      },
      complete: function () {
        $("#loadingOverlay").hide();
      },
      error: function (xhr) {
        console.error("AJAX Error:", xhr.responseText);
        $("#loadingOverlay").hide();
        if (xhr.responseJSON && xhr.responseJSON.error) {
          toastr.error(xhr.responseJSON.error);
        } else {
          toastr.error("Failed to load data. Please try again.");
        }
      },
    },

    columns: [
      {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        orderable: false,
        searchable: false,
        width: "40px",
      },
      {
        data: "image",
        name: "image",
        orderable: false,
        searchable: false,
        render: function (data) {
          return data;
        },
      },
      { data: "deliveryman_name", name: "deliveryman_name" },
      { data: "gdm_id", name: "gdm_id" },
      { data: "email", name: "email" },
      { data: "mobile_number", name: "mobile_number" },
      { data: "role", name: "role" },
      { data: "city", name: "city" },
      { data: "zone", name: "zone" },
      { data: "client_name", name: "client_name" },
      { data: "hub_name", name: "hub_name" },

      {
        data: "rider_status",
        name: "rider_status",
        orderable: false,
        searchable: false,
        render: function (data) {
          return data;
        },
      },
      {
        data: "last_login",
        name: "last_login",
        orderable: false,
        searchable: false,
        width: "160px",
        render: function (data) {
          return data;
        },
      },
      {
        data: "job_status",
        name: "job_status",
        orderable: false,
        searchable: false,
        render: function (data) {
          return data;
        },
      },

      {
        data: "aadhar_verified",
        name: "aadhar_verified",
        orderable: false,
        searchable: false,
        render: function (data) {
          return data;
        },
      },
      {
        data: "pan_verified",
        name: "pan_verified",
        orderable: false,
        searchable: false,
        render: function (data) {
          return data;
        },
      },
      {
        data: "bank_verified",
        name: "bank_verified",
        orderable: false,
        searchable: false,
        render: function (data) {
          return data;
        },
      },
      {
        data: "license_verified",
        name: "license_verified",
        orderable: false,
        searchable: false,
        render: function (data) {
          return data;
        },
      },

      {
        data: "action",
        name: "action",
        orderable: false,
        searchable: false,
        render: function (data) {
          return data;
        },
      },
    ],

    lengthMenu: [
      [15, 25, 50, 100, 250, -1],
      [15, 25, 50, 100, 250, "All"],
    ],
    responsive: false,
    scrollX: true,
    dom: '<"top"lf>rt<"bottom"ip>',

    initComplete: function () {
      $("#loadingOverlay").hide();

      // Improved search with validation
      let searchDelay;
      let lastNotification;
      let lastSearchTerm = "";

      $("#deliveryman-table_filter input")
        .off("keyup")
        .on("keyup", function () {
          const searchTerm = this.value.trim();

          clearTimeout(searchDelay);
          if (lastNotification) {
            toastr.clear(lastNotification);
          }

          if (searchTerm === lastSearchTerm) {
            return;
          }

          if (searchTerm.length > 0 && searchTerm.length < 3) {
            searchDelay = setTimeout(() => {
              lastNotification = toastr.info(
                "Please enter at least 3 characters for better results",
                { timeOut: 2000 }
              );
            }, 500);
            return;
          }

          searchDelay = setTimeout(() => {
            lastSearchTerm = searchTerm;
            table.search(searchTerm).draw();
          }, 400);
        });
    },
  });

  // Error handling
  $.fn.dataTable.ext.errMode = "none";
  $("#deliveryman-table").on(
    "error.dt",
    function (e, settings, techNote, message) {
      console.error("DataTables Error:", message);
      $("#loadingOverlay").hide();
      toastr.error("Error loading data. Please try again.");
    }
  );

  // Show loading when table is being redrawn
  $("#deliveryman-table").on("preDraw.dt", function () {
    $("#loadingOverlay").show();
  });

  // Hide loading when table draw is complete
  $("#deliveryman-table").on("draw.dt", function () {
    $("#loadingOverlay").hide();

    // update a specific element instead of touching every .badge in the page
    var recordsTotal = table.page.info().recordsTotal;
    $("#recordsTotalCount").text(recordsTotal); // safe: only updates this element
  });

  // Filter change handlers
  $("#current_city_id, #zone_id, #client_id").on("change", function () {
    table.ajax.reload();
  });

  // Export button handlers
  $(".all-filter-export-btn").on("click", function (e) {
    e.preventDefault();
    let city_id = $("#current_city_id").val();
    let zone_id = $("#zone_id").val();
    let client_id = $("#client_id").val();

    let baseUrl = $(this).data("baseurl");
    let params = new URLSearchParams({
      city_id: city_id,
      zone_id: zone_id,
      client_id: client_id,
    });

    window.location.href = baseUrl + "?" + params.toString();
  });

  // delegate events for dynamic elements (toggles, etc.)
  // (optional: if you need to handle checkbox changes centrally)
  $(document).on("change", ".toggle-btn-status", function (e) {
    e.preventDefault();

    let checkbox = $(this);
    let id = checkbox.data("id"); // deliveryman ID
    let field = checkbox.data("field"); // which field to update
    let status = checkbox.is(":checked") ? 1 : 0; // checked = 1, unchecked = 0

    // Store original state in case user cancels
    let originalState = !checkbox.is(":checked");

    Swal.fire({
      title: "Are you sure?",
      text: "Do you want to change this status?",
      icon: "warning",
      showCancelButton: true,
      cancelButtonText: "No",
      confirmButtonText: "Yes",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        // ✅ Use GET with query params
        $.ajax({
          url: "{{ route('admin.Green-Drive-Ev.delivery-man.list') }}",
          type: "GET",
          data: {
            id: id,
            field: field,
            status: status,
          },
          success: function (response) {
            if (response.success) {
              Swal.fire("Updated!", response.message, "success");
              $("#deliveryman-table").DataTable().ajax.reload(null, false);
            } else {
              Swal.fire("Error!", "Update failed", "error");
              checkbox.prop("checked", originalState); // revert state
            }
          },
          error: function () {
            Swal.fire("Error!", "Something went wrong!", "error");
            checkbox.prop("checked", originalState); // revert state
          },
        });
      } else {
        // ❌ User canceled → revert checkbox state
        checkbox.prop("checked", originalState);
      }
    });
  });
});

        
    //   document.querySelectorAll('#zone_id, #client_id, #current_city_id').forEach(function(filter) {
    //         filter.addEventListener('change', function() {
    //             let filterName = filter.id;
    //             let filterValue = filter.value;

    //             applyFilter(filterName, filterValue);
    //         });
    //     });


        
    //     function applyFilter() {
    //         const city_id = document.getElementById('current_city_id').value;
    //         const zone_id = document.getElementById('zone_id').value;
    //         const client_id = document.getElementById('client_id').value;
        
    //         const queryParams = new URLSearchParams();
    //         if (city_id) queryParams.append('city_id', city_id);
    //         if (zone_id) queryParams.append('zone_id', zone_id);
    //         if (client_id) queryParams.append('client_id', client_id);
        
    //         const queryString = queryParams.toString();
    //         const url = "{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" + (queryString ? `?${queryString}` : '');
        
    //         console.log("DataTable URL:", url);
        
    //         $('#delivery-man-table').DataTable().ajax.url(url).load();
        
    //         document.querySelectorAll('.all-filter-export-btn').forEach(function (btn) {
    //             const baseUrl = btn.getAttribute('data-baseurl');
    //             const fullUrl = baseUrl + (queryString ? `?${queryString}` : '');
    //             btn.href = fullUrl;
    //             console.log("Updated export URL:", fullUrl);
    //         });
    //     }



    function DmExportFiler(){
       var city_id = $("#current_city_id").val();
       var zone_id = $("#zone_id").val();
       var client_id = $("#client_id").val();
       var url = new URL(window.location.href);
       url.searchParams.set('city_id',city_id);
       url.searchParams.set('zone_id',zone_id);
       url.searchParams.set('client_id',client_id);
       window.location.href = url.toString();
   }
   
    // function status_change_alert(url, message, e) {
    //     e.preventDefault();
    //     Swal.fire({
    //         title: "Are you sure?",
    //         text: message,
    //         icon: 'warning',
    //         showCancelButton: true,
    //         cancelButtonColor: 'default',
    //         confirmButtonColor: '#FC6A57',
    //         cancelButtonText: "No",
    //         confirmButtonText: "Yes",
    //         reverseButtons: true
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             location.href = url;
    //         }
    //     });    }

    //  function route_alert(route, message, title = "Are you sure?") {
    //     Swal.fire({
    //         title: title,
    //         text: message,
    //         icon: 'warning',
    //         showCancelButton: true,
    //         cancelButtonColor: 'default',
    //         confirmButtonColor: '#FC6A57',
    //         cancelButtonText: "No",
    //         confirmButtonText: "Yes",
    //         reverseButtons: true
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             $.ajax({
    //                 url: route,
    //                 type: 'GET',
    //                 success: function (response) {
    //                     if (response.success) {
    //                         Swal.fire('Success!', response.message, 'success');
    //                         setTimeout(function() {
    //                             // location.reload(); 
    //                             $('#delivery-man-table').DataTable().ajax.reload(null, false); 
    //                         }, 1000);
    //                     } else {
    //                         Swal.fire('Error!', response.message, 'error');
    //                     }

    //                 },
    //                 error: function (xhr) {
    //                     Swal.fire('Error!', 'An unexpected error occurred.', 'error');
    //                 }
    //             });
    //         }
    //     });
    // }
    
        function status_change_alert(url, message, e, checkboxElement = null) {
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
            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Please wait...',
                        text: 'Updating status...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Status updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: xhr.responseJSON?.message || 'Something went wrong!',
                    });
                    // If error occurs, revert toggle back
                    if (checkboxElement) {
                        checkboxElement.checked = !checkboxElement.checked;
                    }
                }
            });
        } else {
            // User cancelled — revert toggle state
            if (checkboxElement) {
                checkboxElement.checked = !checkboxElement.checked;
            }
        }
    });
}


    function route_alert(route, message, el) {
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
                $.ajax({
                    url: route,
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            let btn = $(el);
                            let newIcon = response.status == 1
                                ? '<i class="fas fa-undo"></i>'
                                : '<i class="fas fa-trash"></i>';
                            let newText = response.status == 1
                                ? 'Restore'
                                : 'Delete';
                            let newClass = response.status == 1
                                ? 'btn btn-dark-soft btn-outline-dark btn-sm me-1'
                                : 'btn btn-danger-soft btn-sm me-1';
    
                            btn.attr('class', newClass)
                               .attr('title', newText)
                               .html(newIcon);
    
                            let newMessage = `${newText} this Deliveryman`;
                            btn.attr('onclick', `route_alert('${route}', '${newMessage}', this)`);
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                    }
                });
            }
        });
    }
    
    function route_alert_approve(route, message, title = "Are you sure?") {
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

    
    function route_deny(route, message, title = "Are you sure?") {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            input: 'text', // Input field for remarks
            inputPlaceholder: 'Enter remarks here...',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true,
            preConfirm: (remarks) => {
                if (!remarks) {
                    Swal.showValidationMessage('Remarks are required');
                }
                return remarks;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect with remarks as query parameter
                const encodedRemarks = encodeURIComponent(result.value);
                location.href = `${route}?remarks=${encodedRemarks}`;
            }
        });
    }
    
    function route_alert_with_input(route, number) {
        Swal.fire({
            text: "Please enter your message below:",
            input: 'text', // Adds an input field
            inputPlaceholder: 'Type your message here...',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Send",
            reverseButtons: true,
            preConfirm: (inputValue) => {
                if (!inputValue) {
                    Swal.showValidationMessage('Message cannot be empty!');
                }
                return inputValue;
            }
        }).then((result) => {
            if (result.isConfirmed) {   
                // Send the input value to the backend using AJAX
                $.ajax({
                    url: route,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                        message: result.value,
                        number: number
                    },
                    success: function(response) {
                        console.log(response)
                        if (response.status) {
                            Swal.fire('Success!', 'Message sent successfully!', 'success');
                        } else {
                            Swal.fire('Error!', 'Failed to send the message.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    }

    function Profile_Image_View(src){
        $("#ProfileImage_srcModal").modal("show");
        $("#profile_image_src").attr("src",src);
        
    }

</script>
@endsection
</x-app-layout>
