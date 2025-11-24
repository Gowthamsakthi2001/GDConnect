<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- meta manager --}}
    <x-meta-manager />
    {{-- favicon --}}
    <x-favicon />
    {{-- style --}}
    <x-admin.styles />
    <x-language::localizer />
    @yield('css')
    
<style>

    /* Force center on screens smaller than 768px */
@media (max-width: 767.98px) {
  .footer-text .row,
  .footer-text .copy,
  .footer-text .credit,
  .footer-text .col-12 {
    text-align: center !important;
    justify-content: center !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
  }
}
.navbar-custom-menu .navbar-nav {
    gap: 0 !important;          /* Remove any gap spacing */
}
.navbar-custom-menu .navbar-nav .nav-item {
    margin: 0 !important;       /* Remove left/right margins */
    padding: 0 !important;      /* Remove padding */
}

/* Default hidden for mobile */
@media (max-width: 991px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: -270px; /* hidden off-screen */
        height: 100%;
        width: 250px;
        background: #fff;
        box-shadow: 2px 0 8px rgba(0,0,0,0.2);
        transition: left 0.3s ease;
        z-index: 1050;
    }

    .sidebar.active {
        left: 0; /* slide in */
    }

    /* Optional overlay effect */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
    }
    .sidebar-overlay.active {
        display: block;
    }
}


.sidebar-toggle-btn-mobile {
    display: none;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    padding: 8px;
    color: #1a1a1a;
}

@media (max-width: 991px) {
    .sidebar-toggle-btn-mobile {
        display: block;
    }
}
    
body {
    background-color: #f8f9fa;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.main-content-wrapper {  
    padding-top: 60px;
    transition: padding-left 0.3s ease;
}

body.sidebar-minimized .main-content-wrapper {
    padding-left: 70px;
}

/* Make sidebar content scrollable */

    #mainSidebar {
        height: 100vh;            
        overflow-y: auto;         
        overflow-x: hidden;      
        -webkit-overflow-scrolling: touch; 
    }
    
    #mainSidebar::-webkit-scrollbar {
        display: none; 
    }
    
    body.sidebar-open {
        overflow: hidden;
    }
    
/*here ends the scroll code    */
    

/* ==== Default Desktop View (>1024px) ==== */
.sidebar {
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    background: #fff;
    transition: all 0.3s ease;
    z-index: 1050;
}

.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

/* Default: Desktop */
.content-wrapper {
    margin-left: 260px; /* space for sidebar */
    transition: margin-left 0.3s ease;
}

/* Mobile & Tablet */
@media (max-width: 991px) {
    .content-wrapper {
        margin-left: 0; /* full width */
    }

    /* When sidebar is active, you can optionally push content */
    .content-wrapper.sidebar-active {
        margin-left: 260px; /* same as sidebar width */
    }
     .sidebar {
        left: -260px;
        transition: left 0.3s ease;
    }
    
    .sidebar.mobile-active {
        left: 0;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1050;
    }
    
    .content-wrapper {
        margin-left: 0;
    }
    
    body.sidebar-open {
        overflow: hidden;
    }
}



/* ==== Tablet View (≤ 1024px) ==== */
/*@media (max-width: 1024px) {*/
/*    .sidebar {*/
/*        width: 200px;*/
/*    }*/
/*    .content-wrapper {*/
/*        margin-left: 200px;*/
/*    }*/
/*}*/

/* ==== Mobile View (≤ 768px) ==== */
@media (max-width: 768px) {
    .sidebar-toggle-btn{
        display:none;
    }
}

/* ==== Extra Small Mobile (≤ 480px) ==== */
/*@media (max-width: 480px) {*/
/*    .sidebar {*/
/*        left: -200px;*/
/*        width: 200px;*/
/*    }*/
/*    .sidebar.active {*/
/*        left: 0;*/
/*    }*/
/*}*/



