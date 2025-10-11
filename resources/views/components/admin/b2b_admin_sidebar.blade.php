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
         
          @if (can('b2b_admin_dashboard'))
                <x-admin.nav-link href="{{ route('b2b.admin.dashboard') }}">
                    <div class="mr-2">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1 16H7C7.55 16 8 15.55 8 15V7C8 6.45 7.55 6 7 6H1C0.45 6 0 6.45 0 7V15C0 15.55 0.45 16 1 16ZM1 24H7C7.55 24 8 23.55 8 23V19C8 18.45 7.55 18 7 18H1C0.45 18 0 18.45 0 19V23C0 23.55 0.45 24 1 24ZM11 24H17C17.55 24 18 23.55 18 23V15C18 14.45 17.55 14 17 14H11C10.45 14 10 14.45 10 15V23C10 23.55 10.45 24 11 24ZM10 7V11C10 11.55 10.45 12 11 12H17C17.55 12 18 11.55 18 11V7C18 6.45 17.55 6 17 6H11C10.45 6 10 6.45 10 7Z"
                                fill="{{ $routeName == 'admin.dashboard' ? '#52c552' : '#3a3a3a' }}" />
                        </svg>

                    </div>
                    {{ localize('Dashboard') }}
                </x-admin.nav-link>
            @endif
            
            @php
                $isDeployedAssetActive = request()->routeIs('b2b.admin.deployed_asset.*');
                $fillColor_DeployedAsset = $isDeployedAssetActive ? '#52c552' : '#bbbfc4';
                @endphp
                                  
            @if (can('b2b_admin_deployed_asset_list'))
                <li class="{{ $isDeployedAssetActive ? 'mm-active' : '' }}">
                    <a href="{{ route('b2b.admin.deployed_asset.list') }}" class="d-flex align-items-center">
                        <div class="mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="{{$fillColor_DeployedAsset}}">
                                <path d="M12 6C13.1046 6 14 5.10457 14 4C14 2.89543 13.1046 2 12 2C10.8954 2 10 2.89543 10 4C10 5.10457 10.8954 6 12 6Z" stroke="#1A1A1A" stroke-width="1.5"/>
                                <path d="M10 4H6" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18 4H14" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 20C7.6725 19.9645 6.90036 19.8282 6.42177 19.3045C5.77472 18.5965 5.9693 17.5144 6.35847 15.35L6.96989 11.9497C7.21514 10.5857 7.33777 9.90371 7.69445 9.38625C8.0453 8.87725 8.55358 8.47814 9.15294 8.24104C9.76224 8 10.5082 8 12 8C13.4918 8 14.2378 8 14.8471 8.24104C15.4464 8.47814 15.9547 8.87725 16.3056 9.38625C16.6622 9.90371 16.7849 10.5857 17.0301 11.9497L17.6415 15.35C18.0307 17.5144 18.2253 18.5965 17.5782 19.3045C17.1018 19.8258 16.3345 19.9636 15.018 20" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M12 18V22" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        {{ localize('Deployed Asset List') }}
                    </a>
                </li>
            @endif

                      @php
                $isRiderListActive = request()->routeIs('b2b.admin.rider.*');
                $fillColor_RiderList = $isRiderListActive ? '#52c552' : '#bbbfc4';
                @endphp
                 @if (can('b2b_admin_rider_list'))
                <li class="{{ $isRiderListActive ? 'mm-active' : '' }}">
                    <a href="{{ route('b2b.admin.rider.list') }}" class="d-flex align-items-center">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="{{$fillColor_RiderList}}">
                    <mask id="mask0_4744_11281" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                    <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_4744_11281)">
                    <path d="M5.1905 21.6914C3.89167 21.6914 2.78525 21.2337 1.87125 20.3184C0.957083 19.4031 0.5 18.296 0.5 16.9972C0.5 15.6985 0.957667 14.5921 1.873 13.6779C2.7885 12.7639 3.89558 12.3069 5.19425 12.3069C6.49292 12.3069 7.59933 12.7646 8.5135 13.6799C9.4275 14.5952 9.8845 15.7023 9.8845 17.0012C9.8845 18.2998 9.42683 19.4062 8.5115 20.3204C7.59617 21.2344 6.48917 21.6914 5.1905 21.6914ZM5.19 20.1914C6.07617 20.1914 6.83017 19.8813 7.452 19.2612C8.07383 18.6408 8.38475 17.8876 8.38475 17.0014C8.38475 16.1152 8.07458 15.3612 7.45425 14.7394C6.83392 14.1177 6.08067 13.8069 5.1945 13.8069C4.3085 13.8069 3.55458 14.117 2.93275 14.7372C2.31092 15.3575 2 16.1107 2 16.9969C2 17.8831 2.31017 18.6371 2.9305 19.2589C3.55067 19.8806 4.30383 20.1914 5.19 20.1914ZM11.25 18.7492V13.7779L7.9155 11.0644C7.741 10.9067 7.60858 10.7223 7.51825 10.5112C7.42792 10.3 7.38275 10.0779 7.38275 9.84491C7.38275 9.61191 7.4305 9.39216 7.526 9.18566C7.6215 8.97932 7.75133 8.79407 7.9155 8.62991L10.773 5.77216C10.9475 5.59782 11.1493 5.46549 11.3785 5.37516C11.6078 5.28482 11.8482 5.23966 12.0997 5.23966C12.3512 5.23966 12.5918 5.28482 12.8212 5.37516C13.0506 5.46549 13.2525 5.59782 13.427 5.77216L15.327 7.67216C15.7937 8.13882 16.3222 8.50716 16.9125 8.77716C17.5028 9.04699 18.1282 9.19791 18.7885 9.22991V10.7492C17.909 10.7172 17.0737 10.5258 16.2828 10.1752C15.4917 9.82449 14.7885 9.34149 14.173 8.72616L13.2 7.75291L10.5115 10.4414L12.75 12.7799V18.7492H11.25ZM15.3077 5.44141C14.8217 5.44141 14.4086 5.27124 14.0682 4.93091C13.7279 4.59057 13.5577 4.17741 13.5577 3.69141C13.5577 3.20557 13.7279 2.79249 14.0682 2.45216C14.4086 2.11166 14.8217 1.94141 15.3077 1.94141C15.7936 1.94141 16.2067 2.11166 16.547 2.45216C16.8875 2.79249 17.0578 3.20557 17.0578 3.69141C17.0578 4.17741 16.8875 4.59057 16.547 4.93091C16.2067 5.27124 15.7936 5.44141 15.3077 5.44141ZM18.8057 21.6914C17.5071 21.6914 16.4007 21.2337 15.4865 20.3184C14.5725 19.4031 14.1155 18.296 14.1155 16.9972C14.1155 15.6985 14.5732 14.5921 15.4885 13.6779C16.4038 12.7639 17.5108 12.3069 18.8095 12.3069C20.1083 12.3069 21.2148 12.7646 22.1288 13.6799C23.0429 14.5952 23.5 15.7023 23.5 17.0012C23.5 18.2998 23.0423 19.4062 22.127 20.3204C21.2115 21.2344 20.1044 21.6914 18.8057 21.6914ZM18.8055 20.1914C19.6915 20.1914 20.4454 19.8813 21.0673 19.2612C21.6891 18.6408 22 17.8876 22 17.0014C22 16.1152 21.6898 15.3612 21.0695 14.7394C20.4493 14.1177 19.6962 13.8069 18.81 13.8069C17.9238 13.8069 17.1698 14.117 16.548 14.7372C15.9262 15.3575 15.6152 16.1107 15.6152 16.9969C15.6152 17.8831 15.9254 18.6371 16.5458 19.2589C17.1661 19.8806 17.9193 20.1914 18.8055 20.1914Z" fill="#1A1A1A"/>
                    </g>
                    </svg>

                    </div>
                    {{ localize('Riders List') }}
                </a>
                </li>
                @endif
                
                                      @php
                $isAgentListActive = request()->routeIs('b2b.admin.agent.*');
                $fillColor_AgentList = $isAgentListActive ? '#52c552' : '#bbbfc4';
                @endphp
                
                @if (can('b2b_admin_agent_list'))
                <li class="{{ $isAgentListActive ? 'mm-active' : '' }}">
                        <a href="{{ route('b2b.admin.agent.list') }}" class="d-flex align-items-center">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="{{$fillColor_AgentList}}">
                    <path d="M15 8C15 9.65685 13.6569 11 12 11C10.3431 11 9 9.65685 9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8Z" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 4C17.6568 4 19 5.34315 19 7C19 8.22309 18.268 9.27523 17.2183 9.7423" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.7143 14H10.2857C7.91876 14 5.99998 15.9188 5.99998 18.2857C5.99998 19.2325 6.76749 20 7.71426 20H16.2857C17.2325 20 18 19.2325 18 18.2857C18 15.9188 16.0812 14 13.7143 14Z" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17.7143 13C20.0812 13 22 14.9188 22 17.2857C22 18.2325 21.2325 19 20.2857 19" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 4C6.34315 4 5 5.34315 5 7C5 8.22309 5.73193 9.27523 6.78168 9.7423" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.71429 19C2.76751 19 2 18.2325 2 17.2857C2 14.9188 3.91878 13 6.28571 13" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    </div>
                     
                    {{ localize('Agents List') }}
                </a>
                </li>
                @endif
                
                 @if (can('b2b_admin_dashboard_issue_ticket'))
                <x-admin.nav-link href="{{ route('b2b.admin.dashboard_ticket.list') }}">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.24994 3.36355C8.24994 3.07656 8.24994 2.93307 8.15935 2.84476C8.06875 2.75645 7.92675 2.76008 7.64275 2.76733C7.05451 2.78235 6.52992 2.81039 6.06101 2.86271C5.02531 2.97828 4.17455 3.21963 3.44263 3.76216C2.93948 4.13512 2.50398 4.5968 2.15456 5.12508C1.50929 6.10064 1.31897 7.28333 1.25114 8.84643C1.21876 9.5926 1.84733 10.0938 2.46435 10.0938C3.39625 10.0938 4.22354 10.9064 4.22354 12C4.22354 13.0936 3.39625 13.9062 2.46435 13.9062C1.84732 13.9062 1.21876 14.4074 1.25114 15.1536C1.31897 16.7167 1.50929 17.8994 2.15456 18.8749C2.50398 19.4032 2.93948 19.8649 3.44263 20.2378C4.17455 20.7804 5.02531 21.0217 6.06101 21.1373C6.52992 21.1896 7.05452 21.2176 7.64275 21.2327C7.92675 21.2399 8.06875 21.2435 8.15935 21.1552C8.24994 21.0669 8.24994 20.9234 8.24994 20.6365V3.36355ZM9.74994 21.0532C9.74994 21.1619 9.83804 21.25 9.94672 21.25H14.0533C15.66 21.25 16.9288 21.25 17.9391 21.1373C18.9748 21.0217 19.8255 20.7804 20.5574 20.2378C21.0606 19.8649 21.4961 19.4032 21.8455 18.8749C22.4907 17.8995 22.6811 16.7169 22.7489 15.1541C22.7813 14.4075 22.1523 13.9062 21.5351 13.9062C20.6032 13.9062 19.7759 13.0936 19.7759 12C19.7759 10.9064 20.6032 10.0938 21.5351 10.0938C22.1523 10.0938 22.7813 9.59249 22.7489 8.8459C22.6811 7.28307 22.4907 6.10053 21.8455 5.12508C21.4961 4.5968 21.0606 4.13512 20.5574 3.76216C19.8255 3.21963 18.9748 2.97828 17.9391 2.86271C16.9288 2.74998 15.66 2.74999 14.0533 2.75H9.94677C9.83806 2.75 9.74994 2.83812 9.74994 2.94683V21.0532Z" fill="#1A1A1A"/>
                    </svg>

                    </div>
                    {{ localize('Dashboard Issue Ticket') }}
                </x-admin.nav-link>
                @endif
                
                 @php
                $isDeploymentListActive = request()->routeIs('b2b.admin.deployment_request.*');
                $fillColor_DeploymentList = $isDeploymentListActive ? '#52c552' : '#bbbfc4';
                @endphp
                
                @if (can('b2b_admin_deployment_request_list'))
                <li class="{{ $isDeploymentListActive ? 'mm-active' : '' }}">
                        <a href="{{ route('b2b.admin.deployment_request.list') }}" class="d-flex align-items-center">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="{{$fillColor_DeploymentList}}">
                    <path d="M4 14H6.39482C6.68897 14 6.97908 14.0663 7.24217 14.1936L9.28415 15.1816C9.54724 15.3089 9.83735 15.3751 10.1315 15.3751H11.1741C12.1825 15.3751 13 16.1662 13 17.142C13 17.1814 12.973 17.2161 12.9338 17.2269L10.3929 17.9295C9.93707 18.0555 9.449 18.0116 9.025 17.8064L6.84211 16.7503" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13 16.5L17.5928 15.0889C18.407 14.8352 19.2871 15.136 19.7971 15.8423C20.1659 16.3529 20.0157 17.0842 19.4785 17.3942L11.9629 21.7305C11.4849 22.0063 10.9209 22.0736 10.3952 21.9176L4 20.0199" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15 12H13C11.1144 12 10.1716 12 9.58579 11.4142C9 10.8284 9 9.88562 9 8V6C9 4.11438 9 3.17157 9.58579 2.58579C10.1716 2 11.1144 2 13 2H15C16.8856 2 17.8284 2 18.4142 2.58579C19 3.17157 19 4.11438 19 6V8C19 9.88562 19 10.8284 18.4142 11.4142C17.8284 12 16.8856 12 15 12Z" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13 5H15" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    </div>
                    {{ localize('Deployment Req List') }}
                </a>
                </li>
                 @endif
                 
                @php
                $isServiceListActive = request()->routeIs('b2b.admin.service_request.*');
                $fillColor_ServiceList = $isServiceListActive ? '#52c552' : '#bbbfc4';
                @endphp
                
                 @if (can('b2b_admin_service_list'))
                    <li class="{{ $isServiceListActive ? 'mm-active' : '' }}">
                        <a href="{{ route('b2b.admin.service_request.list') }}" class="d-flex align-items-center">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="{{$fillColor_ServiceList}}">
                    <path d="M11.4584 7.33594L9.16675 11.0026H12.8334L10.5417 14.6693" stroke="#1A1A1A" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M1.83325 9.1945V12.8004C4.45188 12.8004 6.15465 15.6446 4.82887 17.9028L8.0043 19.7057C8.67556 18.5624 9.83768 17.9031 10.9999 17.9028C12.1622 17.9031 13.3243 18.5624 13.9955 19.7057L17.1709 17.9028C15.8451 15.6446 17.5479 12.8004 20.1666 12.8004V9.1945C17.5479 9.1945 15.8437 6.35022 17.1695 4.09197L13.9941 2.28906C13.3231 3.43189 12.1616 4.12219 10.9999 4.12245C9.83823 4.12219 8.67669 3.43189 8.00575 2.28906L4.83032 4.09197C6.15612 6.35022 4.45192 9.1945 1.83325 9.1945Z" stroke="#1A1A1A" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </div>
                    {{ localize('Service List') }}
                </a>
                </li>
                @endif
                
             @php
                $isReturnListActive = request()->routeIs('b2b.admin.return_request.*');
                $fillColor_ReturnList = $isReturnListActive ? '#52c552' : '#bbbfc4';
                @endphp
                
                 @if (can('b2b_admin_return_list'))
                <li class="{{ $isReturnListActive ? 'mm-active' : '' }}">
                        <a href="{{ route('b2b.admin.return_request.list') }}" class="d-flex align-items-center">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="{{$fillColor_ReturnList}}">
                    <path d="M3 13V8H21V13C21 16.7712 21 18.6569 19.8284 19.8284C18.6569 21 16.7712 21 13 21H11C7.22876 21 5.34315 21 4.17157 19.8284C3 18.6569 3 16.7712 3 13Z" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 8L3.86538 6.07692C4.53654 4.58547 4.87211 3.83975 5.55231 3.41987C6.23251 3 7.105 3 8.85 3H15.15C16.895 3 17.7675 3 18.4477 3.41987C19.1279 3.83975 19.4635 4.58547 20.1346 6.07692L21 8" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M12 8V3" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M8.5 13.5H14C15.1046 13.5 16 14.3954 16 15.5C16 16.6046 15.1046 17.5 14 17.5H13M10 11.5L8 13.5L10 15.5" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </div>
                    {{ localize('Return List') }}
                 </a>
                </li>
                 @endif
                 
                @php
                $isRecoveryActive = request()->routeIs('b2b.admin.recovery_request.*');
                $fillColor_RecoveryList = $isRecoveryActive ? '#52c552' : '#bbbfc4';
                @endphp
                
                 @if (can('b2b_admin_recovery_list'))
                    <li class="{{ $isRecoveryActive ? 'mm-active' : '' }}">
                        <a href="{{ route('b2b.admin.recovery_request.list') }}" class="d-flex align-items-center">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="{{$fillColor_RecoveryList}}">
                    <path d="M17.0021 21C18.1063 21 19.0014 20.1046 19.0014 19C19.0014 17.8954 18.1063 17 17.0021 17C15.898 17 15.0029 17.8954 15.0029 19C15.0029 20.1046 15.898 21 17.0021 21Z" stroke="#141B34" stroke-width="1.5"/>
                    <path d="M7.00605 21C8.11019 21 9.00527 20.1046 9.00527 19C9.00527 17.8954 8.11019 17 7.00605 17C5.90192 17 5.00684 17.8954 5.00684 19C5.00684 20.1046 5.90192 21 7.00605 21Z" stroke="#141B34" stroke-width="1.5"/>
                    <path d="M12.0039 12L6.00625 3M6.00625 3L8.00547 13M6.00625 3H3.83792C3.64888 3 3.48943 3.16098 3.46601 3.37547L3.13264 6.42857C3.75363 6.42857 4.25704 7.00421 4.25704 7.71429C4.25704 8.42437 3.75363 9 3.13264 9C2.64307 9 2.16217 8.64223 2.00781 8.14286M19.0012 19C21.6834 19 22 18.072 22 15.5125C22 14.2875 22 13.675 21.76 13.1578C21.5094 12.6178 21.0578 12.2814 20.108 11.5931C19.1647 10.9095 18.494 10.141 17.8549 9.08239C16.9435 7.57263 16.4878 6.81775 15.8043 6.40888C15.1209 6 14.3148 6 12.7026 6H12.0039V13" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5.00651 18.996C5.00651 18.996 3.84703 19.0063 3.48717 18.96C3.18729 18.84 2.82289 18.5585 2.58753 18.402C1.86781 17.9235 2.01175 18.12 2.01175 17.688C2.01175 17.0126 2.00768 14.0063 2.00768 14.0063V13.0463C2.00768 12.9863 1.946 12.9991 2.40752 13.0063H21.4801M9.00508 19.002H15.0027" stroke="#141B34" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    </div>
                    {{ localize('Recovery List') }}
                 </a>
                </li>
                 @endif
                
                                @php
                $isAccidentActive = request()->routeIs('b2b.admin.accident_report.*');
                $fillColor_AccidentList = $isAccidentActive ? '#52c552' : '#bbbfc4';
                @endphp
                
                @if (can('b2b_admin_accident_list'))
                 <li class="{{ $isAccidentActive ? 'mm-active' : '' }}">
                        <a href="{{ route('b2b.admin.accident_report.list') }}" class="d-flex align-items-center">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="{{$fillColor_AccidentList}}">
                    <path d="M7.49991 20.004C7.49991 21.1086 6.60448 22.004 5.49991 22.004C4.39534 22.004 3.49991 21.1086 3.49991 20.004M7.49991 20.004C7.49991 18.8995 6.60448 18.004 5.49991 18.004C4.39534 18.004 3.49991 18.8995 3.49991 20.004M7.49991 20.004H9.49991C10.0522 20.004 10.4999 19.5563 10.4999 19.004V16.0185C10.4999 15.6956 10.3439 15.3925 10.0811 15.2048L6.99991 13.004M3.49991 20.004H1.99991M6.99991 13.004H1.99991M6.99991 13.004L3.79852 8.43046C3.61137 8.1631 3.30554 8.00388 2.97919 8.00391L1.99991 8.004" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16.5 20.004C16.5 21.1086 17.3954 22.004 18.5 22.004C19.6046 22.004 20.5 21.1086 20.5 20.004M16.5 20.004C16.5 18.8995 17.3954 18.004 18.5 18.004C19.6046 18.004 20.5 18.8995 20.5 20.004M16.5 20.004H14.5C13.9477 20.004 13.5 19.5563 13.5 19.004V16.0185C13.5 15.6956 13.656 15.3925 13.9188 15.2048L17 13.004M20.5 20.004H22M17 13.004L20.2014 8.43046C20.3885 8.1631 20.6944 8.00388 21.0207 8.00391L22 8.004M17 13.004H22" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9.50009 10.0038L7.00009 7.44934L9.00009 7.00385L7.59282 3.01059L11.0001 4.50385L12.4529 2.00391L13.5001 6.00385L17.0001 4.98724L15.0347 10.0039" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12.5 10.0039L12 8.00391" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </div>
                    {{ localize('Accident List') }}
                    </a>
                </li>
                @endif
                
                @if (can('b2b_admin_report_list'))
                <x-admin.nav-link href="{{ route('b2b.admin.report.list') }}">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                    <path d="M7 17V13" stroke="#141B34" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M12 17V7" stroke="#141B34" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M17 17V11" stroke="#141B34" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" stroke="#141B34" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    </div>
                    {{ localize('Reports List') }}
                </x-admin.nav-link>
                 @endif
                 
                 @php
                $isZoneActive = request()->routeIs('b2b.admin.zone.*');
                $fillColor_ZoneList = $isZoneActive ? '#52c552' : '#bbbfc4';
                @endphp
                
                @if (can('b2b_admin_zone_list'))
                  <li class="{{ $isZoneActive ? 'mm-active' : '' }}">
                        <a href="{{ route('b2b.admin.zone.zone_list') }}" class="d-flex align-items-center">
                    <div class="mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="{{$fillColor_ZoneList}}">
                    <path d="M13.1977 8H10.8023C7.35836 8 5.03641 11.5806 6.39304 14.7994C6.58202 15.2477 7.0156 15.5385 7.49535 15.5385H8.33892C8.62326 15.5385 8.87111 15.7352 8.94007 16.0157L10.0261 20.4328C10.2525 21.3539 11.0663 22 12 22C12.9337 22 13.7475 21.3539 13.9739 20.4328L15.0599 16.0157C15.1289 15.7352 15.3767 15.5385 15.6611 15.5385H16.5047C16.9844 15.5385 17.418 15.2477 17.607 14.7994C18.9636 11.5806 16.6416 8 13.1977 8Z" stroke="#141B34" stroke-width="1.5"/>
                    <path d="M12 8C13.6569 8 15 6.65685 15 5C15 3.34315 13.6569 2 12 2C10.3431 2 9 3.34315 9 5C9 6.65685 10.3431 8 12 8Z" stroke="#141B34" stroke-width="1.5"/>
                    </svg>
                    </div>
                    {{ localize('Client Zone List') }}
                    </a>
                </li>
                @endif
                
               <!-- wrtite here-->
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





