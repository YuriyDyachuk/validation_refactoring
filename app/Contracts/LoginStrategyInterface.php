<?php
declare(strict_types=1);

namespace App\Contracts;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;

interface LoginStrategyInterface
{
    public function authenticate(LoginRequest $request): ?User;
}
