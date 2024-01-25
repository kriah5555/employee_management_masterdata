<?php

namespace App\Http\Resources\Dimona;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DimonaErrorCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'error_code'  => $this->error_code,
            'description' => $this->description,
        ];
    }
}
