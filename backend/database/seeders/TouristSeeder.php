<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TouristSeeder extends Seeder
{
    public function run(): void
    {
        $tourists = [
            [
                'name' => 'Youssef Touriste',
                'email' => 'youssef@example.com',
                'preferred_language' => 'fr',
                'avatar_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/User_icon_2.svg',
            ],
            [
                'name' => 'Emily Carter',
                'email' => 'emily@example.com',
                'preferred_language' => 'en',
                'avatar_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/User_icon_2.svg',
            ],
        ];

        foreach ($tourists as $tourist) {
            User::updateOrCreate(
                ['email' => $tourist['email']],
                [
                    'name' => $tourist['name'],
                    'password' => Hash::make('password'),
                    'role' => 'tourist',
                    'preferred_language' => $tourist['preferred_language'],
                    'avatar_url' => $tourist['avatar_url'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
