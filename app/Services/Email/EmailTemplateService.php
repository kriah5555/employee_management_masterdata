<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\DB;
use App\Models\Email\EmailTemplate;
use App\Services\BaseService;
class EmailTemplateService extends BaseService
{
    public function __construct(protected EmailTemplate $emailTemapet)
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
            DB::commit();
            return $emailTemplate;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreate()
    {
        $options = config('constants.EMAIL_TEMPLATES');

        return ['email_template_type' => array_map(function ($key, $value) {
            return ['value' => $key, 'label' => $value];
        }, array_keys($options), $options)];
    }

    public function getOptionsToEdit($email_template_id)
    {
        $email_template     = $this->get($email_template_id);
        $options            = $this->getOptionsToCreate();
        $email_template['template_type'] = [
            'value' => $email_template['template_type'],
            'label' => config('constants.EMAIL_TEMPLATES')[$email_template['template_type']]
        ];
        $options['details'] = $email_template;
        return $options;
    }
}
