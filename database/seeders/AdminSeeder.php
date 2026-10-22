<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@prismadana.test']);
        $admin->forceFill([
            'name' => 'Admin Prisma Dana Gorontalo',
            'password' => Hash::make('123456'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ])->save();
    }
}
