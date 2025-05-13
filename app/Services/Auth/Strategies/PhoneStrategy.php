<?php
declare(strict_types=1);

namespace App\Services\Auth\Strategies;

use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthStrategyInterface;

class PhoneStrategy implements AuthStrategyInterface
{
    public function authenticate(LoginRequest $request): ?User
    {
        if (\Auth::attempt(
            [
                'phone' => (string) $request->input('phone'),
                'password' => (string) $request->input('password')
            ])
        ) {
            return \Auth::user();
        }

        return null;
    }
}
