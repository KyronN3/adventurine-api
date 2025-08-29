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
            $table->string('control_no'); // Foreign key to vwActive.ControlNo
            $table->string('medical_history', 100)->default('NONE');
            $table->integer('bpm_systolic');
            $table->integer('bpm_diastolic');
            $table->date('bpm_dateTaken');
            $table->timestamps();

            // Add foreign key constraint (if needed)
            // $table->foreign('control_no')->references('ControlNo')->on('vwActive');
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
