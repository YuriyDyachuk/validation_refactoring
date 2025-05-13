<?php
declare(strict_types=1);

namespace App\Services\Auth\Strategies;

use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthStrategyInterface;

class EmailStrategy implements AuthStrategyInterface
{

    public function authenticate(LoginRequest $request): ?User
    {
        if (\Auth::attempt(
            [
                'email' => (string) $request->input('email'),
                'password' => (string) $request->input('password')
            ])
        ) {
            return \Auth::user();
        }

        return null;
    }
}
