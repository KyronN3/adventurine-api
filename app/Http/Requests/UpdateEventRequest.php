<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $map = [
            'event_name' => ['required', 'string', 'max:255'],
            'event_description' => ['required', 'string', 'max:1000'],
            'event_schedule' => ['required', 'array'],
            'event_schedule.*.date' => ['required', 'date', 'date_format:Y-m-d'],
            'event_schedule.*.timeStart' => ['required', 'date_format:H:i:s'],
            'event_schedule.*.timeEnd' => ['required', 'date_format:H:i:s'],
            'event_location' => ['required', 'string', 'max:255'],
            'event_model' => ['required', 'string', 'max:100', 'in:in-house,external'],
            'event_types' => ['required', 'string', 'max:50', 'in:seminar,training,workshop,orientation,conference,webinar,team_building,assessment'],
            'event_departments' => ['required', 'array'],
            'event_departments.*' => ['string', 'max:100'],
            'event_forms' => ['required', 'array'],
            'event_forms.*' => ['string', 'max:60'],
            'event_status' => ['required', 'string', 'in:active,pending,completed,cancelled'],
            'event_verify' => ['required', 'string', 'in:unverified,verified,past'],
        ];

        $rule = [];

        foreach ($map as $field => $rules) {
            switch ($this->method()) {
                case 'PATCH':
                    array_unshift($rules, 'bail', 'sometimes');
                    $rule[$field] = $rules;
                    break;
                case 'PUT':
                    array_unshift($rules, 'bail');
                    $rule[$field] = $rules;
                    break;
            }
        }

        return $rule;
    }

    protected function prepareForValidation()
    {

        $fieldMappings = [
            'eventName' => 'event_name',
            'eventDescription' => 'event_description',
            'eventSchedule' => 'event_schedule',
            'eventModel' => 'event_model',
            'eventLocation' => 'event_location',
            'eventTypes' => 'event_types',
            'eventDepartments' => 'event_departments',
            'eventForms' => 'event_forms',
            'eventStatus' => 'event_status',
            'eventVerify' => 'event_verify'
        ];

        $transform = [];

        switch ($this->method()) {
            case 'PATCH':
                foreach ($fieldMappings as $fieldName => $fieldMapping) {
                    if ($this->has($fieldName)) {
                        $transform[$fieldMapping] = $this->input($fieldName);
                    }
                }
                break;
            case 'PUT':
                foreach ($fieldMappings as $fieldName => $fieldMapping) {
                    $transform[$fieldMapping] = $this->input($fieldName);
                }
                break;
        }
        $this->merge($transform);
    }


    function failedAuthorization()
    {
        throw new AuthorizationException('You are not authorized to perform this action only ADMIN . ');
    }

}
