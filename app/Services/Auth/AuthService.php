<?php
declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\LoginRequest;
use App\Actions\Auth\CreateAffiliateUser;
use App\Exceptions\UnsupportedLoginMethodException;
use App\Contracts\LoginStrategyInterface;
use App\Enums\UserType;
use App\Models\User;

class AuthService
{
    /**
     * @param array<string, LoginStrategyInterface> $strategies
     */
    public function __construct(
        protected array $strategies
    ) {}

    /**
     * @throws ValidationException
     * @throws UnsupportedLoginMethodException
     */
    public function login(LoginRequest $request): void
    {
        $this->ensureIsNotRateLimited($request);

        $user = $this->authenticateUser($request);

        Auth::login($user, $request->boolean('remember'));

        $this->handleAffiliate($user, $request);

        RateLimiter::clear($this->throttleKey($request));
    }

    private function authenticateUser(LoginRequest $request): User
    {
        $method = $request->authenticateMethod();

        $strategy = $this->strategies[$method] ?? throw new UnsupportedLoginMethodException($method);

        $user = $strategy->authenticate($request);

        if (!$user) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                $method => __('auth.failed'),
            ]);
        }

        return $user;
    }

    private function handleAffiliate(User $user, LoginRequest $request): void
    {
        if ($request->input('user_type') === UserType::AFFILIATE) {
            CreateAffiliateUser::run($user);
        }
    }

    private function ensureIsNotRateLimited(LoginRequest $request): void
    {
        $key = $this->throttleKey($request);

        if (!RateLimiter::tooManyAttempts($key, 5)) {
            return;
        }

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

        return Str::lower((string) $field) . '|' . $request->ip();
    }
}
