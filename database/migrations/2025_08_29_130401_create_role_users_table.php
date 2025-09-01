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
        if (!Schema::hasTable('ldrRole_user')) {
            Schema::create('ldrRole_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('ldrUser')->onDelete('cascade');
                $table->foreignId('role_id')->constrained('ldrRole')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ldrRole_user');
    }
};