/* Sidebar Base */
/* Sidebar header */
.sidebar-header {
    display: flex;
    justify-content: space-between; /* logo left, button right */
    align-items: center;
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
    background-color: #fff;
}

/* Logo section */
.sidebar-brand {
    display: flex;
    align-items: center;
    text-decoration: none;
}

.sidebar-logo-sm {
    width: 36px;
    height: 36px;
    object-fit: contain;
    margin-right: 10px;
}

.brand-text {
    font-size: 18px;
    font-weight: 400;
    color: #1a1a1a;
    letter-spacing: 0.5px;
}

/* Toggle button */
.sidebar-toggle-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.sidebar-toggle-btn:hover {
    transform: scale(1.05);
}

.sidebar.collapsed {
    /*transform: rotate(180deg);*/
    width: 70px;
    min-width:70px;
    max-width:80px;
    
}
.sidebar-brand{
   max-width:60px;
    max-height:80px; 
}

/* Hide the input, keep only the icon */
.sidebar.collapsed .search-box{
    display: none;
}

.sidebar.collapsed .nav-container .sidebar-menu .nav-link{
    margin: 4px 5px;
}

/* Rotate toggle arrow when collapsed */
.sidebar.collapsed .sidebar-toggle-btn i {
    /*transform: rotate(180deg);*/
    width: 100%;
}



.content-wrapper.collapsed {
    /*transform: rotate(180deg);*/
    margin-left: 70px;
}


/* Navigation Links */
.sidebar-menu .nav-link {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 12px 18px;
    margin: 4px 10px;
    color: #1A1A1A;
    text-decoration: none;
    font-size: 15px;
    border-radius: 10px;
    transition: background 0.3s, color 0.3s;
    white-space: nowrap;
    position: relative;
}

.sidebar-menu .nav-link svg,
.sidebar-menu .nav-link i {
    font-size: 18px;     /* keep icon size fixed */
    color: inherit;
    min-width: 24px;     /* make sure spacing is stable */
    height: 24px;
    margin-right: 12px;  /* spacing for expanded mode */
    transition: margin 0.3s ease;
}

/* Hide menu text and center icons when collapsed */
.sidebar.collapsed .nav-link span.nav-text {
    display: none;
}

.sidebar.collapsed .brand-text {
    display: none;
}

.sidebar.collapsed .nav-link svg,
.sidebar.collapsed .nav-link i {
    margin-right: 0;     /* remove extra spacing */
    text-align: center;
    width: auto;         /* don’t stretch */
    min-width: 20px;
    height: 20px;
}

/* Hover Effect */
.sidebar-menu .nav-link:hover {
    background: #f1f5ff;
    color: #2563eb;
}

/* Active Link */
.sidebar-menu .nav-link.active {
    background: #2563EB1A;
    color: #2563eb;
    font-weight: 500;
}

/* Submenu */
.sub-menu .dropdown-item {
    display: block;
    font-size: 15px;
    border-radius: 10px;
    padding: 8px 14px;
    margin: 0px -5px;
    color: #333;
    text-decoration: none;
    transition: background 0.3s, color 0.3s;
}

.sub-menu .dropdown-item:hover {
    background: #f1f5ff;
    color: #2563eb;
}


/* Active Child Link */
.sidebar-menu .sub-menu .dropdown-item.active {
    background: #e5edff;   /* even lighter background */
    color: #2563eb;
    font-weight: 500;
    border-radius: 6px;    /* optional for nicer look */
}


/* Chevron Rotation for Dropdowns */
.sidebar-menu .nav-link[aria-expanded="true"] .chevron {
    transform: rotate(90deg);
    transition: transform 0.3s ease;
}

/* Tooltip for collapsed sidebar */
.tooltip-text {
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: #1a1a1a;
    color: #fff;
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    margin-left: 8px;
}

.sidebar.collapsed .nav-link:hover .tooltip-text {
    opacity: 1;
}

