<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
   
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [
            'event_name' => 'required|string|max:255',
            'event_description' => 'required|string|max:1000',
            'event_date' => 'required|date_format:Y-m-d',
            'event_venue' => 'required|string|max:255',
            'event_mode' => 'nullable|string|max:100',
            'event_activity' => 'nullable|string|max:255',
            'event_tags' => 'sometimes|array',
            'event_tags.*' => 'string|max:50',
            'event_departments' => 'sometimes|array',
            'event_departments.*' => 'string|max:50',
            'event_forms' => 'sometimes|array',
            'event_status' => 'nullable|string|in:active,completed,cancelled',
        ];
    }

   
    public function messages(): array
    {
        return [
            'event_name.required' => 'Event name is required.',
            'event_name.max' => 'Event name cannot exceed 255 characters.',
            'event_description.required' => 'Event description is required.',
            'event_description.max' => 'Event description cannot exceed 1000 characters.',
            'event_date.required' => 'Event date is required.',
            'event_date.date_format' => 'Event date must be in Y-m-d format.',
            'event_venue.required' => 'Event venue is required.',
            'event_venue.max' => 'Event venue cannot exceed 255 characters.',
            'event_mode.max' => 'Event mode cannot exceed 100 characters.',
            'event_activity.max' => 'Event activity cannot exceed 255 characters.',
            'event_status.in' => 'Event status must be active, completed, or cancelled.',
        ];
    }
}
