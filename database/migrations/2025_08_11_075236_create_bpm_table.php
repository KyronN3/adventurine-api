<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = config('database.default');

        Schema::create('ldrBpm', function (Blueprint $table) {
            $table->id();
            $table->string('control_no'); // Foreign key to vwActive.ControlNo
            $table->string('medical_history', 100)->default('NONE');
            $table->integer('bpm_systolic');
            $table->integer('bpm_diastolic');
            $table->date('bpm_dateTaken');
            $table->timestamps();
        });

        // Apply database-specific collation to match vwActive
        // This bad boy fixes weird UTF shennanigans, cuz it's diff in mariadb/mysql - Fishmans 🎣
        $this->applyCollation($connection);
    }

    /**
     * Apply appropriate collation based on database type
     */
    private function applyCollation(string $connection): void
    {
        switch ($connection) {
            case 'mysql':
            case 'mariadb':
                // MySQL/MariaDB: Convert to utf8mb4_general_ci to match vwActive
                try {
                    DB::statement('ALTER TABLE ldrBpm CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
                } catch (\Exception $e) {
                    \Log::warning('Could not apply MySQL collation: ' . $e->getMessage());
                }
                break;
                
            default:
                break;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ldrBpm');
    }
};
