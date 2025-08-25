<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MessengerService;

class SendTestMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messenger:test {phone} {--message=Привет! Это тестовое сообщение из системы.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test message to WhatsApp number to test messenger functionality';

    protected $messengerService;

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
        $phone = $this->argument('phone');
        $message = $this->option('message');

        $this->info("Sending test message to {$phone}...");
        $this->line("Message: {$message}");
        $this->newLine();

        try {
            // Обрабатываем как входящее сообщение для создания мессенджер чата
            $this->messengerService->handleIncomingMessage($phone, $message);

            $this->info('✓ Test message processed successfully!');
            $this->line('This will create a messenger chat and trigger the menu system.');
            $this->newLine();
            
            $this->info('What happens next:');
            $this->line('1. A messenger chat will be created for this phone number');
            $this->line('2. The system will send a welcome menu to the client');
            $this->line('3. You can view the chat in the messenger section of admin panel');

        } catch (\Exception $e) {
            $this->error('✗ Error occurred: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
