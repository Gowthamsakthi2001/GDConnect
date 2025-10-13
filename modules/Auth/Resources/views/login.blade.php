
<x-guest-layout>
    
  <style>
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
      width: 100%;
      height: 100vh;
      display: flex;
      border-radius: 0;        
      box-shadow: none;        
      background-color: #fff;   
    }

    .left-panel {
      flex: 0 0 40%;
      position: relative;
      background: linear-gradient(160deg, #0c0c0c 0%, #1a1f14 25%, #5fa443 70%, #7ec556 100%);
      color: white;
      padding: 3rem 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      font-weight: 500;
      overflow: hidden;
    }

    .left-panel::before {
      content: "";
      position: absolute;
      bottom: -120px;
      left: -120px;
      width: 450px;
      height: 450px;
      background: radial-gradient(circle, #84d14f 0%, rgba(132, 209, 79, 0) 70%);
      border-radius: 50%;
      z-index: 0;
    }

    .left-panel h2,
    .left-panel p {
      position: relative;
      z-index: 1;
    }

    .left-panel h2 {
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 1.5rem;
      line-height: 1.3;
    }

    .left-panel p {
      font-size: 1rem;
      opacity: 0.9;
      max-width: 100%;
      margin-top: 0;
      line-height: 1.6;
    }

    /* RIGHT PANEL at 60% width */
    .right-panel {
      flex: 0 0 60%;
      background-color: #fff;
      padding: 2rem 3rem;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      box-sizing: border-box;
      overflow-y: auto;
    }

    .logo-container {
      display: flex;
      justify-content: center;
      margin-bottom: 1rem;
    }

    .title {
      font-weight: 700;
      font-size: 1.5rem;
      text-align: center;
      margin-bottom: 0.5rem;
    }

    .form-label {
      font-weight: 600;
      margin-bottom: 0.3rem;
    }

    form .form-control {
      border-radius: 0.35rem;
      padding-left: 2.6rem;
      height: 2.9rem;
      font-size: 0.9rem;
    }

    .form-group {
      position: relative;
      margin-bottom: 1.25rem;
    }

    form {
      padding: 30px 100px;
    }

    .input-wrap {
      position: relative;
    }

    .input-wrap .form-control {
      height: 2.9rem;
      border-radius: 0.35rem;
      font-size: 0.9rem;
      padding-left: 2.6rem; /* space for left icon */
      padding-right: 2.6rem; /* space for eye icon */
    }

    /* Left icon */
    .input-wrap .input-icon {
      position: absolute;
      top: 0;
      bottom: 0;
      left: 1rem;
      display: flex;
      align-items: center;
      font-size: 1.1rem;
      color: #a3a3a3;
      pointer-events: none;
      z-index: 5;
    }

    /* Right eye toggle */
    .input-wrap .password-toggle {
      position: absolute;
      top: 0;
      bottom: 0;
      right: 1rem;
      display: flex;
      align-items: center;
      font-size: 1.1rem;
      color: #a3a3a3;
      cursor: pointer;
      z-index: 5;
    }

    .btn-login {
      width: 100%;
      font-weight: 600;
      padding: 0.55rem 0;
      border-radius: 2rem;
      background-image: linear-gradient(to right, #2c6b5c, #a1c95d);
      border: none;
      color: white;
      margin-top: 1rem;
      text-transform: lowercase;
    }

    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .form-check {
      margin-bottom: 0;
    }

    img {
      width: 200px;
      height: 200px;
    }

    /* Mobile view */
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
        height: auto;
        min-height: 100vh;
      }

      .left-panel {
        flex: 0 0 auto;
        padding: 2rem 1.5rem;
        min-height: 200px;
        width: 100%;
      }

      .right-panel {
        flex: 0 0 auto;
        padding: 1.5rem;
        width: 100%;
      }

      form {
        padding: 20px 0;
      }
    }
    .container-fluid{
        padding: 0px !important;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <section class="left-panel">
      <h2>Welcome</h2>
      <p>
        Discover, learn and thrive with us. Experience a smooth and rewarding educational adventure — let's get started!
      </p>
    </section>

    <section class="right-panel">
      <div class="logo-container">
        <img src="{{asset('public/admin-assets/img/GDMlogo.png')}}" alt="Logo">
      </div>
      <h1 class="title">Sign In!</h1>

      <form id="loginForm" action="{{ route('login') }}"  method="POST" novalidate>
        @csrf
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <div class="input-wrap">
            <span class="input-icon"><i class="bi bi-envelope"></i></span>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" autocomplete="email" value="{{old('email')}}" required />
          </div>
          @error('email')
              <span class="invalid-feedback text-start" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
          @enderror
        </div>


        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="input-wrap">
            <span class="input-icon"><i class="bi bi-lock"></i></span>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" value="{{old('password')}}" required />
            <span class="password-toggle" id="togglePassword">
              <i class="bi bi-eye"></i>
            </span>
          </div>
          @error('password')
              <span class="invalid-feedback text-start" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
          @enderror
        </div>

        <div class="form-options">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="autologin" />
            <label class="form-check-label" for="autologin">
              Remember me
            </label>
          </div>
          @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password?</a>
          @endif
        </div>

        <button type="submit" class="btn btn-login">Log In</button>
      </form>
    </section>
  </div>
  <!--Global script(used by all pages)-->
  <script src="{{ admin_asset('vendor/jQuery/jquery.min.js') }}"></script>
  <!-- Select2 cdn added by Gowtham.s-->
  <script src="{{ asset('public/admin-assets/js/select2.min.js') }}"></script>
  <script src="{{ nanopkg_asset('vendor/toastr/build/toastr.min.js') }}"></script>
  
  <script>
    document.getElementById("togglePassword").addEventListener("click", function () {
      const password = document.getElementById("password");
      const type = password.type === "password" ? "text" : "password";
      password.type = type;
      this.querySelector("i").classList.toggle("bi-eye");
      this.querySelector("i").classList.toggle("bi-eye-slash");
    });
  </script>
</x-guest-layout>