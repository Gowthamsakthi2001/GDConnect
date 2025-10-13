<style>
    .login-bg{
        height: 550px !important;
    }
</style>
<div class="row h-100vh align-aitems-center px-0">
    <div class="col-lg-6 d-flex align-aitems-center">
        <div class="form-wrapper m-auto">
            <div>
                {{ $slot }}
            </div>
        </div>
    </div>
    <div class="col-lg-6 login-bg d-none d-lg-block overflow-hidden text-end py-2"
        style="background-image: url({{ setting('site.auth_banner', admin_asset('img/login-bg.png'), true) }});">
        <img class="brand_logo" src="{{ setting('site.logo_light', admin_asset('img/logo-light.png'), true) }}"
            alt="" style="width:40px;">
    </div>
</div>
