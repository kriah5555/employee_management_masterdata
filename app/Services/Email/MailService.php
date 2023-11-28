<?php

namespace App\Services\Email;

use App\Mail\SendMail;
use App\Services\Email\EmailTemplateService;

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
                self::triggerMail(['sunilgangadhar.infanion@gmail.com'], $email_template->subject[$language], $email_template->body[$language]);
                new SendMail();
            }
        }
    }

    public function triggerMail($mail_ids, $subject, $htmlContent)
    {
        Mail::to($mail_ids)->send(new SendMail($subject, $htmlContent)); # mail_ids field can be single or array of ids
    }
}
