<?php

namespace App\Services\Contract;

use Illuminate\Support\Facades\DB;
use App\Models\Contract\ContractTemplate;
use Exception;
use App\Models\EmployeeType\EmployeeType;

class ContractTemplateService
{

    public function __construct(
    ) {
    }

    public function create($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                $contract_template = ContractTemplate::create([
                    'contract_type_id' => $values['contract_type_id'],
                ]);
                $contract_template->socialSecretary()->sync($values['social_secretary'] ?? []);
                foreach (config('app.available_locales') as $locale) {
                    $contract_template->setTranslation('body', $locale, $values['body'][$locale]);
                }
                $contract_template->save();
                return $contract_template;
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllContractTemplates($with = [])
    {
        return ContractTemplate::with($with)->get();
    }

    public function get($id)
    {
        return ContractTemplate::findOrFail($id)->with(['contractType', 'socialSecretary'])->first();
    }

    public function update($contractTemplate, $values)
    {
        try {
            $body = $values['body'];
            unset($values['body']);
            $contractTemplate->update($values);
            foreach (config('app.available_locales') as $locale) {
                $contractTemplate->setTranslation('body', $locale, $body[$locale]);
            }
            $contractTemplate->save();
            $contractTemplate->socialSecretary()->sync($values['social_secretary'] ?? []);
            return $contractTemplate;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getTemplateForEmployeeType(EmployeeType $employeeType)
    {
        return ContractTemplate::where('employee_type_id', $employeeType->id)->first();
    }
}
