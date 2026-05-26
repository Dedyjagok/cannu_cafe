<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Owner
        User::updateOrCreate(
            ['email' => 'owner@cannu.id'],
            [
                'name'      => 'Owner Cannu',
                'password'  => Hash::make('password'),
                'role'      => 'owner',
                'is_active' => true,
            ]
        );

        // Kasir
        User::updateOrCreate(
            ['email' => 'kasir@cannu.id'],
            [
                'name'      => 'Kasir Budi',
                'password'  => Hash::make('password'),
                'role'      => 'kasir',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Users seeded: owner@cannu.id / kasir@cannu.id  (password: password)');
    }
}
