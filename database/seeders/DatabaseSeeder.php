<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        \App\Models\Karyawan::updateOrCreate(
            ['staff_id' => 'EMP101'],
            [
                'name' => 'Budi Santoso',
                'position' => 'Developer',
                'salary' => 8500000,
                'bank_name' => 'BCA',
                'bank_account' => '802930491',
                'bank_account_name' => 'Budi Santoso',
                'npwp' => '12.345.678.9-012.000',
                'phone' => '6287857580910',
            ]
        );
    }
}
