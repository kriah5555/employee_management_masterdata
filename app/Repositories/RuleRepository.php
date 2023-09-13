<?php

namespace App\Repositories;

use App\Interfaces\RuleRepositoryInterface;
use App\Models\Rule\Rule;

class RuleRepository implements RuleRepositoryInterface
{
    public function getAllRules()
    {
        return Rule::all();
    }

    public function getRuleById(string $ruleId): Rule
    {
        return Rule::findOrFail($ruleId);
    }

    public function deleteRule(string $ruleId)
    {
        Rule::destroy($ruleId);
    }

    public function createRule(array $ruleDetails): Rule
    {
        return Rule::create($ruleDetails);
    }

    public function updateRule(string $ruleId, array $newDetails)
    {
        return Rule::whereId($ruleId)->update($newDetails);
    }
}
