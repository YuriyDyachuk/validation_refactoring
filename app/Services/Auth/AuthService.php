<?php
declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Actions\Auth\CreateAffiliateUser;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected array $strategies // Injected via ServiceProvider
    ) {}

    public function login(LoginRequest $request): void
    {
        $this->ensureIsNotRateLimited($request);

        $method = $request->authenticateMethod();
        $strategy = $this->strategies[$method] ?? throw new \Exception("Unsupported login method: $method");

        $user = $strategy->authenticate($request);

        if (!$user) {
            RateLimiter::hit($this->throttleKey($request));
            throw ValidationException::withMessages([
                $method => __('auth.failed'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        if ($request->input('user_type') === 'affiliate') {
            CreateAffiliateUser::run($user);
        }

        RateLimiter::clear($this->throttleKey($request));
    }

    private function ensureIsNotRateLimited(LoginRequest $request): void
    {
        $key = $this->throttleKey($request);

        if (!RateLimiter::tooManyAttempts($key, 5)) return;

        event(new Lockout($request));
        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            $request->authenticateMethod() => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    private function throttleKey(LoginRequest $request): string
    {
        $field = $request->input('token') ?? $request->input($request->authenticateMethod());
        return Str::lower($field) . '|' . $request->ip();
    }
}
