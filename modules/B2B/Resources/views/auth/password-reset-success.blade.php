<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset Successful </title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
  
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6 col-xl-5">
        
        <!-- Logo + Title -->
        <div class="text-center mb-4">
          <img src="{{ asset('admin-assets/icons/gdm_logo.png') }}" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
          <h2 class="h5 fw-semibold text-success">Password Reset Successful!</h2>
        </div>

        <!-- Success Message -->
        <div class="card border-success shadow-sm">
          <div class="card-body text-center p-4">
            
            <!-- Check Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" stroke="currentColor" stroke-width="2" class="text-success mb-3" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

            <h3 class="h6 fw-bold text-success mb-2">Password Changed Successfully</h3>
            <p class="text-muted mb-4">Your password has been reset successfully. </p>
            
        
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
