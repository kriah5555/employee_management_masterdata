<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\EmailTemplate;

class EmailTemplateService extends BaseService
{
    protected $sectorService;

    public function __construct(EmailTemplate $emailTemapet)
    {
        parent::__construct($emailTemapet);
    }

    public function create($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                $emailTemplate = $this->model::create([
                    'template_type' => $values['template_type'],
                ]);
    
                foreach (config('app.available_locales') as $locale) {
                    $emailTemplate->setTranslation('body', $locale, $values['body'][$locale]);
                    $emailTemplate->setTranslation('subject', $locale, $values['subject'][$locale]);
                }
    
                $emailTemplate->save();
                return $emailTemplate;
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($emailTemplate, $values)
    {
        try {
            DB::beginTransaction();
                $emailTemplate->update([
                    'status' => $values['status'],
                ]);
        
                foreach (config('app.available_locales') as $locale) {
                    $emailTemplate->setTranslation('body', $locale, $values['body'][$locale]);
                    $emailTemplate->setTranslation('subject', $locale, $values['subject'][$locale]);
                }
        
                $emailTemplate->save();
                return $emailTemplate;
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
