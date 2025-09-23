<?php

namespace App\Services\Webhook;

use App\Contracts\WebhookContactProcessorInterface;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

class WebhookContactProcessor implements WebhookContactProcessorInterface
{
    /**
     * Обработка массива контактов
     */
    public function handleContacts(array $contacts): array
    {
        $results = [];
        
        foreach ($contacts as $contact) {
            $results[] = $this->processContact($contact);
        }
        
        return $results;
    }

    /**
     * Обработка одного контакта
     */
    public function processContact(array $contactData): bool
    {
        try {
            Log::info('Processing webhook contact:', [
                'contact_id' => $contactData['id'] ?? 'unknown',
                'name' => $contactData['name']['formatted_name'] ?? 'unknown'
            ]);

            $phone = $this->cleanPhoneNumber($contactData['id'] ?? '');
            $name = $contactData['name']['formatted_name'] ?? null;
            $avatar = $contactData['avatar'] ?? null;

            if (empty($phone)) {
                Log::warning('Invalid contact data - no phone:', $contactData);
                return false;
            }

            // Находим или создаем клиента
            $client = Client::where('phone', $phone)->first();
            
            if (!$client) {
                $client = Client::create([
                    'name' => $name ?: 'Клиент ' . $phone,
                    'phone' => $phone,
                    'is_active' => true,
                    'avatar' => $avatar
                ]);

                Log::info('Client created from contact:', [
                    'client_id' => $client->id,
                    'phone' => $phone,
                    'name' => $name
                ]);
            } else {
                // Обновляем данные клиента если они изменились
                $updated = false;
                $updates = [];

                if ($name && $client->name !== $name) {
                    $updates['name'] = $name;
                    $updated = true;
                }

                if ($avatar && $client->avatar !== $avatar) {
                    $updates['avatar'] = $avatar;
                    $updated = true;
                }

                if ($updated) {
                    $client->update($updates);
                    Log::info('Client updated from contact:', [
                        'client_id' => $client->id,
                        'phone' => $phone,
                        'updates' => $updates
                    ]);
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error processing webhook contact:', [
                'contact' => $contactData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Очистка номера телефона
     */
    private function cleanPhoneNumber(string $phone): string
    {
        // Убираем все символы кроме цифр
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Если номер начинается с 8, заменяем на 7
        if (str_starts_with($phone, '8')) {
            $phone = '7' . substr($phone, 1);
        }
        
        // Если номер начинается с +7, убираем +
        if (str_starts_with($phone, '+7')) {
            $phone = '7' . substr($phone, 2);
        }
        
        return $phone;
    }
}
