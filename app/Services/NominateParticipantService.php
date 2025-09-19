<?php

namespace App\Services;

use App\Exceptions\EventServiceException;
use App\Exceptions\NominateParticipantException;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\DB;

class NominateParticipantService
{

    public function nominateParticipant($participantData)
    {
        try {
            DB::beginTransaction();

            $event = Event::find($participantData['nominated'][0]['event_id']);
            if (!$event) {
                throw new EventServiceException('Event does not exist.', '', '404');
            }

            foreach ($participantData['nominated'] as $participant) {
                $eventCheck = EventParticipant::query()->where('employee_control_no', $participant['employee_control_no'])->first();
                if ($eventCheck && $eventCheck->is_training) {
                    DB::rollBack();
                    throw new NominateParticipantException('Participant ' . $eventCheck->employee_name . ' already registered to another event ', '', 409);
                }

                $updateTotalNominated = EventParticipant::query()
                    ->where('employee_control_no', $participant['employee_control_no'])
                    ->increment('total_nominated', $participant['total_nominated']);

                if ($updateTotalNominated) {
                    $key = array_search($participant['employee_control_no'], $participantData['nominated'][0]);
                    if ($key) {
                        unset($participantData['nominated'][0]);
                    }
                }
            }

            $successUpdateEvent = $event->update([
                "event_status" => "active",
                "event_verify" => "verified"
            ]);

            $nominated = EventParticipant::query()->insert($participantData['nominated']);

            if ($nominated && $successUpdateEvent) {
                DB::commit();
                return $participantData['nominated'];
            }

            throw new \Exception('insertion failed');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new NominateParticipantException('Failed to nominate Participant, ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }
}
