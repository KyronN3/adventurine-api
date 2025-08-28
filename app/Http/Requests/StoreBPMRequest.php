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

    protected function prepareForValidation()
    {
        // Batch save man ang bpm
        if ($this->isJson()) {
            $jsonData = $this->json()->all();
            // Check kung batch ba or single. Sa frontend
                // sa create bpm batch jud na iya i send. pero
                // i leave nlng nko just in case

                if (array_keys($jsonData) === range(0, count($jsonData) - 1)) { //batch
                    $this->merge(['bpm_entries' => $jsonData]);
                } else {
                    $this->merge(['bpm_entries' => [$jsonData]]); // not batch
                }
        } else {
            // Handle form data
            $data = $this->all();
            if (!isset($data[0]) && !is_array($data)) {
                $data = [$data];
            }
            $this->merge(['bpm_entries' => $data]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bpm_entries' => 'required|array',
            'bpm_entries.*.employee_name' => 'required|string|max:255',
            'bpm_entries.*.designation' => 'required|string|max:255',
            'bpm_entries.*.sex' => 'required|in:M,F',
            'bpm_entries.*.medical_history' => 'nullable|string|max:100',
            'bpm_entries.*.status' => 'required|in:Permanent,Coterminous,Temporary,Casual,Job Order,Honorarium',
            'bpm_entries.*.employee_department' => 'required|string|max:255',
            'bpm_entries.*.bpm_systolic' => 'required|integer|min:0|max:300',
            'bpm_entries.*.bpm_diastolic' => 'required|integer|min:0|max:200',
            'bpm_entries.*.bpm_dateTaken' => 'required|date_format:Y-m-d',
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
            'bpm_entries.required' => 'BPM entries are required',
            'bpm_entries.array' => 'BPM entries must be an array',
            'bpm_entries.*.employee_name.required' => 'Employee name is required',
            'bpm_entries.*.employee_name.string' => 'Employee name must be a string',
            'bpm_entries.*.employee_name.max' => 'Employee name must not exceed 255 characters',
            'bpm_entries.*.designation.required' => 'Designation is required',
            'bpm_entries.*.designation.string' => 'Designation must be a string',
            'bpm_entries.*.designation.max' => 'Designation must not exceed 255 characters',
            'bpm_entries.*.sex.required' => 'Sex is required',
            'bpm_entries.*.sex.in' => 'Sex must be either M or F',
            'bpm_entries.*.medical_history.string' => 'Medical history must be a string',
            'bpm_entries.*.medical_history.max' => 'Medical history must not exceed 100 characters',
            'bpm_entries.*.status.required' => 'Status is required',
            'bpm_entries.*.status.in' => 'Status must be one of: Permanent, Coterminous, Temporary, Casual, Job Order, Honorarium',
            'bpm_entries.*.employee_department.required' => 'Employee department is required',
            'bpm_entries.*.employee_department.string' => 'Employee department must be a string',
            'bpm_entries.*.employee_department.max' => 'Employee department must not exceed 255 characters',
            'bpm_entries.*.bpm_systolic.required' => 'Systolic blood pressure is required',
            'bpm_entries.*.bpm_systolic.integer' => 'Systolic blood pressure must be an integer',
            'bpm_entries.*.bpm_systolic.min' => 'Systolic blood pressure must be at least 0',
            'bpm_entries.*.bpm_systolic.max' => 'Systolic blood pressure must not exceed 300',
            'bpm_entries.*.bpm_diastolic.required' => 'Diastolic blood pressure is required',
            'bpm_entries.*.bpm_diastolic.integer' => 'Diastolic blood pressure must be an integer',
            'bpm_entries.*.bpm_diastolic.min' => 'Diastolic blood pressure must be at least 0',
            'bpm_entries.*.bpm_diastolic.max' => 'Diastolic blood pressure must not exceed 200',
            'bpm_entries.*.bpm_dateTaken.required' => 'Date taken is required',
            'bpm_entries.*.bpm_dateTaken.date_format' => 'Date taken must be in YYYY-MM-DD format',
        ];
    }
}
