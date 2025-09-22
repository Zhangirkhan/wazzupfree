<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'inn' => $this->inn,
            'kpp' => $this->kpp,
            'ogrn' => $this->ogrn,
            'legal_address' => $this->legal_address,
            'actual_address' => $this->actual_address,
            'passport_series' => $this->passport_series,
            'passport_number' => $this->passport_number,
            'passport_issued_by' => $this->passport_issued_by,
            'passport_issued_date' => $this->passport_issued_date?->format('Y-m-d'),
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'bik' => $this->bik,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'clients_count' => $this->when(isset($this->clients_count), $this->clients_count),
            'clients' => ClientResource::collection($this->whenLoaded('clients')),
        ];
    }
}
