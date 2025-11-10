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
        }    ?>
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
            
          @if (can('recovery_dashboard'))
                <x-admin.nav-link href="{{ route('admin.recovery_management.dashboard') }}">
                    <div class="mr-2">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1 16H7C7.55 16 8 15.55 8 15V7C8 6.45 7.55 6 7 6H1C0.45 6 0 6.45 0 7V15C0 15.55 0.45 16 1 16ZM1 24H7C7.55 24 8 23.55 8 23V19C8 18.45 7.55 18 7 18H1C0.45 18 0 18.45 0 19V23C0 23.55 0.45 24 1 24ZM11 24H17C17.55 24 18 23.55 18 23V15C18 14.45 17.55 14 17 14H11C10.45 14 10 14.45 10 15V23C10 23.55 10.45 24 11 24ZM10 7V11C10 11.55 10.45 12 11 12H17C17.55 12 18 11.55 18 11V7C18 6.45 17.55 6 17 6H11C10.45 6 10 6.45 10 7Z"
                                fill="{{ $routeName == 'admin.recovery_management.dashboard' ? '#52c552' : '#3a3a3a' }}" />
                        </svg>

                    </div>
                    {{ localize('Dashboard') }}
                </x-admin.nav-link>
            @endif
                
               @php
                    $type = request()->route('type');
                    $currentRouteName = request()->route()->getName();
                
                    // Check if current route is the agent list route
                    $isAgentListActive = $currentRouteName === 'admin.recovery_management.agent_list';
                
                    // Specific checks for sub-items
                    $isAll = $isAgentListActive && $type === 'all';
                    $isActiveAgentActive = $isAgentListActive && $type === 'active';
                    $isInactiveAgentActive = $isAgentListActive && $type === 'inactive';
                
                    // Dropdown active only if one of the subroutes is active
                    $isAgentDropdownActive = $isAll || $isActiveAgentActive || $isInactiveAgentActive;
                
                    // Parent icon color
                    $fillColor_AgentList = $isAgentDropdownActive ? '#52c552' : '#3a3a3a';
                
                    // Font color for dropdown items
                    $activeFontColor = '#52c552';
                    $inactiveFontColor = '#8a8e91';
                    
                    $activeBgColor = '#e5f6e5';
                    $inactiveBgColor = '#fff';
                    
                @endphp
                
                <style>
                    .sub-menu li a:hover {
                            background-color: #e5f6e5 !important;
                        }
                    
                </style>
                
                @if (can('recovery_agent_list'))
                <li class="{{ $isAgentDropdownActive ? 'mm-active' : '' }}">
                    <a href="javascript:void(0);" class="has-arrow d-flex align-items-center ">
                        <div class="mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                 viewBox="0 0 30 30" fill="{{ $fillColor_AgentList }}">
                                <path d="M15 8C15 9.65685 13.6569 11 12 11C10.3431 11 9 9.65685 9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8Z"
                                      stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 4C17.6568 4 19 5.34315 19 7C19 8.22309 18.268 9.27523 17.2183 9.7423"
                                      stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.7143 14H10.2857C7.91876 14 5.99998 15.9188 5.99998 18.2857C5.99998 19.2325 6.76749 20 7.71426 20H16.2857C17.2325 20 18 19.2325 18 18.2857C18 15.9188 16.0812 14 13.7143 14Z"
                                      stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17.7143 13C20.0812 13 22 14.9188 22 17.2857C22 18.2325 21.2325 19 20.2857 19"
                                      stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 4C6.34315 4 5 5.34315 5 7C5 8.22309 5.73193 9.27523 6.78168 9.7423"
                                      stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.71429 19C2.76751 19 2 18.2325 2 17.2857C2 14.9188 3.91878 13 6.28571 13"
                                      stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        {{ localize('Agents') }}
                    </a>
                
                    <ul class="sub-menu" aria-expanded="{{ $isAgentDropdownActive ? 'true' : 'false' }}">
                        <li class="">
                            <a href="{{ route('admin.recovery_management.agent_list',['type'=>'all']) }}"
                               style="color: {{ $isAll ? $activeFontColor : $inactiveFontColor }} ; background: {{ $isAll ? $activeBgColor : $inactiveBgColor }}">
                                {{ localize('All') }}
                            </a>
                        </li>
                        <li class="">
                            <a href="{{ route('admin.recovery_management.agent_list',['type'=>'active']) }}"
                               style="color: {{ $isActiveAgentActive ? $activeFontColor : $inactiveFontColor }} ; background: {{ $isActiveAgentActive ? $activeBgColor : $inactiveBgColor }}">
                                {{ localize('Active') }}
                            </a>
                        </li>
                        <li class="">
                            <a href="{{ route('admin.recovery_management.agent_list',['type'=>'inactive']) }}"
                               style="color: {{ $isInactiveAgentActive ? $activeFontColor : $inactiveFontColor }} ; background: {{ $isInactiveAgentActive ? $activeBgColor : $inactiveBgColor }}">
                                {{ localize('Inactive') }}
                            </a>
                        </li>
                    </ul>
                </li>
                @endif


                                
               
                @php
                    $type = request()->route('type');
                    $currentRouteName = request()->route()->getName();
                
                    // Check if current route is the agent list route
                    $isListActive = $currentRouteName === 'admin.recovery_management.list';
                  
                    // Specific checks for sub-items
                    $isAll = $isListActive && $type === 'all';
                    $isPending = $isListActive && $type === 'pending';
                    $isAgentAssigned = $isListActive && $type === 'agent-assigned';
                    $isNotRecovered = $isListActive && $type === 'not-recovered';
                    $isClosed = $isListActive && $type === 'closed';
                
                    // Dropdown active only if one of the subroutes is active
                    $isDropdownActive = $isAll || $isPending || $isAgentAssigned || $isNotRecovered || $isClosed;
                
                    // Parent icon color
                    $fillColor_List = $isDropdownActive ? '#52c552' : 'white';
                
                    // Font color for dropdown items
                    $activeFontColor = '#52c552';
                    $inactiveFontColor = '#8a8e91';
                    
                    $activeBgColor = '#e5f6e5';
                    $inactiveBgColor = '#fff';
                @endphp
                
                @if (can('recovery_request_list'))
                <li class="{{ $isDropdownActive ? 'mm-active' : '' }}">
                    <a href="javascript:void(0);" class="has-arrow d-flex align-items-center ">
                        <div class="mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                 viewBox="0 0 25 25" fill="{{$fillColor_List}}">
                                <!-- Wheels -->
                                <path d="M13.4601 16.6252C14.3343 16.6252 15.0429 15.9163 15.0429 15.0418C15.0429 14.1674 14.3343 13.4585 13.4601 13.4585C12.5861 13.4585 11.8774 14.1674 11.8774 15.0418C11.8774 15.9163 12.5861 16.6252 13.4601 16.6252Z"
                                      fill="{{$fillColor_List}}" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5.54658 16.6252C6.42069 16.6252 7.12929 15.9163 7.12929 15.0418C7.12929 14.1674 6.42069 13.4585 5.54658 13.4585C4.67247 13.4585 3.96387 14.1674 3.96387 15.0418C3.96387 15.9163 4.67247 16.6252 5.54658 16.6252Z"
                                      fill="{{$fillColor_List}}" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        
                                <!-- Truck body -->
                                <path d="M9.50293 9.5L4.75479 2.375M4.75479 2.375L6.3375 10.2917M4.75479 2.375H3.03819C2.88854 2.375 2.7623 2.50244 2.74376 2.67225L2.47985 5.08928C2.97146 5.08928 3.37 5.545 3.37 6.10715C3.37 6.66929 2.97146 7.125 2.47985 7.125C2.09227 7.125 1.71156 6.84177 1.58936 6.44643M15.0425 15.0417C17.1659 15.0417 17.4165 14.307 17.4165 12.2807C17.4165 11.3109 17.4165 10.826 17.2265 10.4166C17.0281 9.98909 16.6706 9.72277 15.9187 9.17787C15.1719 8.63669 14.6409 8.02829 14.135 7.19023C13.4134 5.995 13.0527 5.39739 12.5116 5.0737C11.9706 4.75 11.3324 4.75 10.0561 4.75H9.50293V10.2917"
                                      fill="{{$fillColor_List}}" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        
                                <!-- Base line -->
                                <path d="M3.96365 15.0384C3.96365 15.0384 3.04573 15.0465 2.76084 15.0099C2.52343 14.9149 2.23495 14.692 2.04862 14.5681C1.47885 14.1893 1.5928 14.3449 1.5928 14.0029C1.5928 13.4682 1.58958 11.0882 1.58958 11.0882V10.3282C1.58958 10.2807 1.54075 10.2908 1.90612 10.2965H17.0052M7.12918 15.0431H11.8773"
                                      stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        {{ localize('Recovery Requests') }}
                    </a>
                
                    <ul class="sub-menu" aria-expanded="{{ $isAgentDropdownActive ? 'true' : 'false' }}">
                        <li class="">
                            <a href="{{ route('admin.recovery_management.list',['type'=>'all']) }}"
                               style="color: {{ $isAll ? $activeFontColor : $inactiveFontColor }} ; background: {{ $isAll ? $activeBgColor : $inactiveBgColor }} ">
                                {{ localize('All') }}
                            </a>
                        </li>
                        
                        <li class="">
                            <a href="{{ route('admin.recovery_management.list',['type'=>'pending']) }}"
                               style="color: {{ $isPending ? $activeFontColor : $inactiveFontColor }} ; background: {{ $isPending ? $activeBgColor : $inactiveBgColor }}">
                                {{ localize('Pending') }}
                            </a>
                        </li>
                        <li class="">
                            <a href="{{ route('admin.recovery_management.list',['type'=>'agent-assigned']) }}"
                               style="color: {{ $isAgentAssigned ? $activeFontColor : $inactiveFontColor }} ; background: {{ $isAgentAssigned ? $activeBgColor : $inactiveBgColor }}">
                                {{ localize('Agent Assigned') }}
                            </a>
                        </li>
                        <li class="">
                            <a href="{{ route('admin.recovery_management.list',['type'=>'not-recovered']) }}"
                               style="color: {{ $isNotRecovered ? $activeFontColor : $inactiveFontColor }} ; background: {{ $isNotRecovered ? $activeBgColor : $inactiveBgColor }}">
                                {{ localize('Not Recovered') }}
                            </a>
                        </li>
                        <li class="">
                            <a href="{{ route('admin.recovery_management.list',['type'=>'closed']) }}"
                               style="color: {{ $isClosed ? $activeFontColor : $inactiveFontColor }} ; background: {{ $isClosed ? $activeBgColor : $inactiveBgColor }}">
                                {{ localize('Closed') }}
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                
               <!-- wrtite here-->
            </ul>
        </nav>

    </div>
    


    <!-- sidebar-body -->
</nav>





