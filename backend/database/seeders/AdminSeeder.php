<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@maghrebpass.test'],
            [
                'name' => 'Admin MaghrebPass',
                'password' => Hash::make('password'),
                'preferred_language' => 'fr',
                'avatar_url' => 'https://upload.wikimedia.org/wikipedia/commons/2/2c/Flag_of_Morocco.svg',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $admin->forceFill(['role' => 'admin'])->save();
    }
}
