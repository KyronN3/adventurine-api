<?php

namespace App\Services;

class ResponseData
{

    public static function recognition($recognition, array $images = [], array $files = []): array
    {
        $imageUrls = array_map(fn($image) => [
            'id' => $image['id'] ?? '',
            'name' => $image['name'] ?? '',
            'url' => $image['url'] ?? '',
            'expires' => $image['expires'] ?? '',
        ], $images);

        $fileUrls = array_map(fn($file) => [
            'id' => $file['id'] ?? '',
            'name' => $file['name'] ?? '',
            'url' => $file['url'] ?? '',
            'expires' => $file['expires'] ?? '',
        ], $files);

        return [
            'id' => $recognition->id ?? '',
            'status' => $recognition->status ?? '',
            'hrComment' => $recognition->hr_comment ?? '',
            'dateSubmitted' => $recognition->date_submitted?->format('Y-m-d') ?? null,
            'employeeId' => $recognition->employee_id ?? '',
            'employeeDepartment' => $recognition->employee_department ?? '',
            'employeeName' => $recognition->employee_name ?? '',
            'recognitionDate' => $recognition->recognition_date?->format('Y-m-d'),
            'recognitionType' => $recognition->recognition_type ?? '',
            'achievementDescription' => $recognition->achievement_description ?? '',

            'images' => $imageUrls,
            'files' => $fileUrls,
        ];
    }


    public static function event($event): array
    {
        return [
            'id' => $event->id ?? '',
            'eventName' => $event->event_name ?? '',
            'eventType' => $event->event_type ?? '',
            'eventDescription' => $event->event_description ?? '',
            'eventDepartments' => $event->event_departments ?? [],
            'eventDuration' => $event->event_duration ?? '',
            'eventDate' => $event->event_date?->format('Y-m-d') ?? null,
            'eventEndDate' => $event->event_end_date?->format('Y-m-d') ?? null,
            'eventLocation' => $event->event_location ?? '',
            'eventModel' => $event->event_model?? '',
            'eventForms' => $event->event_forms ?? [],
            'eventActivity' => $event->event_activity ?? '',          
            'eventCreated' => $event->created_at?->format('Y-m-d') ?? null,
            'eventStatus' => $event->event_status ?? '',
            'eventVerify' => $event->event_verify ?? '',
            'eventUpdated' => $event->updated_at?->format('Y-m-d') ?? null,

            'outcomes' => $event->outcomes ?? [],
            'participants' => $event->participants ?? [],
            'attendance' => $event->attendance ?? [],
        ];
    }


}
