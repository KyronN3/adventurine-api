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
        if ($this->isJson()) {
            $jsonData = $this->json()->all();

            if (isset($jsonData['bpm_entries'])) {
                $this->merge(['bpm_entries' => $jsonData['bpm_entries']]);
            } elseif (array_keys($jsonData) === range(0, count($jsonData) - 1)) {
                $this->merge(['bpm_entries' => $jsonData]);
            } else {
                $this->merge(['bpm_entries' => [$jsonData]]);
            }
        } else {
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
            'bpm_entries.*.control_no' => 'nullable|string|max:50',
            'bpm_entries.*.medical_history' => 'nullable|string|max:100',
            'bpm_entries.*.bpm_systolic' => 'nullable|integer|min:0|max:300',
            'bpm_entries.*.bpm_diastolic' => 'nullable|integer|min:0|max:200',
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
            'bpm_entries.*.control_no.string' => 'Control number must be a string',
            'bpm_entries.*.control_no.max' => 'Control number must not exceed 50 characters',
            'bpm_entries.*.medical_history.string' => 'Medical history must be a string',
            'bpm_entries.*.medical_history.max' => 'Medical history must not exceed 100 characters',
            'bpm_entries.*.bpm_systolic.integer' => 'Systolic blood pressure must be an integer',
            'bpm_entries.*.bpm_systolic.min' => 'Systolic blood pressure must be at least 0',
            'bpm_entries.*.bpm_systolic.max' => 'Systolic blood pressure must not exceed 300',
            'bpm_entries.*.bpm_diastolic.integer' => 'Diastolic blood pressure must be an integer',
            'bpm_entries.*.bpm_diastolic.min' => 'Diastolic blood pressure must be at least 0',
            'bpm_entries.*.bpm_diastolic.max' => 'Diastolic blood pressure must not exceed 200',
            'bpm_entries.*.bpm_dateTaken.required' => 'Date taken is required',
            'bpm_entries.*.bpm_dateTaken.date_format' => 'Date taken must be in YYYY-MM-DD format',
        ];
    }
}
