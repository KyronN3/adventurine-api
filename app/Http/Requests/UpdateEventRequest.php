<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [
            'event_name'        => 'required|string|max:255',
            'event_tags'        => 'required|array',
            'event_tags.*'      => 'string|max:50',
            'event_description' => 'required|string|nullable|max:1000',
            'event_departments' => 'required|array',
            'event_departments.*'=> 'string|max:50',
            'event_date'        => 'required|date_format:Y-m-d|nullable',
            'event_activity'    => 'required|string|nullable|max:255',
            'event_venue'       => 'required|string|nullable|max:255',
            'event_mode'        => 'required|string|nullable|max:100',
            'event_forms'       => 'required|array|nullable',
            'event_created'     => 'sometimes|date_format:Y-m-d|nullable',
            'event_status'      => 'required|string|nullable|in:active,completed,cancelled',

            'outcomes'                 => 'sometimes|array|nullable',
            'outcomes.*.title'         => 'required_with:outcomes|string|max:255',
            'outcomes.*.description'   => 'nullable|string',

            'participants'                 => 'sometimes|array|nullable',
            'participants.*.participant_id'=> 'nullable|string',
            'participants.*.name'          => 'nullable|string|max:255',

            'attendance'              => 'sometimes|array|nullable',
            'attendance.*.name'       => 'required_with:attendance|string|max:255',
            'attendance.*.email'      => 'nullable|email',
            'attendance.*.status'     => 'nullable|string|max:255',
            'attendance.*.check_in'   => 'nullable|date',
            'attendance.*.notes'      => 'nullable|string',
        ];
    }

    
    public function messages(): array
    {
        return [
            'event_name.max' => 'Event name cannot exceed 255 characters.',
            'event_description.max' => 'Event description cannot exceed 1000 characters.',
            'event_date.date_format' => 'Event date must be in Y-m-d format.',
            'event_venue.max' => 'Event venue cannot exceed 255 characters.',
            'event_mode.max' => 'Event mode cannot exceed 100 characters.',
            'event_activity.max' => 'Event activity cannot exceed 255 characters.',
            'event_status.in' => 'Event status must be active, completed, or cancelled.',
            'event_tags.*.max' => 'Event tag cannot exceed 50 characters.',
            'event_departments.*.max' => 'Department name cannot exceed 50 characters.',
            'outcomes.*.title.required_with' => 'Outcome title is required when outcomes are provided.',
            'outcomes.*.title.max' => 'Outcome title cannot exceed 255 characters.',
            'attendance.*.name.required_with' => 'Attendance name is required when attendance is provided.',
            'attendance.*.name.max' => 'Attendance name cannot exceed 255 characters.',
            'attendance.*.email.email' => 'Attendance email must be a valid email address.',
        ];
    }
}
