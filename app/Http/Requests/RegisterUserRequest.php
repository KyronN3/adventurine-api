<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:60|',
            'email' => 'required|email|unique:users,email|max:100',
            'role' => 'required|in:hr,admin',
            'password' => ['required', 'confirmed', Password::min(8)
                ->symbols()
                ->numbers()
                ->letters()
                ->mixedCase()
                ->uncompromised()
            ],

        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => $this->input('fullname'),
            'email' => $this->input('email'),
            'role' => $this->input('role'),
            'password' => $this->input('password'),
            'password_confirmation' => $this->input('passwordConfirmation')
        ]);
    }
}
