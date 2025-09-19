<?php

namespace App\Http\Requests;

use App\Exceptions\NominateParticipantException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class NominateParticipantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nominated' => 'bail|required|array',
            'nominated.*.total_nominated' => 'bail|required|integer|min:1',
            'nominated.*.event_id' => 'bail|required|integer|exists:ldrEvents,id',
            'nominated.*.is_training' => 'bail|required|boolean',
            'nominated.*.event_name' => 'bail|required|string|exists:ldrEvents,event_name',
            'nominated.*.employee_control_no' => 'bail|required|digits:6|exists:vwActive,ControlNo',
            'nominated.*.employee_name' => 'bail|required|string',
            'nominated.*.created_at' => 'required|date:Y-m-d H:i:s',
            'nominated.*.updated_at' => 'required|date:Y-m-d H:i:s',
        ];
    }

    /**
     * @throws NominateParticipantException
     */

    protected function prepareForValidation()
    {
        $transform = [];
        foreach ($this->input('nominated') as $participant => $key) {
            $transform[] = [
                'total_nominated' => $key['totalNominated'] ?? '',
                'employee_control_no' => $key['controlNo'] ?? '',
                'employee_name' => $key['employeeName'] ?? '',
                'event_name' => $key['eventName'] ?? '',
                'is_training' => $key['isTraining'] ?? '',
                'event_id' => $key['eventId'] ?? '',
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s')
            ];

        }
        $unique = [];
        /* Check for duplicates 👌🤖*/

        foreach ($transform as $item) {
            $check = $item['employee_control_no'];
            if (!isset($unique[$check])) {
                $unique[$check] = $item;
            } else {
                throw new NominateParticipantException('Duplicate entry for employee control no: ' . $check, '', 409);
            }
        }
        $this->merge(['nominated' => array_values($unique)]);
    }

    public function messages(): array
    {
        return [
            'nominated.*.employee_control_no.unique' => 'Employee Control No Already Exists',
            'nominated.*.employee_control_no.exists' => 'Employee does not exist',
            'nominated.*.event_id.exists' => 'Event Does not exist',
            'nominated.*.event_name.exists' => 'Event Name does not exist',
        ];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('You are not authorized to perform this action only ADMIN.');
    }
}
