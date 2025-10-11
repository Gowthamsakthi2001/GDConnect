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
                <x-admin.nav-link href="{{ route('admin.tracking') }}" target="_blank">
                    <div class="mr-2">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                          <path 
                            d="M15 2C10.03 2 6 6.03 6 11C6 18.25 15 28 15 28C15 28 24 18.25 24 11C24 6.03 19.97 2 15 2ZM15 14.5C13.07 14.5 11.5 12.93 11.5 11C11.5 9.07 13.07 7.5 15 7.5C16.93 7.5 18.5 9.07 18.5 11C18.5 12.93 16.93 14.5 15 14.5Z"
                            fill="{{ $routeName == 'admin.tracking' ? '#52c552' : '#3a3a3a' }}" />
                        </svg>


                    </div>
                    {{ localize('Tracking') }}
                </x-admin.nav-link>
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





