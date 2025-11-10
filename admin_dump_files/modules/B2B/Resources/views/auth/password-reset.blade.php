<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset - B2B Agent</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .error-message {
      color: #e53e3e;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
    .success-message {
      color: #38a169;
      font-size: 0.875rem;
      margin-bottom: 1rem;
      padding: 0.75rem;
      background-color: #f0fff4;
      border-radius: 0.375rem;
    }
  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

  <div class="w-full px-6 sm:px-10 md:max-w-lg">

    <!-- Logo & Title -->
    <div class="text-center mb-8">
      <img src="{{ asset('admin-assets/icons/gdm_logo.png') }}" alt="Logo" class="mx-auto mb-4">
      <h2 class="text-lg sm:text-xl font-semibold text-blue-600">Reset Your Password</h2>
    </div>

    <!-- Password Reset Form -->
    <form method="POST" class="space-y-5" id="passwordResetForm">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}" id="tokenInput">
      <input type="hidden" name="email" value="{{ $email }}" id="emailInput">

      <!-- New Password -->
      <div>
        <label for="password" class="block mb-2 text-sm font-medium text-gray-700">New Password</label>
        <input type="password" id="password" name="password" placeholder="Enter New Password" required
               class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
        <p class="error-message" id="passwordError" style="display: none;"></p>
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-700">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password" required
               class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
        <p class="error-message" id="confirmPasswordError" style="display: none;"></p>
      </div>

      <!-- Submit Button -->
      <button type="submit"
              class="w-full bg-blue-500 text-white font-semibold py-3 rounded-lg hover:bg-blue-600 transition">
        Create New Password
      </button>
    </form>

    <!-- Success Message -->
    <div id="successMessage" class="success-message mt-4" style="display: none;">
      <p>Password reset successfully! Redirecting to login page...</p>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('passwordResetForm');
        const passwordError = document.getElementById('passwordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');
        const successMessage = document.getElementById('successMessage');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Clear previous errors
            passwordError.style.display = 'none';
            confirmPasswordError.style.display = 'none';
            successMessage.style.display = 'none';

            // Get form data
            const formData = new FormData(form);
            const data = {
                token: document.getElementById('tokenInput').value,
                email: document.getElementById('emailInput').value,
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation')
            };

            try {
                // Show loading state
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;
                submitButton.textContent = 'Resetting Password...';
                submitButton.disabled = true;

                // Send request to web.php route instead of API
                const response = await fetch("{{ route('b2b.resetPassword') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Show success message
                    successMessage.textContent = result.message || 'Password reset successfully! Redirecting...';
                    successMessage.style.display = 'block';

                    // Hide form
                    form.style.display = 'none';

                    // Redirect to success page after 2 seconds
                    setTimeout(() => {
                        window.location.href = "{{ route('b2b-agent.password.reset.success') }}";
                    }, 2000);
                } else {
                    // Show error messages
                    if (result.errors) {
                        if (result.errors.password) {
                            passwordError.textContent = result.errors.password[0];
                            passwordError.style.display = 'block';
                        }
                        if (result.errors.password_confirmation) {
                            confirmPasswordError.textContent = result.errors.password_confirmation[0];
                            confirmPasswordError.style.display = 'block';
                        }
                    } else {
                        passwordError.textContent = result.message || 'An error occurred. Please try again.';
                        passwordError.style.display = 'block';
                    }
                }
            } catch (error) {
                passwordError.textContent = 'Network error. Please check your connection and try again.';
                passwordError.style.display = 'block';
            } finally {
                // Restore button state
                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.textContent = 'Create New Password';
                submitButton.disabled = false;
            }
        });
    });
  </script>
</body>
</html>
