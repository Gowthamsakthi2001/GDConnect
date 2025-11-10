
     <!--<div id="custom-spinner-wrapper" >		 -->
     <!--   <div id="custom-spinner-corpus"></div>-->
     <!--   <div id="custom-spinner-spinner"></div>-->
     <!-- </div>-->
     <!-- <div id="custom-spinner-text">&nbsp;Loading ...</div>-->
  
  

<nav class="navbar-custom-menu navbar navbar-expand-lg m-0 shadow-none">
    <div class="sidebar-toggle-icon" id="sidebarCollapse">
        sidebar toggle<span></span>
    </div>
    
    <div class="input-group my-2 w-25 ms-2">
      <input type="text" class="form-control" placeholder="Search here" aria-label="Search">
      <button class="btn btn-outline-secondary bg-white border-start-0" type="button" style="border:1px solid #ced4da;">
        <i class="fas fa-search"></i>
      </button>
    </div>
    
    
    <!--/.sidebar toggle icon-->
    <div class="navbar-icon d-flex">
        <ul class="navbar-nav flex-row align-items-center ">
            <!--<li class="nav-item dropdown language-menu notification  me-3">-->
            <!--    <a class="language-menu_item border rounded-circle d-flex justify-content-center align-items-center overflow-hidden"-->
            <!--        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">-->

            <!--        <img src=" {{ getLocalizeLang()->where('code', app()->getLocale())?->first()?->flag_url ?? '' }}">-->
            <!--    </a>-->
            <!--    <div class="dropdown-menu language_dropdown">-->
            <!--        @foreach (getLocalizeLang() as $language)-->
            <!--            <a href="{{ route('lang.switch', $language->code) }}"-->
            <!--                class="language_item d-flex align-items-center gap-3">-->
            <!--                <img src="{{ $language->flag_url }}">-->
            <!--                <span>-->
            <!--                    {{ $language->title }}-->
            <!--                </span>-->
            <!--            </a>-->
            <!--        @endforeach-->
            <!--    </div>-->
                <!--/.dropdown-menu -->
            <!--</li>-->
            <!--/.dropdown-->
            
            <li class="nav-item dropdown language-menu notification  me-5">
               <button type="button" class="btn position-relative" style='background-color:#2D9CDB26;'>
                   <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info"> 0</span>
                </button>


            </li>
            
            
            
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
            <li class="nav-item dropdown user_profile me-2">
         
                <?php
                  $role = \Illuminate\Support\Facades\DB::table('roles')->where('id',auth()->user()->role)->first();
                ?>
                <a class="dropdown-toggle rounded-circle  d-flex justify-content-center align-items-center overflow-hidden"
                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3 bg-white p-2">
                            <div class="user_img border rounded-circle overflow-hidden" style="width: 40px; height: 40px;">
                                <img src="<?= $img ?>" alt="" class="img-fluid w-100 h-100 object-fit-cover">
                            </div>
                            <div class="text-start">
                                 <p class="mb-0 text-dark fs-15">{{ auth()->user()->name ?? '' }}</p>
                                 <small style="font-size:11px;" class="text-secondary text-muted">{{ $role->name ?? '' }}</small>
                            </div>
                            <div class="text-start">
                                <img src="{{url('/')}}/public/admin-assets/icons/custom/Vector.png" alt="">
                            </div>
                        </div>
                    </a>

                </a>
                <div class="dropdown-menu">
                    <div class="d-flex align-items-center gap-3 border-bottom pb-3">
                        <div class="user_img">
                            <img src="<?= $img ?>" alt="">
                        </div>
                        <div>
                            <p class="mb-0 fw-bold fs-16">
                                {{ auth()->user()->name ?? '' }}
                            </p>
                            <p class="mb-0 text-muted fs-14">
                                {{ auth()->user()->email ?? '' }}
                            </p>
                        </div>
                    </div>

                    <ul class="list-unstyled mt-3  dropdown_menu_inner">
                        <li class="">
                            <a class="d-block"
                                href="{{ route('user-profile-information.index') }}">{{ localize('My Profile') }}</a>
                        </li>
                        <li class="">
                            <a class="d-block"
                                href="{{ route('user-profile-information.edit') }}">{{ localize('Edit Profile') }}</a>
                        </li>
                        <li class="">
                            <a class="d-block"
                                href="{{ route('user-profile-information.general') }}">{{ localize('Settings') }}</a>
                        </li>
                        <x-logout class="btn_sign_out text-black w-auto">{{ localize('Sign Out') }}</x-logout>

                    </ul>


                </div>
                <!--/.dropdown-menu -->
            </li>
        </ul>
        <!--/.navbar nav-->

    </div>
</nav>
