<?php

namespace App\Interfaces\SocialSecretary;

use App\Models\SocialSecretary\SocialSecretary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface SocialSecretaryRepositoryInterface
{
    public function getSocialSecretaries(): Collection;

    public function getActiveSocialSecretaries(): Collection;

    public function getSocialSecretaryById(string $socialSecretaryId, array $relations = []): Collection|Builder|SocialSecretary;

    public function deleteSocialSecretary(SocialSecretary $socialSecretary): bool;

    public function createSocialSecretary(array $details): SocialSecretary;

    public function updateSocialSecretary(SocialSecretary $socialSecretary, array $updatedDetails): bool;
}