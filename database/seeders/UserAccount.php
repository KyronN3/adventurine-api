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
        if (!User::where('email_control_no', $email)->exists()) {
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
                'email_control_no' => 'admin@admin.com',
                'office' => 'OFFICE OF THE CITY ADMINISTRATOR',
                'control_no' => '1',
                'password' => Hash::make('admin123')
            ]);
            $admin->assignRole('admin');
        });

        $this->existChecker('hr@hr.com', function () {
            $hr = User::query()->firstOrCreate([
                'email_control_no' => 'hr@hr.com',
                'control_no' => '12',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('hr123')
            ]);
            $hr->assignRole('hr');
        });

        $this->existChecker('jabagat@admin.com', function () {
            $jabagatHR = User::query()->firstOrCreate([
                'email_control_no' => 'jabagat@hr.com',
                'control_no' => '123',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('hr123')
            ]);
            $jabagatHR->assignRole('hr');
        });

        $this->existChecker('jabagat@admin.com', function () {
            $jabagatAdmin = User::query()->firstOrCreate([
                'email_control_no' => 'jabagat@admin.com',
                'control_no' => '1234',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('admin123')
            ]);
            $jabagatAdmin->assignRole('admin');
        });

        $this->existChecker('kyron@admin.com', function () {
            $kyronAdmin = User::query()->firstOrCreate([
                'email_control_no' => 'kyron@admin.com',
                'control_no' => '12345',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('admin123')
            ]);
            $kyronAdmin->assignRole('admin');
        });


        $this->existChecker('kyron@hr.com', function () {
            $kyronHR = User::query()->firstOrCreate([
                'email_control_no' => 'kyron@hr.com',
                'control_no' => '123456',
                'office' => 'OFFICE OF THE CITY ACCOUNTANT',
                'password' => Hash::make('hr123')
            ]);
            $kyronHR->assignRole('hr');
        });

        $this->existChecker('engineer@admin.com', function () {
            $engineer = User::query()->firstOrCreate([
                'email_control_no' => 'engineer@admin.com',
                'office' => 'OFFICE OF THE CITY ENGINEER',
                'control_no' => '1234567',
                'password' => Hash::make('admin123')
            ]);
            $engineer->assignRole('admin');
        });


    }
}
