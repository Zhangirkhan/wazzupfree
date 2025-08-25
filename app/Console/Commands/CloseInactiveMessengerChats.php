<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MessengerService;

class CloseInactiveMessengerChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messenger:close-inactive {--dry-run : Show what would be closed without actually closing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close inactive messenger chats that have been inactive for 7 days';

    protected $messengerService;

    /**
     * Create a new command instance.
     */
    public function __construct(MessengerService $messengerService)
    {
        parent::__construct();
        $this->messengerService = $messengerService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for inactive messenger chats...');

        if ($this->option('dry-run')) {
            $this->info('DRY RUN MODE - No chats will be actually closed');
        }

        $closedCount = $this->messengerService->closeInactiveChats();

        if ($this->option('dry-run')) {
            $this->info("Would close {$closedCount} inactive chats");
        } else {
            $this->info("Successfully closed {$closedCount} inactive chats");
        }

        return 0;
    }
}
