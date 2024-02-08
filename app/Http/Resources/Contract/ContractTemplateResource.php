<?php

namespace App\Http\Resources\Contract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Contract\ContractTypeResource;
use App\Http\Resources\SocialSecretary\SocialSecretaryResource;

class ContractTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'body'             => $this->getTranslations('body'),
            'contract_type_id' => $this->contract_type_id,
            'status'           => $this->status,
            'contract_type'    => new ContractTypeResource($this->contractType),
            'social_secretary' => SocialSecretaryResource::collection($this->socialSecretary)
        ];
    }
}
