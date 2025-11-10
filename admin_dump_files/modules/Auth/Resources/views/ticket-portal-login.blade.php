<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket Portal Login</title>
    <link rel="shortcut icon" href="{{url('/')}}/storage/setting/ycsbDAa4bOn4ouFfSKkJ0o5C8prSzthSJEUHG078.png?v=1"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" />
    <link href="{{ admin_asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="{{ nanopkg_asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ nanopkg_asset('vendor/toastr/build/toastr.min.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .loginin-btn {
            background: rgb(82, 197, 82);
            color: white;
            padding: 13px;
        }

        .loginin-btn:hover {
            color: rgb(82, 197, 82);
            background: white;
            border: 1px solid rgb(82, 197, 82);
        }

        .btn-web-portal {
            color: #344054;
            border: 1px solid #d0d5dd;
            padding: 13px;
        }

        .btn-web-portal:hover {
            color: #344054;
            border: 1px solid #344054;
        }

        .auth-box {
            max-width: 100%;
            width: 100%;
            max-width: 400px;
        }

        .side-image {
            max-height: 100vh;
            object-fit: cover;
        }
        hr {
            height: 0;
            border-top: 3px solid black;
        }

        input::placeholder {
          font-size: 0.85rem; 
          color:#b8bfcb;
        }
    </style>
  </head>
  <body>

    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 align-items-center">
            <div class="col-md-2 text-center d-none d-md-block">
                <img src="{{ asset('public/admin-assets/img/tc_side_2.png') }}" class="img-fluid side-image">
            </div>

            <div class="col-12 col-md-8 d-flex flex-column align-items-center justify-content-center">
                <a href="javascript:void(0);">
                    <img src="{{ setting('site.logo_black', admin_asset('img/sidebar-logo.png'), true) }}" class="img-fluid mb-3" style="max-width:150px;">
                </a>

                <div class="bg-white shadow p-4 auth-box rounded">
                    <h5>Login</h5>
                    <p>Enter your credentials to access the portal</p><br>
                    <form class="mt-3" method="POST" action="{{route('user.ticket_portal.login')}}">
                        @csrf
                        <div class="mb-3">
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
                                name="email" placeholder="Email" required autocomplete="email" value="{{ old('email') }}">
                            <span class="invalid-feedback text-start"></span>
                            @error('email')
                                <span class="invalid-feedback text-start" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-input mb-3 position-relative">
                            <input 
                                class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                type="password" 
                                name="password" 
                                placeholder="Password" 
                                id="password" 
                                required 
                                value="{{ old('password') }}">
                                
                            <button type="button" id="togglePassword" 
                                class="position-absolute top-50 end-0 translate-middle-y me-3" 
                                style="z-index: 10; border:none; background:white; color:#b8bfcb;" 
                                tabindex="-1">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        
                            @error('password')
                                <span class="invalid-feedback text-start" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <button type="submit" class="btn loginin-btn w-100">{{ localize('Sign In') }}</button>

                       <div class="d-flex align-items-center my-3">
                            <hr class="flex-grow-1 border-muted">
                            <span class="px-2 text-muted">OR</span>
                            <hr class="flex-grow-1 border-muted">
                        </div>


                        <a href="{{route('admin.web.vehicle-ticket.create')}}" class="btn btn-web-portal w-100">
                            <img src="{{ asset('public/admin-assets/img/portal_user.png') }}" class="img-fluid me-2" style="max-height: 24px;">
                            Continue as Web Portal User
                        </a>
                    </form>
                </div>
            </div>

            <div class="col-md-2 text-center d-none d-md-block">
                <img src="{{ asset('public/admin-assets/img/tc_side_1.png') }}" class="img-fluid">
            </div>
        </div>
    </div>

   
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{ admin_asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ nanopkg_asset('vendor/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ nanopkg_asset('vendor/toastr/build/toastr.min.js') }}"></script>
    
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
    
        togglePassword.addEventListener('click', function () {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
    
            // toggle the eye / eye-slash icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>

  </body>
</html>
