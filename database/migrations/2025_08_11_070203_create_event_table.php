<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('ldrEvents', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->string('event_types');
            $table->text('event_description');
            $table->json('event_departments');
            $table->json('event_schedule');
            $table->string('event_location');
            $table->string('event_model');
            $table->json('event_forms')->nullable();
            $table->string('event_status');
            $table->string('event_verify');
            $table->timestamps();
        });

        Schema::create('ldrEvent_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('ldrEvents')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('ldrEvent_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('ldrEvents')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('check_in')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // New event_participants table
        Schema::create('ldrEvent_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('ldrEvents')->onDelete('cascade');
            $table->string('participant_id')->nullable(); // corresponds to 'id' in your participant data
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('ldrEvent_participants');
        Schema::dropIfExists('ldrEvent_attendance');
        Schema::dropIfExists('ldrEvent_outcomes');
        Schema::dropIfExists('ldrEvents');
    }
};
