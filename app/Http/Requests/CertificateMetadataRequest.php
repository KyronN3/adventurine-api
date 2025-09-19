<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificateMetadataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'id' => ['required', 'int'],
            'employeeName' => ['required', 'string'],
            'citation' => ['required', 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'issue' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID is required',
            'id.int' => 'ID must be a integer',
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'citation.required' => 'Citation is required',
            'citation.string' => 'Citation must be a string',
            'title.required' => 'Title is required',
            'title.string' => 'Title must be a string',
            'description.required' => 'Description is required',
            'description.string' => 'Description must be a string',
            'issue.required' => 'Day issue is required',
            'issue.string' => 'Day issue must be a string',
        ];
    }

}
