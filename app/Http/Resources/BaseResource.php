<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    protected function formatDate($date): ?string
    {
        return $date ? $date->toISOString() : null;
    }

    protected function formatDateTime($dateTime): ?string
    {
        return $dateTime ? $dateTime->toISOString() : null;
    }

    protected function getStatusLabel(string $status): string
    {
        $statusMap = [
            'active' => 'Активный',
            'inactive' => 'Неактивный',
            'pending' => 'Ожидает',
            'closed' => 'Закрыт'
        ];

        return $statusMap[$status] ?? $status;
    }

    protected function getStatusSeverity(string $status): string
    {
        $severityMap = [
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning',
            'closed' => 'info'
        ];

        return $severityMap[$status] ?? 'info';
    }
}
