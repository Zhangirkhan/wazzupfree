<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $organization = \App\Models\Organization::first();

        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'admin',
                'display_name' => 'Администратор',
                'description' => 'Полный доступ к системе',
                'organization_id' => $organization->id,
                'slug' => 'admin'
            ]);
        }

        User::updateOrCreate(
            ['email' => 'admin@erp.ap.kz'],
            [
                'name' => 'Администратор',
                'email' => 'admin@erp.ap.kz',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}
