<?php

namespace App\Repositories\SocialSecretary;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\SocialSecretary\SocialSecretaryRepositoryInterface;
use App\Models\SocialSecretary\SocialSecretary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SocialSecretaryRepository implements SocialSecretaryRepositoryInterface
{
    public function getSocialSecretaries(): Collection
    {
        return SocialSecretary::all();
    }
    public function getActiveSocialSecretaries(): Collection
    {
        return SocialSecretary::allActive();
    }

    public function getSocialSecretaryById(string $socialSecretaryId, array $relations = []): Collection|Builder|SocialSecretary
    {
        return SocialSecretary::with($relations)->findOrFail($socialSecretaryId);
    }

    public function deleteSocialSecretary(SocialSecretary $socialSecretary): bool
    {
        if ($socialSecretary->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete social secretary');
        }
    }

    public function createSocialSecretary(array $details): SocialSecretary
    {
        return SocialSecretary::create($details);
    }

    public function updateSocialSecretary(SocialSecretary $socialSecretary, array $updatedDetails): bool
    {
        if ($socialSecretary->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update social secretary');
        }
    }
}
