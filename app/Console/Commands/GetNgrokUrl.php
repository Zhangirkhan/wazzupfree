<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetNgrokUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ngrok:url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get current ngrok URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Getting current ngrok URL...');
        
        try {
            $response = Http::timeout(5)->get('http://localhost:4040/api/tunnels');
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['tunnels']) && !empty($data['tunnels'])) {
                    $httpsUrl = null;
                    
                    foreach ($data['tunnels'] as $tunnel) {
                        if ($tunnel['proto'] === 'https') {
                            $httpsUrl = $tunnel['public_url'];
                            break;
                        }
                    }
                    
                    if ($httpsUrl) {
                        $this->info('✅ Found ngrok URL: ' . $httpsUrl);
                        $this->newLine();
                        
                        $webhookUrl = $httpsUrl . '/api/webhook';
                        $this->info('🔗 Webhook URL would be: ' . $webhookUrl);
                        $this->newLine();
                        
                        $this->info('💡 To setup webhook, run:');
                        $this->line('   php artisan webhook:setup ' . $webhookUrl);
                        
                        return 0;
                    } else {
                        $this->error('❌ No HTTPS tunnel found');
                        return 1;
                    }
                } else {
                    $this->error('❌ No tunnels found');
                    return 1;
                }
            } else {
                $this->error('❌ Could not connect to ngrok API');
                $this->info('💡 Make sure ngrok is running: ngrok http 8000');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error getting ngrok URL: ' . $e->getMessage());
            $this->info('💡 Make sure ngrok is running: ngrok http 8000');
            return 1;
        }
    }
}
