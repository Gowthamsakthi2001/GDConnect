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
                @if(isset($auth_user) && $auth_user->role != "12")
                <x-admin.nav-link href="{{ route('admin.dashboard') }}">
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

            
                
                    @if (module_active('Clients') && (can('Clients') || can('client_edit')))
                    <?php
                    //   dd(request()->route()->getName());
                    ?>
                    <x-admin.multi-nav>
                        <x-slot name="title">
                            <div>
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 12c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V20h14v-2.5c0-2.33-4.67-3.5-7-3.5zM18 10c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-12 0c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm12 2.5c-1.19 0-3.17.39-4.73 1.12.81.54 1.45 1.23 1.91 1.96H20v-1c0-1.33-2.67-2.5-4-2.5zM6 12.5c-1.33 0-4 1.17-4 2.5v1h5.82c.46-.73 1.1-1.42 1.91-1.96C9.17 12.89 7.19 12.5 6 12.5z" fill="#3a3a3a"/>
                                </svg>
                            </div>
                            {{'Clients & Hub' }}
                        </x-slot>
                        @if (module_active('permission') && can('client_create'))
                             <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.clients.create') }}">
                                    {{ localize('Client Create') }}
                            </x-admin.nav-link>
                        @endif
                        @if (module_active('permission') && can('client_list'))
                             <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.clients.list') }}">
                                    {{ localize('Client List') }}
                            </x-admin.nav-link>
                        @endif
                        <!--@if(request()->routeIs('admin.Green-Drive-Ev.clients.list'))-->
                        <!--    @if (module_active('permission') && can('client_list'))-->
                        <!--         <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.clients.list') }}">-->
                        <!--                {{ localize('Client List') }}-->
                        <!--        </x-admin.nav-link>-->
                        <!--    @endif-->
                        <!--@elseif(request()->routeIs('admin.Green-Drive-Ev.clients.edit'))-->
                        <!--    @if (module_active('permission') && can('client_eidt'))-->
                        <!--         <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.clients.list') }}">-->
                        <!--                {{ localize('Client List2') }}-->
                        <!--        </x-admin.nav-link>-->
                        <!--    @endif-->
                        <!--@else-->
                        <!--     @if (module_active('permission') && can('client_list'))-->
                        <!--         <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.clients.list') }}">-->
                        <!--                {{ localize('Client List1') }}-->
                        <!--        </x-admin.nav-link>-->
                        <!--    @endif-->
                        <!--@endif-->
                        
    
                        @if (module_active('permission') && can('hub_create'))
                             <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.clients.hub.create') }}">
                                    {{ localize('Hub Create') }}
                            </x-admin.nav-link>
                        @endif
                        @if (module_active('permission') && (can('hub_list') || can('hub_edit')))
                            <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.clients.hub.list') }}">
                                {{ localize('Hub List') }}
                            </x-admin.nav-link> 
                        @endif
                    </x-admin.multi-nav>
                @endif


                     
                     @if (module_active('mastermanagement') && can('master_management'))
                         <x-admin.multi-nav>
                            <x-slot name="title">
                                <div>
                                    <!-- Master Management SVG Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <g clip-path="url(#clip0_1920_18263)">
                                    <path d="M13.125 23.25H10.875C10.5766 23.25 10.2905 23.1315 10.0795 22.9205C9.86853 22.7095 9.75 22.4234 9.75 22.125V20.7113C8.93476 20.5033 8.1532 20.1806 7.42875 19.7528L6.43012 20.7514C6.21637 20.9558 5.93201 21.0699 5.63625 21.0699C5.34049 21.0699 5.05612 20.9558 4.84237 20.7514L3.24863 19.1576C3.14424 19.0534 3.06143 18.9297 3.00493 18.7935C2.94843 18.6573 2.91934 18.5112 2.91934 18.3638C2.91934 18.2163 2.94843 18.0702 3.00493 17.934C3.06143 17.7978 3.14424 17.6741 3.24863 17.5699L4.24725 16.5712C3.81942 15.8468 3.4967 15.0652 3.28875 14.25H1.875C1.57663 14.25 1.29048 14.1315 1.0795 13.9205C0.868526 13.7095 0.75 13.4234 0.75 13.125V10.875C0.75 10.5766 0.868526 10.2905 1.0795 10.0795C1.29048 9.86853 1.57663 9.75 1.875 9.75H3.28875C3.4967 8.93476 3.81942 8.1532 4.24725 7.42875L3.24863 6.43012C3.14424 6.32594 3.06143 6.2022 3.00493 6.06598C2.94843 5.92975 2.91934 5.78373 2.91934 5.63625C2.91934 5.48877 2.94843 5.34274 3.00493 5.20652C3.06143 5.0703 3.14424 4.94656 3.24863 4.84237L4.84237 3.24863C5.05612 3.04421 5.34049 2.93012 5.63625 2.93012C5.93201 2.93012 6.21637 3.04421 6.43012 3.24863L7.42875 4.24725C8.1532 3.81942 8.93476 3.4967 9.75 3.28875V1.875C9.75 1.57663 9.86853 1.29048 10.0795 1.0795C10.2905 0.868526 10.5766 0.75 10.875 0.75H13.125C13.4234 0.75 13.7095 0.868526 13.9205 1.0795C14.1315 1.29048 14.25 1.57663 14.25 1.875V3.28875C15.0652 3.49647 15.8467 3.81921 16.5709 4.24725L17.5699 3.24863C17.7807 3.03864 18.0662 2.92074 18.3638 2.92074C18.6613 2.92074 18.9468 3.03864 19.1576 3.24863L20.7514 4.84237C20.8558 4.94656 20.9386 5.0703 20.9951 5.20652C21.0516 5.34274 21.0807 5.48877 21.0807 5.63625C21.0807 5.78373 21.0516 5.92975 20.9951 6.06598C20.9386 6.2022 20.8558 6.32594 20.7514 6.43012L19.7531 7.42875C20.1809 8.15321 20.5037 8.93477 20.7116 9.75H22.125C22.4234 9.75 22.7095 9.86853 22.9205 10.0795C23.1315 10.2905 23.25 10.5766 23.25 10.875V13.125C23.25 13.4234 23.1315 13.7095 22.9205 13.9205C22.7095 14.1315 22.4234 14.25 22.125 14.25H20.7116C20.5037 15.0652 20.1809 15.8468 19.7531 16.5712L20.7514 17.5699C20.8558 17.6741 20.9386 17.7978 20.9951 17.934C21.0516 18.0702 21.0807 18.2163 21.0807 18.3638C21.0807 18.5112 21.0516 18.6573 20.9951 18.7935C20.9386 18.9297 20.8558 19.0534 20.7514 19.1576L19.1576 20.7514C18.9468 20.9614 18.6613 21.0793 18.3638 21.0793C18.0662 21.0793 17.7807 20.9614 17.5699 20.7514L16.5709 19.7528C15.8467 20.1808 15.0652 20.5035 14.25 20.7113V22.125C14.25 22.4234 14.1315 22.7095 13.9205 22.9205C13.7095 23.1315 13.4234 23.25 13.125 23.25ZM7.37287 18.9037C7.44394 18.9038 7.51352 18.9241 7.5735 18.9622C8.38006 19.4765 9.27093 19.8447 10.2052 20.0497C10.2886 20.068 10.3632 20.1142 10.4168 20.1806C10.4704 20.247 10.4998 20.3297 10.5 20.415V22.125C10.5 22.2245 10.5395 22.3198 10.6098 22.3902C10.6802 22.4605 10.7755 22.5 10.875 22.5H13.125C13.2245 22.5 13.3198 22.4605 13.3902 22.3902C13.4605 22.3198 13.5 22.2245 13.5 22.125V20.415C13.5 20.3295 13.5292 20.2465 13.5828 20.1799C13.6364 20.1132 13.7112 20.0669 13.7948 20.0486C14.7291 19.8435 15.6199 19.4753 16.4265 18.9611C16.4985 18.9154 16.5839 18.8956 16.6686 18.905C16.7533 18.9143 16.8323 18.9522 16.8926 19.0125L18.1001 20.22C18.171 20.2881 18.2655 20.3261 18.3638 20.3261C18.462 20.3261 18.5565 20.2881 18.6274 20.22L20.2211 18.6262C20.2558 18.5917 20.2834 18.5507 20.3022 18.5055C20.321 18.4602 20.3306 18.4118 20.3306 18.3628C20.3306 18.3139 20.321 18.2654 20.3022 18.2202C20.2834 18.175 20.2558 18.1339 20.2211 18.0994L19.0125 16.8926C18.9522 16.8323 18.9143 16.7533 18.905 16.6686C18.8956 16.5839 18.9154 16.4985 18.9611 16.4265C19.4752 15.6199 19.8434 14.729 20.0486 13.7948C20.0669 13.7112 20.1132 13.6364 20.1799 13.5828C20.2465 13.5292 20.3295 13.5 20.415 13.5H22.125C22.2245 13.5 22.3198 13.4605 22.3902 13.3902C22.4605 13.3198 22.5 13.2245 22.5 13.125V10.875C22.5 10.7755 22.4605 10.6802 22.3902 10.6098C22.3198 10.5395 22.2245 10.5 22.125 10.5H20.415C20.3295 10.4999 20.2467 10.4707 20.1801 10.4171C20.1135 10.3635 20.0673 10.2887 20.049 10.2052C19.8437 9.27098 19.4756 8.38014 18.9615 7.5735C18.9158 7.50153 18.896 7.41615 18.9053 7.33142C18.9147 7.24668 18.9526 7.16766 19.0129 7.10737L20.2204 5.89987C20.2551 5.86534 20.2826 5.82429 20.3014 5.77908C20.3202 5.73387 20.3299 5.6854 20.3299 5.63644C20.3299 5.58748 20.3202 5.539 20.3014 5.49379C20.2826 5.44858 20.2551 5.40753 20.2204 5.373L18.6266 3.77887C18.5557 3.71081 18.4613 3.6728 18.363 3.6728C18.2647 3.6728 18.1703 3.71081 18.0994 3.77887L16.8919 4.98637C16.8316 5.04664 16.7526 5.08457 16.6678 5.09391C16.5831 5.10325 16.4977 5.08344 16.4257 5.03775C15.6192 4.52353 14.7283 4.15541 13.794 3.95025C13.7108 3.93184 13.6363 3.88563 13.5829 3.81924C13.5294 3.75285 13.5002 3.67023 13.5 3.585V1.875C13.5 1.77554 13.4605 1.68016 13.3902 1.60984C13.3198 1.53951 13.2245 1.5 13.125 1.5H10.875C10.7755 1.5 10.6802 1.53951 10.6098 1.60984C10.5395 1.68016 10.5 1.77554 10.5 1.875V3.585C10.5 3.67053 10.4708 3.75349 10.4172 3.82012C10.3636 3.88676 10.2888 3.93307 10.2052 3.95138C9.27093 4.15646 8.38006 4.52459 7.5735 5.03888C7.50153 5.08457 7.41615 5.10438 7.33142 5.09504C7.24668 5.0857 7.16766 5.04777 7.10737 4.9875L5.89987 3.78C5.82901 3.71205 5.73462 3.6741 5.63644 3.6741C5.53825 3.6741 5.44387 3.71205 5.373 3.78L3.77887 5.37375C3.70935 5.44342 3.67031 5.53783 3.67031 5.63625C3.67031 5.73467 3.70935 5.82908 3.77887 5.89875L4.98637 7.10663C5.04664 7.16691 5.08457 7.24593 5.09391 7.33067C5.10325 7.4154 5.08344 7.50078 5.03775 7.57275C4.52347 8.37931 4.15534 9.27018 3.95025 10.2045C3.93214 10.288 3.88606 10.3628 3.81964 10.4165C3.75322 10.4703 3.67044 10.4997 3.585 10.5H1.875C1.77554 10.5 1.68016 10.5395 1.60984 10.6098C1.53951 10.6802 1.5 10.7755 1.5 10.875V13.125C1.5 13.2245 1.53951 13.3198 1.60984 13.3902C1.68016 13.4605 1.77554 13.5 1.875 13.5H3.585C3.67053 13.5 3.75349 13.5292 3.82012 13.5828C3.88676 13.6364 3.93307 13.7112 3.95138 13.7948C4.15646 14.7291 4.52459 15.6199 5.03888 16.4265C5.08457 16.4985 5.10438 16.5839 5.09504 16.6686C5.0857 16.7533 5.04777 16.8323 4.9875 16.8926L3.78 18.1001C3.71048 18.1698 3.67143 18.2642 3.67143 18.3626C3.67143 18.4611 3.71048 18.5555 3.78 18.6251L5.37375 20.2192C5.44448 20.2867 5.53849 20.3244 5.63625 20.3244C5.73401 20.3244 5.82802 20.2867 5.89875 20.2192L7.10663 19.0118C7.14173 18.9771 7.18331 18.9498 7.229 18.9312C7.27468 18.9127 7.32357 18.9034 7.37287 18.9037Z" fill="#3a3a3a"/>
                                    <path d="M12 18.375C10.7391 18.375 9.50661 18.0011 8.45824 17.3006C7.40988 16.6001 6.59278 15.6045 6.11027 14.4396C5.62776 13.2747 5.50152 11.9929 5.7475 10.7563C5.99348 9.51967 6.60064 8.38376 7.4922 7.4922C8.38376 6.60064 9.51967 5.99348 10.7563 5.7475C11.9929 5.50152 13.2747 5.62776 14.4396 6.11027C15.6045 6.59278 16.6001 7.40988 17.3006 8.45824C18.0011 9.50661 18.375 10.7391 18.375 12C18.3731 13.6902 17.7009 15.3106 16.5057 16.5057C15.3106 17.7009 13.6902 18.3731 12 18.375ZM12 6.375C10.8875 6.375 9.79995 6.7049 8.87492 7.32299C7.94989 7.94107 7.22892 8.81957 6.80318 9.84741C6.37744 10.8752 6.26604 12.0062 6.48309 13.0974C6.70013 14.1885 7.23586 15.1908 8.02253 15.9775C8.8092 16.7641 9.81148 17.2999 10.9026 17.5169C11.9938 17.734 13.1248 17.6226 14.1526 17.1968C15.1804 16.7711 16.0589 16.0501 16.677 15.1251C17.2951 14.2001 17.625 13.1125 17.625 12C17.6233 10.5087 17.0301 9.07892 15.9756 8.02439C14.9211 6.96986 13.4913 6.37669 12 6.375Z" fill="#3a3a3a"/>
                                    <path d="M13.5454 8.57968C13.4459 8.57965 13.3506 8.54013 13.2802 8.4698C12.9404 8.1308 12.48 7.94041 12 7.94041C11.52 7.94041 11.0596 8.1308 10.7197 8.4698C10.649 8.53811 10.5543 8.57591 10.456 8.57505C10.3576 8.5742 10.2636 8.53476 10.1941 8.46523C10.1245 8.3957 10.0851 8.30165 10.0842 8.20333C10.0834 8.105 10.1212 8.01028 10.1895 7.93955C10.6701 7.46022 11.3212 7.19104 12 7.19104C12.6788 7.19104 13.3299 7.46022 13.8105 7.93955C13.8629 7.992 13.8986 8.05881 13.9131 8.13154C13.9276 8.20427 13.9201 8.27966 13.8917 8.34817C13.8634 8.41668 13.8153 8.47525 13.7537 8.51645C13.692 8.55766 13.6195 8.57966 13.5454 8.57968Z" fill="#3a3a3a"/>
                                    <path d="M12.75 9.37497C12.6506 9.37495 12.5552 9.33542 12.4849 9.26509C12.3562 9.13672 12.1818 9.06463 12 9.06463C11.8182 9.06463 11.6438 9.13672 11.5151 9.26509C11.4444 9.3334 11.3497 9.3712 11.2514 9.37035C11.153 9.36949 11.059 9.33005 10.9895 9.26052C10.9199 9.191 10.8805 9.09694 10.8796 8.99862C10.8788 8.90029 10.9166 8.80557 10.9849 8.73484C11.2543 8.46596 11.6194 8.31494 12 8.31494C12.3806 8.31494 12.7457 8.46596 13.0151 8.73484C13.0676 8.78729 13.1033 8.8541 13.1177 8.92683C13.1322 8.99956 13.1248 9.07495 13.0964 9.14346C13.068 9.21198 13.02 9.27054 12.9583 9.31174C12.8967 9.35295 12.8242 9.37495 12.75 9.37497ZM16.53 11.4075L15.3113 10.7962C14.4243 10.3559 13.4477 10.1262 12.4575 10.125H11.625C11.3341 10.1257 11.0472 10.1938 10.787 10.3239C10.5267 10.454 10.3002 10.6426 10.125 10.875L9.54001 11.655L8.69251 11.7975C8.25398 11.8687 7.85522 12.094 7.5679 12.4328C7.28059 12.7717 7.12355 13.2019 7.12501 13.6462V14.25C7.12501 14.3494 7.16452 14.4448 7.23484 14.5151C7.30517 14.5855 7.40055 14.625 7.50001 14.625H8.31751C8.3943 14.8442 8.53731 15.0341 8.72676 15.1685C8.91621 15.3029 9.14274 15.375 9.37501 15.375C9.60728 15.375 9.83381 15.3029 10.0233 15.1685C10.2127 15.0341 10.3557 14.8442 10.4325 14.625H13.5675C13.6468 14.8517 13.797 15.047 13.9958 15.1819C14.1946 15.3168 14.4315 15.3842 14.6715 15.3741C14.9115 15.364 15.142 15.277 15.3288 15.1259C15.5155 14.9748 15.6488 14.7676 15.7088 14.535L16.62 14.2312C16.6823 14.2102 16.738 14.1731 16.7814 14.1237C16.8248 14.0744 16.8545 14.0144 16.8675 13.95L17.13 12.6337C17.1783 12.3918 17.1457 12.1408 17.0373 11.9192C16.9289 11.6976 16.7506 11.5178 16.53 11.4075ZM13.035 10.9087C13.5752 10.9669 14.1043 11.102 14.6063 11.31L14.1375 11.625H12.855L13.035 10.9087ZM10.725 11.325C10.83 11.1855 10.966 11.0723 11.1221 10.9942C11.2783 10.9161 11.4504 10.8753 11.625 10.875H12.27L12.0825 11.625H10.5L10.725 11.325ZM9.37501 14.625C9.30084 14.625 9.22834 14.603 9.16667 14.5618C9.105 14.5206 9.05694 14.462 9.02856 14.3935C9.00017 14.325 8.99275 14.2496 9.00722 14.1768C9.02169 14.1041 9.0574 14.0372 9.10984 13.9848C9.16229 13.9324 9.22911 13.8966 9.30185 13.8822C9.37459 13.8677 9.44999 13.8751 9.51852 13.9035C9.58704 13.9319 9.64561 13.98 9.68681 14.0416C9.72802 14.1033 9.75001 14.1758 9.75001 14.25C9.75001 14.3494 9.7105 14.4448 9.64018 14.5151C9.56985 14.5855 9.47447 14.625 9.37501 14.625ZM14.625 14.625C14.5508 14.625 14.4783 14.603 14.4167 14.5618C14.355 14.5206 14.3069 14.462 14.2786 14.3935C14.2502 14.325 14.2427 14.2496 14.2572 14.1768C14.2717 14.1041 14.3074 14.0372 14.3598 13.9848C14.4123 13.9324 14.4791 13.8966 14.5519 13.8822C14.6246 13.8677 14.7 13.8751 14.7685 13.9035C14.837 13.9319 14.8956 13.98 14.9368 14.0416C14.978 14.1033 15 14.1758 15 14.25C15 14.3494 14.9605 14.4448 14.8902 14.5151C14.8198 14.5855 14.7245 14.625 14.625 14.625ZM16.395 12.4875L16.1738 13.5862L15.6375 13.7662C15.5425 13.5653 15.39 13.3972 15.1992 13.2832C15.0084 13.1692 14.788 13.1146 14.566 13.1263C14.3441 13.1379 14.1306 13.2154 13.9528 13.3487C13.775 13.482 13.6409 13.6652 13.5675 13.875H10.4325C10.3557 13.6558 10.2127 13.4658 10.0233 13.3314C9.83381 13.1971 9.60728 13.1249 9.37501 13.1249C9.14274 13.1249 8.91621 13.1971 8.72676 13.3314C8.53731 13.4658 8.3943 13.6558 8.31751 13.875H7.87501V13.6462C7.87407 13.3794 7.96836 13.1211 8.1409 12.9176C8.31344 12.7141 8.55291 12.5789 8.81626 12.5362L9.78001 12.375H14.25C14.3236 12.3751 14.3956 12.3529 14.4563 12.3112L15.4013 11.6812L16.1963 12.0787C16.2696 12.1157 16.3287 12.1757 16.3646 12.2496C16.4005 12.3234 16.4112 12.407 16.395 12.4875Z" fill="#3a3a3a"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_1920_18263">
                                    <rect width="24" height="24" fill="white"/>
                                    </clipPath>
                                    </defs>
                                    </svg>
                                </div>
                                {{ localize('Master Management') }}
                            </x-slot>
                            
                            
                                @php
                                    $SidebarModuleActive = request()->routeIs('admin.Green-Drive-Ev.master_management.sidebar_module*');
                                    $fillColor_SidebarModule = $SidebarModuleActive ? '#52c552' : '#bbbfc4';
                                @endphp
                                 
                                 @if (can('sidebar_modules'))
                                <li class="{{ $SidebarModuleActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $SidebarModuleActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$SidebarModuleActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.sidebar_module.index')}}" target="_self">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_SidebarModule}}">
                                        <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                        </svg>
                                       Sidebar Modules
                                    </a>
                                </li>
                                 @endif
                                 
                                @php
                                    $StateTab = request()->routeIs('admin.Green-Drive-Ev.State*');
                                    $fillColor_StateTab = $StateTab ? '#52c552' : '#bbbfc4';
                                @endphp
                            
                             @if (module_active('city') && can('state'))
                                <li class="{{ $StateTab ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $StateTab == true ? 'submenu-activeclass' : '' }}" style="color:{{$StateTab == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.State.list')}}" target="_self">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_StateTab}}">
                                        <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                        </svg>
                                        States
                                    </a>
                                </li>
                            @endif
                                 
                                 @php
                                    $CityActive = request()->routeIs('admin.Green-Drive-Ev.City*');
                                    $fillColor_CityActive = $CityActive ? '#52c552' : '#bbbfc4';
                                @endphp
                                    
                                @if (module_active('city') && can('city'))
                                <li class="{{ $CityActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $CityActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$CityActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.City.list')}}" target="_self">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_CityActive}}">
                                        <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                        </svg>
                                        City
                                    </a>
                                </li>
                                @endif
                                
                                @php
                                    $AreaActive = request()->routeIs('admin.Green-Drive-Ev.Area*');
                                    $fillColor_AreaActive = $AreaActive ? '#52c552' : '#bbbfc4';
                                @endphp
                                    
                                @if (module_active('city') && can('area'))
                                <li class="{{ $AreaActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $AreaActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$AreaActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.Area.list')}}" target="_self">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_AreaActive}}">
                                        <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                        </svg>
                                        Area
                                    </a>
                                </li>
                                @endif
                                
                                 @php
                                    $ZoneTab = request()->routeIs('admin.Green-Drive-Ev.zone*');
                                    $fillColor_ZoneTab = $ZoneTab ? '#52c552' : '#bbbfc4';
                                @endphp
                            
                                 @if (module_active('zones') && can('zone'))
                                    <li class="{{ $ZoneTab ? 'mm-active' : '' }}">
                                        <a class="text-capitalize {{ $ZoneTab == true ? 'submenu-activeclass' : '' }}" style="color:{{$ZoneTab == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.zone.list')}}" target="_self">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_ZoneTab}}">
                                            <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                            </svg>
                                            Zones
                                        </a>
                                    </li>
                                @endif
                                
                                @php
                                    $RiderCategory = request()->routeIs('admin.Green-Drive-Ev.rider-type*');
                                    $fillColor_RiderCategory = $RiderCategory ? '#52c552' : '#bbbfc4';
                                @endphp
                                    
                                @if (module_active('ridertype') && can('rider'))
                                    <li class="{{ $RiderCategory ? 'mm-active' : '' }}">
                                        <a class="text-capitalize {{ $RiderCategory == true ? 'submenu-activeclass' : '' }}" style="color:{{$RiderCategory == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.rider-type.list')}}" target="_self">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_RiderCategory}}">
                                            <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                            </svg>
                                            Rider Category
                                        </a>
                                    </li>
                                @endif
                                
                            
                                @php
                                    $isVehicleTypeActive = request()->routeIs('admin.vehicle.type.*');
                                    $fillColor_VehicleType = $isVehicleTypeActive ? '#52c552' : '#bbbfc4';
                                @endphp
                                 
                               @if (can('vehicle_types'))
                                <li class="{{ $isVehicleTypeActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isVehicleTypeActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isVehicleTypeActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.vehicle.type.index')}}" target="_self">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_VehicleType}}">
                                        <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                        </svg>
                                       Vehicle Types
                                    </a>
                                </li>
                                 @endif
                                 
                                   @php
                                    $isVehicleModelActive = request()->routeIs('admin.asset_management.vehicle_model_master.*');
                                    $fillColor_VehicleModel = $isVehicleModelActive ? '#52c552' : '#bbbfc4';
                                @endphp
                                 
                              @if (can('vehicle_model_master'))
                                <li class="{{ $isVehicleModelActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isVehicleModelActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isVehicleModelActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.asset_management.vehicle_model_master.list')}}" target="_self">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_VehicleModel}}">
                                        <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                        </svg>
                                       Vehicle Model Master
                                    </a>
                                </li>
                                 @endif
                             
                             
                             
                             @php
                                    $isBrandModelActive = request()->routeIs('admin.asset_management.brand_model_master.*');
                                    $fillColor_BrandModel = $isBrandModelActive ? '#52c552' : '#bbbfc4';
                                @endphp
                         @if (can('brand_model_master'))
                            <li class="{{ $isBrandModelActive ? 'mm-active' : '' }}">
                                <a class="text-capitalize {{ $isBrandModelActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isBrandModelActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.asset_management.brand_model_master.list')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_BrandModel}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                   Brand Model Master
                                </a>
                            </li>
                             @endif
                             
                             

                             
                             
                             @php
                                $isLocationMasterActive = request()->routeIs('admin.asset_management.location_master.*');
                                $fillColor_LocationMaster = $isLocationMasterActive ? '#52c552' : '#bbbfc4';
                            @endphp
                             
                       @if (can('location_master'))
                            <li class="{{ $isLocationMasterActive ? 'mm-active' : '' }}">
                                <a class="text-capitalize {{ $isLocationMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isLocationMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.asset_management.location_master.list')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_LocationMaster}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                   Location Master
                                </a>
                            </li>
                        @endif
                        
                        @php
                            $isQC_CheckListActive = request()->routeIs('admin.asset_management.quality_check_list.*');
                            $fillColor_QC_Check_List = $isQC_CheckListActive ? '#52c552' : '#bbbfc4';
                        @endphp
                        
                         @if (can('quality_check_list'))
                            <li class="{{ $isQC_CheckListActive ? 'mm-active' : '' }}">
                                <a class="text-capitalize {{ $isQC_CheckListActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isQC_CheckListActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.asset_management.quality_check_list.index')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_QC_Check_List}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                   Quality Check Lists
                                </a>
                            </li>
                        @endif
                            
                                @if (can('telemetric_master'))
                                
                                    @php
                                    $isTelematricMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.telematric_oem_master.*');
                                    $fillColor_Telematric_master = $isTelematricMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                        
                                <li class="{{ $isTelematricMasterActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isTelematricMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isTelematricMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.telematric_oem_master.list')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_Telematric_master}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                       Telemetric OEM
                                    </a>
                                </li>
                                @endif
                                
                              @if (can('financing_type_master'))
                                
                                    @php
                                    $isFinancingTypeMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.financing_type_master.*');
                                    $fillColor_FinancingTypeMaster = $isFinancingTypeMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                        
                                <li class="{{ $isFinancingTypeMasterActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isFinancingTypeMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isFinancingTypeMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.financing_type_master.list')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_FinancingTypeMaster}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                       Financing  Types
                                    </a>
                                </li>
                                
                                @endif
                                
                                @if (can('asset_ownership_master'))
                                
                                    @php
                                    $isAssetOwnershipMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.asset_ownership_master.*');
                                    $fillColor_AssetOwnershipMaster = $isAssetOwnershipMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                        
                                <li class="{{ $isAssetOwnershipMasterActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isAssetOwnershipMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isAssetOwnershipMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.asset_ownership_master.list')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_AssetOwnershipMaster}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                       Asset Ownership
                                    </a>
                                </li>
                                
                                @endif
                                
                                @if (can('hypothecation_master'))
                                
                                    @php
                                    $isHypothecationMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.hypothecation.*');
                                    $fillColor_HypothecationMaster = $isHypothecationMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                    
                                <li class="{{ $isHypothecationMasterActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isHypothecationMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isHypothecationMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.hypothecation.index')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_HypothecationMaster}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                       Hypothecations
                                    </a>
                                </li>
                                
                                
                                @endif
                                
                                @if (can('insurer_name_master'))
                                
                                    @php
                                    $isInsurerNameMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.insurer_name.*');
                                    $fillColor_InsurerNameMaster = $isInsurerNameMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                        
                                <li class="{{ $isInsurerNameMasterActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isInsurerNameMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isInsurerNameMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.insurer_name.index')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_InsurerNameMaster}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                       Insurer Names
                                    </a>
                                </li>
                                
                                
                                @endif
                                @if (can('insurance_type_master'))
                                    @php
                                    $isInsuranceTypeMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.insurance_type.*');
                                    $fillColor_InsuranceTypeMaster = $isInsuranceTypeMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                                  
                        
                                <li class="{{ $isInsuranceTypeMasterActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isInsuranceTypeMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isInsuranceTypeMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.insurance_type.index')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_InsuranceTypeMaster}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                       Insurance Types
                                    </a>
                                </li>
                                
                                @endif
                                 @if (can('registration_type_master'))
                                
                                    @php
                                    $isRegistrationTypeMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.registration_type.*');
                                    $fillColor_RegistrationTypeMaster = $isRegistrationTypeMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                        
                                <li class="{{ $isRegistrationTypeMasterActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isRegistrationTypeMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isRegistrationTypeMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.registration_type.index')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_RegistrationTypeMaster}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                       Registration Types
                                    </a>
                                </li>
                                
                                @endif
                                
                              @if (can('inventory_location_master'))
                                
                                    @php
                                    $isInventoryLocationMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.inventory_location_master.*');
                                    $fillColor_InventoryLocationMaster = $isInventoryLocationMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                        
                                <li class="{{ $isInventoryLocationMasterActive ? 'mm-active' : '' }}">
                                    <a class="text-capitalize {{ $isInventoryLocationMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isInventoryLocationMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.inventory_location_master.index')}}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_InventoryLocationMaster}}">
                                    <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                    </svg>
                                       Inventory Locations
                                    </a>
                                </li>
                                
                                @endif
                                
                                
                              
                                @php
                                    $isCustomerMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.customer_master.*');
                                    $fillColor_CustomerMaster = $isCustomerMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                                @if (can('inventory_location_master'))
                                    <li class="{{ $isCustomerMasterActive ? 'mm-active' : '' }}">
                                        <a class="text-capitalize {{ $isCustomerMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isCustomerMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.customer_master.index')}}" target="_self">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_CustomerMaster}}">
                                        <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                        </svg>
                                           Customer Master
                                        </a>
                                    </li>
                                 @endif
                                
                                
                                 @php
                                    $isCustomerTypeMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.customer_type_master.*');
                                    $fillColor_CustomerTypeMaster = $isCustomerTypeMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                                @if (can('customer_type_master'))
                                    <li class="{{ $isCustomerTypeMasterActive ? 'mm-active' : '' }}">
                                        <a class="text-capitalize {{ $isCustomerTypeMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isCustomerTypeMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.customer_type_master.index')}}" target="_self">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_CustomerTypeMaster}}">
                                        <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                        </svg>
                                           Customer Types
                                        </a>
                                    </li>
                                 @endif
                                 
                                 
                                
                                 @php
                                    $isColorMasterActive = request()->routeIs('admin.Green-Drive-Ev.master_management.color_master.*');
                                    $fillColor_ColorMaster = $isColorMasterActive ? '#52c552' : '#bbbfc4';
                                  @endphp
                                @if (can('color_master'))
                                    <li class="{{ $isColorMasterActive ? 'mm-active' : '' }}">
                                        <a class="text-capitalize {{ $isColorMasterActive == true ? 'submenu-activeclass' : '' }}" style="color:{{$isColorMasterActive == true ? '#52c552' : '#8a8e91'}}" href="{{route('admin.Green-Drive-Ev.master_management.color_master.index')}}" target="_self">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="{{$fillColor_ColorMaster}}">
                                        <path d="M5.75 15.25H23.75V17.75H5.75V20.499L0.416992 16.5L5.75 12.5V15.25ZM23.582 4.5L18.25 8.49902V5.75H0.25V3.25H18.25V0.5L23.582 4.5Z" stroke="#4B5563" stroke-width="0.5"/>
                                        </svg>
                                           Color Master
                                        </a>
                                    </li>
                                 @endif
                                 
                                 
                        
                     </x-admin.multi-nav>
                     @endif
                     
                     
                      <x-admin.multi-nav>
                            <x-slot name="title">
                                <div>
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M21 7V17C21 18.1 20.1 19 19 19H5C3.9 19 3 18.1 3 17V7C3 5.9 3.9 5 5 5H19C20.1 5 21 5.9 21 7ZM19 7H5V17H19V7ZM7 9H17V11H7V9ZM7 13H17V15H7V13Z" fill="#3a3a3a"/>
                                </svg>

                                </div>
                                {{ localize('Ticket Management') }}
                            </x-slot>
                        
                            
                            
                            <!--<li >-->
                            <!--    <a class="text-capitalize" style="color:#8a8e91"  href="{{ route('admin.ticket_management.list', ['type' => 'all']) }}"  target="_self">-->
                            <!--       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">-->
                            <!--        <path d="M10.5 13H3.5C3.2 13 3 13.2 3 13.5V20.5C3 20.8 3.2 21 3.5 21H10.5C10.8 21 11 20.8 11 20.5V13.5C11 13.2 10.8 13 10.5 13ZM10 20H4V14H10V20ZM10.5 3H3.5C3.2 3 3 3.2 3 3.5V10.5C3 10.8 3.2 11 3.5 11H10.5C10.8 11 11 10.8 11 10.5V3.5C11 3.2 10.8 3 10.5 3ZM10 10H4V4H10V10ZM20.5 3H13.5C13.2 3 13 3.2 13 3.5V10.5C13 10.8 13.2 11 13.5 11H20.5C20.8 11 21 10.8 21 10.5V3.5C21 3.2 20.8 3 20.5 3ZM20 10H14V4H20V10ZM20.5 16.5H17.5V13.5C17.5 13.2 17.3 13 17 13C16.7 13 16.5 13.2 16.5 13.5V16.5H13.5C13.2 16.5 13 16.7 13 17C13 17.3 13.2 17.5 13.5 17.5H16.5V20.5C16.5 20.8 16.7 21 17 21C17.3 21 17.5 20.8 17.5 20.5V17.5H20.5C20.8 17.5 21 17.3 21 17C21 16.7 20.8 16.5 20.5 16.5Z" fill="#bbbfc4"/>-->
                            <!--        </svg>-->
                            <!--       All-->
                            <!--    </a>-->
                            <!--</li>-->
                            
                           <li >
                                <a class="text-capitalize" style="color:#8a8e91"  href="{{ route('admin.ticket_management.list', ['type' => 'pending']) }}" target="_self">
                                    <!-- Pending (Clock) -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke="#bbbfc4" stroke-width="2"/>
                                        <path d="M12 6v6l4 2" stroke="#bbbfc4" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                   Pending
                                </a>
                            </li>
                            
                           <li >
                                <a class="text-capitalize" style="color:#8a8e91"  href="{{ route('admin.ticket_management.list', ['type' => 'assigned']) }}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="8" r="4" stroke="#bbbfc4" stroke-width="2"/>
                                        <path d="M4 20c0-4 8-4 8-4s8 0 8 4" stroke="#bbbfc4" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                   Assigned
                                </a>
                            </li>
                            
                               <li >
                                <a class="text-capitalize" style="color:#8a8e91"  href="{{ route('admin.ticket_management.list', ['type' => 'work_in_progress']) }}" target="_self">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#bbbfc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                      <path d="M12 2a10 10 0 1 0 10 10"/>
                                      <path d="M22 2v6h-6"/>
                                    </svg>

                                   Inprogress
                                </a>
                            </li>
                            
                            <li >
                                <a class="text-capitalize" style="color:#8a8e91"  href="{{ route('admin.ticket_management.list', ['type' => 'hold']) }}" target="_self">
                                    <!-- Hold (Pause Circle) -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke="#bbbfc4" stroke-width="2"/>
                                        <rect x="9" y="7" width="2" height="10" fill="#bbbfc4"/>
                                        <rect x="13" y="7" width="2" height="10" fill="#bbbfc4"/>
                                    </svg>
                                   Hold
                                </a>
                            </li>
                            
                            
                             <li >
                                <a class="text-capitalize" style="color:#8a8e91"  href="{{ route('admin.ticket_management.list', ['type' => 'closed']) }}" target="_self">
                                    <!-- Closed (Check Circle) -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke="#bbbfc4" stroke-width="2"/>
                                        <path d="M7 12l3 3 7-7" stroke="#bbbfc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                   Closed
                                </a>
                            </li>
                        
                            
                        </x-admin.multi-nav>
                            
                            
                    <!--<x-admin.nav-link href="#">-->
                    <!--    <div>-->
                    <!--        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                                <!-- Car 1 -->
                    <!--            <path d="M3 16C3 15.4477 3.44772 15 4 15H6.5L7.4 13H16.6L17.5 15H20C20.5523 15 21 15.4477 21 16V19C21 19.5523 20.5523 20 20 20H19C18.4477 20 18 19.5523 18 19H6C6 19.5523 5.55228 20 5 20H4C3.44772 20 3 19.5523 3 19V16Z" fill="#3a3a3a"/>-->
                    <!--            <circle cx="6.5" cy="18" r="1.5" fill="#3a3a3a"/>-->
                    <!--            <circle cx="17.5" cy="18" r="1.5" fill="#3a3a3a"/>-->
                    <!--            <path d="M7.5 13L5.5 8H18.5L16.5 13H7.5Z" fill="#3a3a3a"/>-->

                                <!-- Car 2 -->
                    <!--            <path d="M3 6C3 5.44772 3.44772 5 4 5H6.5L7.4 3H16.6L17.5 5H20C20.5523 5 21 5.44772 21 6V9C21 9.55228 20.5523 10 20 10H19C18.4477 10 18 9.55228 18 9H6C6 9.55228 5.55228 10 5 10H4C3.44772 10 3 9.55228 3 9V6Z" fill="#3a3a3a"/>-->
                    <!--            <circle cx="6.5" cy="8" r="1.5" fill="#3a3a3a"/>-->
                    <!--            <circle cx="17.5" cy="8" r="1.5" fill="#3a3a3a"/>-->
                    <!--            <path d="M7.5 5L5.5 0H18.5L16.5 5H7.5Z" fill="#3a3a3a"/>-->
                    <!--        </svg>-->
                    <!--    </div>-->
                    <!--    {{ localize('Fleet Management') }                    <!--</x-admin.nav-link>-->
               
              
                <!-- Vehicle Management -->
                @if (module_active('VehicleManagement') &&
                        (can('vehicle_management') ||
                            can('vehicle_type_management') ||
                            can('vehicle_rta_office_management') ||
                            can('vehicle_ownership_type_management') ||
                            can('document_type_management') ||
                            can('legal_document_management')))
                    <x-admin.multi-nav>
                        <x-slot name="title">
                            <div>
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.4918 7.00008C10.1962 6.99965 9.90121 7.00098 9.60565 7.00386C8.01722 7.03138 6.419 7.06997 4.84544 7.28978C4.3024 7.56581 4.02391 8.17179 3.68474 8.65119C3.07151 9.6773 2.78616 10.8564 2.44829 11.9894C2.30655 12.1435 2.20961 12.3579 2.05066 12.4874C1.90848 12.453 2.06096 12.1323 2.02605 11.9799C2.10928 11.5092 1.54587 11.2226 1.15315 11.4024C0.755676 11.556 0.153352 11.5966 2.14938e-05 12.0651C-0.0039533 12.5964 0.544065 12.9942 1.05658 12.9286C1.29249 12.9671 1.39243 13.261 1.53753 13.4228C1.54986 14.2809 1.35484 15.1411 1.40309 16.0055C1.42787 16.5524 1.37311 17.1277 1.54132 17.6528C1.58869 17.7027 1.63416 17.7172 1.67765 17.7115C1.66816 17.7428 1.6606 17.7749 1.6606 17.8081V22.2615C1.66073 22.4558 1.83745 22.6327 2.03173 22.6327H3.51622C3.7105 22.6325 3.88734 22.4558 3.88734 22.2615V20.7259H16.9202V22.2615C16.9202 22.4558 17.097 22.6325 17.2913 22.6327H18.7758C18.9701 22.6325 19.1468 22.4558 19.1469 22.2615V17.8081C19.1469 17.7729 19.1405 17.7389 19.1299 17.7058C19.3969 17.7073 19.3294 17.2768 19.3722 17.0848C19.4175 15.9314 19.4143 14.7577 19.234 13.6216C19.2644 13.3465 19.5056 13.0925 19.7093 12.9361C20.1997 12.9603 20.7416 12.6789 20.817 12.1541C20.7476 11.6668 20.1364 11.5673 19.7528 11.427C19.4057 11.2834 18.8164 11.3507 18.7815 11.8133C18.7446 12.024 18.8807 12.3521 18.7872 12.4912C18.5716 12.401 18.4857 12.1209 18.3403 11.9459C18.0443 10.9107 17.7757 9.83831 17.2686 8.87652C16.9189 8.31716 16.5661 7.70257 16.0265 7.30493C15.091 7.10785 14.1067 7.15406 13.1522 7.06256C12.2664 7.01973 11.3786 7.00136 10.4918 7.00008ZM10.7285 8.60764C12.3003 8.62902 13.8856 8.6184 15.447 8.77048C15.9459 8.82263 16.0449 9.41787 16.2915 9.76077C16.5295 10.2669 16.8591 10.7828 16.9183 11.3437C16.4899 11.656 15.8967 11.7138 15.4092 11.9288C14.7655 12.1494 14.0584 11.9815 13.3869 12.0348C10.851 12.0288 8.31332 12.0696 5.77893 12.0159C5.15527 11.814 4.48739 11.6621 3.8987 11.3948C3.91991 10.8499 4.24758 10.3476 4.46106 9.85166C4.70393 9.47621 4.8483 8.91469 5.31502 8.76859C7.10455 8.58579 8.92762 8.63029 10.7285 8.60764ZM3.45373 11.9989C4.26169 12.0837 5.09261 12.2883 5.75431 12.7714C5.98897 12.9812 6.06524 13.4147 5.90579 13.6727C5.36009 13.9029 4.68094 13.6311 4.17325 13.9662C3.7962 14.2005 4.09273 14.7223 3.85704 15.0095C3.41224 15.1001 2.88685 15.0845 2.46155 14.9091C2.09446 14.6269 2.28363 14.0655 2.29681 13.6746C2.3981 13.0236 2.65263 12.0487 3.45373 11.9989ZM17.4484 12.0045C18.0011 12.0652 18.2626 12.6859 18.399 13.1577C18.5029 13.6859 18.7065 14.3027 18.452 14.8069C18.1318 15.1514 17.5749 15.0023 17.155 15.0625C16.9144 15.1429 16.8407 14.9093 16.8501 14.7179C16.8797 14.376 16.8146 13.9259 16.4032 13.8621C15.9279 13.7043 15.3886 13.8575 14.9263 13.703C14.6622 13.3078 14.9208 12.7087 15.3618 12.5556C16.0082 12.2366 16.7202 12.0265 17.4484 12.0045Z"
                                        fill="#3a3a3a" />
                                </svg>

                            </div>
                            {{ localize('Vehicle Management') }}
                        </x-slot>
                        @if (module_active('permission') && can('api_club_log_settings'))
                             <x-admin.nav-link href="{{ route('admin.mobitra_api.mobitra_api_setting') }}">
                                {{ localize('Settings') }}
                            </x-admin.nav-link> 
                        @endif
                        

                        @can('vehicle_management')
                            <!--<x-admin.nav-link href="{{ route('admin.vehicle.index') }}">-->
                            <!--    {{ localize('Manage Vehicle') }}-->
                            <!--</x-admin.nav-link>-->
                        @endcan

                        @can('legal_document_management')
                            <!--<x-admin.nav-link href="{{ route('admin.vehicle.legal-document.index') }}">-->
                            <!--    {{ localize('Manage Legal Documents') }}-->
                            <!--</x-admin.nav-link>-->
                        @endcan

                        <!--@can('vehicle_type_management')-->
                        <!--    <x-admin.nav-link href="{{ route('admin.vehicle.type.index') }}">-->
                        <!--        {{ localize('Vehicle Types') }}-->
                        <!--    </x-admin.nav-link>-->
                        <!--@endcan-->
                        
                        @can('vehicle_rta_office_management')
                            <!--<x-admin.nav-link href="{{ route('admin.vehicle.rta-office.index') }}">-->
                            <!--    {{ localize('RTA Office') }}-->
                            <!--</x-admin.nav-link>-->
                        @endcan
                        @can('vehicle_ownership_type_management')
                            <!--<x-admin.nav-link href="{{ route('admin.vehicle.ownership.type.index') }}">-->
                            <!--    {{ localize('Ownership Type') }}-->
                            <!--</x-admin.nav-link>-->
                        @endcan
                        @can('vehicle_division_management')
                            <!--<x-admin.nav-link href="{{ route('admin.vehicle.division.index') }}">-->
                            <!--    {{ localize('Vehicle Division') }}-->
                            <!--</x-admin.nav-link>-->
                        @endcan
                        @can('document_type_management')
                            <!--<x-admin.nav-link href="{{ route('admin.vehicle.document-type.index') }}">-->
                            <!--    {{ localize('Document Type') }}-->
                            <!--</x-admin.nav-link>-->
                        @endcan
                    </x-admin.multi-nav>
                @endif




                <!-- User Interface -->
                @if (module_active('user') && can('user_management'))
                    <x-admin.multi-nav>
                        <x-slot name="title">

                            <div>

                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M14.7626 15C11.5932 15 9 17.4923 9 20.5385C9 21.6462 9.36016 22.7538 10.0084 23.7231C10.1525 23.9308 10.3686 24 10.5847 24C10.8008 24 11.0169 23.8615 11.161 23.7231C11.6652 23.0308 12.3855 22.4769 13.2499 22.2L16.3473 18.0462C16.5634 17.7692 16.9955 17.6308 17.3557 17.9077C17.6438 18.1154 17.7879 18.5308 17.4998 18.8769L15.1947 21.9231C16.4193 22.0615 17.5718 22.6846 18.2921 23.6538C18.4362 23.8615 18.6523 23.9308 18.8684 23.9308C19.0845 23.9308 19.3006 23.7923 19.4446 23.6538C20.0929 22.7538 20.4531 21.6462 20.4531 20.4692C20.5251 17.4923 17.932 15 14.7626 15Z"
                                        fill="#3a3a3a" />
                                    <path
                                        d="M7.31785 20.6357C7.31785 16.6109 10.6109 13.3179 14.6357 13.3179C17.0506 13.3179 19.1728 14.4887 20.49 16.245V8.19536C20.49 6.95132 19.5387 6 18.2946 6H2.19536C0.951321 6 0 6.95132 0 8.19536V21.3675C0 22.6115 0.951321 23.5629 2.19536 23.5629H7.90328C7.53739 22.6115 7.31785 21.6602 7.31785 20.6357ZM7.39103 8.63443C7.46421 8.56125 7.46421 8.48807 7.53739 8.41489C7.8301 8.12218 8.26918 8.12218 8.56189 8.41489C8.63507 8.48807 8.70825 8.56125 8.70825 8.63443C8.78142 8.70761 8.78142 8.85396 8.78142 8.92714C8.78142 9.00032 8.78142 9.14668 8.70825 9.21986C8.63507 9.29303 8.63507 9.36621 8.56189 9.43939C8.41553 9.58575 8.26918 9.65893 8.04964 9.65893C7.97646 9.65893 7.8301 9.65893 7.75693 9.58575C7.68375 9.51257 7.61057 9.51257 7.53739 9.43939C7.46421 9.36621 7.39103 9.29303 7.39103 9.21986C7.31785 9.14668 7.31785 9.00032 7.31785 8.92714C7.31785 8.85396 7.31785 8.70761 7.39103 8.63443ZM5.19568 8.63443C5.26886 8.56125 5.26886 8.48807 5.34203 8.41489C5.41521 8.34171 5.48839 8.26853 5.56157 8.26853C5.85428 8.12218 6.147 8.19536 6.36653 8.41489C6.43971 8.48807 6.51289 8.56125 6.51289 8.63443C6.58607 8.70761 6.58607 8.85396 6.58607 8.92714C6.58607 9.14668 6.51289 9.29303 6.36653 9.43939C6.22018 9.58575 6.07382 9.65893 5.85428 9.65893C5.63475 9.65893 5.48839 9.58575 5.34203 9.43939C5.19568 9.29303 5.1225 9.14668 5.1225 8.92714C5.1225 8.85396 5.1225 8.70761 5.19568 8.63443ZM3.14668 8.41489L3.21986 8.34171C3.29303 8.34171 3.29303 8.26853 3.36621 8.26853C3.43939 8.19536 3.43939 8.19536 3.51257 8.19536C3.58575 8.19536 3.73211 8.19536 3.80528 8.19536C3.87846 8.19536 3.87846 8.19536 3.95164 8.26853C4.02482 8.26853 4.02482 8.34171 4.098 8.34171L4.17118 8.41489C4.24436 8.48807 4.31753 8.56125 4.31753 8.63443C4.39071 8.70761 4.39071 8.85396 4.39071 8.92714C4.39071 9.00032 4.39071 9.14668 4.31753 9.21986C4.31753 9.29303 4.24436 9.36621 4.17118 9.43939C4.02482 9.58575 3.87846 9.65893 3.65893 9.65893C3.43939 9.65893 3.29303 9.58575 3.14668 9.43939C3.00032 9.29303 2.92714 9.14668 2.92714 8.92714C2.92714 8.70761 3.00032 8.56125 3.14668 8.41489ZM3.65893 13.3179H7.31785C7.75693 13.3179 8.04964 13.6106 8.04964 14.0496C8.04964 14.4887 7.75693 14.7814 7.31785 14.7814H3.65893C3.21986 14.7814 2.92714 14.4887 2.92714 14.0496C2.92714 13.6106 3.21986 13.3179 3.65893 13.3179ZM5.1225 16.9768H3.65893C3.21986 16.9768 2.92714 16.6841 2.92714 16.245C2.92714 15.8059 3.21986 15.5132 3.65893 15.5132H5.1225C5.56157 15.5132 5.85428 15.8059 5.85428 16.245C5.85428 16.6841 5.56157 16.9768 5.1225 16.9768Z"
                                        fill="#3a3a3a" />
                                </svg>

                            </div>
                            {{ localize('Staff Management') }}
                        </x-slot>
                         <x-admin.nav-link href="{{ route('admin.user.index') }}">
                            {{ localize('List') }}
                        </x-admin.nav-link>
                        <x-admin.nav-link href="{{ route('admin.user.create') }}">
                            {{ localize('Create Staff') }}
                        </x-admin.nav-link> 
                    </x-admin.multi-nav>
                @endif
                <!-- Role-Permission Management -->
                @if (can('permission_management') || can('role_management'))
                    <x-admin.multi-nav>
                        <x-slot name="title">

                            <div>

                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_2128_398)">
                                        <path
                                            d="M13.6998 15.6349C12.8363 15.6349 11.9923 15.891 11.2743 16.3707C10.5563 16.8505 9.99676 17.5323 9.66632 18.3301C9.33588 19.1278 9.24942 20.0056 9.41787 20.8525C9.58633 21.6994 10.0021 22.4773 10.6127 23.0879C11.2233 23.6985 12.0012 24.1143 12.8481 24.2828C13.695 24.4512 14.5728 24.3648 15.3706 24.0343C16.1683 23.7039 16.8502 23.1443 17.3299 22.4263C17.8096 21.7084 18.0657 20.8643 18.0657 20.0008C18.0596 18.8448 17.5977 17.7378 16.7803 16.9203C15.9628 16.1029 14.8559 15.641 13.6998 15.6349ZM14.4658 20.3838C14.347 20.3808 14.2299 20.3548 14.1211 20.3072L12.4743 21.9539C12.382 22.0454 12.2594 22.0998 12.1297 22.1071C12.0639 22.1135 11.9976 22.1027 11.9372 22.0759C11.8768 22.0491 11.8243 22.007 11.785 21.9539C11.6926 21.8553 11.6413 21.7252 11.6413 21.5901C11.6413 21.455 11.6926 21.3249 11.785 21.2263L13.4318 19.5795C13.3881 19.4694 13.3623 19.3531 13.3552 19.2349C13.3377 19.0523 13.3582 18.868 13.4154 18.6937C13.4727 18.5195 13.5654 18.3589 13.6877 18.2222C13.81 18.0855 13.9593 17.9756 14.1261 17.8994C14.293 17.8232 14.4738 17.7824 14.6573 17.7796C14.776 17.7825 14.8931 17.8085 15.0019 17.8562C15.0785 17.8562 15.0785 17.9328 15.0402 17.9711L14.2743 18.6987C14.2566 18.7076 14.2417 18.7213 14.2313 18.7382C14.2209 18.7551 14.2153 18.7746 14.2153 18.7944C14.2153 18.8143 14.2209 18.8338 14.2313 18.8507C14.2417 18.8676 14.2566 18.8812 14.2743 18.8902L14.7721 19.388C14.7858 19.4055 14.8032 19.4197 14.8231 19.4294C14.843 19.4392 14.8649 19.4442 14.887 19.4442C14.9092 19.4442 14.9311 19.4392 14.951 19.4294C14.9709 19.4197 14.9883 19.4055 15.0019 19.388L15.7296 18.6604C15.7679 18.6221 15.8828 18.6221 15.8828 18.6987C15.9225 18.81 15.9482 18.9257 15.9593 19.0434C15.9565 19.2318 15.9147 19.4176 15.8365 19.5891C15.7584 19.7606 15.6457 19.9141 15.5055 20.04C15.3652 20.1658 15.2005 20.2613 15.0215 20.3205C14.8426 20.3797 14.6534 20.4012 14.4658 20.3838Z"
                                            fill="#3a3a3a" />
                                        <path
                                            d="M7.32654 15.8357C10.0426 15.8357 12.2444 13.6339 12.2444 10.9179C12.2444 8.2018 10.0426 6 7.32654 6C4.61049 6 2.40869 8.2018 2.40869 10.9179C2.40869 13.6339 4.61049 15.8357 7.32654 15.8357Z"
                                            fill="#3a3a3a" />
                                        <path
                                            d="M8.72579 24.2284C9.56775 24.2284 9.1085 23.6602 9.1085 23.6602C8.26352 22.6183 7.80432 21.3214 7.80729 19.9853C7.80323 19.1479 7.98619 18.32 8.34308 17.5607C8.35915 17.5174 8.38538 17.4784 8.41962 17.4471C8.68752 16.9167 8.15173 16.8788 8.15173 16.8788C7.91072 16.8473 7.66759 16.8346 7.42458 16.8409C5.62273 16.8479 3.88304 17.4937 2.52093 18.6614C1.15881 19.829 0.264455 21.4412 0 23.2055C0 23.5844 0.114813 24.2663 1.30121 24.2663H8.61098C8.68752 24.2284 8.68752 24.2284 8.72579 24.2284Z"
                                            fill="#3a3a3a" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_2128_398">
                                            <rect width="30" height="30" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>

                            </div>
                            {{ localize('Role - Permission') }}
                        </x-slot>
                        @if (module_active('permission') && can('permission_management'))
                             <x-admin.nav-link href="{{ route('admin.permission.index') }}">
                                {{ localize('Permission') }}
                            </x-admin.nav-link> 
                        @endif

                        @if (module_active('role') && can('role_management'))
                             <x-admin.nav-link href="{{ route('admin.role.index') }}">
                                {{ localize('Role') }}
                            </x-admin.nav-link> 
                        @endif
                    </x-admin.multi-nav>
                @endif
                
                
                 @if (can('permission_management') || can('api_club_log'))
                    <x-admin.multi-nav>
                        <x-slot name="title">

                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.1114 23.4167V23.1802H3.77808C2.6049 23.1785 1.65485 22.2284 1.65308 21.0552V7.36139H1.41667C0.634849 7.36316 0.002652 7.99624 0 8.77806V23.4167C0.00265631 24.1985 0.634849 24.8307 1.41667 24.8333H13.6948C14.4757 24.8307 15.1088 24.1985 15.1114 23.4167Z" fill="#3a3a3a"></path>
                                    <path d="M14.8134 5.50998C14.6425 5.30811 14.4185 5.15935 14.167 5.0788V8.30517H16.9534C16.9029 8.09799 16.8073 7.90497 16.6718 7.74027L14.8134 5.50998Z" fill="#3a3a3a"></path>
                                    <path d="M8.97217 15.3886H10.3888V19.6386H8.97217V15.3886Z" fill="#3a3a3a"></path>
                                    <path d="M12.9863 16.8052H14.403V19.6386H12.9863V16.8052Z" fill="#3a3a3a"></path>
                                    <path d="M3.778 5C2.99529 5.00089 2.36221 5.63485 2.36133 6.41667V21.0553C2.36221 21.838 2.99529 22.4711 3.778 22.4719H15.5833C16.3651 22.4711 16.999 21.838 16.9999 21.0553V9.25H14.1666C13.6451 9.24912 13.2227 8.82677 13.2218 8.30526V5H3.778ZM5.19466 8.30526H10.8613C11.1216 8.30526 11.3333 8.51687 11.3333 8.77806C11.3333 9.03837 11.1216 9.24998 10.8613 9.24998H5.19466C4.93348 9.24998 4.72186 9.03837 4.72186 8.77806C4.72186 8.51687 4.93347 8.30526 5.19466 8.30526ZM7.31967 20.1105V20.1114C7.31967 20.2363 7.2692 20.3567 7.18066 20.4452C7.09211 20.5338 6.97258 20.5833 6.84684 20.5833H4.48543H4.48631C4.22513 20.5833 4.01351 20.3717 4.01351 20.1114V17.2781C4.01351 17.0169 4.22512 16.8053 4.48631 16.8053H6.84773H6.84684C6.97257 16.8053 7.0921 16.8558 7.18066 16.9443C7.2692 17.0328 7.31967 17.1524 7.31967 17.2781V20.1105ZM4.72184 12.5553C4.72184 12.295 4.93345 12.0833 5.19464 12.0833H14.1666C14.4278 12.0833 14.6385 12.295 14.6385 12.5553C14.6385 12.8164 14.4278 13.0281 14.1666 13.0281H5.19464C4.93345 13.0281 4.72184 12.8165 4.72184 12.5553ZM11.3333 20.1114C11.3333 20.2363 11.2837 20.3567 11.1951 20.4452C11.1066 20.5338 10.9862 20.5834 10.8613 20.5834H8.4999C8.23872 20.5834 8.02798 20.3718 8.02798 20.1114V14.9167C8.02798 14.6555 8.23871 14.4448 8.4999 14.4448H10.8613C10.9862 14.4448 11.1066 14.4944 11.1951 14.5829C11.2837 14.6714 11.3333 14.7919 11.3333 14.9167V20.1114ZM15.3468 16.3333V20.1114C15.3468 20.2363 15.2973 20.3567 15.2087 20.4452C15.1202 20.5338 14.9998 20.5834 14.8749 20.5834H12.5135C12.2532 20.5834 12.0416 20.3718 12.0416 20.1114V16.3334C12.0416 16.0722 12.2532 15.8614 12.5135 15.8614H14.8749C14.9998 15.8614 15.1202 15.911 15.2087 15.9996C15.2973 16.0881 15.3468 16.2085 15.3468 16.3333ZM14.1666 10.1948C14.4278 10.1948 14.6385 10.4055 14.6385 10.6667C14.6385 10.9279 14.4278 11.1386 14.1666 11.1386H5.19466C4.93348 11.1386 4.72186 10.9279 4.72186 10.6667C4.72186 10.4055 4.93347 10.1948 5.19466 10.1948H14.1666Z" fill="#3a3a3a"></path>
                                    <path d="M4.9585 17.75H6.37516V19.6386H4.9585V17.75Z" fill="#3a3a3a"></path>
                                </svg>
                            {{ localize('API Club') }}
                        </x-slot>
                        @if (module_active('permission') && can('api_club_log_settings'))
                             <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.apilogmanagement.api_log_settings') }}">
                                {{ localize('Settings') }}
                            </x-admin.nav-link> 
                        @endif
                         @if (module_active('permission') && can('adhaar_log'))
                             <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.apilogmanagement.adhaar_api_log') }}">
                                {{ localize('Adhaar Logs') }}
                            </x-admin.nav-link> 
                        @endif
                        @if (module_active('permission') && can('license_log'))
                             <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.apilogmanagement.license_api_log') }}">
                                {{ localize('License Logs') }}
                            </x-admin.nav-link> 
                        @endif
                        @if (module_active('permission') && can('bank_detail_log'))
                             <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.apilogmanagement.bankdetail_api_log') }}">
                                {{ localize('Bank Detail Logs') }}
                            </x-admin.nav-link> 
                        @endif
                        @if (module_active('permission') && can('pancard_log'))
                             <x-admin.nav-link href="{{ route('admin.Green-Drive-Ev.apilogmanagement.pancard_api_log') }}">
                                {{ localize('Pancard Logs') }}
                            </x-admin.nav-link> 
                        @endif
                        
                    </x-admin.multi-nav>
                @endif
                
                
                
                 @if (can('permission_management') || can('api_club_log'))
                    <x-admin.multi-nav>
                        <x-slot name="title">

                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.1114 23.4167V23.1802H3.77808C2.6049 23.1785 1.65485 22.2284 1.65308 21.0552V7.36139H1.41667C0.634849 7.36316 0.002652 7.99624 0 8.77806V23.4167C0.00265631 24.1985 0.634849 24.8307 1.41667 24.8333H13.6948C14.4757 24.8307 15.1088 24.1985 15.1114 23.4167Z" fill="#3a3a3a"></path>
                                    <path d="M14.8134 5.50998C14.6425 5.30811 14.4185 5.15935 14.167 5.0788V8.30517H16.9534C16.9029 8.09799 16.8073 7.90497 16.6718 7.74027L14.8134 5.50998Z" fill="#3a3a3a"></path>
                                    <path d="M8.97217 15.3886H10.3888V19.6386H8.97217V15.3886Z" fill="#3a3a3a"></path>
                                    <path d="M12.9863 16.8052H14.403V19.6386H12.9863V16.8052Z" fill="#3a3a3a"></path>
                                    <path d="M3.778 5C2.99529 5.00089 2.36221 5.63485 2.36133 6.41667V21.0553C2.36221 21.838 2.99529 22.4711 3.778 22.4719H15.5833C16.3651 22.4711 16.999 21.838 16.9999 21.0553V9.25H14.1666C13.6451 9.24912 13.2227 8.82677 13.2218 8.30526V5H3.778ZM5.19466 8.30526H10.8613C11.1216 8.30526 11.3333 8.51687 11.3333 8.77806C11.3333 9.03837 11.1216 9.24998 10.8613 9.24998H5.19466C4.93348 9.24998 4.72186 9.03837 4.72186 8.77806C4.72186 8.51687 4.93347 8.30526 5.19466 8.30526ZM7.31967 20.1105V20.1114C7.31967 20.2363 7.2692 20.3567 7.18066 20.4452C7.09211 20.5338 6.97258 20.5833 6.84684 20.5833H4.48543H4.48631C4.22513 20.5833 4.01351 20.3717 4.01351 20.1114V17.2781C4.01351 17.0169 4.22512 16.8053 4.48631 16.8053H6.84773H6.84684C6.97257 16.8053 7.0921 16.8558 7.18066 16.9443C7.2692 17.0328 7.31967 17.1524 7.31967 17.2781V20.1105ZM4.72184 12.5553C4.72184 12.295 4.93345 12.0833 5.19464 12.0833H14.1666C14.4278 12.0833 14.6385 12.295 14.6385 12.5553C14.6385 12.8164 14.4278 13.0281 14.1666 13.0281H5.19464C4.93345 13.0281 4.72184 12.8165 4.72184 12.5553ZM11.3333 20.1114C11.3333 20.2363 11.2837 20.3567 11.1951 20.4452C11.1066 20.5338 10.9862 20.5834 10.8613 20.5834H8.4999C8.23872 20.5834 8.02798 20.3718 8.02798 20.1114V14.9167C8.02798 14.6555 8.23871 14.4448 8.4999 14.4448H10.8613C10.9862 14.4448 11.1066 14.4944 11.1951 14.5829C11.2837 14.6714 11.3333 14.7919 11.3333 14.9167V20.1114ZM15.3468 16.3333V20.1114C15.3468 20.2363 15.2973 20.3567 15.2087 20.4452C15.1202 20.5338 14.9998 20.5834 14.8749 20.5834H12.5135C12.2532 20.5834 12.0416 20.3718 12.0416 20.1114V16.3334C12.0416 16.0722 12.2532 15.8614 12.5135 15.8614H14.8749C14.9998 15.8614 15.1202 15.911 15.2087 15.9996C15.2973 16.0881 15.3468 16.2085 15.3468 16.3333ZM14.1666 10.1948C14.4278 10.1948 14.6385 10.4055 14.6385 10.6667C14.6385 10.9279 14.4278 11.1386 14.1666 11.1386H5.19466C4.93348 11.1386 4.72186 10.9279 4.72186 10.6667C4.72186 10.4055 4.93347 10.1948 5.19466 10.1948H14.1666Z" fill="#3a3a3a"></path>
                                    <path d="M4.9585 17.75H6.37516V19.6386H4.9585V17.75Z" fill="#3a3a3a"></path>
                                </svg>
                            GDD - Orders
                        </x-slot>
                             <x-admin.nav-link href="{{ route('admin.report.live_orders') }}">
                                All
                            </x-admin.nav-link> 
                    </x-admin.multi-nav>
                @endif
                
                <!-- Setting Management -->
                @if (module_active('setting') && can('setting_management'))
                    <!--<x-admin.nav-link href="{{ route('admin.setting.index', ['g' => 'Site']) }}">
                        <div>
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.466 4.16746C17.0567 4 16.5378 4 15.5 4C14.4621 4 13.9432 4 13.5339 4.16746C12.9882 4.39075 12.5546 4.81904 12.3285 5.35809C12.2253 5.60417 12.1849 5.89035 12.1691 6.30779C12.1459 6.92125 11.8274 7.48908 11.2891 7.79602C10.7509 8.10296 10.0938 8.09149 9.54427 7.80464C9.17035 7.60943 8.89924 7.50088 8.63188 7.46612C8.0462 7.38996 7.45388 7.54672 6.98523 7.90192C6.63373 8.16832 6.37427 8.61219 5.85536 9.49992C5.33645 10.3877 5.077 10.8315 5.01917 11.2654C4.94206 11.8439 5.10078 12.4289 5.46039 12.8919C5.62454 13.1032 5.85522 13.2807 6.21324 13.5029C6.73958 13.8296 7.07824 14.3861 7.07821 15C7.07818 15.6139 6.73953 16.1703 6.21324 16.4969C5.85516 16.7192 5.62444 16.8968 5.46028 17.1082C5.10066 17.571 4.94196 18.156 5.01906 18.7345C5.07689 19.1683 5.33635 19.6123 5.85525 20.5C6.37416 21.3877 6.63362 21.8317 6.98511 22.098C7.45377 22.4532 8.04609 22.6099 8.63177 22.5338C8.89912 22.499 9.17021 22.3905 9.54409 22.1953C10.0936 21.9084 10.7508 21.897 11.2891 22.2039C11.8274 22.5109 12.1459 23.0787 12.1691 23.6923C12.1849 24.1096 12.2253 24.3959 12.3285 24.6419C12.5546 25.1809 12.9882 25.6093 13.5339 25.8326C13.9432 26 14.4621 26 15.5 26C16.5378 26 17.0567 26 17.466 25.8326C18.0118 25.6093 18.4454 25.1809 18.6714 24.6419C18.7747 24.3959 18.8151 24.1097 18.8309 23.6922C18.8541 23.0787 19.1726 22.5109 19.7108 22.2039C20.2491 21.8969 20.9063 21.9084 21.4558 22.1953C21.8297 22.3905 22.1007 22.4989 22.3681 22.5337C22.9538 22.6099 23.5461 22.4532 24.0147 22.098C24.3663 21.8316 24.6257 21.3877 25.1446 20.4999C25.6635 19.6122 25.9229 19.1683 25.9809 18.7345C26.0579 18.156 25.8992 17.5709 25.5396 17.108C25.3754 16.8967 25.1447 16.7191 24.7866 16.4969C24.2604 16.1703 23.9218 15.6138 23.9218 14.9999C23.9218 14.386 24.2604 13.8297 24.7866 13.5031C25.1448 13.2808 25.3755 13.1033 25.5397 12.8919C25.8993 12.429 26.058 11.844 25.981 11.2655C25.923 10.8316 25.6636 10.3877 25.1447 9.5C24.6258 8.61227 24.3664 8.1684 24.0148 7.902C23.5462 7.5468 22.9539 7.39004 22.3682 7.4662C22.1008 7.50096 21.8297 7.6095 21.4559 7.80468C20.9064 8.09155 20.2492 8.10302 19.7109 7.79606C19.1727 7.4891 18.8541 6.92123 18.8308 6.30773C18.815 5.89033 18.7747 5.60416 18.6714 5.35809C18.4454 4.81904 18.0118 4.39075 17.466 4.16746ZM15.5 18.3C17.3452 18.3 18.841 16.8226 18.841 15C18.841 13.1774 17.3452 11.7 15.5 11.7C13.6548 11.7 12.159 13.1774 12.159 15C12.159 16.8226 13.6548 18.3 15.5 18.3Z"
                                    fill="#3a3a3a" />
                            </svg>
                        </div>
                        {{ localize('Settings') }}
                    </x-admin.nav-link>-->
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





