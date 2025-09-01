```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Event;

class MakeFieldsNonNullableInEventsTable extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            
            Event::whereNull('event_date')->update(['event_date' => now()->format('Y-m-d')]);
            Event::whereNull('event_name')->update(['event_name' => 'Unnamed Event']);
            Event::whereNull('event_description')->update(['event_description' => 'No description provided']);
            Event::whereNull('event_venue')->update(['event_venue' => 'Unknown Venue']);
            Event::whereNull('event_mode')->update(['event_mode' => 'in-person']);
            Event::whereNull('event_activity')->update(['event_activity' => 'No activity specified']);
            
            
            if (Schema::hasColumn('events', 'event_date')) {
                $table->date('event_date')->nullable(false)->change();
            } else {
                $table->date('event_date')->default(now()->format('Y-m-d'));
            }
            if (Schema::hasColumn('events', 'event_name')) {
                $table->string('event_name', 255)->nullable(false)->change();
            } else {
                $table->string('event_name', 255)->default('Unnamed Event');
            }
            if (Schema::hasColumn('events', 'event_description')) {
                $table->string('event_description', 1000)->nullable(false)->change();
            } else {
                $table->string('event_description', 1000)->default('No description provided');
            }
            if (Schema::hasColumn('events', 'event_venue')) {
                $table->string('event_venue', 255)->nullable(false)->change();
            } else {
                $table->string('event_venue', 255)->default('Unknown Venue');
            }
            if (Schema::hasColumn('events', 'event_mode')) {
                $table->string('event_mode', 100)->nullable(false)->change();
            } else {
                $table->string('event_mode', 100)->default('in-person');
            }
            if (Schema::hasColumn('events', 'event_activity')) {
                $table->string('event_activity', 255)->nullable(false)->change();
            } else {
                $table->string('event_activity', 255)->default('No activity specified');
            }
            
            if (!Schema::hasColumn('events', 'event_tags')) {
                $table->json('event_tags')->nullable();
            }
            if (!Schema::hasColumn('events', 'event_departments')) {
                $table->json('event_departments')->nullable();
            }
            if (!Schema::hasColumn('events', 'event_forms')) {
                $table->json('event_forms')->nullable();
            }
            if (!Schema::hasColumn('events', 'event_status')) {
                $table->enum('event_status', ['active', 'completed', 'cancelled'])->default('active');
            }
            if (!Schema::hasColumn('events', 'event_created')) {
                $table->date('event_created')->nullable();
            }
        });
    }

   
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
           
            if (Schema::hasColumn('events', 'event_date')) {
                $table->date('event_date')->nullable()->change();
            }
            if (Schema::hasColumn('events', 'event_name')) {
                $table->string('event_name', 255)->nullable()->change();
            }
            if (Schema::hasColumn('events', 'event_description')) {
                $table->string('event_description', 1000)->nullable()->change();
            }
            if (Schema::hasColumn('events', 'event_venue')) {
                $table->string('event_venue', 255)->nullable()->change();
            }
            if (Schema::hasColumn('events', 'event_mode')) {
                $table->string('event_mode', 100)->nullable()->change();
            }
            if (Schema::hasColumn('events', 'event_activity')) {
                $table->string('event_activity', 255)->nullable()->change();
            }
        });
    }
};