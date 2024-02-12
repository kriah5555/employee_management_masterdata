<?php

namespace App\Http\Resources\Company;

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
            'id'           => $this->id,
            'company_name' => $this->company_name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'vat_number'   => $this->vat_number,
        ];
    }
}
