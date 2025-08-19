<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBPMRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_name' => 'required|string|max:255',
            'employee_department' => 'required|string|max:255',
            'bpm_systolic' => 'required|integer|min:0|max:300',
            'bpm_diastolic' => 'required|integer|min:0|max:200',
            'bpm_dateTaken' => 'required|date_format:Y-m-d',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'employee_name.required' => 'Employee name is required',
            'employee_name.string' => 'Employee name must be a string',
            'employee_name.max' => 'Employee name must not exceed 255 characters',
            'employee_department.required' => 'Employee department is required',
            'employee_department.string' => 'Employee department must be a string',
            'employee_department.max' => 'Employee department must not exceed 255 characters',
            'bpm_systolic.required' => 'Systolic blood pressure is required',
            'bpm_systolic.integer' => 'Systolic blood pressure must be an integer',
            'bpm_systolic.min' => 'Systolic blood pressure must be at least 0',
            'bpm_systolic.max' => 'Systolic blood pressure must not exceed 300',
            'bpm_diastolic.required' => 'Diastolic blood pressure is required',
            'bpm_diastolic.integer' => 'Diastolic blood pressure must be an integer',
            'bpm_diastolic.min' => 'Diastolic blood pressure must be at least 0',
            'bpm_diastolic.max' => 'Diastolic blood pressure must not exceed 200',
            'bpm_dateTaken.required' => 'Date taken is required',
            'bpm_dateTaken.date_format' => 'Date taken must be in YYYY-MM-DD format',
            'measurement_time.string' => 'Measurement time must be a string',
            'measurement_time.max' => 'Measurement time must not exceed 255 characters',
        ];
    }
}