/* Hide chevron in collapsed state */
.sidebar.collapsed .chevron {
    display: none;
}





/* Footer menu is fixed at bottom */
.nav-section .sidebar-footer {
    padding-top: 0.5rem;
}

.search-box {
    padding: 16px 20px; /* keep this as requested */
    position: relative;
    width: 100%;
    max-width: 300px;
    margin: 10px auto;
    box-sizing: border-box;
}

.search-box .search-icon {
    position: absolute;
    top: 50%;
    left: 28px; /* adjust for container padding */
    transform: translateY(-50%);
    color: #555;
    font-size: 18px;
    pointer-events: none;
}

.search-box .search-input {
    width: 100%;
    height: 40px;
    padding: 10px 44px;       /* right padding */
    padding-left: 48px;       /* left padding for icon */
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #fff;
    font-size: 14px;
    color: #333;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    box-sizing: border-box;
}

.search-box .search-input:focus {
    border-color: #4CAF50;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
    outline: none;
}

.search-box .search-input::placeholder {
    color: #aaa;
}

/* Default: show text */
.sidebar .nav-text {
    display: inline;
}

/* When minimized: hide text but keep icons */
.sidebar-minimized .nav-text {
    display: none;
}

.sidebar-minimized .nav-icon {
    display: inline-block;
}

.sub-menu .menu-text {
  display: inline;
}

/*.sidebar.collapsed img{*/
/*    max-width:60px;*/
/*    max-height:60px;*/
/*    object-fit: contain;*/
/*}*/

.sidebar-brand img{
    max-width:120px;
    max-height:120px;
    object-fit: contain;
}

.sidebar.collapsed .sidebar-header .sidebar-brand img{
    max-width:60px;
    max-height:60px;
    object-fit: contain;
}
/* Mini mode (collapsed sidebar): hide submenu text */
#mainSidebar.collapsed .sub-menu .menu-text {
  display: none;
}

.sub-menu .nav-text {
    display: inline-block;
}

/* When sidebar is collapsed → hide the text inside dropdown */
#mainSidebar.collapsed .sub-menu .nav-text {
    display: none !important;
}

.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* When collapsed (mini mode) */
.sidebar.collapsed .sidebar-header {
    flex-direction: column;       /* stack logo and toggle vertically */
    align-items: center;          /* center align */
    justify-content: center;
    padding: 10px 0;
    margin-top:16px;
}

.sidebar.collapsed .sidebar-toggle-btn {
    margin-top: 8px;              /* spacing below logo */
    align-self: center;           /* center button */
}

/* Show left arrow by default */
.arrow-left {
    display: inline-block;
}
.arrow-right {
    display: none;
}

/* When collapsed: show right arrow */
.sidebar.collapsed .arrow-left {
    display: none;
}
.sidebar.collapsed .arrow-right {
    display: inline-block;
}

@media (max-width: 991px) { /* Adjust breakpoint if needed */
    .arrow-left {
        display: none !important;
    }
}


/* Default size for mobile */
.profile-img {
    width: 32px;
    height: 32px;
}

/* Small screens (sm ≥ 576px) */
@media (min-width: 576px) {
    .profile-img {
        width: 40px;
        height: 40px;
    }
}

/* Medium screens (md ≥ 768px) */
@media (min-width: 768px) {
    .profile-img {
        width: 45px;
        height: 45px;
    }
}

/* Large screens (lg ≥ 992px) */
@media (min-width: 992px) {
    .profile-img {
        width: 50px;
        height: 50px;
    }
}

/* Extra large screens (xl ≥ 1200px) */
@media (min-width: 1200px) {
    .profile-img {
        width: 55px;
        height: 55px;
    }
}


/* Wrap and truncate text */
.navbar-user-text {
    max-width: 200px; /* adjust width for your layout */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
    transition: max-width 0.3s ease;
}

/* On larger screens, allow more space */
@media (min-width: 768px) {
    .navbar-user-text {
        max-width: 250px;
    }
}

