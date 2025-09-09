<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificateRecognitionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recognition_id' => ['required'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'date' => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'recognition_id' => $this->input('recognitionId'),
        ]);
    }
    public function messages(): array
    {
        return [
            'recognition_id.required' => 'Recognition ID is required',
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'description.required' => 'Description is required',
            'description.string' => 'Description must be a string',
            'date.required' => 'Date is required',
            'date.date_format' => 'Date must be in Y-m-d format',
        ];
    }
}
