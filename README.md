# 🔐 Modular Authentication System for Laravel 12

> Clean and extensible authentication system using Strategy Pattern, SOLID principles, Laravel Service Container and Form Request Validation.

## 📦 Stack

- PHP 8.3
- Laravel 12.x
- MySQL / PostgreSQL
- Redis (Rate Limiting)
- OAuth (Google) / Email / Phone login
- PSR-12 / SOLID-compliant architecture

---

## 📁 Project Structure



---

## 🔑 Supported Authentication Strategies

| Strategy | Key Field | Description                        |
|----------|-----------|------------------------------------|
| `email`  | email     | Classic login via email + password |
| `phone`  | phone     | Login via phone number             |
| `google` | token     | OAuth2 Google Sign-in              |

> Each strategy is implemented using the **Strategy Pattern** and dynamically resolved in the `AuthServiceProvider`.

---

## 🚀 How It Works

### 🔐 Login Flow

1. User submits login data via `POST /api/login`
2. `LoginRequest` handles **validation** based on strategy
3. `AuthService` resolves appropriate **authentication strategy**
4. Strategy handles **authentication logic**
5. Post-login actions are triggered (e.g., wallet creation, logging)

---

## 📦 Strategy Injection

All strategies implement the shared interface:

```php
    namespace App\Services\Auth\Contracts;
    
    interface AuthStrategyInterface
    {
        public function attempt(LoginRequest $request): ?User;
    }
```


They are dynamically resolved via the Laravel Service Container
```php
    $this->app->bind(AuthStrategyInterface::class, function ($app) {
        $method = request('login_through');
    
        return match ($method) {
            'email' => new EmailAuthStrategy(),
            'phone' => new PhoneAuthStrategy(),
            'google' => new GoogleAuthStrategy(),
            default => throw new \InvalidArgumentException('Unsupported method'),
        };
    });
```

### Benefits of Architecture

1. SOLID-compliant (SRP, OCP, DIP, etc.)
2. KISS & DRY: simple and reusable code
3. Easy to extend — add new login methods like Apple ID, Facebook
4. Centralized rate-limiting and validation
5. Compliant with Clean Architecture and Best Practices
