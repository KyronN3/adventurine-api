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
        //
        Schema::create('recognitions', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->text('hr_comment')->nullable();;
            $table->date('date_submitted');
            $table->string('employee_id');
            $table->string('employee_department');
            $table->string('employee_name');
            $table->date('recognition_date');
            $table->string('recognition_type');
            $table->text('achievement_description');
            $table->timestamps();
        });

        Schema::create('recognition_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recognition_id')->constrained('recognitions')->onDelete('cascade');
            $table->string('image_name');
            $table->timestamps();
        });

        Schema::create('recognition_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recognition_id')->constrained('recognitions')->onDelete('cascade');
            $table->string('file_name');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('recognition_images');
        Schema::dropIfExists('recognition_files');
        Schema::dropIfExists('recognitions');

    }
};
