<x-app-layout>
    @section('style_css')
    <style>
        /* Slow spinning animation */
        .spin-slow {
            animation: spin 8s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Bouncing text */
        .bounce {
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }

        /* Page background */
        .coming-soon-bg {
            background: linear-gradient(135deg, #e0f7fa, #fff, #ede7f6);
        }
    </style>
    @endsection

    <div class="main-content d-flex justify-content-center align-items-center min-vh-100 coming-soon-bg">
        <div class="card shadow-lg border-0 p-5 text-center" style="max-width: 600px;">
            
            <!-- Animated Circle with Icon -->
            <div class="position-relative mx-auto mb-4" style="width:120px; height:120px;">
                <div class="position-absolute top-0 start-0 w-100 h-100 border border-4 border-primary border-dashed rounded-circle spin-slow"></div>
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary bg-gradient rounded-circle d-flex justify-content-center align-items-center text-white fs-1">
                    <i class="bi bi-rocket-takeoff-fill"></i>
                </div>
            </div>

            <!-- Heading -->
            <h1 class="fw-bold display-4 text-dark bounce">
                Coming Soon
            </h1>

            <!-- Subtitle -->
            <p class="mt-3 fs-5 text-secondary">
                <i class="bi bi-hourglass-split text-primary"></i> We’re working hard to bring you something amazing. <br>
                <i class="bi bi-lightning-charge-fill text-warning"></i> Stay tuned for updates!
            </p>
        </div>
    </div>

    @section('script_js')
    @endsection
</x-app-layout>
