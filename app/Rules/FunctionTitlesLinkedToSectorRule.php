<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\FunctionTitle;

class FunctionTitlesLinkedToSectorRule implements Rule
{
    protected $sectorIds;

    public function __construct(array $sectorIds)
    {
        $this->sectorIds = $sectorIds;
    }

    public function passes($attribute, $value)
    {
        $functionTitle = FunctionTitle::find($value);

        if (!$functionTitle || !$this->isFunctionTitleLinkedToSectors($functionTitle)) {
            return false;
        }

        return true;
    }

    protected function isFunctionTitleLinkedToSectors(FunctionTitle $functionTitle)
    {
        return in_array($functionTitle->functionCategory->sector->id, $this->sectorIds);
    }

    public function message()
    {
        return "The selected function title is not linked to the provided sector(s).";
    }
}
