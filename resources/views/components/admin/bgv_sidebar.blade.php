<nav class="sidebar sidebar-bunker sidebar-sticky overflow-hidden">
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-brand">
            <img class="sidebar-logo-lg"
                src="{{ setting('site.logo_black', admin_asset('img/sidebar-logo.png'), true) }}">
            <img class="sidebar-logo-sm" src="{{ setting('site.favicon', admin_asset('img/favicon.png'), true) }}">
        </a>
    </div>

    <!--/.sidebar header-->
    <!--<div class=" sidebar_user_profile d-flex justify-start align-items-center p-3 bg-light my-2">-->
   
    <?php
        $user = \App\Models\User::find(auth()->id());
        if ($user && !empty($user->profile_photo_path)) {
            $img = str_starts_with($user->profile_photo_path, 'http') 
                ? $user->profile_photo_path 
                : asset('/uploads/users/' . $user->profile_photo_path);
        } else {
            $img = asset('storage/setting/byQpJL3dVU32cdP6xIpHNL2MTi9AtXu0UfPdJTuG.png');
        }
    ?>
     <?php 
        // Role Verification
        $db = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('model_id', auth()->user()->id)
            ->first();
            
        $roles = DB::table('roles')
            ->where('id', $db->role_id)
            ->first();
            
        $auth_user = auth()->user();

    ?>
    
    <!--/.sidebar header-->
    <div class="sidebar-body">
        <nav class="sidebar-nav">
              <?php
                $routeName = request()->route()->getName();
              ?>
            <ul class="metismenu text-capitalize">
                <x-admin.nav-link href="javascript:void(0);">
                    <input type="text" id="sidebar_searchdata" class="form-control form-control-sm" placeholder="Search Here...">
                </x-admin.nav-link>
         
                @if (module_active('BgvVendor') && can('bgv_vendor'))
                 
                        <?php
                            $routeName5 = request()->route()->getName();
                            $isActive5 = \Illuminate\Support\Str::is('admin.Green-Drive-Ev.bgvvendor.*', $routeName5);
                            // dd($isActive5);
                            $BGVfillColor = $isActive5 ? '#52c552' : '#3a3a3a';
                        ?>
                 
                    <x-admin.multi-nav onclick="bgv_dashboard_redirect()">
                        <x-slot name="title">
                            <div class="mr-2 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24"  fill="{{$BGVfillColor}}">
                                  <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                  <path  fill="{{$BGVfillColor}}" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
                                </svg>
                            </div>
                            {{ localize('BGV Vendor ') }}
                        </x-slot>
                        @if(can('bgv_verification_list'))
                                 <?php
                                    $BGVListAddClass = $routeName == 'admin.Green-Drive-Ev.bgvvendor.*' ? true : false;
                                    $BGVListfillColor = $routeName == 'admin.Green-Drive-Ev.bgvvendor.*' ? '#52c552' : '#3a3a3a';
                                ?>
                           <li class="{{ $routeName == 'admin.Green-Drive-Ev.bgvvendor.summary' ? 'mm-active' : '' }} {{ $routeName == 'admin.Green-Drive-Ev.bgvvendor.bgv_list' ? 'mm-active' : '' }} {{ $routeName == 'admin.Green-Drive-Ev.bgvvendor.dashboard' ? 'mm-active' : '' }} {{ $routeName == 'admin.Green-Drive-Ev.bgvvendor.recruiter_query_list' ? 'mm-active' : '' }} {{ $routeName == 'admin.Green-Drive-Ev.bgvvendor.dashboard_filter_data' ? 'mm-active' : '' }}">
                                <a class="text-capitalize {{$BGVListAddClass == true ? 'submenu-activeclass' : ''}}" href="{{ route('admin.Green-Drive-Ev.bgvvendor.summary') }}" target="_self">
                                   
                                    
                                    <svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 12c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V20h14v-2.5c0-2.33-4.67-3.5-7-3.5zM18 10c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-12 0c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm12 2.5c-1.19 0-3.17.39-4.73 1.12.81.54 1.45 1.23 1.91 1.96H20v-1c0-1.33-2.67-2.5-4-2.5zM6 12.5c-1.33 0-4 1.17-4 2.5v1h5.82c.46-.73 1.1-1.42 1.91-1.96C9.17 12.89 7.19 12.5 6 12.5z" fill="{{ $BGVListfillColor }}"/>
                                    </svg>

                                    BGV
                                </a>
                            </li>
                        @endif
                    </x-admin.multi-nav>
                @endif
            </ul>
        </nav>
        <!--<div class="mt-auto p-3 sidebar-logout">-->
        <!--    <x-logout>-->
        <!--        <span class="btn btn-dark w-100"> <img class="me-2"-->
        <!--                src="{{ admin_asset('img/logout.png') }}"><span>{{ localize('Logout') }}</span></span>-->
        <!--    </x-logout>-->
        <!--</div>-->
    </div>
    


    <!-- sidebar-body -->
</nav>





