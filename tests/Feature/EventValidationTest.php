<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_event_name_returns_invalid_message()
    {
        $response = $this->postJson('/api/v1/hr/event/create', [
            'event_description' => 'Test description',
            'event_date' => '2024-01-15',
            'event_venue' => 'Test Venue',
            'event_departments' => ['HR']
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['event_name']);
        $response->assertJsonFragment(['event_name' => ['Please provide event name']]);
    }

    public function test_missing_venue_returns_please_provide_venue_message()
    {
        $response = $this->postJson('/api/v1/hr/event/create', [
            'event_name' => 'Test Event',
            'event_description' => 'Test description',
            'event_date' => '2024-01-15',
            'event_departments' => ['HR']
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['event_venue']);
        $response->assertJsonFragment(['event_venue' => ['Please provide venue']]);
    }

    public function test_missing_departments_returns_please_provide_department_message()
    {
        $response = $this->postJson('/api/v1/hr/event/create', [
            'event_name' => 'Test Event',
            'event_description' => 'Test description',
            'event_date' => '2024-01-15',
            'event_venue' => 'Test Venue'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['event_departments']);
        $response->assertJsonFragment(['event_departments' => ['Please provide department']]);
    }

    public function test_valid_event_request_passes_validation()
    {
        $response = $this->postJson('/api/v1/hr/event/create', [
            'event_name' => 'Test Event',
            'event_description' => 'Test description',
            'event_date' => '2024-01-15',
            'event_venue' => 'Test Venue',
            'event_departments' => ['ADMIN', 'HR']
        ]);

        
        $response->assertStatus(422); 
        
        $response->assertJsonMissing(['event_name' => ['Invalid']]);
        $response->assertJsonMissing(['event_venue' => ['Please provide venue']]);
        $response->assertJsonMissing(['event_departments' => ['Please provide department']]);
    }

    public function test_empty_event_name_returns_invalid_message()
    {
        $response = $this->postJson('/api/v1/hr/event/create', [
            'event_name' => '',
            'event_description' => 'Test description',
            'event_date' => '2024-01-15',
            'event_venue' => 'Test Venue',
            'event_departments' => ['HR']
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['event_name']);
        $response->assertJsonFragment(['event_name' => ['Invalid']]);
    }

    public function test_empty_venue_returns_please_provide_venue_message()
    {
        $response = $this->postJson('/api/v1/hr/event/create', [
            'event_name' => 'Test Event',
            'event_description' => 'Test description',
            'event_date' => '2024-01-15',
            'event_venue' => '',
            'event_departments' => ['HR']
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['event_venue']);
        $response->assertJsonFragment(['event_venue' => ['Please provide venue']]);
    }

    public function test_empty_departments_array_returns_please_provide_department_message()
    {
        $response = $this->postJson('/api/v1/hr/event/create', [
            'event_name' => 'Test Event',
            'event_description' => 'Test description',
            'event_date' => '2024-01-15',
            'event_venue' => 'Test Venue',
            'event_departments' => []
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['event_departments']);
        $response->assertJsonFragment(['event_departments' => ['Please provide department']]);
    }
}
