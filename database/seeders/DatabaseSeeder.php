<?php

namespace Database\Seeders;

use App\Models\User;
use BaconQrCode\Encoder\QrCode;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@majisapainter.com',
            'password' => Hash::make('majisapainter111'),
            'mobile_number' => '9876543210',
            'is_admin' => 1,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'password' => Hash::make('12345678'),
            'mobile_number' => '12345678',
            'is_admin' => 0,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Test',
            'email' => 'test1@gmail.com',
            'password' => Hash::make('12345678'),
            'mobile_number' => '9876543211',
            'is_admin' => 0,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        DB::table('settings')->insert([
            [
                'key' => 'ADMIN_EMAIL',
                'value' => 'admin@maajasolpaints.com',
            ],
            [
                'key' => 'APP_LINK',
                'value' => 'https://maajasolpaints.com/',
            ],
            [
                'key' => 'APP_NAME',
                'value' => 'Maajasol Paints',
            ],
            [
                'key' => 'APP_VERSION',
                'value' => '1.0.0',
            ],
            [
                'key' => 'MAINTENANCE_MODE',
                'value' => 0,
            ],
            [
                'key' => 'FORCE_UPDATE',
                'value' => 0,
            ],
        ]);
    }
}
