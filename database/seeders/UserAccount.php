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

        $admin = User::FirstOrCreate([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'office' => 'OFFICE OF THE CITY ADMINISTRATOR',
            'password' => Hash::make('admin123')
        ]);
        $hr = User::FirstOrCreate([
            'name' => 'hr',
            'email' => 'hr@hr.com',
            'office' => 'OFFICE OF THE CITY ACCOUNTANT',
            'password' => Hash::make('hr123')
        ]);

        $jabagatHR = User::FirstOrCreate([
            'name' => 'jabagat',
            'email' => 'jabagat@hr.com',
            'office' => 'OFFICE OF THE CITY ACCOUNTANT',
            'password' => Hash::make('hr123')
        ]);

        $jabagatAdmin = User::FirstOrCreate([
            'name' => 'jabagat',
            'email' => 'jabagat@admin.com',
            'office' => 'OFFICE OF THE CITY ACCOUNTANT',
            'password' => Hash::make('admin123')
        ]);

        $kyronAdmin = User::FirstOrCreate([
            'name' => 'kyron',
            'email' => 'kyron@admin.com',
            'office' => 'OFFICE OF THE CITY ACCOUNTANT',
            'password' => Hash::make('admin123')
        ]);
            
        $kyronHR = User::FirstOrCreate([
            'name' => 'kyron',
            'email' => 'kyron@hr.com',
            'office' => 'OFFICE OF THE CITY ACCOUNTANT',
            'password' => Hash::make('hr123')
        ]);

        $kyronAdmin->assignRole('admin');
        $kyronHR->assignRole('hr');
        $jabagatAdmin->assignRole('admin');
        $jabagatHR->assignRole('hr');
        $admin->assignRole('admin');
        $hr->assignRole('hr');

    }
}