</style>
</head>

<body >
        <!-- Preloader -->
    <x-admin.preloader />
    <!-- react page -->
    <div id="app">
        <!-- Begin page -->
        <div class="wrapper">
            @unless(request()->routeIs('b2b.tracking'))
                <x-b2b.left-sidebar />   <!-- fixed sidebar -->
                <div class="content-wrapper" id="contentWrapper" style="overflow: hidden;height:100vh">
            @endunless
            
            
                @unless(request()->routeIs('b2b.tracking'))
                        <x-b2b.header />
                    @endunless
               
                    
                    
                    @if(request()->routeIs('b2b.tracking'))
                     <div class="main-content">
                <div class="body-content-b2b" >
                    @else
                     <div class="main-content" style="overflow:auto;height: calc(100vh - 65px);">
                    <div class="body-content-b2b" style="padding:16px;overflow:auto">
            @endif
                    
                        @yield('content')
                    </div>
                </div>
                @unless(request()->routeIs('b2b.tracking'))
                        <x-admin.footer />
                        </div>
                    @endunless
                
            
        </div>
        <!--end  vue page -->
    </div>
    <!-- END layout-wrapper -->

    @stack('modal')
   
    <!-- start scripts -->
    <x-admin.scripts />
    <!-- end scripts -->
    <x-toster-session />
      @yield('js')
     <script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById('mainSidebar');
        const contentWrapper = document.getElementById('contentWrapper');
        const sidebarBrand = document.getElementById('sidebarBrand');
        const desktopToggleBtn = document.getElementById('sidebarToggle');
        const mobileToggleBtn = document.getElementById('sidebarToggleMobile');
        
        // Create overlay for mobile
        const sidebarOverlay = document.createElement('div');
        sidebarOverlay.classList.add('sidebar-overlay');
        document.body.appendChild(sidebarOverlay);

        // Desktop toggle functionality
        if (desktopToggleBtn) {
            desktopToggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                contentWrapper.classList.toggle('collapsed');
                sidebarBrand.classList.toggle('collapsed');
                
                // Close dropdowns when collapsing sidebar
                if (sidebar.classList.contains('collapsed')) {
                    closeAllDropdowns();
                }
            });
        }

        // Mobile toggle functionality
        if (mobileToggleBtn) {
            mobileToggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-active');
                sidebarOverlay.classList.toggle('active');
                document.body.classList.toggle('sidebar-open');
            });
        }

        // Close sidebar when clicking outside on mobile
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-active');
            sidebarOverlay.classList.remove('active');
            document.body.classList.remove('sidebar-open');
        });

        // Search functionality
        const searchInput = document.querySelector(".search-input");
        const navLinks = document.querySelectorAll(".sidebar-menu .nav-link");

        if (searchInput) {
            searchInput.addEventListener("input", function() {
                const query = this.value.toLowerCase();
                navLinks.forEach(link => {
                    const text = link.textContent.toLowerCase();
                    if (text.includes(query)) {
                        link.closest("li").style.display = "block";
                    } else {
                        link.closest("li").style.display = "none";
                    }
                });
            });
        }

        // Close all dropdowns function
        function closeAllDropdowns() {
            const openDropdowns = sidebar.querySelectorAll(".collapse.show");
            openDropdowns.forEach(dropdown => {
                const bsCollapse = bootstrap.Collapse.getInstance(dropdown);
                if (bsCollapse) {
                    bsCollapse.hide();
                } else {
                    dropdown.classList.remove("show");
                }
            });
        }

        // Close sidebar when clicking anywhere outside sidebar
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 991 && 
                sidebar.classList.contains('mobile-active') &&
                !event.target.closest('#mainSidebar') &&
                !event.target.closest('#sidebarToggleMobile')) {
                
                sidebar.classList.remove('mobile-active');
                sidebarOverlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
        });
    });
</script>

  </script>
  

</body>

</html>
