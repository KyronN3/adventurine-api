<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bpm', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('designation');
            $table->enum('sex', ['M', 'F']);
            $table->string('medical_history', 100)->default('NONE');
            $table->enum('status', ['Permanent', 'Coterminous', 'Temporary', 'Casual', 'Job Order', 'Honorarium']);
            $table->integer('bpm_systolic');
            $table->integer('bpm_diastolic');
            $table->date('bpm_dateTaken');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpm');
    }
};
