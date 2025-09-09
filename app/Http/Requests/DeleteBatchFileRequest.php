<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBatchFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'files' => ['required', 'array'],
            'filesType' => ['required', 'string', 'max:100', 'in:recognition-image,recognition-file,event-image,event-file'],
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Files is required',
            'filesType.required' => 'Files Type is required',
            'filesType.in' => 'Files Type must be one of: recognition-image, recognition-file, event-image, event-file',
        ];
    }
}
