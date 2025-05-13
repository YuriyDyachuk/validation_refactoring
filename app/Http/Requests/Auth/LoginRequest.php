<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->has('token')) {
            return ['token' => ['required', 'string']];
        }

        $rules = ['password' => ['required', 'string']];

        match ($this->authenticateMethod()) {
            'phone' => $rules['phone'] = ['required', 'string', 'starts_with:+'],
            'email' => $rules['email'] = ['required', 'string', 'email'],
        };

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function authenticateMethod(): string
    {
        return $this->input('login_through', 'email');
    }
}
