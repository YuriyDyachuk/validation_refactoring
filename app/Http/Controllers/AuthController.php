<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Auth\AuthService;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function __construct(
        readonly private AuthService $authService
    ) {}

    /**
     * @throws \Exception
     */
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->authService->login($request);

        return response()->json(['message' => 'Successfully logged in.']);
    }
}
