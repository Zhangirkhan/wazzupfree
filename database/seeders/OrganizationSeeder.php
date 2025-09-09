<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organization::updateOrCreate(
            ['slug' => 'akzhol-pharm'],
            [
                'name' => 'Акжол Фарм',
                'slug' => 'akzhol-pharm',
                'description' => 'Система корпоративного общения',
                'domain' => 'chat.ap.kz',
                'is_active' => true,
            ]
        );
    }
}
