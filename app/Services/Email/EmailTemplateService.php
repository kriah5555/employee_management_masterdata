<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\DB;
use App\Models\Email\EmailTemplate;
use App\Services\BaseService;

class EmailTemplateService
{
    public function __construct()
    {
    }

    public function create($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                $emailTemplate = EmailTemplate::create([
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
        return [
            'email_template_type' => $this->transformOptions(config('constants.EMAIL_TEMPLATES')),
            'tokens'              => array_merge(
                config('tokens.EMPLOYEE_TOKENS'),
                config('tokens.COMPANY_TOKENS'),
                config('tokens.CONTRACT_TOKENS'),
                config('tokens.ATTACHMENT_TOKENS'),
                config('tokens.SIGNATURE_TOKENS'),
                config('tokens.FLEX_SALARY_TOKENS'),
                config('tokens.ADDITIONAL_TOKENS'),
                config('tokens.PLANNING_TOKENS'),
            ),
        ];
    }

    public function getEmailTemplateDetailsById($email_template_id)
    {
        return $this->get($email_template_id);
    }

    public function getEmailTemplateDetailsByType($email_template_type)
    {
        return EmailTemplate::where(['template_type' => $email_template_type])->get()->first();
    }

    public function getOptionsToEdit($email_template_id)
    {
        $email_template = self::getEmailTemplateDetailsById($email_template_id);
        $options = $this->getOptionsToCreate();
        $email_template['template_type'] = [
            'value' => $email_template['template_type'],
            'label' => config('constants.EMAIL_TEMPLATES')[$email_template['template_type']]
        ];
        $options['details'] = $email_template;
        return $options;
    }

    private function transformOptions($options)
    {
        return array_map(function ($key, $value) {
            return ['value' => $key, 'label' => $value];
        }, array_keys($options), $options);
    }
}
