<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {email} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user with full permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password') ?? 'password123';

        $this->info('🔧 Creating admin user...');
        $this->line('Email: ' . $email);
        $this->line('Password: ' . $password);
        $this->newLine();

        try {
            // Проверяем, существует ли пользователь
            $existingUser = User::where('email', $email)->first();
            
            if ($existingUser) {
                $this->warn('⚠️  User already exists!');
                
                if ($this->confirm('Update existing user to admin role?')) {
                    $existingUser->update([
                        'role' => 'admin',
                        'is_admin' => true,
                        'password' => Hash::make($password)
                    ]);
                    
                    $this->info('✅ User updated to admin role!');
                    $this->displayUserInfo($existingUser);
                    return 0;
                } else {
                    $this->info('Operation cancelled.');
                    return 0;
                }
            }

            // Создаем или находим организацию
            $organization = Organization::firstOrCreate([
                'name' => 'Test Company'
            ], [
                'description' => 'Test organization for admin user'
            ]);

            // Создаем админа
            $user = User::create([
                'name' => 'Admin User',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_admin' => true,
                'organization_id' => $organization->id,
                'email_verified_at' => now()
            ]);

            $this->info('✅ Admin user created successfully!');
            $this->displayUserInfo($user);
            
            $this->newLine();
            $this->info('🔗 Login URL: http://127.0.0.1:8001/admin/dashboard');
            $this->info('📧 Email: ' . $email);
            $this->info('🔑 Password: ' . $password);

        } catch (\Exception $e) {
            $this->error('❌ Error creating admin user: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Отображает информацию о пользователе
     */
    protected function displayUserInfo(User $user)
    {
        $this->newLine();
        $this->info('📋 User Information:');
        $this->line('  ID: ' . $user->id);
        $this->line('  Name: ' . $user->name);
        $this->line('  Email: ' . $user->email);
        $this->line('  Role: ' . $user->role);
        $this->line('  Is Admin: ' . ($user->is_admin ? 'Yes' : 'No'));
        $this->line('  Organization: ' . ($user->organization->name ?? 'None'));
        $this->line('  Created: ' . $user->created_at->format('Y-m-d H:i:s'));
    }
}
