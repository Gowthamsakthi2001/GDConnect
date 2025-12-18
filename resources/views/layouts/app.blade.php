<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- meta manager --}}
    <x-meta-manager />
    {{-- favicon --}}
    <x-favicon />
    {{-- style --}}
    <x-admin.styles />
     @yield('style_css')
    <x-language::localizer />
</head>

<body {{ $attributes->merge(['class' => 'fixed sidebar-mini']) }}>
    <!-- Preloader -->
    <x-admin.preloader />
    <!-- react page -->
    <div id="app">
        <!-- Begin page -->
        <div class="wrapper">
            <!-- start header -->
            <?php
            
                $Modules = \Illuminate\Support\Facades\DB::table('sidebar_modules')->where('status',1)->get();
                $userRole = auth()->user()->role;
                if (!session()->has('active_module_id') && $Modules->count() > 0) {
                    if($userRole == 24){
                        $module = $Modules->where('id', 7)->first();
                        if ($module) {
                            session(['active_module_id' => $module->id]);
                        }else{
                         session(['active_module_id' => $Modules->first()->id]);
                        }
                    }else{
                         session(['active_module_id' => $Modules->first()->id]);
                    }
                   
                }
                $activeModule = $Modules->where('id', session('active_module_id'))->first();
                // dd($activeModule->id,$activeModule);
            ?>
            
            @unless(request()->routeIs('admin.tracking'))
                @if(!empty($activeModule) && $activeModule->id == 1)  <!-- GDC sidebar-->
                     <x-admin.left-sidebar />
                @elseif(!empty($activeModule) && $activeModule->id == 2) <!-- HR Management sidebar-->
                  <x-admin.HR_sidebar />
                @elseif(!empty($activeModule) && $activeModule->id == 3) <!-- Tracking sidebar-->
                  <x-admin.tracking_sidebar />
                @elseif(!empty($activeModule) && $activeModule->id == 4) <!-- Asset Management sidebar-->
                  <x-admin.asset_management_sidebar />
                @elseif(!empty($activeModule) && $activeModule->id == 5) <!-- B2B Admin sidebar-->
                  <x-admin.b2b_admin_sidebar />
                @elseif(!empty($activeModule) && $activeModule->id == 6) <!-- BGV Vendor sidebar-->
                  <x-admin.bgv_sidebar />
                @elseif(!empty($activeModule) && $activeModule->id == 7) <!-- Recovery Manager sidebar-->
                  <x-admin.recovery_manager_sidebar />
                
                @else
                  <x-admin.left-sidebar />
                @endif
            @endunless
            
          
            
            <!-- end header -->
            <div class="content-wrapper">
                <div class="main-content">
                    @unless(request()->routeIs('admin.tracking'))
                        <x-admin.header />
                    @endunless

                    @if(request()->routeIs('admin.tracking'))
                    <div class="body-content" style="margin:0px;padding:0px">
                        {{ $slot }}
                    </div>
                    @else
                    <div class="body-content">
                        {{ $slot }}
                    </div>
                    @endif
                </div>
                <div class="overlay"></div>
                
                @unless(request()->routeIs('admin.tracking'))
                 <x-admin.footer />
            @endunless
            </div>
        </div>
        <!--end  vue page -->
    </div>
    <!-- END layout-wrapper -->

    @stack('modal')
    <x-modal id="delete-modal" :title="localize('Delete Modal')">
        <form action="javascript:void(0);" class="needs-validation" id="delete-modal-form">
            <div class="modal-body">
                <p>{{ localize("Are you sure you want to delete this item? You won't be able to revert this item back!") }}
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ localize('Close') }}</button>
                <button class="btn btn-danger" type="submit" id="delete_submit">{{ localize('Delete') }}</button>
            </div>
        </form>
    </x-modal>

    <!-- start scripts -->
    <x-admin.scripts />
    <!-- end scripts -->
    <x-toster-session />
    @yield('script_js')
  <script>
      $(document).ready(function () {
        $("#AddorUpdateLeaveForm").on("submit", function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = form.serialize();
            let url = $("#Store_Url").val(); 
            $(".text-danger1").text("");
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message); 
                        if(response.reset == true){
                           $("#AddorUpdateLeaveForm")[0].reset(); 
                        }
                        $("#AddOrEditLeaveModal").modal("hide");
                        setTimeout(function() {
                        location.reload(); 
                    }, 1000);
                        
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            $.each(errors, function (key, value) {
                                $("." + key + "_err").text(value[0]);
                            });
                        }
                    } else {
                        toastr.error("The network connection has failed. Please try again.");
                    }
                }
            });
        });
    });
    
     function AddorEditLeaveFunction(id){
        $(".text-danger1").text("");
        document.getElementById("day_or_hourLabel").innerHTML = "Days";
        if (id == 0) {
            $("#EditLeaveId").val('');
            $("#AddOrEditLeaveLabel").text('Enter a Leave Details');
            $('#AddOrEditLeave form').trigger('reset');
            $("#submitBtnLeave").text("Submit");
        }else{
            $("#AddOrEditLeaveLabel").text('Update a Leave Details');
            $("#submitBtnLeave").text("Update");
            
            const url = "{{ route('admin.Green-Drive-Ev.leavemanagement.edit', ['id' => '__id__']) }}".replace('__id__', id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $("#EditLeaveId").val(response.data.id);
                            $("#leave_name").val(response.data.leave_name);
                            $("#short_name").val(response.data.short_name);
                            if(response.data.leave_type == 'day'){
                                document.getElementById("day_or_hourLabel").innerHTML = "Days";
                            }else{
                                document.getElementById("day_or_hourLabel").innerHTML = "Hour (Per Day)";
                            }
                            if (response.data.leave_type != null) {
                                $("#leave_type").val(response.data.leave_type);
                            } else {
                                $("#leave_type").val('').change(); // Trigger change event to ensure UI update
                            }

                            $("#days").val(response.data.days);
                        } else {
                            toastr.error(response.message); 
                        }
                    },
                    error: function(xhr, status, error) {
                         toastr.error("The network connection has failed. Please try again.");
                    }
                });

        }
        $("#AddOrEditLeave").modal('show');
    }
    
    function isNumberKeyNew(evt) {
        var charCode = evt.which || evt.keyCode;
    
        // Allow backspace, delete, arrow keys, and numbers (0-9)
        if (
            charCode === 8 || // Backspace
            charCode === 46 || // Delete
            (charCode >= 37 && charCode <= 40) || // Arrow keys
            (charCode >= 48 && charCode <= 57) // Numbers 0-9
        ) {
            return true;
        }
    
        return false;
    }

  </script>
</body>

</html>
