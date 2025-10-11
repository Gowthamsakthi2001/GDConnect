@props(['active_tab'])

<?php
$approve_users = \Illuminate\Support\Facades\DB::table('model_has_roles')
    ->join('users', 'model_has_roles.model_id', '=', 'users.id') 
    ->select('users.id as user_id', 'users.name as user_name')   
    ->where('model_has_roles.role_id', 1) // Filter role_id = 1 (Administrator)
    ->where('users.status', 'Active')
    ->get();

$login_user_id = auth()->id();
$get_approve_ids = [];

foreach ($approve_users as $user) {
    $get_approve_ids[] = $user->user_id;
}
?>

<div class="row">
    <div class="col-md-3 setting-nav">
        <ul class="nav flex-column" id="pills-tab" role="tablist">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'general' ? 'active' : '' }} "
                        href="{{ route('user-profile-information.general') }}">
                        {{ localize('General Info') }}
                    </a>
                </li>
            @endif
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'password' ? 'active' : '' }} "
                        href="{{ route('user-password.index') }}">
                        {{ localize('Password Update') }}
                    </a>
                </li>
            @endif
            @if(in_array($login_user_id, $get_approve_ids))
             <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'sms_settings_tab' ? 'active' : '' }} "
                        href="{{ route('sms_settings_view.index') }}">
                        SMS Settings
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'app_version_settings_tab' ? 'active' : '' }} "
                        href="{{ route('app_version_manage.settings.index') }}">
                        App Version Management
                    </a>
                </li>
            @endif
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <!--<li class="nav-item">-->
                <!--    <a class="nav-link {{ $active_tab == 'two-factor-authentication' ? 'active' : '' }} "-->
                <!--        href="{{ route('user-two-factor.index') }}">-->
                <!--        {{ localize('Two Factor Authentication') }}-->
                <!--    </a>-->
                <!--</li>-->
            @endif
            <!--<li class="nav-item">-->
            <!--    <a class="nav-link {{ $active_tab == 'browser-session' ? 'active' : '' }} "-->
            <!--        href="{{ route('user-browser-sessions.index') }}">-->
            <!--        {{ localize('Browser Sessions') }}-->
            <!--    </a>-->
            <!--</li>-->
        </ul>
    </div>
    <div class="col-md-9 ">
        <div class=" setting-content">
            {{ $slot }}

        </div>
    </div>
</div>
@push('css')
    <link rel="stylesheet" href="{{ module_asset('Auth/css/setting.min.css') }}">
@endpush
