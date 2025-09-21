<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class SubmitAttendanceRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user()->hasRole('hr');
    }


    public function rules(): array
    {
        return [
            'event_id' => 'bail|required|integer|exists:events,id',
            'name' => 'bail|required|string|max:255',
            'email' => 'bail|required|email|max:255',
            'status' => 'bail|required|string|max:50',
            'check_in' => 'bail|required|date',
            'notes' => 'bail|nullable|string|max:1000',
        ];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('You are not authorized to perform this action. Only HR can submit attendance.');
    }
}