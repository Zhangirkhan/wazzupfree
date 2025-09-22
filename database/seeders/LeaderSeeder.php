<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LeaderSeeder extends Seeder
{
    public function run(): void
    {
        $organization = \App\Models\Organization::first();

        $leaderRole = Role::where('name', 'leader')->first();

        if (!$leaderRole) {
            $leaderRole = Role::create([
                'name' => 'leader',
                'display_name' => 'Руководитель',
                'description' => 'Руководство отделом и принятие решений',
                'organization_id' => $organization->id,
                'slug' => 'leader'
            ]);
        }

        User::updateOrCreate(
            ['email' => 'leader@erp.ap.kz'],
            [
                'name' => 'Руководитель',
                'email' => 'leader@erp.ap.kz',
                'password' => Hash::make('password'),
                'role' => 'leader',
            ]
        );
    }
}
