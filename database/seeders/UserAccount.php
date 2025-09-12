<?php

namespace Database\Seeders;

use App\Models\User;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAccount extends Seeder
{

    protected function existChecker(string $email, Closure $callback): void
    {
        if (!User::where('email', $email)->exists()) {
            $callback();
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->existChecker('admin@admin.com', function () {
            $admin = User::create([
                'email' => 'admin@admin.com',
                'name' => 'admin',
                'office' => 'OFFICE OF THE CITY ADMINISTRATOR',
                'password' => Hash::make('admin123')
            ]);
            $admin->assignRole('admin');
        });

        $this->existChecker('hr@hr.com', function () {
            $hr = User::query()->firstOrCreate([
                'name' => 'hr',
                'email' => 'hr@hr.com',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('hr123')
            ]);
            $hr->assignRole('hr');
        });

        $this->existChecker('jabagat@admin.com', function () {
            $jabagatHR = User::query()->firstOrCreate([
                'name' => 'jabagat',
                'email' => 'jabagat@hr.com',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('hr123')
            ]);
            $jabagatHR->assignRole('hr');
        });

        $this->existChecker('jabagat@admin.com', function () {
            $jabagatAdmin = User::query()->firstOrCreate([
                'name' => 'jabagat',
                'email' => 'jabagat@admin.com',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('admin123')
            ]);
            $jabagatAdmin->assignRole('admin');
        });

        $this->existChecker('kyron@admin.com', function () {
            $kyronAdmin = User::query()->firstOrCreate([
                'name' => 'kyron',
                'email' => 'kyron@admin.com',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('admin123')
            ]);
            $kyronAdmin->assignRole('admin');
        });


        $this->existChecker('kyron@hr.com', function () {
            $kyronHR = User::query()->firstOrCreate([
                'name' => 'kyron',
                'email' => 'kyron@hr.com',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('hr123')
            ]);
            $kyronHR->assignRole('hr');
        });

        $this->existChecker('engineer@admin.com', function () {
            $engineer = User::query()->firstOrCreate([
                'name' => 'engineer',
                'email' => 'engineer@admin.com',
                'office' => 'OFFICE OF THE CITY ENGINEER',
                'password' => Hash::make('admin123')
            ]);
            $engineer->assignRole('admin');
        });


    }
}
