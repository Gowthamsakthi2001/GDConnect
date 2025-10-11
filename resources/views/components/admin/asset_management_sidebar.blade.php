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
                 @if (module_active('assetmaster') && can('asset_master'))
                     <x-admin.multi-nav>
                        <x-slot name="title">
                            <div>
                                <!-- Asset Management SVG Icon -->
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 3H9V9H3V3Z" fill="#3a3a3a"/>
                                    <path d="M15 3H21V9H15V3Z" fill="#3a3a3a"/>
                                    <path d="M3 15H9V21H3V15Z" fill="#3a3a3a"/>
                                    <path d="M15 15H21V21H15V15Z" fill="#3a3a3a"/>
                                    <path d="M9.5 12H14.5V14H9.5V12Z" fill="#3a3a3a"/>
                                    <path d="M4.5 10.5H7.5V13.5H4.5V10.5Z" fill="#3a3a3a"/>
                                    <path d="M16.5 10.5H19.5V13.5H16.5V10.5Z" fill="#3a3a3a"/>
                                </svg>
                            </div>
                            {{ localize('Asset Management') }}
                        </x-slot>
                        
                        <!--<x-admin.multi-nav>-->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('Model Master Vehicle') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.create') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.list') }}">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        
                        
                        
                        
                        <!--<x-admin.multi-nav> -->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('Model Master Battery') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.modal_master_battery_index') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.modal_master_battery_list') }}">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        <!--<x-admin.multi-nav> -->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('Model Master Charger') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.model_master_charger_index') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.model_master_charger_list') }}">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        <!--<x-admin.multi-nav>-->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('Manufacturer Master') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.manufacturer_master_index') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.manufacturer_master_list') }}">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        <!--<x-admin.multi-nav> -->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('PO Table') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.po_table_index') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.po_table_list') }}">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        <!--<x-admin.multi-nav> -->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('AMS Location Master') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.ams_location_master_index') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.ams_location_master_list') }}">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        <!--<x-admin.multi-nav>-->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('Asset Insurance details') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.asset_insurance_details_index') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.asset_insurance_details_list') }} ">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        <!--<x-admin.multi-nav> -->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('Asset Master - Vehicle') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.asset_status_list_handle') }}">-->
                        <!--        {{ localize('Asset Status Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_index') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_list') }} ">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        <!--<x-admin.multi-nav> -->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('Asset Master - Battery') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_battery_index') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_battery_list') }} ">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        <!--<x-admin.multi-nav> -->
                        <!--    <x-slot name="title">-->
                        <!--        {{ localize('Asset Master - Charger') }}-->
                        <!--    </x-slot>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_charger_index') }}">-->
                        <!--        {{ localize('Create') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--    <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_charger_list') }} ">-->
                        <!--        {{ localize('List') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--</x-admin.multi-nav>-->
                        
                        
                         @if (can('asset_manage_dashboard'))
                         
                        @php
                            $isAssetDashboardActive = request()->routeIs('admin.asset_management.asset_master.dashboard');
                            $fillAssetDashboard = $isAssetDashboardActive ? '#52c552' : '#bbbfc4';
                        @endphp
                        
                        <li class="{{ $isAssetDashboardActive ? 'mm-active' : '' }}" >
                            <a class="text-capitalize {{ $isAssetDashboardActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isAssetDashboardActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.asset_management.asset_master.dashboard')}}" target="_self">
                               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M10.5 13H3.5C3.2 13 3 13.2 3 13.5V20.5C3 20.8 3.2 21 3.5 21H10.5C10.8 21 11 20.8 11 20.5V13.5C11 13.2 10.8 13 10.5 13ZM10 20H4V14H10V20ZM10.5 3H3.5C3.2 3 3 3.2 3 3.5V10.5C3 10.8 3.2 11 3.5 11H10.5C10.8 11 11 10.8 11 10.5V3.5C11 3.2 10.8 3 10.5 3ZM10 10H4V4H10V10ZM20.5 3H13.5C13.2 3 13 3.2 13 3.5V10.5C13 10.8 13.2 11 13.5 11H20.5C20.8 11 21 10.8 21 10.5V3.5C21 3.2 20.8 3 20.5 3ZM20 10H14V4H20V10ZM20.5 16.5H17.5V13.5C17.5 13.2 17.3 13 17 13C16.7 13 16.5 13.2 16.5 13.5V16.5H13.5C13.2 16.5 13 16.7 13 17C13 17.3 13.2 17.5 13.5 17.5H16.5V20.5C16.5 20.8 16.7 21 17 21C17.3 21 17.5 20.8 17.5 20.5V17.5H20.5C20.8 17.5 21 17.3 21 17C21 16.7 20.8 16.5 20.5 16.5Z" fill="{{$fillAssetDashboard}}"/>
                                </svg>
                               Dashboard
                            </a>
                        </li>
                         @endif
                        
                        
                          @if (can('quality_check'))
                          
                          @php
                            $isQualityCheckActive = request()->routeIs('admin.asset_management.quality_check.*');
                            $fillColor = $isQualityCheckActive ? '#52c552' : '#bbbfc4';
                        @endphp

                        <li class="{{ $isQualityCheckActive ? 'mm-active' : '' }}" >
                            <a class="text-capitalize {{ $isQualityCheckActive ? 'submenu-activeclass' : '' }}"  style="color: {{ $isQualityCheckActive ? '#52c552' : '#8a8e91' }}" href="{{route('admin.asset_management.quality_check.list')}}" target="_self">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12.6039 6.69911H5.39621C5.29436 6.69911 5.19669 6.65865 5.12468 6.58664C5.05266 6.51463 5.01221 6.41695 5.01221 6.31511V4.05167C5.01221 3.94983 5.05266 3.85216 5.12468 3.78014C5.19669 3.70813 5.29436 3.66767 5.39621 3.66767H6.77501V2.39999C6.77501 2.29815 6.81546 2.20048 6.88748 2.12846C6.95949 2.05645 7.05716 2.01599 7.15901 2.01599H10.8411C10.9429 2.01599 11.0406 2.05645 11.1126 2.12846C11.1846 2.20048 11.2251 2.29815 11.2251 2.39999V3.66767H12.6039C12.7057 3.66767 12.8034 3.70813 12.8754 3.78014C12.9474 3.85216 12.9879 3.94983 12.9879 4.05167V6.31511C12.9879 6.41695 12.9474 6.51463 12.8754 6.58664C12.8034 6.65865 12.7057 6.69911 12.6039 6.69911ZM5.78021 5.93111H12.2199V4.43567H10.8411C10.7392 4.43567 10.6416 4.39521 10.5696 4.3232C10.4975 4.25119 10.4571 4.15351 10.4571 4.05167V2.78399H7.54301V4.05167C7.54301 4.15351 7.50255 4.25119 7.43054 4.3232C7.35852 4.39521 7.26085 4.43567 7.15901 4.43567H5.78021V5.93111Z" fill="{{$fillColor}}"/>
                            <path d="M15.7114 21.984H2.28842C2.18658 21.984 2.0889 21.9435 2.01689 21.8715C1.94488 21.7995 1.90442 21.7018 1.90442 21.6V5.13595C1.90442 5.03411 1.94488 4.93644 2.01689 4.86442C2.0889 4.79241 2.18658 4.75195 2.28842 4.75195H5.39618C5.49802 4.75195 5.59569 4.79241 5.66771 4.86442C5.73972 4.93644 5.78018 5.03411 5.78018 5.13595C5.78018 5.2378 5.73972 5.33547 5.66771 5.40748C5.59569 5.4795 5.49802 5.51995 5.39618 5.51995H2.67242V21.216H15.3274V18.2251C15.3274 18.1232 15.3678 18.0256 15.4398 17.9535C15.5119 17.8815 15.6095 17.8411 15.7114 17.8411C15.8132 17.8411 15.9109 17.8815 15.9829 17.9535C16.0549 18.0256 16.0954 18.1232 16.0954 18.2251V21.6C16.0954 21.7018 16.0549 21.7995 15.9829 21.8715C15.9109 21.9435 15.8132 21.984 15.7114 21.984ZM15.7114 10.9291C15.6095 10.9291 15.5119 10.8886 15.4398 10.8166C15.3678 10.7446 15.3274 10.6469 15.3274 10.5451V5.51995H12.6039C12.502 5.51995 12.4043 5.4795 12.3323 5.40748C12.2603 5.33547 12.2199 5.2378 12.2199 5.13595C12.2199 5.03411 12.2603 4.93644 12.3323 4.86442C12.4043 4.79241 12.502 4.75195 12.6039 4.75195H15.7114C15.8132 4.75195 15.9109 4.79241 15.9829 4.86442C16.0549 4.93644 16.0954 5.03411 16.0954 5.13595V10.5451C16.0954 10.6469 16.0549 10.7446 15.9829 10.8166C15.9109 10.8886 15.8132 10.9291 15.7114 10.9291Z" fill="{{$fillColor}}"/>
                            <path d="M9.30698 9.22344H2.28842C2.18658 9.22344 2.0889 9.18299 2.01689 9.11097C1.94488 9.03896 1.90442 8.94129 1.90442 8.83944C1.90442 8.7376 1.94488 8.63993 2.01689 8.56792C2.0889 8.4959 2.18658 8.45544 2.28842 8.45544H9.30698C9.40882 8.45544 9.50649 8.4959 9.57851 8.56792C9.65052 8.63993 9.69098 8.7376 9.69098 8.83944C9.69098 8.94129 9.65052 9.03896 9.57851 9.11097C9.50649 9.18299 9.40882 9.22344 9.30698 9.22344ZM9.30698 12.648H2.28842C2.18658 12.648 2.0889 12.6075 2.01689 12.5355C1.94488 12.4635 1.90442 12.3658 1.90442 12.264C1.90442 12.1622 1.94488 12.0645 2.01689 11.9925C2.0889 11.9205 2.18658 11.88 2.28842 11.88H9.30698C9.40882 11.88 9.50649 11.9205 9.57851 11.9925C9.65052 12.0645 9.69098 12.1622 9.69098 12.264C9.69098 12.3658 9.65052 12.4635 9.57851 12.5355C9.50649 12.6075 9.40882 12.648 9.30698 12.648ZM9.30698 16.0586H2.28842C2.18658 16.0586 2.0889 16.0182 2.01689 15.9462C1.94488 15.8742 1.90442 15.7765 1.90442 15.6746C1.90442 15.5728 1.94488 15.4751 2.01689 15.4031C2.0889 15.3311 2.18658 15.2906 2.28842 15.2906H9.30698C9.40882 15.2906 9.50649 15.3311 9.57851 15.4031C9.65052 15.4751 9.69098 15.5728 9.69098 15.6746C9.69098 15.7765 9.65052 15.8742 9.57851 15.9462C9.50649 16.0182 9.40882 16.0586 9.30698 16.0586ZM9.30698 19.4597H2.28842C2.18658 19.4597 2.0889 19.4192 2.01689 19.3472C1.94488 19.2752 1.90442 19.1775 1.90442 19.0757C1.90442 18.9738 1.94488 18.8762 2.01689 18.8042C2.0889 18.7321 2.18658 18.6917 2.28842 18.6917H9.30698C9.40882 18.6917 9.50649 18.7321 9.57851 18.8042C9.65052 18.8762 9.69098 18.9738 9.69098 19.0757C9.69098 19.1775 9.65052 19.2752 9.57851 19.3472C9.50649 19.4192 9.40882 19.4597 9.30698 19.4597ZM15.7116 18.6091C13.3836 18.6091 11.4876 16.7131 11.4876 14.3851C11.4876 12.0571 13.3836 10.1611 15.7116 10.1611C18.0396 10.1611 19.9356 12.0559 19.9356 14.3851C19.9356 16.7143 18.0406 18.6091 15.7116 18.6091ZM15.7116 10.9291C13.806 10.9291 12.2556 12.4795 12.2556 14.3851C12.2556 16.2907 13.806 17.8411 15.7116 17.8411C17.6172 17.8411 19.1676 16.2907 19.1676 14.3851C19.1676 12.4795 17.6172 10.9291 15.7116 10.9291Z" fill="{{$fillColor}}"/>
                            <path d="M14.9451 16.3351C14.8907 16.3352 14.8369 16.3238 14.7872 16.3016C14.7376 16.2794 14.6932 16.2468 14.6571 16.2062L13.5408 14.9503C13.5073 14.9126 13.4816 14.8687 13.4651 14.821C13.4485 14.7734 13.4415 14.7229 13.4445 14.6726C13.4475 14.6222 13.4603 14.573 13.4823 14.5276C13.5043 14.4822 13.5351 14.4416 13.5728 14.4081C13.6105 14.3746 13.6544 14.3489 13.702 14.3323C13.7497 14.3158 13.8001 14.3088 13.8504 14.3118C13.9008 14.3148 13.9501 14.3276 13.9954 14.3496C14.0408 14.3716 14.0814 14.4023 14.1149 14.44L14.9369 15.365L17.3002 12.5709C17.366 12.4932 17.46 12.4447 17.5615 12.4363C17.6629 12.4278 17.7636 12.46 17.8414 12.5258C17.9191 12.5916 17.9676 12.6856 17.976 12.787C17.9845 12.8885 17.9523 12.9892 17.8865 13.067L15.2384 16.2C15.2029 16.2419 15.1588 16.2757 15.1091 16.2991C15.0594 16.3226 15.0053 16.3351 14.9504 16.3358L14.9451 16.3351Z" fill="{{$fillColor}}"/>
                            </svg>
                               Quality Check
                            </a>
                        </li>
                         @endif
                       @php
                            $isAssetMasterActive = false;
                            $fillColor_AssetMaster = '#bbbfc4';
                        
                            if ($routeName != "admin.asset_management.asset_master.dashboard") {
                                $isAssetMasterActive = request()->routeIs('admin.asset_management.asset_master.*');
                                $fillColor_AssetMaster = $isAssetMasterActive ? '#52c552' : '#bbbfc4';
                            }
                        @endphp

                         
                        @if (can('asset_master'))
                            <li class="{{ $isAssetMasterActive ? 'mm-active' : '' }}">
                                <a class="text-capitalize {{ $isAssetMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isAssetMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.asset_management.asset_master.list')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M1.53599 3.456C1.43999 1.44 6.33599 0 12 0C17.664 0 22.464 1.344 22.464 3.456V20.736C22.464 22.656 17.472 24 12 24C6.52799 24 1.53599 22.752 1.53599 20.736V3.456ZM12 0.576C6.14399 0.576 2.20799 2.016 2.20799 3.36C2.20799 4.8 6.14399 6.144 12 6.144C17.856 6.144 21.792 4.8 21.792 3.456C21.792 2.112 17.856 0.576 12 0.576ZM2.20799 20.736C2.49599 22.08 6.43199 23.328 12 23.328C17.568 23.328 21.504 21.984 21.792 20.736V15.552C20.64 16.992 16.8 17.952 12 17.952C7.29599 17.952 3.35999 16.992 2.20799 15.648V20.736ZM2.20799 14.88C2.59199 16.224 6.43199 17.472 12 17.472C17.568 17.472 21.504 16.128 21.792 14.88V14.976V10.176C20.64 11.616 16.8 12.48 12 12.48C7.29599 12.48 3.35999 11.52 2.20799 10.176V14.88ZM2.20799 9.408C2.59199 10.752 6.43199 12 12 12C17.568 12 21.504 10.656 21.792 9.408V9.6V4.416C20.64 5.664 16.704 6.816 12 6.816C7.29599 6.816 3.35999 5.856 2.20799 4.512V9.408Z" fill="{{$fillColor_AssetMaster}}"/>
                                    </svg>
                                   Asset Master
                                </a>
                            </li>
                         @endif
                         
                             @php
                                $isVehicleTransferActive = request()->routeIs('admin.asset_management.vehicle_transfer.*');
                                $fillColor_Vehicle_Transfer = $isVehicleTransferActive ? '#52c552' : '#bbbfc4';
                            @endphp
                         @if (can('vehicle_transfer'))
                            <li class="{{ $isVehicleTransferActive ? 'mm-active' : '' }}">
                                <a class="text-capitalize {{ $isVehicleTransferActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isVehicleTransferActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.asset_management.vehicle_transfer.show')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_Vehicle_Transfer}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                   Vehicle Transfer
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





