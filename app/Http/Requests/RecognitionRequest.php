<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecognitionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'employee_id' => $this->input('employeeId'),
            'employee_department' => $this->input('employeeDepartment'),
            'employee_name' => $this->input('employeeName'),
            'recognition_date' => $this->input('recognitionDate'),
            'achievement_description' => $this->input('achievementDescription'),
            'title' => $this->input('title'),
            'images' => $this->input('images', []),
            'files' => $this->input('files', []),
        ]);
    }


    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer'],
            'employee_department' => ['required', 'string', 'max:100'],
            'employee_name' => ['required', 'string', 'max:100'],
            'recognition_date' => ['required', 'date_format:Y-m-d'],
            'achievement_description' => ['required', 'string', 'max:1000'],
            'title' => ['required', 'string', 'max:100'],
            'images' => ['array'],                     // must be an array
            'images.*' => ['string', 'max:255'],       // each element is a string
            'files' => ['array'],
            'files.*' => ['string', 'max:255'],
        ];
    }


    public function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required',
            'employee_id.integer' => 'Employee ID must be an integer',

            'employee_department.required' => 'Employee Department is required',
            'employee_name.required' => 'Employee Name is required',

            'recognition_date.required' => 'Recognition Date is required',
            'recognition_date.date_format' => 'Recognition Date must be in YYYY-MM-DD format',

            'achievement_description.required' => 'Achievement Description is required',
            'title.required' => 'Title is required',

            'images.array' => 'Images must be an array',
            'images.*.string' => 'Each image name must be a string',
            'files.array' => 'Files must be an array',
            'files.*.string' => 'Each file name must be a string',
        ];
    }

}
