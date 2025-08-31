<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAccount extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'position' => 'admin',
            'password' => Hash::make('admin123')
        ]);
        $hr = User::create([
            'name' => 'hr',
            'position' => 'hr',
            'email' => 'hr@hr.com',
            'password' => Hash::make('hr123')
        ]);
        
        $admin->assignRole('admin');
        $hr->assignRole('hr');

    }
}
