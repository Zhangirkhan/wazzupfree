<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        $organization = \App\Models\Organization::first();

        $managerRole = Role::where('name', 'manager')->first();

        if (!$managerRole) {
            $managerRole = Role::create([
                'name' => 'manager',
                'display_name' => 'Менеджер',
                'description' => 'Управление операциями и клиентами',
                'organization_id' => $organization->id,
                'slug' => 'manager'
            ]);
        }

        User::updateOrCreate(
            ['email' => 'manager@chat.ap.kz'],
            [
                'name' => 'Менеджер',
                'email' => 'manager@chat.ap.kz',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]
        );
    }
}
