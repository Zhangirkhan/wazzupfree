<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign role to user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $role = $this->argument('role');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found");
            return 1;
        }

        if (!in_array($role, ['admin', 'manager', 'employee'])) {
            $this->error("Invalid role. Available roles: admin, manager, employee");
            return 1;
        }

        $user->update(['role' => $role]);

        $this->info("Role '{$role}' assigned to user {$user->name} ({$email})");

        return 0;
    }
}
