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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->json('event_tags')->nullable();
            $table->text('event_description')->nullable();
            $table->json('event_departments')->nullable();
            $table->date('event_date')->nullable();
            $table->string('event_activity')->nullable();
            $table->string('event_venue')->nullable();
            $table->string('event_mode')->nullable();
            $table->json('event_forms')->nullable();
            $table->date('event_created')->nullable();
            $table->string('event_status')->nullable();
            $table->timestamps();
        });

        Schema::create('event_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('event_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('check_in')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // New event_participants table
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
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
        Schema::dropIfExists('event_participants');
        Schema::dropIfExists('event_attendance');
        Schema::dropIfExists('event_outcomes');
        Schema::dropIfExists('events');
    }
};
