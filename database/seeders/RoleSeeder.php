<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
       * Custom Role Create
       */
        Role::FirstOrCreate(['name' => 'hr']);
        Role::FirstOrCreate(['name' => 'admin']);
    }
}
