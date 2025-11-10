<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Green Drive Mobility</title>
  <link rel="icon" type="image/png" href="{{ asset('b2b/img/b2b_logo.png') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      font-family: "Inter", sans-serif;
      background: #fff;
    }

    .login-container {
      display: flex;
      height: 100vh;
      width:100%;
    }

    .left-panel {
      flex: 1;
      background: #000;
    }

    .left-panel img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .right-panel {
      flex: 1;
      background: #fff;
      padding: 48px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .login-header {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .login-header img {
      height: 40px;
    }

    .login-box {
      max-width: 612px;
      margin-top: 80px;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    /* Toggle container with outer border */
    .login-toggle {
      display: inline-flex;
      border: 1px solid #ccc;
      border-radius: 8px;
      overflow: hidden;
    }

    .login-toggle input[type="radio"] {
      display: none;
    }

    .button {
      width: fit-content;
    }

    .btn-toggle {
      width: 115px;
      height: 39px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 500;
      color: #6c757d;
      background-color: #fff;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
    }

    #masterLogin:checked+label {
      background-color: #E7DDFB;
      color: #4A148C;
      font-weight: 600;
      border: 1px solid #4A148C;
      border-radius: 8px;
      /* full border */
      z-index: 1;
      /* make sure it overlaps cleanly */
    }

    /* Zone selected */
    #zoneLogin:checked+label {
      background-color: #D1FADF;
      color: #166534;
      font-weight: 600;
      border: 1px solid #166534;
      border-radius: 8px;
      /* full border */
      z-index: 1;
    }

    .form-control {
      height: 48px;
      border-radius: 8px;
      font-size: 14px;
      padding: 0 12px;
    }

    .btn-login {
      background-color: #3b82f6;
      color: #fff;
      font-weight: 500;
      border-radius: 8px;
      border: none;
      height: 48px;
      padding: 8px 24px;
      transition: background-color 0.2s ease-in-out;
    }

    .btn-login:hover {
      background-color: #2563eb;
    }

    .footer-links {
      display: flex;
      justify-content: flex-start;
      gap: 16px;
      font-size: 0.9rem;
      margin-top: auto;
      padding-top: 24px;
    }

    .footer-links a {
      text-decoration: none;
      color: #6c757d;
    }

    .footer-links a:hover {
      text-decoration: underline;
      color: #198754;
    }

    @media (max-width: 992px) {
      .login-container {
        flex-direction: column;
      }

      .left-panel {
        display: none;
      }

      .right-panel {
        padding: 32px 24px;
      }

      .login-box {
        margin-top: 40px;
        width: 100%;
      }

      .footer-links {
        text-align: left;
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="left-panel">
      <img src="{{ asset('b2b/img/b2b_login.png') }}" alt="Login Image">
    </div>

    <div class="right-panel">
      <div class="login-header">
        <img src="{{ asset('b2b/img/b2b_logo.png') }}"  alt="Logo">
        <h6 class="mb-0"><b>Green Drive mobility </b><span style="font-weight:300;">PLATFORM</span></h6>
      </div>

      <div class="login-box">
        <h3 class="mb-3 fw-semibold">Login</h3>
        



        <form class="d-flex flex-column gap-3" method="POST" action="{{ route('b2b_login_check') }}">
            @csrf
                    <!-- Toggle Buttons -->
        <div class="button">
          <div class="login-toggle mb-3">
            <input type="radio" {{ old('login_type', 'master') == 'master' ? 'checked' : '' }} name="login_type" id="masterLogin" value="master" checked>
            <label class="btn-toggle" for="masterLogin">Master Login</label>

            <input type="radio" {{ old('login_type') == 'zone' ? 'checked' : '' }} name="login_type" id="zoneLogin" value="zone">
            <label class="btn-toggle" for="zoneLogin">Zone Login</label>
          </div>
        </div>
        
          <div>
            <label class="form-label">Email *</label>
            <input type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror"  name="email" placeholder="Enter your Email" required>
            @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
         @enderror
          </div>
          <div>
            <label class="form-label">Password *</label>
            <div class="position-relative">
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="passwordInput"  name="password" placeholder="Enter your password" required autocomplete="off">
              <span class="position-absolute top-50 end-0 translate-middle-y me-2 text-muted"  style="cursor:pointer;" id="togglePassword">
                <i class="bi bi-eye" id="eyeIcon"></i>
              </span>
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between">
             <a href="{{route('b2b.forgot_password')}}" class="text-decoration-none">Forgot Password?</a>
            <button type="submit" class="btn btn-login">Login</button>
          </div>
        </form>
      </div>
      

      <div class="footer-links">
        <a href="#">Contact</a>
        <a href="#">Terms of service</a>
        <a href="#">Privacy policy</a>
      </div>
    </div>
  </div>
  
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('passwordInput');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    if (passwordInput && togglePassword && eyeIcon) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle eye icon
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    }
});
</script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  


</body>

</html>