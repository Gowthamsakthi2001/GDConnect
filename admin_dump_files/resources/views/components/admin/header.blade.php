<style>
.ev-modules-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* exactly 3 per row */
  gap: 20px;
  padding: 10px;
}

.ev-module-card {
  background: #fff;
  border-radius: 16px;
  padding: 20px 10px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 160px;
}

.ev-module-card img {
  width: 60px;
  height: 60px;
  margin-bottom: 12px;
}

.ev-module-card small {
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

.ev-module-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

/* Responsive breakpoints */
@media (max-width: 992px) {
  .ev-modules-grid {
    grid-template-columns: repeat(2, 1fr); /* tablet → 2 per row */
  }
}

@media (max-width: 576px) {
  .ev-modules-grid {
    grid-template-columns: 1fr; /* mobile → 1 per row */
  }
}

</style>

<nav class="navbar-custom-menu navbar navbar-expand-lg m-0 shadow-none">
    <div class="sidebar-toggle-icon" id="sidebarCollapse">
        sidebar toggle<span></span>
    </div>

    
        <?php
        
          $role = \Illuminate\Support\Facades\DB::table('roles')->where('id',auth()->user()->role)->first();
          $Modules = \App\Models\SidebarModule::where('status',1)->get();
            if (!session()->has('active_module_id') && $Modules->count() > 0) {
                session(['active_module_id' => $Modules->first()->id]);
            }
       
        ?>

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
            
            <!--<li class="nav-item dropdown language-menu notification p-3 d-flex align-items-center gap-2">-->
             
                <!-- Bell button -->
            <!--    <button type="button" class="btn position-relative" style="background-color:#2D9CDB26;">-->
            <!--        <i class="bi bi-bell"></i>-->
            <!--        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info">0</span>-->
            <!--    </button>-->
            <!--</li>-->

         <li class="nav-item ev-nav-item p-3 position-relative">
            <!-- Selected module image -->
            @php
                $activeModule = $Modules->where('id', session('active_module_id'))->first();
                $activeImage = $activeModule && $activeModule->image
                    ? asset('admin-assets/sidebar_icon/' . $activeModule->image)
                    : 'https://img.icons8.com/color/96/bus.png';
            @endphp
        
           <img id="activeModuleImage"
                 src="{{ $activeImage }}"
                 alt="Active Module"
                 style="width:40px;height:40px;border-radius:50%;object-fit:cover;cursor:pointer;"
                 data-bs-toggle="offcanvas"
                 data-bs-target="#EvModuleSplit_Offcanvas"
                 title="Modules">


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

<div class="offcanvas offcanvas-end" tabindex="-1" id="EvModuleSplit_Offcanvas" data-bs-scroll="true"
    aria-labelledby="EvModuleSplit_OffcanvasLabel">
    <div class="offcanvas-header d-flex justify-content-between">
        <div>
            <h6 class="offcanvas-title" id="EvModuleSplit_OffcanvasLabel">EV Vehicle Management</h6>
            <small class="text-muted">Select a module to manage your EV ecosystem</small>
        </div>
        <div class="text-end">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    </div>
    <div class="offcanvas-body">
      <div class="ev-modules-grid">
          
          <?php
        //   dd($Modules);
         ?>
         
        @if(isset($Modules))
            @foreach($Modules as $module)
                @php
                    $imageUrl = $module->image 
                        ? asset('admin-assets/sidebar_icon/' . $module->image) 
                        : 'https://img.icons8.com/color/96/bus.png';
        
                    $routeUrl = ($module->route_name && Route::has($module->route_name))
                        ? route($module->route_name)
                        : 'javascript:void(0)';
        
                    $activeModuleId = session('active_module_id');
                @endphp
                
              @if(in_array($role->id, $module->view_roles_id))
                <div class="ev-module-card {{ $activeModuleId == $module->id ? 'active' : '' }}" 
                     onclick="setActiveModule({{ $module->id }}, '{{ $imageUrl }}', '{{ $routeUrl }}')">
                    <img src="{{ $imageUrl }}" alt="{{ $module->module_name ?? 'N/A' }}">
                    <small>{{ $module->module_name ?? 'N/A' }}</small>
                </div>
            @endif


            @endforeach
        @endif

      </div>
    </div>
</div>
<script>
    function setActiveModule(id, imageUrl, routeUrl) {
    // Update image in navbar
    document.getElementById('activeModuleImage').src = imageUrl;

    // Call backend to update session
    fetch('/set-active-module/' + id)
        .then(() => {
            if (routeUrl !== 'javascript:void(0)') {
                window.location.href = routeUrl;
            }
        });
}

</script>

