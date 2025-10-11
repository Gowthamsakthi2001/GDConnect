<x-app-layout>
    @push('css')
    <style>
    /* Base styles */
    body {
        margin: 0;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #f8f9fa;
    }
    
    #runningVehiclesList {
          position: absolute !important;
          top: 100%; /* directly below input */
          left: 0;
          width: 100%; /* same width as input */
          z-index: 1050;
        }

    .vehicle-details-section{
        height:55vh;
        overflow-x: auto;
    }
    
    .vehicle-status-container{
       height:24vh; 
       /*overflow-x: auto;*/
    }
    /* Main container */
    .main-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    div::-webkit-scrollbar {
          display: none;
        }
    /* Hide scrollbars */
    .table-responsive::-webkit-scrollbar,
    .vehicle-list-container::-webkit-scrollbar {
        display: none;
    }
    
    .table-responsive,
    .vehicle-list-container {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Filter tabs */
    .filter-tabs-container {
        height:30px;
        position: relative;
    }
    
    .filter-btn {
        position: relative;
        background-color: white;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
        z-index: 1;
        white-space: nowrap;
        font-size: 0.875rem;
    }
    
    .filter-btn.active {
        background-color: #e7f1ff;
        color: #0d6efd;
        border: 1px solid #0d6efd;
    }
    
    .filter-btn .badge {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: normal;
    }
    
    .filter-btn.active .badge {
        background-color: #FFFFFF;
        color: #0d6efd;
        
    }
    
    .active-tab-indicator {
        position: absolute;
        bottom: 8px;
        left: 0;
        height: calc(100% - 15px);
        background-color: #e7f1ff;
        border-radius: 0.375rem;
        z-index: 0;
        transition: all 0.3s ease;
        box-shadow: 0 0 0 1px #dee2e6;
    }
    
    /* Active link styling */
    .active-link {
        color: #0d6efd !important;
        font-weight: 500;
        position: relative;
        padding-bottom: 4px;
    }
    
    .active-link::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 2px;
        background-color: #0d6efd;
    }
    
    /* Vehicle card styling */
    .vehicle-card {
        border-radius: 6.4px;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
        cursor: pointer;
        margin-bottom: 12px;
        background: white;
    }
    
    .vehicle-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    
    .vehicle-card .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    
    .view-location-btn {
        font-size: 0.875rem;
    }
    
    .view-location-btn:hover {
        text-decoration: underline !important;
    }
    
    .bg-light-green {
        background-color: #e8f5e9;
    }
    
    .bg-light-orange {
        background-color: #fff3e0;
    }
    
    /* Navbar styling */
    .custom-navbar {
        background-color: white;
        padding: 0.5rem 1rem;
        /*height: 6.25vh;*/
        /*box-shadow: 0 2px 4px rgba(0,0,0,0.1);*/
        height: 60px;
    }
    
    .custom-navbar .navbar-brand {
        font-weight: 600;
        font-size: 1.5rem;
        margin-right: 2rem;
    }
    
    .custom-navbar .notification-btn {
        background-color: #FFFFFF;
        border: 1px solid #e9ecef;
        border-radius:6.44px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .custom-navbar .user-profile {
        display: flex;
        align-items: center;
    }
    
    .custom-navbar .user-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
    }
    
    /* Map container styling */
    #map {
        width: 100%;
        height: 100%;
        border-radius: 8px;
    }
    
    .map-container {
        position: relative;
        width: 37vw;
        height: 80vh;
        background: #f8f9fa;
        border-radius: 8px;
    }
    
    .map-placeholder {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        z-index: 1;
    }
    
    .error-message {
        position: absolute;
        top: 0;
        left: 30vh;
        right: 30vh;
        bottom: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        z-index: 1;
    }
    
    .map-infowindow {
        padding: 8px;
        font-size: 14px;
        line-height: 1.4;
    }
    
    .map-infowindow strong {
        display: block;
        margin-bottom: 4px;
        color: #0d6efd;
    }

    /* Main header */
    .main-header {
        padding: 0.75rem 1rem;
        height: 60px;
        z-index: 1000;
    }
    
    /* Content area */
    .content-area {
        flex: 1;
        display: flex;
        overflow: hidden;
        padding: 1rem;
        gap: 1rem;
        background: #f8f9fa;
        height: calc(100vh - 110px);
    }
    
    /* Sidebars */
    .vehicle-sidebar {
        width: 27.7vw;
        height: 80vh;
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
    }
    
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    
    /* For Firefox */
    .no-scrollbar {
        scrollbar-width: none; /* hides scrollbar */
        -ms-overflow-style: none; /* IE/Edge */
    }

    .vehicle-details-sidebar {
        width: 350px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
    }
    
    .sidebar-header {
        padding: 5px;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
    }
    
    .sidebar-content {
        flex: 1;
        /*overflow-y: scroll;*/
        
        padding: 10px;
    }
    
    /* Status indicators */
    .status-badge {
        position: absolute;
        top: 0;
        right: 0;
        max-width:73px;
        max-height:24px;
        border-top-right-radius: 8px !important;
        border-bottom-left-radius: 8px !important;
        /*padding: 0.35rem 0.75rem;*/
        padding: 0.25rem 0.75rem 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    
    
    .status-distance {
        background-color: #EEE9CA ;
        color: #58490F;
        border:1px solid #58490F;
    }
    
    .status-speed {
        background-color: #CAEDCE;
        color: #1E580F ;
        border:1px solid #1E580F;
    }
    
    .status-offline {
        background-color: #EDEDED !important;
        color:#000000;
        border:1px solid #000000;
    }
    
    .status-indicator {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        width: 100%;
        border-bottom-left-radius: 8px;
    }
    
    /* Vehicle details table */
    .vehicle-details-table {
        width: 100%;
        font-size: 0.875rem;
    }
    
    .status-card {
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 4px;
        margin-bottom: 10px;
        text-align: center;
    }
        
    .vehicle-details-table th {
        color: #6c757d;
        font-weight: 500;
        width: 40%;
        padding: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .vehicle-details-table td {
        padding: 0.5rem;
        border-bottom: 1px solid #dee2e6;
        font-weight: 500;
    }
    
    /* Skeleton loading */
    .skeleton-container {
        padding: 10px;
    }
    
    .skeleton-text {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 4px;
    }
    
    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }
    
    /* Search container */
    .search-container {
        position: relative;
        display: flex;
        width: 100%;
        max-width: 600px; /* Optional: You can remove this if you want full width without max constraint */
    }
    
    .search-container i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
    }
    
    .search-container input {
        padding-left: 40px;
        border-radius: 4px 0 0 4px;
        border-right: none;
        height: 35px;
        width: 100%;
    }
    
    .search-container .dropdown-toggle {
        border-radius: 0 4px 4px 0;
        background-color: white;
        border: 1px solid #ced4da;
        /*border-left: none;*/
        height: 35px;
        min-width: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .search-container .dropdown-menu {
        top: 100%; /* directly below input */
        left: 0;
        width: 100%;
        max-height: 300px;
        /*overflow-y: auto;*/
    }
    
    .dropdown-toggle {
        position: absolute;
        /*position: relative;*/
        right: 0px;
        /*top: 5px;*/
        
        /*background: transparent;*/
        color: #6c757d;
    }
    
    .dropdown-toggle:focus {
        box-shadow: none;
    }
    
    .dropdown-menu {
        max-height: 300px;
        /*overflow-x: scroll;*/
        width: 100%;
    }
    
    .vehicle-status {
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 12px;
    }
    
    .status-running {
        background-color: #D8E4FE;
        color: #3B62AD;
        border:1px solid #3B62AD;
        
    }
    
    .status-idle {
        background-color: #EDEDED;
        color: #000000;
        border:1px solid #000000;
    }
    
    .status-stopped {
        background-color: #EECACB;
        color: #58110F;
        border:1px solid #58110F;
       
    }
    
    .vehicle-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
    }
    
    .vehicle-info {
        flex-grow: 1;
    }
    
    .vehicle-number {
        font-weight: 500;
    }
    
    /* Enhanced responsive adjustments */
    @media (max-width: 1400px) {
        .vehicle-sidebar,
        .vehicle-details-sidebar {
        width: 27.7vw;
        height: 80vh;
        }
        
        
        
        .vehicle-card .card-body {
            padding: 0.75rem;
        }
        
        .view-location-btn {
            font-size: 0.8rem;
        }
        /*.table-responsive {*/
        /*    max-height: 430px;*/
        /*}*/
    }

    @media (max-width: 1200px) {
        
        
        
        /*.table-responsive {*/
        /*    max-height: 230px;*/
        /*}*/
        .vehicle-sidebar,
        .vehicle-details-sidebar {
            width: 27.7vw;
            height: 80vh;
        }
        
        .filter-btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }
        
        .vehicle-card .card-body {
            padding: 0.6rem;
        }
        
        .vehicle-card h6 {
            font-size: 0.9rem;
        }
        
        .vehicle-card p {
            font-size: 0.75rem;
        }
        
        .view-location-btn {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 992px) {
        /*.table-responsive {*/
        /*    max-height: 180px;*/
        /*}*/
        
        
        .content-area {
            flex-direction: column;
            padding: 0.75rem;
            gap: 0.75rem;
        }
        .vehicle-sidebar,
        .vehicle-details-sidebar
         {
            flex: 0 0 auto;
            width: 27.7vw;
            height: 80vh;
        }
        
        .map-container {
            flex: 0 0 auto;
            width: 37vw;
            height: 80vh;
        }
        
        .vehicle-sidebar {
            width: 27.7vw;
            height: 80vh;
            order: 2;
        }
        
        .map-container {
            width: 37vw;
            height: 80vh;
            order: 1;
        }
        
        .vehicle-details-sidebar {
            width: 100%;
            height: 300px;
            order: 3;
        }
        
        .main-header {
            padding: 0.5rem 0.75rem;
            height: auto;
            min-height: 60px;
        }
        
        .main-header .col-auto {
            margin-top: 0.5rem;
        }
        
        .custom-navbar .navbar-brand {
            font-size: 1.2rem;
            margin-right: 1rem;
        }
        
        .filter-btn {
            padding: 0.35rem 0.7rem;
            font-size: 0.75rem;
        }
        
        .search-container {
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .custom-navbar {
            padding: 0.5rem;
        }
        
        .custom-navbar .navbar-brand {
            font-size: 1.1rem;
        }
        
        .notification-btn {
            width: 35px;
            height: 35px;
        }
        
        .user-img {
            width: 35px;
            height: 35px;
        }
        
        .main-header h5 {
            font-size: 1rem;
        }
        
        .main-header .btn {
            padding: 0.35rem 0.7rem;
            font-size: 0.8rem;
        }
        
        .content-area {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            padding-bottom: 0.5rem;
            gap: 0.5rem;
        }
        
        .vehicle-sidebar,
        .vehicle-details-sidebar {
            width: 27.7vw;
            height: 80vh;
        }
        
        .map-container {
            width: 37vw;
            height: 80vh;
        }
        
        .filter-btn {
            padding: 0.3rem 0.6rem;
            font-size: 0.7rem;
        }
        
        .sidebar-header h5 {
            font-size: 0.9rem;
        }
        
        .vehicle-card {
            margin-top: 8px;
        }
        
        .vehicle-card .card-body {
            padding: 0.5rem;
        }
        
        .vehicle-card h6 {
            font-size: 0.85rem;
        }
        
        .vehicle-card p {
            font-size: 0.7rem;
            margin-bottom: 0.4rem;
        }
        
        .view-location-btn {
            font-size: 0.7rem;
        }
        
        .status-badge {
            padding: 0.15rem 0.5rem 0.25rem 0.5rem;
            font-size: 0.65rem;
        }
    }

    @media (max-width: 576px) {
        .custom-navbar .navbar-brand {
            font-size: 1rem;
        }
        
        .user-profile .d-none.d-md-block {
            display: none !important;
        }
        
        .main-header {
            padding: 0.4rem 0.5rem;
        }
        
        .main-header h5 {
            font-size: 0.9rem;
        }
        
        .main-header .btn {
            padding: 0.3rem 0.6rem;
            font-size: 0.75rem;
        }
        
        .main-header .d-flex.gap-3 {
            gap: 0.5rem !important;
        }
        
        .content-area {
            padding-left: 0.4rem;
            padding-right: 0.4rem;
            padding-bottom: 0.4rem;
            gap: 0.4rem;
        }
        
        .vehicle-sidebar,
        .vehicle-details-sidebar {
            width: 27.7vw;
            height: 80vh;
        }
        
        .map-container {
            width: 37vw;
            height: 80vh;
        }
        
        .sidebar-content {
            padding: 8px;
        }
        
        .filter-tabs-container > div {
            padding: 2px;
        }
        
        .filter-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.65rem;
        }
        
        .search-container i {
            left: 10px;
            top: 10px;
            font-size: 0.8rem;
        }
        
        .search-container input {
            padding-left: 30px;
            font-size: 0.8rem;
            height: 30px;
        }
        
        .dropdown-toggle {
            /*top: 3px;*/
            right: 0px;
        }
        
        .vehicle-list-container {
            max-height: 180px;
        }
        
        .vehicle-card {
            margin-top: 6px;
        }
        
        .vehicle-card .card-body {
            padding: 0.4rem;
        }
        
        .vehicle-card h6 {
            font-size: 0.8rem;
        }
        
        .vehicle-card p {
            font-size: 0.65rem;
        }
        
        .rounded-pill {
            padding: 0.2rem 0.4rem !important;
            font-size: 0.65rem;
        }
        
        .view-location-btn {
            font-size: 0.65rem;
        }
        
        .status-badge {
            /*padding: 0.2rem 0.4rem;*/
            padding: 0.10rem 0.5rem 0.25rem 0.5rem;
            font-size: 0.6rem;
        }
        
        .status-card {
            padding: 2px;
            margin-bottom: 8px;
        }
        
        .status-card .small {
            font-size: 0.65rem !important;
        }
        
        .status-value {
            font-size: 0.75rem !important;
        }
        
        .vehicle-details-table th,
        .vehicle-details-table td {
            padding: 0.3rem;
            font-size: 0.75rem;
        }
        
        /*.table-responsive {*/
        /*    max-height: 180px;*/
        /*}*/
    }

    @media (max-width: 400px) {
        .custom-navbar .container-fluid {
            flex-wrap: nowrap;
        }
        
        .custom-navbar .navbar-brand {
            font-size: 0.9rem;
            margin-right: 0.5rem;
        }
        
        .notification-btn {
            width: 30px;
            height: 30px;
            margin-right: 0.5rem !important;
        }
        
        .notification-btn i {
            font-size: 0.8rem;
        }
        
        .user-img {
            width: 30px;
            height: 30px;
        }
        
        .main-header .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
        }
        
        .main-header .btn i {
            margin-right: 0.2rem;
        }
        
        .filter-btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.6rem;
        }
        
        .filter-btn .badge {
            font-size: 0.55rem;
            padding: 0.2em 0.4em;
        }
        
        .vehicle-sidebar,
        .vehicle-details-sidebar {
            width: 27.7vw;
            height: 80vh;
        }
        
        .map-container {
            width: 37vw;
            height: 80vh;
        }
        
        .vehicle-card .card-body {
            padding: 0.3rem;
        }
        
        .vehicle-card h6 {
            font-size: 0.75rem;
        }
        
        .d-flex.justify-content-between.mb-2 {
            flex-direction: column;
            gap: 4px;
        }
        
        .rounded-pill {
            width: 100%;
            text-align: center;
            margin-bottom: 2px;
        }
        
        .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 4px;
        }
        
        .view-location-btn {
            align-self: flex-start;
        }
        
        .text-muted.small {
            align-self: flex-end;
        }
    }

    /* Orientation-specific adjustments */
    @media (max-height: 500px) and (orientation: landscape) {
        .content-area {
            flex-direction: row;
        }
        
        .vehicle-sidebar {
            width: 27.7vw;
            height: 80vh;
            order: 1;
        }
        
        .map-container {
            width: 37vw;
            height: 80vh;
            order: 2;
        }
        
        .vehicle-details-sidebar {
            width: 280px;
            height: 100%;
            order: 3;
        }
        
        .vehicle-list-container {
            max-height: calc(100vh - 200px);
        }
    }
    </style>
    @endpush

    <!-- The rest of your HTML/PHP code remains exactly the same -->
    @php
   
    $data = $results['data'];
    @endphp

    <div class="main-container">
        <!-- Navbar -->
        <nav class="custom-navbar navbar navbar-expand-lg fixed-top shadow-sm">
            <!--<div class="container-fluid">-->
                <div class="d-flex align-items-center">
                    <span class="navbar-brand mb-0" style="font-size:16px">Live Tracking</span>
                </div>

                <div class="d-flex align-items-center ms-auto">
                    <button type="button" class="btn position-relative notification-btn me-3">
                        <i class="bi bi-bell"></i>
                        <!--<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info">0</span>-->
                    </button>

                    @php
                        
                        $user = \App\Models\User::find(auth()->id());
                        if ($user && !empty($user->profile_photo_path)) {
                            $img = str_starts_with($user->profile_photo_path, 'http') 
                                ? $user->profile_photo_path 
                                : asset('/uploads/users/' . $user->profile_photo_path);
                        } else {
                            $img = asset('storage/setting/byQpJL3dVU32cdP6xIpHNL2MTi9AtXu0UfPdJTuG.png');
                        }

                        $role = \Illuminate\Support\Facades\DB::table('roles')->where('id', auth()->user()->role)->first();
                    @endphp

                    <div class="dropdown user-profile" >
                        <a class="text-decoration-none d-flex align-items-center" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="max-height:56px;width:185px;">
                            <div class="d-flex align-items-center gap-2 bg-white p-1 rounded">
                                <div class="user_img border rounded-circle overflow-hidden">
                                    <img src="{{ $img }}" alt="User" class="user-img">
                                </div>
                                <div class="text-start d-none d-md-block">
                                    <p class="mb-0 text-dark " style="font-size:16px">{{ auth()->user()->name ?? '' }}</p>
                                    <small class="text-secondary" style="font-size:12px">{{ $role->name ?? '' }}</small>
                                </div>
                                <i class="bi bi-chevron-down ms-1 text-dark"></i>
                            </div>
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li class="d-flex align-items-center gap-3 pb-3 border-bottom">
                                <!--<div class="user_img border rounded-circle overflow-hidden" style="width: 40px; height: 40px;">-->
                                <!--    <img src="{{ $img }}" alt="User" class="img-fluid w-100 h-100 object-fit-cover">-->
                                <!--</div>-->
                                <div>
                                    <p class="px-2 mb-0 fw-bold">{{ auth()->user()->name ?? '' }}</p>
                                    <p class="px-2 mb-0 text-muted small" style="font-size:12px;">{{ auth()->user()->email ?? '' }}</p>
                                </div>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('user-profile-information.index') }}"><i class="bi bi-person me-2"></i> {{ localize('My Profile') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('user-profile-information.edit') }}"><i class="bi bi-pencil-square me-2"></i> {{ localize('Edit Profile') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('user-profile-information.general') }}"><i class="bi bi-gear me-2"></i> {{ localize('Settings') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="dropdown-item">
                                 <x-logout class="btn_sign_out text-black w-auto ">{{ localize('Sign Out') }}</x-logout>
                            </li>
                           

                    </ul>
                        
                    </div>
                </div>
            <!--</div>-->
        </nav>

        <!-- Main header -->
        <div class="main-header fixed-top" style="top:60px;height: 6.25vh;">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0 fw-semibold" style="font-size:16px">Vehicle Live Tracking View</h5>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-3" style="height:4.3vh;">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark d-flex align-items-center gap-2" style="max-height:42px;max-width:84px">
                            <i class="bi bi-arrow-left"></i>
                            Back
                        </a>
                        @if(!isset($api_mode) || $api_mode==true)
                        <button class="btn btn-primary d-flex align-items-center gap-2" id="refreshBtn" style="max-height:42px;max-width:104px"> 
                            <i class="bi bi-arrow-clockwise"></i>
                            Refresh
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area" style="margin-top: calc(6.25vh + 60px)">
            <!-- Vehicle List Sidebar -->
            
             @if(!isset($api_mode) || $api_mode==false)
             <div class="map-container flex-grow-1">
                
                <div class="error-message bg-white" id="mapPlaceholder">
                    
                        <div class="alert alert-warning d-flex align-items-center justify-content-center text-center shadow-sm rounded-3 p-3" role="alert" style="max-width: 600px; margin: 0 auto;">
                            <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 2rem; color: #856404;"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Production Mode Disabled</h6>
                                <p class="mb-0">Production Mode is not enabled in the <strong>API MODE Settings</strong>. Please enable it to view Tracking.</p>
                            </div>
                        </div>
                  
                </div>
            </div>
             @else
             
            <div class="vehicle-sidebar">
    <div class="sidebar-content d-flex flex-column" >
        <!-- Fixed Header Section -->
        <div class="sidebar-headers" style="padding: 10px; flex-shrink: 0;">
            <h5 class="d-flex align-items-center mb-0" style="font-size:14px">
               Vehicle Details
            </h5>
        </div>
        
        <div class="filter-tabs-container mb-2" style="flex-shrink: 0;">
            <div class="d-flex flex-nowrap position-relative border rounded " style="overflow-x: scroll; -ms-overflow-style: none; scrollbar-width: none;">
                <div class="active-tab-indicator"></div>
                
                <button class="btn filter-btn active flex-shrink-0 border-0" data-filter="all" style="font-size:12px">
                    Overall
                    <span class="badge rounded-pill ms-1">{{ count($data['vehicles']) }}</span>
                </button>
                <button class="btn filter-btn flex-shrink-0 border-0" data-filter="running" style="font-size:12px">
                    Running
                    <span class="badge rounded-pill ms-1">{{ count(array_filter($data['vehicles'], fn($v) => $v['vehicleStatus'] === 'running')) }}</span>
                </button>
                <button class="btn filter-btn flex-shrink-0 border-0" data-filter="stopped" style="font-size:12px">
                    Stopped
                    <span class="badge rounded-pill ms-1">{{ count(array_filter($data['vehicles'], fn($v) => $v['vehicleStatus'] === 'stopped')) }}</span>
                </button>
                <button class="btn filter-btn flex-shrink-0 border-0" data-filter="offline" style="font-size:12px">
                    Offline
                    <span class="badge rounded-pill ms-1">{{ count(array_filter($data['vehicles'], fn($v) => $v['vehicleStatus'] === 'offline')) }}</span>
                </button>
            </div>
        </div>
        
        <div class="search-container mb-2 mt-2" style="flex-shrink: 0;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3"></i>
            <input type="text" style="max-height:35px; width:100%" class="form-control"  id="vehicleSearch" placeholder="Search by Vehicle No or IMEI No">
            <button class="dropdown-toggle" type="button" id="runningVehiclesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                
            </button>
            <ul class="dropdown-menu" aria-labelledby="runningVehiclesDropdown" id="runningVehiclesList" style="overflow-y: auto;">
                 Running vehicles will be populated here 
            </ul>
        </div>
        
        
        <!-- Scrollable Vehicle List Container -->
        <div class="vehicle-list-container flex-grow-1" id="vehicleListContainer" style="overflow-y: auto;height:55vh;padding-top:5px">
            @foreach($data['vehicles'] as $vehicle)
                <div class="card vehicle-card position-relative"
                     data-vehicle-id="{{ $vehicle['vehicleNumber'] }}"
                     data-vehicle-status="{{ strtolower($vehicle['vehicleStatus']) }}"
                     data-imei-number="{{ $vehicle['IMEINumber'] }}"
                     data-latitude="{{ $vehicle['latitude'] }}"
                     data-longitude="{{ $vehicle['longitude'] }}"
                     data-vehicle-type="{{ $vehicle['vehicleType'] }}"
                     data-last-updated="{{ $vehicle['lastDbTime'] }}"
                     data-last-speed="{{ $vehicle['lastSpeed'] }}"
                     data-distance-travelled="{{ $vehicle['distanceTravelled'] }}"
                     data-battery="{{ $vehicle['battery'] }}"
                     data-roleName="{{ $vehicle['roleName'] }}"
                     data-prRoleName="{{ $vehicle['prRoleName'] }}"
                     >
                    
                    <span class="status-badge 
                        @if($vehicle['vehicleStatus'] == 'running') status-running
                        @elseif($vehicle['vehicleStatus'] == 'stopped') status-stopped
                        @else status-offline @endif">
                        {{ ucfirst($vehicle['vehicleStatus']) }}
                    </span>
                    
                    <!--<div class="status-indicator -->
                    <!--    @if($vehicle['vehicleStatus'] == 'running') status-running-->
                    <!--    @elseif($vehicle['vehicleStatus'] == 'stopped') status-stopped-->
                    <!--    @else status-offline @endif">-->
                    <!--</div>-->
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0" style="font-size:12px">{{ $vehicle['vehicleNumber'] }}</h6>
                        </div>
                        <p class="text-muted small mb-2" style="font-size:12px">
                            Type: {{ $vehicle['vehicleType'] }} | 
                            Last Updated On: {{ date('d M Y, H:i:s', $vehicle['lastDbTime'] / 1000000000) }}
                        </p>
                        <div class="d-flex justify-content-between mb-2">
                            <span class=" px-2 py-1 {{ number_format($vehicle['lastSpeed'], 2) > 0.00 ? 'status-speed' : 'bg-white text-secondary border border-dark' }}" style="font-size:12px ;border-radius:6.4px;">
                                Speed: {{ number_format($vehicle['lastSpeed'], 2) }} Km/h
                            </span>
                            <span class=" px-2 py-1 {{ number_format($vehicle['distanceTravelled'], 2) > 0.00 ? 'status-distance': 'bg-white text-secondary border border-dark' }}" style="font-size:12px ;border-radius:6.4px;">
                                Today Distance: {{ number_format($vehicle['distanceTravelled'], 2) }} Km
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <button class="btn btn-link p-0 text-decoration-none text-primary view-location-btn" style="font-size:12px">
                                <img height = 16 width = 18 src="{{ asset("admin-assets/img/chevron_left.svg") }}"> View Vehicle Current Location
                            </button>
                            <!--<span class="text-muted small">IMEI: {{ $vehicle['IMEINumber'] }}</span>-->
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

            <!-- Map Section -->
            <div class="map-container flex-grow-1">
                <div id="map"></div>
                <div class="map-placeholder bg-white" id="mapPlaceholder">
                    <div class="text-center">
                        <i class="bi bi-car-front-fill" style="font-size: 3rem;"></i>
                        <p class="mt-2">Please select a vehicle to track</p>
                    </div>
                </div>
            </div>
            
            <!-- Vehicle Details Sidebar -->
            <div class="vehicle-details-sidebar">
                <div class="sidebar-content">
                    <div class="vehicle-status-container" style="flex-shrink: 0;">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2">Select a vehicle to view status</p>
                        </div>
                    </div>
                    
                    <div class="vehicle-details-section" style="flex-shrink: 0;margin-top:15px">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2">Select a vehicle to view details</p>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            @endif
        </div>
    </div>

    @push('js')
   <script>
    // Global variables
    let map;
    let marker = null;
    let currentVehicleData = null;
    let infoWindow = null;
    let mapInitialized = false;
    let selectedVehicleCard = null;
    let bikeIcon = null;

    function loadGoogleMaps() {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key={{ env('MAP_KEY') }}&callback=initMap`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    // Load API
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadGoogleMaps);
    } else {
        loadGoogleMaps();
    }

    function initMap() {
        if (mapInitialized) return;

        const indiaCenter = { lat: 20.5937, lng: 78.9629 };

        map = new google.maps.Map(document.getElementById('map'), {
            center: indiaCenter,
            zoom: 5,
            mapTypeId: google.maps.MapTypeId.ROADMAP, // Lock to roadmap
            mapTypeControl: false,
            streetViewControl: true,
            fullscreenControl: false,
            styles: [
                { featureType: "poi", stylers: [{ visibility: "off" }] },
                { featureType: "transit", stylers: [{ visibility: "off" }] }
            ]
        });

        // Create bike icon
        bikeIcon = {
            url: '{{ asset("admin-assets/img/bike_image.svg") }}',
            scaledSize: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(20, 20)
        };

        // Prepare empty marker (we'll move it later)
        marker = new google.maps.Marker({
            map: map,
            position: indiaCenter,
            icon: bikeIcon,
            title: 'India'
        });

        infoWindow = new google.maps.InfoWindow();
        mapInitialized = true;
        
        // Show skeleton loading for vehicle details
        showSkeletonLoading();
    }

    // Reset map to default India view
    function resetMapToDefault() {
        if (!mapInitialized) return;

        const indiaCenter = { lat: 20.5937, lng: 78.9629 };
        map.setCenter(indiaCenter);
        map.setZoom(5);

        marker.setPosition(indiaCenter);
        marker.setTitle('India');
        marker.setIcon(bikeIcon);

        if (document.getElementById('mapPlaceholder')) {
            document.getElementById('mapPlaceholder').style.display = 'flex';
        }
        
        // Close info window if open
        infoWindow.close();
    }

    function updateMapWithCoordinates(lat, lng, vehicleInfo = {}) {
        if (!mapInitialized) {
            initMap();
        }

        const position = {
            lat: parseFloat(lat) || 20.5937,
            lng: parseFloat(lng) || 78.9629
        };

        // Center map on new position
        map.setCenter(position);
        map.setZoom(15);

        // Keep roadmap locked
        if (map.getMapTypeId() !== google.maps.MapTypeId.ROADMAP) {
            map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
        }

        // Update existing marker
        marker.setPosition(position);
        marker.setTitle(vehicleInfo.vehicleNumber || 'Vehicle Location');
        marker.setIcon(bikeIcon);

        let infoContent = `
            <div class="map-infowindow">
                <strong>${vehicleInfo.vehicleNumber || 'Vehicle'}</strong><br>
                ${vehicleInfo.imei ? 'IMEI: ' + vehicleInfo.imei + '<br>' : ''}
                ${vehicleInfo.vehicleType ? 'Type: ' + vehicleInfo.vehicleType + '<br>' : ''}
                Location: ${position.lat.toFixed(6)}, ${position.lng.toFixed(6)}
            </div>
        `;

        infoWindow.setContent(infoContent);

        // Remove any existing click listeners to prevent duplicates
        google.maps.event.clearListeners(marker, 'click');
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });

        infoWindow.open(map, marker);

        if (document.getElementById('mapPlaceholder')) {
            document.getElementById('mapPlaceholder').style.display = 'none';
        }
    }
    
    // Show skeleton loading for vehicle details
    function showSkeletonLoading() {
        // Skeleton for status container
        const statusContainer = document.querySelector('.vehicle-status-container');
        if (statusContainer) {
            statusContainer.innerHTML = `
                <div class="skeleton-container" >
                    <div class="border-bottom pb-3 mb-3" height:6.3vh">
                        <div class="skeleton-text" style="height: 20px; width: 40%; margin-bottom: 10px;"></div>
                    </div>
                    <div class="vehicle-status-card" style="overflow-y: auto;">
                    <div class="row g-3 mb-2" style="height:9.3vh;">
                        <div class="col-4">
                            <div class="status-card">
                                <div class="skeleton-text" style="height: 10px; width: 60%; margin: 0 auto 8px;"></div>
                                <div class="skeleton-text" style="height: 14px; width: 80%; margin: 0 auto;"></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="status-card">
                                <div class="skeleton-text" style="height: 10px; width: 60%; margin: 0 auto 8px;"></div>
                                <div class="skeleton-text" style="height: 14px; width: 80%; margin: 0 auto;"></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="status-card">
                                <div class="skeleton-text" style="height: 10px; width: 60%; margin: 0 auto 8px;"></div>
                                <div class="skeleton-text" style="height: 14px; width: 80%; margin: 0 auto;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3" style="height:9.3vh;">
                        <div class="col-4">
                            <div class="status-card">
                                <div class="skeleton-text" style="height: 10px; width: 60%; margin: 0 auto 8px;"></div>
                                <div class="skeleton-text" style="height: 14px; width: 80%; margin: 0 auto;"></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="status-card">
                                <div class="skeleton-text" style="height: 10px; width: 60%; margin: 0 auto 8px;"></div>
                                <div class="skeleton-text" style="height: 14px; width: 80%; margin: 0 auto;"></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="status-card">
                                <div class="skeleton-text" style="height: 10px; width: 60%; margin: 0 auto 8px;"></div>
                                <div class="skeleton-text" style="height: 14px; width: 80%; margin: 0 auto;"></div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            `;
        }
        
        // Skeleton for details section
        const detailsSection = document.querySelector('.vehicle-details-section');
        if (detailsSection) {
            detailsSection.innerHTML = `
                <div class="skeleton-container mt-2" >
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="skeleton-text" style="height: 20px; width: 40%;"></div>
                    </div>
                    <div class="table-responsive" style="max-height:42vh;overflow-y: auto;">
                        <table class="table table-sm table-bordered">
                            <tbody>
                                ${Array(20).fill().map(() => `
                                    <tr>
                                        <th scope="row" class="text-muted w-25"><div class="skeleton-text" style="height: 16px;"></div></th>
                                        <td><div class="skeleton-text" style="height: 16px;"></div></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }
    }
    
    // Reset to default state
    function resetToDefaultState() {
        // Reset selected vehicle card
        if (selectedVehicleCard) {
            selectedVehicleCard.classList.remove('border-primary');
            selectedVehicleCard = null;
        }
        
        // Reset map to default view
        resetMapToDefault();
        
        // Show skeleton loading
        showSkeletonLoading();
        
        // // After a short delay, show the default "no vehicle selected" state
        // setTimeout(() => {
        //     const statusContainer = document.querySelector('.vehicle-status-container');
        //     if (statusContainer) {
        //         statusContainer.innerHTML = `
        //             <div class="text-center text-muted py-5">
        //                 <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
        //                 <p class="mt-2">Select a vehicle to view status</p>
        //             </div>
        //         `;
        //     }
            
        //     const detailsSection = document.querySelector('.vehicle-details-section');
        //     if (detailsSection) {
        //         detailsSection.innerHTML = `
        //             <div class="text-center text-muted py-5">
        //                 <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
        //                 <p class="mt-2">Select a vehicle to view details</p>
        //             </div>
        //         `;
        //     }
        // }, 1000);
    }
    
    // Fetch vehicle data from API
    async function fetchVehicleData(vehicleNumber) {
        try {
            // Show loading state
            document.querySelector('.vehicle-details-section').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading vehicle data...</p>
                </div>
            `;
            const url = "{{ route('admin.mobitra_api.get_user_devices') }}" + `?vehicle_number=${vehicleNumber}`;
            const response = await fetch(url);
            // const response = await fetch(`https://evms.greendrivemobility.com/admin/mobitra-api/get-user-devices?vehicle_number=${vehicleNumber}`);
            const data = await response.json();
            
            if (data.status === 200 && data.data.payload.totalRecords > 0) {
                return data.data.payload.deviceList[0];
            }
            return null;
        } catch (error) {
            console.error('Error fetching vehicle data:', error);
            return null;
        }
    }
    
    // Load vehicle status card
    function loadVehicleCard(vehicleStatus, battery, speed, distance, roleName, prRoleName) {
        const statusContainer = document.querySelector('.vehicle-status-container');
        if (!statusContainer) return;
        
        // Determine battery status text and color
        let batteryText = 'Unknown';
        let batteryColor = 'text-secondary';
        
        if (battery) {
            const batteryLevel = parseFloat(battery);
            if (batteryLevel >= 70) {
                batteryText = 'High';
                batteryColor = 'text-success';
            } else if (batteryLevel >= 30) {
                batteryText = 'Medium';
                batteryColor = 'text-warning';
            } else {
                batteryText = 'Low';
                batteryColor = 'text-danger';
            }
        }
        
        // Determine status color
        let statusColor = 'text-secondary';
        if (vehicleStatus === 'running') statusColor = 'text-success';
        else if (vehicleStatus === 'stopped') statusColor = 'text-warning';
        else if (vehicleStatus === 'offline') statusColor = 'text-danger';
        
        // Capitalize status
        const statusText = vehicleStatus ? vehicleStatus.charAt(0).toUpperCase() + vehicleStatus.slice(1) : 'Unknown';
        
        // Update the status card
        statusContainer.innerHTML = `
            <div class="bg-white rounded-3"  >
                <div class="border-bottom pb-3 mb-3" style="padding:8px;height:6.3vh">
                    <h6 class="mb-0 fw-semibold" style="font-size:14px">Vehicle Status</h6>
                </div>
                <div class="vehicle-status-card" style="overflow-y: auto;">
                <div class="row g-2 mb-2" style="height:9.3vh;">
                    <div class="col-4">
                        <div class="status-card text-center">
                            <div class="small text-muted" style="font-size:12px">Speed</div>
                            <div class="status-value fw-semibold" style="font-size:13px">${speed || '0'} Km/h</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-card text-center">
                            <div class="small text-muted" style="font-size:12px">Total Distance</div>
                            <div class="status-value fw-semibold" style="font-size:13px">${distance || '0'} Km</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-card text-center">
                            <div class="small text-muted" style="font-size:12px">Battery</div>
                            <div class="status-value fw-semibold ${batteryColor}" style="font-size:13px">${batteryText}</div>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2" style="height:9.3vh;">
                    <div class="col-4">
                        <div class="status-card text-center">
                            <div class="small text-muted" style="font-size:12px">Status</div>
                            <div class="status-value fw-semibold ${statusColor}" style="font-size:13px">${statusText}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-card text-center">
                            <div class="small text-muted" style="font-size:12px">City</div>
                            <div class="status-value fw-semibold" style="font-size:13px">${prRoleName || '-'}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-card text-center">
                            <div class="small text-muted" style="font-size:12px">Location</div>
                            <div class="status-value fw-semibold" style="font-size:13px">${roleName || '-'}</div>
                        </div>
                    </div>
                    
                </div>
            </div>
            </div>
        `;
    }
    
    // Load vehicle data and update UI
    async function loadVehicleData(vehicleId, vehicleElement) {
        // Show loading state for status card
        const statusContainer = document.querySelector('.vehicle-status-container');
        if (statusContainer) {
            statusContainer.innerHTML = `
                <div class="bg-white rounded-3 p-3 text-center">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0 small text-muted">Loading status...</p>
                </div>
            `;
        }
        
        const vehicleData = await fetchVehicleData(vehicleId);
        
        if (!vehicleData) {
            document.querySelector('.vehicle-details-section').innerHTML = `
                <div class="text-center text-danger py-5">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                    <p class="mt-2">No data found for selected vehicle</p>
                </div>
            `;
            
            if (statusContainer) {
                statusContainer.innerHTML = `
                    <div class="bg-white rounded-3 p-3 text-center text-danger" >
                        <i class="bi bi-exclamation-triangle"></i>
                        <p class="mt-2 mb-0 small">No status data available</p>
                    </div>
                `;
            }
            return;
        }
        
        currentVehicleData = vehicleData;
        updateVehicleUI(vehicleData, vehicleElement);
    }
    
    // Update UI with vehicle data
    function updateVehicleUI(vehicleData, vehicleElement) {
        // Get coordinates from vehicle data
        const vehicleLat = vehicleData.latitude || 20.5937;
        const vehicleLng = vehicleData.longitude || 78.9629;
        
        // Update map with coordinates
        // updateMapWithCoordinates(vehicleLat, vehicleLng, {
        //     vehicleNumber: vehicleData.vehicleNumber,
        //     imei: vehicleData.imei,
        //     vehicleType: vehicleData.deviceType
        // });
        
        // Update vehicle details section
        const additionalInfo = vehicleData.additionalInfo || {};
        
        document.querySelector('.vehicle-details-section').innerHTML = `
        <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
                        <span class="text-decoration-none text-dark active-link cursor-pointer" style="font-size:14px">Vehicle Details</span>
        </div>
            <div class="table-responsive" style="max-height:42vh;overflow-y: auto;">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <!-- Basic Information -->
                        <tr>
                            <th scope="row" class="fw-semibold w-25" style="font-size:14">Vehicle Number</th>
                            <td style="font-size:14">${vehicleData.vehicleNumber || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Vehicle Type</th>
                            <td style="font-size:14">${vehicleData.vehicleType || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">IMEI</th>
                            <td style="font-size:14">${vehicleData.imei || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Device Type</th>
                            <td style="font-size:14">${vehicleData.deviceType || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Display Number</th>
                            <td style="font-size:14">${vehicleData.displayNumber || '-'}</td>
                        </tr>
                        
                        <!-- Vehicle Specifications -->
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Color</th>
                            <td style="font-size:14">${additionalInfo.color || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Variant</th>
                            <td style="font-size:14">${additionalInfo.variant || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Customer</th>
                            <td style="font-size:14">${additionalInfo.customer || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Registration Type</th>
                            <td style="font-size:14">${additionalInfo.reg_type || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Ownership</th>
                            <td style="font-size:14">${additionalInfo.ownership || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Registration Status</th>
                            <td style="font-size:14">${additionalInfo.reg_status || '-'}</td>
                        </tr>
                        
                        <!-- Battery Information -->
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Battery Type</th>
                            <td style="font-size:14">${additionalInfo.battery_type || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Battery Variant</th>
                            <td style="font-size:14">${additionalInfo.battery_variant_name || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Battery Serial</th>
                            <td style="font-size:14">${additionalInfo.battery_serial_number || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Charger Variant</th>
                            <td style="font-size:14">${additionalInfo.charger_variant_name || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Charger Serial</th>
                            <td style="font-size:14">${additionalInfo.charger_serial_num || '-'}</td>
                        </tr>
                        
                        <!-- Insurance Information -->
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Insurer Name</th>
                            <td style="font-size:14">${additionalInfo.insurer_name || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Insurance Type</th>
                            <td style="font-size:14">${additionalInfo.insurance_type || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Insurance Number</th>
                            <td style="font-size:14">${additionalInfo.insurance_number || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Insurance Start</th>
                            <td style="font-size:14">${additionalInfo.insurance_start_dt || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Insurance End</th>
                            <td style="font-size:14">${additionalInfo.insurance_end_dt || '-'}</td>
                        </tr>
                        
                        <!-- Registration & Compliance -->
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Permanent Reg No.</th>
                            <td>${additionalInfo.permanent_reg_num || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Permanent Reg Date</th>
                            <td>${additionalInfo.permanent_reg_date || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Registration Cert Expiry</th>
                            <td style="font-size:14">${additionalInfo.reg_cert_exp_dt || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Fitness Cert Expiry</th>
                            <td style="font-size:14">${additionalInfo.fit_cert_exp_dt || '-'}</td>
                        </tr>
                        
                        <!-- Financial Information -->
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Lease Amount</th>
                            <td style="font-size:14">${additionalInfo.lease_amount || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Master Lease</th>
                            <td style="font-size:14">${additionalInfo.master_lease_agreement || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Lease Start Date</th>
                            <td style="font-size:14">${additionalInfo.lease_start_date || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Lease End Date</th>
                            <td style="font-size:14">${additionalInfo.lease_end_date || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Financing Type</th>
                            <td style="font-size:14">${additionalInfo.financing_type || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Hypothecation</th>
                            <td style="font-size:14">${additionalInfo.hypothecation || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Hypothecated To</th>
                            <td style="font-size:14">${additionalInfo.hypothecated_to || '-'}</td>
                        </tr>
                        
                        <!-- Other Information -->
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Engine Number</th>
                            <td style="font-size:14">${additionalInfo.engine_number || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Inventory Status</th>
                            <td style="font-size:14"> ${additionalInfo.inventory_status || '-'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="fw-semibold" style="font-size:14">Vehicle Delivery Date</th>
                            <td style="font-size:14">${additionalInfo.vehicle_delivery_date || '-'}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
        
        // Update status card with vehicle data
        if (vehicleElement) {
            const vehicleStatus = vehicleElement.getAttribute('data-vehicle-status');
            const battery = vehicleElement.getAttribute('data-battery') || '70'; // Default to 70% if not available
            const speed = vehicleElement.getAttribute('data-last-speed') || '0';
            const distance = vehicleElement.getAttribute('data-distance-travelled') || '0';
            const roleName = vehicleElement.getAttribute('data-roleName') || 'Driver';
            const prRoleName = vehicleElement.getAttribute('data-prRoleName') || 'Primary';
            
            loadVehicleCard(vehicleStatus, battery, speed, distance, roleName, prRoleName);
        }
    }
    
    // Function to apply filter to vehicle list
    function applyFilter(filter) {
        const vehicleCards = document.querySelectorAll('.vehicle-card');
        
        vehicleCards.forEach(card => {
            const status = card.getAttribute('data-vehicle-status').toLowerCase();
            
            if (filter === 'all' || status === filter.toLowerCase()) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Function to perform search
    function performSearch() {
        const searchTerm = document.getElementById('vehicleSearch').value.trim().toLowerCase();
        const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
        const vehicleCards = document.querySelectorAll('.vehicle-card');
        
        vehicleCards.forEach(card => {
            const vehicleNumber = card.getAttribute('data-vehicle-id').toLowerCase();
            const imeiNumber = card.getAttribute('data-imei-number').toLowerCase();
            const status = card.getAttribute('data-vehicle-status').toLowerCase();
            
            const matchesSearch = vehicleNumber.includes(searchTerm) || imeiNumber.includes(searchTerm);
            const matchesFilter = activeFilter === 'all' || status === activeFilter.toLowerCase();
            
            if (matchesSearch && matchesFilter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    function populateRunningVehiclesDropdown() {
        const runningVehiclesList = document.getElementById('runningVehiclesList');
        runningVehiclesList.innerHTML = ''; // Clear existing items
        
        // Get all vehicle cards
        const vehicleCards = document.querySelectorAll('.vehicle-card');
        let runningVehicles = [];
        
        // Filter running vehicles
        vehicleCards.forEach(card => {
            const status = card.getAttribute('data-vehicle-status');
            if (status === 'running') {
                runningVehicles.push({
                    id: card.getAttribute('data-vehicle-id'),
                    imei: card.getAttribute('data-imei-number')
                });
            }
        });
        
        // If less than 10 running vehicles, add idle vehicles
        if (runningVehicles.length < 10) {
            vehicleCards.forEach(card => {
                const status = card.getAttribute('data-vehicle-status');
                if (status === 'offline' && runningVehicles.length < 10) {
                    runningVehicles.push({
                        id: card.getAttribute('data-vehicle-id'),
                        imei: card.getAttribute('data-imei-number')
                    });
                }
            });
        }
        
        // If still less than 10, add stopped vehicles
        if (runningVehicles.length < 10) {
            vehicleCards.forEach(card => {
                const status = card.getAttribute('data-vehicle-status');
                if (status === 'stopped' && runningVehicles.length < 10) {
                    runningVehicles.push({
                        id: card.getAttribute('data-vehicle-id'),
                        imei: card.getAttribute('data-imei-number')
                    });
                }
            });
        }
        
        // Add vehicles to dropdown
        if (runningVehicles.length === 0) {
            const noVehiclesItem = document.createElement('li');
            noVehiclesItem.innerHTML = '<span class="dropdown-item text-muted">No vehicles available</span>';
            runningVehiclesList.appendChild(noVehiclesItem);
        } else {
            runningVehicles.forEach(vehicle => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `
                    <a class="dropdown-item vehicle-item" href="#" data-vehicle-number="${vehicle.id}">
                        <div class="vehicle-info">
                            <div class="vehicle-number">${vehicle.id}</div>
                        </div>
                    </a>
                `;
                runningVehiclesList.appendChild(listItem);
            });
        }
    }
            
    // Tab indicator functionality
    function setupTabIndicator() {
        const tabs = document.querySelectorAll('.filter-btn');
        const indicator = document.querySelector('.active-tab-indicator');
        const container = document.querySelector('.filter-tabs-container > div');
        
        function updateIndicator(activeTab) {
            const rect = activeTab.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();
            
            indicator.style.width = `${rect.width}px`;
            indicator.style.left = `${rect.left - containerRect.left + container.scrollLeft}px`;
        }
        
        // Initialize with first active tab
        const initialActiveTab = document.querySelector('.filter-btn.active');
        if (initialActiveTab && indicator) {
            updateIndicator(initialActiveTab);
        }
        
        // Update on click
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all buttons
                tabs.forEach(t => t.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                applyFilter(filter);
                
                if (indicator) {
                    updateIndicator(this);
                }
                
                // Scroll to make the tab visible if needed
                this.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            });
        });
        
        // Update on scroll and resize
        if (container && indicator) {
            container.addEventListener('scroll', function() {
                const activeTab = document.querySelector('.filter-btn.active');
                if (activeTab) {
                    updateIndicator(activeTab);
                }
            });
        }
        
        window.addEventListener('resize', function() {
            const activeTab = document.querySelector('.filter-btn.active');
            if (activeTab && indicator) {
                updateIndicator(activeTab);
            }
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Set up tab indicator
        setupTabIndicator();
        
        // Set up event listeners
        const vehicleCards = document.querySelectorAll('.vehicle-card');
        const filterButtons = document.querySelectorAll('.filter-btn');
        const searchInput = document.getElementById('vehicleSearch');
        const refreshBtn = document.getElementById('refreshBtn');
        populateRunningVehiclesDropdown();
            
        // Set up event listeners for dropdown items
        document.getElementById('runningVehiclesList').addEventListener('click', function(e) {
            // Check if the clicked element or its parent has the data-vehicle-number attribute
            const target = e.target.closest('[data-vehicle-number]');
            if (target) {
                const vehicleNumber = target.getAttribute('data-vehicle-number');
                document.getElementById('vehicleSearch').value = vehicleNumber;
                performSearch();
                
                // Close the dropdown
                const dropdown = document.getElementById('runningVehiclesDropdown');
                const bootstrapDropdown = bootstrap.Dropdown.getInstance(dropdown);
                if (bootstrapDropdown) {
                    bootstrapDropdown.hide();
                }
            }
        });
        
        // Event listeners for vehicle cards
        vehicleCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove highlight from previously selected card
                if (selectedVehicleCard) {
                    selectedVehicleCard.classList.remove('border-primary');
                }
                
                // Add highlight to selected card
                this.classList.add('border-primary');
                selectedVehicleCard = this;
                
                const vehicleId = this.getAttribute('data-vehicle-id');
                const lat = this.getAttribute('data-latitude');
                const lng = this.getAttribute('data-longitude');
                const vehicleNumber = this.getAttribute('data-vehicle-id');
                const imei = this.getAttribute('data-imei-number');
                const vehicleType = this.getAttribute('data-vehicle-type');
                const vehicleStatus = this.getAttribute('data-vehicle-status');
                const battery = this.getAttribute('data-battery') || '70';
                const speed = this.getAttribute('data-last-speed') || '0';
                const distance = this.getAttribute('data-distance-travelled') || '0';
                const roleName = this.getAttribute('data-roleName') || 'Driver';
                const prRoleName = this.getAttribute('data-prRoleName') || 'Primary';
                
                if(vehicleStatus=="no_data"){
                    return;
                }
                // Update the map with these coordinates
                updateMapWithCoordinates(lat, lng, {
                    vehicleNumber: vehicleNumber,
                    imei: imei,
                    vehicleType: vehicleType
                });
                
                // Immediately update status card with basic data
                loadVehicleCard(vehicleStatus, battery, speed, distance, roleName, prRoleName);
                
                // Load detailed vehicle data
                loadVehicleData(vehicleId, this);
            });
        });
        
        // Filter buttons functionality
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                applyFilter(filter);
            });
        });
        
        // Search functionality - search as you type
        searchInput.addEventListener('input', function() {
            performSearch();
        });
        
        document.getElementById('vehicleSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        // Refresh button functionality
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                resetToDefaultState();
            });
        }
        
        // Initialize with first vehicle if available
        if (vehicleCards.length > 0) {
            const firstVehicleCard = vehicleCards[0];
            const firstVehicleId = firstVehicleCard.getAttribute('data-vehicle-id');
            const lat = firstVehicleCard.getAttribute('data-latitude');
            const lng = firstVehicleCard.getAttribute('data-longitude');
            const vehicleNumber = firstVehicleCard.getAttribute('data-vehicle-id');
            const imei = firstVehicleCard.getAttribute('data-imei-number');
            const vehicleType = firstVehicleCard.getAttribute('data-vehicle-type');
            const vehicleStatus = firstVehicleCard.getAttribute('data-vehicle-status');
            const battery = firstVehicleCard.getAttribute('data-battery') || '70';
            const speed = firstVehicleCard.getAttribute('data-last-speed') || '0';
            const distance = firstVehicleCard.getAttribute('data-distance-travelled') || '0';
            const roleName = firstVehicleCard.getAttribute('data-roleName') || 'Driver';
            const prRoleName = firstVehicleCard.getAttribute('data-prRoleName') || 'Primary';
            
            // Update the map with these coordinates
            updateMapWithCoordinates(lat, lng, {
                vehicleNumber: vehicleNumber,
                imei: imei,
                vehicleType: vehicleType
            });
            
            // Immediately update status card with basic data
            loadVehicleCard(vehicleStatus, battery, speed, distance, roleName, prRoleName);
            
            // Load detailed vehicle data
            loadVehicleData(firstVehicleId, firstVehicleCard);
            
            // Highlight the first vehicle card
            firstVehicleCard.classList.add('border-primary');
            selectedVehicleCard = firstVehicleCard;
        }
    });

    // Handle Google Maps API errors
    window.gm_authFailure = function() {
        console.error('Google Maps authentication failed');
        alert('Google Maps failed to load. Please check your API key.');
    };
</script>
    @endpush
</x-app-layout>