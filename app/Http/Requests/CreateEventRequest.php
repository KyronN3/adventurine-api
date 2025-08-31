<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user()->hasRole('hr');
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
            'event_end_date' => 'bail|nullable|string|max:255',
            'event_types' => 'bail|required|string|max:50|in:seminar,training,workshop,orientation,conference,webinar,team_building, assessment',
            'event_departments.*' => 'bail|string|max:50',
            'event_forms' => 'bail|nullable|array',
            'event_status' => 'bail|required|string|in:active,completed,cancelled',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'event_name' => $this->input('eventName'),
            'event_description' => $this->input('eventDescription'),
            'event_date' => $this->input('eventDate'),
            'event_end_date' => $this->input('eventEndDate'),
            'event_duration' => $this->input('eventDuration'),
            'event_location' => $this->input('eventLocation'),
            'event_model' => $this->input('eventModel'),
            'event_types' => $this->input('eventTypes'),
            'event_departments' => $this->input('eventDepartments'),
            'event_forms' => $this->input('eventForms'),
            'event_status' => $this->input('eventStatus'),
        ]);
    }

    public function messages(): array
    {
        // You can Create customize error message here ✍️😋
        return [

        ];
    }
}
