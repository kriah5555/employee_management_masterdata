<?php

namespace App\Services\Rule;

use App\Models\Rule\Rule;
use Exception;
use App\Repositories\RuleRepository;

class RuleService
{
    protected $ruleRepository;

    public function __construct(RuleRepository $ruleRepository)
    {
        $this->ruleRepository = $ruleRepository;
    }

    public function index()
    {
        return $this->ruleRepository->getAllRules()->map(function ($item) {
            $item->rule_type = $item->getRuleTypeName();
            return $item;
        });
    }

    public function show($id)
    {
        $rule = $this->ruleRepository->getRuleById($id);
        $rule->getRuleTypeName();
        return $rule;
    }

    public function edit($id)
    {
        return [
            'details' => $this->ruleRepository->getRuleById($id)
        ];
    }

    public function update(Rule $rule, $values)
    {
        $this->ruleRepository->updateRule($rule->id, $values);
        return $rule;
    }
}
