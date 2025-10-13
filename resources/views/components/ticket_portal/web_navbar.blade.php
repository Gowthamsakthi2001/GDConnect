<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm p-0">
  <div class="container-fluid px-3">
    <!-- Left: Logo -->
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="{{ asset('public/admin-assets/icons/gdm_logo.png') }}" class="img-fluid" style="max-width:150px;" alt="Logo">
      <span class="fw-semibold ms-2" style="color:#4d586a;">Ticket Portal</span>
    </a>

    <!-- Mobile toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
      aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <?php
      $customer = \Illuminate\Support\Facades\Auth::guard('customer')->user();
    ?>

    <!-- Right: User Info -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
      <ul class="navbar-nav align-items-center d-flex gap-2 mt-2 mt-lg-0">
        <li class="nav-item">
          <span class="nav-link px-2">
            <i class="fa-regular fa-user"></i>&nbsp; 
            @if($customer)
             <span style="color:#4d586a;font-size: 0.8rem;">{{$customer->name}}</span>
            @else
             <span style="color:#4d586a;font-size: 0.8rem;">Web User</span>
            @endif
          </span>
        </li>
        <li class="nav-item">
            @if($customer)
            <span class="badge-custom">Customer</span>
            @else
             <span class="badge-custom">Guest</span>
            @endif
        </li>
        <li class="nav-item">
         @if($customer)   
         <form id="logout-form" action="{{ route('user.ticket_portal.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

         <a href="javascript:void(0);" onclick="confirmLogout()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>

         @else
          <a href="{{url('/')}}/ticket-portal/login" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Go to Login
          </a>
         @endif
        </li>
      </ul>
    </div>
  </div>
</nav>