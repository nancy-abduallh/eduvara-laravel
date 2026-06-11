<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@eduvara.com')],
            [
                'name'                 => 'Eduvara Admin',
                'password'             => Hash::make('Admin@123456'),
                'role'                 => 'admin',
                'onboarding_completed' => true,
                'email_verified_at'    => now(),
            ]
        );

        $this->command->info('Admin created: admin@eduvara.com / Admin@123456');
    }
}
