<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'bail|required|max:60|',
            'email' => 'bail|required|email|unique:users,email|max:100',
            'role' => 'bail|required|in:hr,admin',
            'office' => 'bail|required|string|max:100|exists:vwofficearrangement,office',
            'password' => ['bail', 'required', 'confirmed', Password::min(8)
                ->symbols()
                ->numbers()
                ->letters()
                ->mixedCase()
                ->uncompromised()
            ],

        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->input('fullname'),
            'email' => $this->input('email'),
            'office' => $this->input('office'),
            'role' => $this->input('role'),
            'password' => $this->input('password'),
            'password_confirmation' => $this->input('passwordConfirmation')
        ]);
    }
}
