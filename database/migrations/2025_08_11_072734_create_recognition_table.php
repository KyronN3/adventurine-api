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
        Schema::create('ldrRecognitions', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->text('hr_comment')->nullable();;
            $table->date('date_submitted');
            $table->string('employee_id');
            $table->string('employee_department');
            $table->string('employee_name');
            $table->date('recognition_date');
            $table->string('title');
            $table->text('achievement_description');
            $table->timestamps();
        });

        Schema::create('ldrRecognition_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recognition_id')->constrained('ldrRecognitions')->onDelete('cascade');
            $table->string('original_name');
            $table->string('image_name');
            $table->timestamps();
        });

        Schema::create('ldrRecognition_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recognition_id')->constrained('ldrRecognitions')->onDelete('cascade');
            $table->string('original_name');
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
        Schema::dropIfExists('ldrRecognition_images');
        Schema::dropIfExists('ldrRecognition_files');
        Schema::dropIfExists('ldrRecognitions');

    }
};
