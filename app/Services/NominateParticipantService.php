<?php

namespace App\Services;

use App\Exceptions\NominateParticipantException;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\DB;

class NominateParticipantService
{

    public function nominateParticipant($data)
    {
        try {
            DB::beginTransaction();
            foreach ($data['nominated'] as $participant) {
                if (EventParticipant::query()->where('employee_control_no', $participant['employee_control_no'])->exists()) {
                    DB::rollBack();
                    throw new NominateParticipantException('Participant already registered to another event', '', 409);
                }
            }

            $nominated = EventParticipant::query()->insert($data['nominated']);
            if ($nominated) {
                DB::commit();
                return $data['nominated'];
            }
            throw new \Exception('insertion failed');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new NominateParticipantException('Failed to nominate Participant, ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }
}
