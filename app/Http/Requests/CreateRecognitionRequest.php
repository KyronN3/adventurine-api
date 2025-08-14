<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRecognitionRequest extends FormRequest
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
            'recognition_type' => $this->input('recognitionType'),
            'achievement_description' => $this->input('achievementDescription'),
        ]);
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer'],
            'employee_department' => ['required', 'string', 'max:100'],
            'employee_name' => ['required', 'string', 'max:100'],
            'recognition_date' => ['required', 'date_format:Y-m-d'],
            'recognition_type' => ['required', 'string', 'max:100'],
            'achievement_description' => ['required', 'string', 'max:1000'],
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

            'recognition_type.required' => 'Recognition Type is required',
            'achievement_description.required' => 'Achievement Description is required',
        ];
    }
}
