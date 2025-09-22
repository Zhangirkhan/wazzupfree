<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'inn' => $this->inn,
            'kpp' => $this->kpp,
            'ogrn' => $this->ogrn,
            'legal_address' => $this->legal_address,
            'actual_address' => $this->actual_address,
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
            'clients_count' => $this->when(isset($this->clients_count), $this->clients_count),
            'clients' => ClientResource::collection($this->whenLoaded('clients')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
