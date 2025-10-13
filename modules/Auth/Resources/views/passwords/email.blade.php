<x-guest-layout>
    <x-auth::card>
        <div class="">
            <div class="mb-4">
                <h2 class="fs-32 fw-bold">{{ localize('Forget password!') }}</h3>
                    <p class="text-muted text-center mb-0">
                        @localize('Please enter your email address. You will receive a link to create a new password via email.')
                    </p>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form class="register-form validate" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <input type="email" class="form-control input-py @error('email') is-invalid @enderror"
                        id="email" name="email" placeholder="Enter email" required autocomplete="email">
                    <span class="invalid-feedback text-start"></span>
                    @error('email')
                        <span class="invalid-feedback text-start" role="alert">
                            <strong>{{ localize($message) }}</strong>
                        </span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success w-100">
                    {{ localize('Send Password Reset Link') }}
                </button>
            </form>


        </div>
        <div class="bottom-text text-center my-3">
            @if (Route::has('register'))
                {{ localize('Don\'t have an account?') }}
                <a href="{{ route('register') }}" class="fw-bold text-success">{{ localize('Sign Up') }}</a>
            @endif
        </div>
    </x-auth::card>
</x-guest-layout>
