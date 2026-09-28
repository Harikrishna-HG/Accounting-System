<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super-admin' => Role::where('slug', 'super-admin')->first(),
            'admin' => Role::where('slug', 'admin')->first(),
            'accountant' => Role::where('slug', 'accountant')->first(),
            'auditor' => Role::where('slug', 'auditor')->first(),
        ];

        $users = [
            [
                'email' => 'admin@gmail.com',
                'name' => 'सुपर प्रशासक',
                'password' => 'Admin@123',
                'role' => $roles['super-admin'],
            ],
            [
                'email' => 'manager@gmail.com',
                'name' => 'प्रशासक',
                'password' => 'Manager@123',
                'role' => $roles['admin'],
            ],
            [
                'email' => 'accountant@gmail.com',
                'name' => 'लेखापाल',
                'password' => 'Accountant@123',
                'role' => $roles['accountant'],
            ],
            [
                'email' => 'auditor@gmail.com',
                'name' => 'लेखापरीक्षक',
                'password' => 'Auditor@123',
                'role' => $roles['auditor'],
            ],
        ];

        foreach ($users as $user) {
            $attributes = [
                'name' => $user['name'],
                'role_id' => $user['role']?->id,
            ];

            $existing = User::where('email', $user['email'])->first();

            if ($existing) {
                $existing->update($attributes);

                continue;
            }

            User::create($attributes + [
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
            ]);
        }
    }
}
