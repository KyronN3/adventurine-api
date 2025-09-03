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
            'event_name' => 'bail|required|string|max:255',
            'event_description' => 'bail|required|string|max:1000',
            'event_date' => 'bail|required|date_format:Y-m-d',
            'event_duration' => 'bail|required|string|in:single,multi',
            'event_location' => 'bail|required|string|max:255',
            'event_model' => 'bail|required|string|max:100|in:in-house,external',
            'event_end_date' => 'bail|nullable|string|date_format:Y-m-d',
            'event_types' => 'bail|required|string|max:50|in:seminar,training,workshop,orientation,conference,webinar,team_building, assessment',
            'event_departments' => 'bail|required|array',
            'event_departments.*' => 'bail|string|max:60',
            'event_forms' => 'bail|required|array',
            'event_forms.*' => 'bail|string|max:60',
            // event status can be active, completed, cancelled, verified
            'event_status'      => 'required|string|nullable|in:active,completed,cancelled,verified',
            'event_verify' => 'sometimes|in:verified,unverified',

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
        'event_name.required' => 'The event name is required.',
        'event_name.string'   => 'The event name must be text.',
        'event_name.max'      => 'The event name may not be longer than 255 characters.',
        'event_description.required' => 'The event description is required.',
        'event_description.max'      => 'The event description may not exceed 1000 characters.',
        'event_date.required'    => 'Please provide the event date.',
        'event_date.date_format' => 'The event date must be in YYYY-MM-DD format.',
        'event_duration.required' => 'The event duration is required.',
        'event_duration.in'       => 'The event duration must be either single or multi.',
        'event_location.required' => 'The event location is required.',
        'event_location.max'      => 'The event location may not exceed 255 characters.',
        'event_model.required' => 'The event model is required.',
        'event_model.in'       => 'The event model must be either in-house or external.',
        'event_end_date.date_format' => 'The event end date must be in YYYY-MM-DD format.',
        'event_types.required' => 'The event type is required.',
        'event_types.in'       => 'The event type must be one of seminar, training, workshop, orientation, conference, webinar, team_building, or assessment.',
        'event_departments.required' => 'At least one department must be selected.',
        'event_departments.array'    => 'Departments must be provided as an array.',
        'event_departments.*.string' => 'Each department must be a valid text value.',
        'event_departments.*.max'    => 'Each department name may not exceed 60 characters.',
        'event_forms.required' => 'At least one form must be provided.',
        'event_forms.array'    => 'Forms must be provided as an array.',
        'event_forms.*.string' => 'Each form must be text.',
        'event_forms.*.max'    => 'Each form name may not exceed 60 characters.',
        'event_status.required' => 'The event status is required.',
        'event_status.in'       => 'The event status must be active, completed, cancelled, or verified.',
        'event_verify.in' => 'The verification status must be either verified or unverified.',
        ];
    }
}
