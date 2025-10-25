<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@mail.com', // Kunci pencarian (unique key)
                'data' => [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                    'abilities' => [
                        'task_read',
                        'task_create',
                        'task_update',
                        'task_delete',
                    ],
                ]
            ],
            [
                'email' => 'viewer@mail.com',
                'data' => [
                    'name' => 'Viewer User',
                    'password' => Hash::make('password'),
                    'abilities' => [
                        'task_read'
                    ],
                ]
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge(['email' => $userData['email']], $userData['data'])
            );
        }
    }
}
