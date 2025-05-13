<?php
declare(strict_types=1);

namespace App\Services\Auth\Strategies;

use App\Models\User;
use App\Models\OAuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthStrategyInterface;

class GoogleStrategy implements AuthStrategyInterface
{
    public function authenticate(LoginRequest $request): ?User
    {
        return User::whereHas('oAuthTokens', function ($query) use ($request) {
            $query->where('token', $request->input('token'))
                ->where('o_auth_service_id', OAuthService::getGoogleAuth()->id);
        })->first();
    }
}
