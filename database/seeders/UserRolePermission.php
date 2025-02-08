<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserRolePermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(['name' => 'super_admin']);
        Role::updateOrCreate(['name' => 'admin']);
        Role::updateOrCreate(['name' => 'user']);

        $super_admin = User::updateOrCreate([
            'name'              => 'Super Admin',
            'email'             => 'super_admin@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('Superadmin@123'),
            'remember_token'    => str::random(10),
        ]);
        $super_admin->syncRoles(['super_admin']);

        $admin = User::updateOrCreate([
            'name'              => 'Admin',
            'email'             => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('Admin@123'),
            'remember_token'    => str::random(10),
        ]);
        $admin->syncRoles(['admin']);

        $user = User::updateOrCreate([
            'name'              => 'User',
            'email'             => 'user@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('User@123'),
            'remember_token'    => str::random(10),
        ]);
        $user->syncRoles(['user']);
    }
}
