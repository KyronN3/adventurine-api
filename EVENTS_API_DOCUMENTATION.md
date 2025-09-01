# Events API Documentation

## Overview
This document describes the Events API endpoints for the Project-Adventurine application. The API provides full CRUD operations for managing training events.

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication
Most endpoints require authentication. Include the authentication token in the request headers:
```
Authorization: Bearer {your-token}
```

## Endpoints

### 1. Get All Events
**GET** `/event/search/all`

Returns all events in the system.

**Response:**
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "Events retrieved successfully",
  "data": [
    {
      "id": 1,
      "event_name": "Team Building Workshop",
      "event_description": "Annual team building event",
      "event_date": "2024-02-15",
      "event_venue": "Conference Room A",
      "event_mode": "In-person",
      "event_activity": "Workshop",
      "event_tags": ["team-building", "workshop"],
      "event_departments": ["HR", "IT"],
      "event_status": "active",
      "event_created": "2024-01-10",
      "created_at": "2024-01-10T10:00:00.000000Z",
      "updated_at": "2024-01-10T10:00:00.000000Z"
    }
  ]
}
```

### 2. Get Event by ID
**GET** `/event/search/{id}`

Returns a specific event by its ID.

**Parameters:**
- `id` (integer, required): The event ID

**Response:**
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "Event retrieved successfully",
  "data": {
    "id": 1,
    "event_name": "Team Building Workshop",
    "event_description": "Annual team building event",
    "event_date": "2024-02-15",
    "event_venue": "Conference Room A",
    "event_mode": "In-person",
    "event_activity": "Workshop",
    "event_tags": ["team-building", "workshop"],
    "event_departments": ["HR", "IT"],
    "event_status": "active",
    "event_created": "2024-01-10",
    "outcomes": [],
    "attendance": [],
    "participants": [],
    "created_at": "2024-01-10T10:00:00.000000Z",
    "updated_at": "2024-01-10T10:00:00.000000Z"
  }
}
```

### 3. Get Events by Status
**GET** `/event/search/status?status={status}`

Returns events filtered by status.

**Parameters:**
- `status` (string, optional): Filter by status. Options: `all`, `active`, `completed`, `cancelled`. Default: `all`

**Response:**
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "Events retrieved successfully",
  "data": [
    {
      "id": 1,
      "event_name": "Team Building Workshop",
      "event_status": "active",
      // ... other fields
    }
  ]
}
```

### 4. Get Upcoming Events
**GET** `/event/search/upcoming`

Returns all upcoming events (events with dates >= today and status = active).

**Response:**
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "Upcoming events retrieved successfully",
  "data": [
    {
      "id": 1,
      "event_name": "Team Building Workshop",
      "event_date": "2024-02-15",
      "event_status": "active",
      // ... other fields
    }
  ]
}
```

### 5. Get Past Events
**GET** `/event/search/past`

Returns all past events (events with dates < today).

**Response:**
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "Past events retrieved successfully",
  "data": [
    {
      "id": 2,
      "event_name": "Previous Training",
      "event_date": "2024-01-10",
      // ... other fields
    }
  ]
}
```

### 6. Create New Event
**POST** `/hr/event/create`

Creates a new event. Requires HR permissions.

**Request Body:**
```json
{
  "event_name": "New Training Event",
  "event_description": "Description of the training event",
  "event_date": "2024-03-15",
  "event_venue": "Conference Room B",
  "event_mode": "Hybrid",
  "event_activity": "Training",
  "event_tags": ["training", "skill-development"],
  "event_departments": ["IT", "Marketing"],
  "event_status": "active"
}
```

**Required Fields:**
- `event_name` (string, max 255 chars)
- `event_description` (string, max 1000 chars)
- `event_date` (date, format: Y-m-d)
- `event_venue` (string, max 255 chars)

**Optional Fields:**
- `event_mode` (string, max 100 chars)
- `event_activity` (string, max 255 chars)
- `event_tags` (array of strings, max 50 chars each)
- `event_departments` (array of strings, max 50 chars each)
- `event_forms` (array)
- `event_status` (string: active, completed, cancelled)

**Response:**
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "New event created successfully!",
  "data": {
    "id": 3,
    "event_name": "New Training Event",
    "event_description": "Description of the training event",
    "event_date": "2024-03-15",
    "event_venue": "Conference Room B",
    "event_mode": "Hybrid",
    "event_activity": "Training",
    "event_tags": ["training", "skill-development"],
    "event_departments": ["IT", "Marketing"],
    "event_status": "active",
    "event_created": "2024-01-15",
    "outcomes": [],
    "attendance": [],
    "participants": [],
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
  }
}
```

### 7. Update Event
**PUT** `/hr/event/{event}`

Updates an existing event. Requires HR permissions.

**Parameters:**
- `event` (integer, required): The event ID

**Request Body:**
```json
{
  "event_name": "Updated Training Event",
  "event_description": "Updated description",
  "event_status": "completed"
}
```

**Response:**
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "Event updated successfully!",
  "data": {
    "id": 3,
    "event_name": "Updated Training Event",
    "event_description": "Updated description",
    "event_status": "completed",
    // ... other fields
  }
}
```

### 8. Delete Event
**DELETE** `/hr/event/{event}`

Deletes an event. Requires HR permissions.

**Parameters:**
- `event` (integer, required): The event ID

**Response:**
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "Event deleted successfully!"
}
```

## Error Responses

### Validation Error
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "The event name field is required."
}
```

### Not Found Error
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "Event not found"
}
```

### Server Error
```json
{
  "requestAt": "2024-01-15 10:30:00",
  "message": "Error creating new event: Database connection failed"
}
```

## Data Models

### Event Model
```php
{
  "id": "integer",
  "event_name": "string (255 chars)",
  "event_description": "text (1000 chars)",
  "event_date": "date (Y-m-d)",
  "event_venue": "string (255 chars)",
  "event_mode": "string (100 chars)",
  "event_activity": "string (255 chars)",
  "event_tags": "json array",
  "event_departments": "json array",
  "event_forms": "json array",
  "event_created": "date (Y-m-d)",
  "event_status": "string (active|completed|cancelled)",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Related Models

#### EventOutcome
```php
{
  "id": "integer",
  "event_id": "integer (foreign key)",
  "title": "string (255 chars)",
  "description": "text",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

#### EventAttendance
```php
{
  "id": "integer",
  "event_id": "integer (foreign key)",
  "name": "string (255 chars)",
  "email": "string (email)",
  "status": "string (255 chars)",
  "check_in": "datetime",
  "notes": "text",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

#### EventParticipant
```php
{
  "id": "integer",
  "event_id": "integer (foreign key)",
  "participant_id": "string",
  "name": "string (255 chars)",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

## Testing

You can test the API using the provided Laravel command:

```bash
php artisan test:event-api
```

This command will test all CRUD operations and verify the API functionality.

## Notes

1. All dates should be in `Y-m-d` format (e.g., "2024-01-15")
2. Arrays (tags, departments, forms) are stored as JSON in the database
3. The API automatically sets `event_created` to the current date when creating events
4. Default `event_status` is "active" for new events
5. All endpoints return consistent response format with `requestAt`, `message`, and `data` fields
