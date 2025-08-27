<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateEventRequest extends FormRequest
{
   
    public function authorize(): bool
    {
        return true;
    }

  
    public function rules(): array
    {
        return [
            'event_name' => 'required|string|max:255|not_in:0', 
            'event_description' => 'required|string|max:1000|not_in:0', 
            'event_date' => 'required|date_format:Y-m-d', 
            'event_venue' => 'required|string|max:255|not_in:0',            
            'event_mode' => 'required|string|max:100|not_in:0', 
            'event_activity' => 'required|string|max:255|not_in:0', 
            'event_tags' => 'required|array|min:1',
            'event_tags.*' => 'required|string|max:50|not_in:0',
            'event_departments' => 'required|array|min:1',
            'event_departments.*' => 'required|string|max:50|not_in:0',
            'event_forms' => 'required|array|min:1',
            'event_forms.*' => 'required|string|not_in:0',
            'event_status' => 'required|string|in:active,completed,cancelled',
        ];
    }

   
    public function messages(): array
    {
        return [
         'event_name.required' => 'Please provide event name.',
            'event_name.string' => 'Event name must be a string.',
            'event_name.max' => 'Event name cannot exceed 255 characters.',
            'event_name.not_in' => 'Event name cannot be empty.',
            'event_description.required' => 'Please provide event description.',
            'event_description.string' => 'Event description must be a string.',
            'event_description.max' => 'Event description cannot exceed 1000 characters.',
            'event_description.not_in' => 'Event description cannot be empty.',
            'event_date.required' => 'Please provide event date.',
            'event_date.date_format' => 'Event date must be in Y-m-d format.',
            'event_venue.required' => 'Please provide event venue.',
            'event_venue.string' => 'Event venue must be a string.',
            'event_venue.max' => 'Event venue cannot exceed 255 characters.',
            'event_venue.not_in' => 'Event venue cannot be empty.',
            'event_mode.required' => 'Please provide event mode.',
            'event_mode.string' => 'Event mode must be a string.',
            'event_mode.max' => 'Event mode cannot exceed 100 characters.',
            'event_mode.not_in' => 'Event mode cannot be empty.',
            'event_activity.required' => 'Please provide event activity.',
            'event_activity.string' => 'Event activity must be a string.',
            'event_activity.max' => 'Event activity cannot exceed 255 characters.',
            'event_activity.not_in' => 'Event activity cannot be empty.',
            'event_tags.required' => 'Please provide event tags.',
            'event_tags.array' => 'Event tags must be an array.',
            'event_tags.min' => 'Please provide at least one event tag.',
            'event_tags.*.required' => 'Please provide valid event tags.',
            'event_tags.*.string' => 'Each event tag must be a string.',
            'event_tags.*.max' => 'Each event tag cannot exceed 50 characters.',
            'event_tags.*.not_in' => 'Each event tag cannot be empty.',
            'event_departments.required' => 'Please provide event departments.',
            'event_departments.array' => 'Event departments must be an array.',
            'event_departments.min' => 'Please provide at least one event department.',
            'event_departments.*.required' => 'Please provide valid event departments.',
            'event_departments.*.string' => 'Each event department must be a string.',
            'event_departments.*.max' => 'Each event department name cannot exceed 50 characters.',
            'event_departments.*.not_in' => 'Each event department cannot be empty.',
            'event_forms.required' => 'Please provide event forms.',
            'event_forms.array' => 'Event forms must be an array.',
            'event_forms.min' => 'Please provide at least one event form.',
            'event_forms.*.required' => 'Please provide valid event forms.',
            'event_forms.*.string' => 'Each event form must be a string.',
            'event_forms.*.not_in' => 'Each event form cannot be empty.',
            'event_status.required' => 'Please provide event status.',
            'event_status.in' => 'Event status must be active, completed, or cancelled.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . implode(' ', $validator->errors()->all()),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
