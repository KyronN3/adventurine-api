<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
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
            'event_schedule' => 'bail|required|array',
            'event_schedule.*.date' => 'bail|required|date|date_format:Y-m-d',
            'event_schedule.*.timeStart' => 'bail|required|date_format:H:i:s',
            'event_schedule.*.timeEnd' => 'bail|required|date_format:H:i:s',
            'event_location' => 'bail|required|string|max:255',
            'event_model' => 'bail|required|string|max:100|in:in-house,external',
            'event_types' => 'bail|required|string|max:50|in:seminar,training,workshop,orientation,conference,webinar,team_building, assessment',
            'event_departments' => 'bail|required|array',
            'event_departments.*' => 'bail|string|max:100',
            'event_forms' => 'bail|required|array',
            'event_forms.*' => 'bail|string|max:60',
            'event_status' => 'bail|required|string|in:active,pending,completed,cancelled',
            'event_verify' => 'bail|required|string|in:unverified,verified,past',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'event_name' => $this->input('eventName'),
            'event_description' => $this->input('eventDescription'),
            'event_schedule' => $this->input('eventSchedule'),
            'event_location' => $this->input('eventLocation'),
            'event_model' => $this->input('eventModel'),
            'event_types' => $this->input('eventTypes'),
            'event_departments' => $this->input('eventDepartments'),
            'event_forms' => $this->input('eventForms'),
            'event_status' => $this->input('eventStatus'),
            'event_verify' => $this->input('eventVerify'),
        ]);
    }

    public function messages(): array
    {
        // You can Create a Customize error message here ✍️😋
        return [

        ];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('You are not authorized to perform this action only HR.');
    }
}
