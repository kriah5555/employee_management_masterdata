<?php

namespace App\Services\Email;

use App\Mail\SendMail;
use App\Services\Email\EmailTemplateService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Config;

class MailService
{
    public function __construct(protected EmailTemplateService $email_template_service)
    {

    }

    public function sendEmployeeCreationMail($employee_id, $new_employee = true, $language = 'en')
    {
        if ($new_employee) {
            $email_template = $this->email_template_service->getEmailTemplateDetailsByType('new_employee_creation_mail');
            if ($email_template) {
                $subject = $email_template->getTranslation('subject', $language);
                $body    = $email_template->getTranslation('body', $language);

                self::triggerMail('sunilgangadhar.infanion@gmail.com', $subject, $body);
            }
        }
    }

    public function sendEmployeeAccountUpdateMail($values, $language = 'en')
    {
        $email_template = $this->email_template_service->getEmailTemplateDetailsByType('employee_account_update_mail');
        if ($email_template) {
            $subject = $email_template->getTranslation('subject', $language);
            $body    = $email_template->getTranslation('body', $language);

            // Get employee data
            $employeeData = $this->getEmployeeTokensData($values);

            // Replace employee tokens in the body content using config tokens
            $configTokens = array_keys(config('tokens.EMPLOYEE_TOKENS'));
            $body = replaceTokens($body, $employeeData);

            $this->triggerMail('jyotibarawoot@gmail.com', $subject, $body);
        }
    }


    private function getEmployeeTokensData($employee_id)
    {
        return [
            '{employee_first_name}'    => $values['first_name'],
            '{employee_last_name}'     => $values['last_name'],
            '{employee_date_of_birth}' => 'DOB',
            '{employee_phone}'         => 'Phone',
            '{employee_ssn}'           => 'SSN',
            '{employee_gender}'        => 'Gender',
            '{employee_email}'         => 'Email',
            '{employee_address}'       => 'Address:Street + number Postal code City Country',
            '{employee_nationality}'   => 'Nationality',
            '{employee_bank}'          => $values['account_number'],
        ];
    }


    public function triggerMail($mail_id, $subject, $htmlContent)
    {
        try {
            Mail::to($mail_id)->send(new SendMail($subject, $htmlContent));
            Log::info('Email sent successfully.');
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            // You might want to throw the exception to propagate it to the calling code
            throw $e;
        }
    }
}
