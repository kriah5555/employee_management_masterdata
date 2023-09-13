<?php

namespace App\Interfaces;

interface RuleRepositoryInterface
{
    public function getAllRules();

    public function getRuleById(string $ruleId);

    public function deleteRule(string $ruleId);

    public function createRule(array $ruleDetails);

    public function updateRule(string $ruleId, array $newDetails);
}
