<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\PassportCustomModel;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
{
    $this->registerPolicies();

    Passport::tokensCan([
        'agent' => 'Access for agents',
        'rider' => 'Access for riders',
    ]);

    Passport::useTokenModel(Token::class);

    Auth::viaRequest('passport', function ($request) {
        $token = $request->bearerToken();

        if (! $token) {
            return null;
        }

        $accessToken = Token::where('id', explode('.', $token)[0] ?? '')->first();

        if (! $accessToken) {
            return null;
        }

        // Guard is stored when token created
        $guard = $accessToken->guard;

        if ($guard === 'agent') {
            return \Modules\B2B\Entities\B2BAgent::find($accessToken->user_id);
        } elseif ($guard === 'rider') {
            return \Modules\B2B\Entities\B2BRider::find($accessToken->user_id);
        }

        return null;
    });
}
}
