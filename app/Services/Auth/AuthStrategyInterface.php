<?php
declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;

interface AuthStrategyInterface
{
    public function authenticate(LoginRequest $request): ?User;
}
