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
        $officeRule = 'bail|required|string|max:100';
        if ($this->input('role') !== 'hr') {
            $officeRule = 'bail|required|string|max:100|exists:vwofficearrangement,office';
        }

        return [
            'role' => 'bail|required|in:hr,admin',
            'email_control_no' => 'bail|required|email',
            'control_no' => 'bail|required|digits:6|unique:ldrUser,control_no|exists:vwActive,ControlNo',
            'office' => $officeRule,
            'password' => ['bail', 'required', 'confirmed', Password::min(8)
                ->symbols()
                ->numbers()
                ->letters()
            ],

        ];
    }

    protected function prepareForValidation(): void
    {
        $office = 'hr';
        if ($this->input('role') !== 'hr') {
            $office = 'OFFICE OF THE CITY ' . $this->input('office');
        }

        $this->merge([
            'email_control_no' => $this->input('controlNo') . '@gmail.com',
            'office' => $office,
            'control_no' => $this->input('controlNo'),
            'role' => $this->input('role'),
            'password' => $this->input('password'),
            'password_confirmation' => $this->input('passwordConfirmation')
        ]);
    }

    public function messages(): array
    {
        return [
            'control_no.exists' => 'The control id does not exists in the database.',
            'office.exists' => 'The office does not exists in the database.',
        ];
    }
}
