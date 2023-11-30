<?php

namespace App\Services\Email;

use App\Mail\SendMail;
use App\Services\Email\EmailTemplateService;
use Illuminate\Support\Facades\Mail;

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

    public function triggerMail($mail_id, $subject, $htmlContent)
    {
        Mail::to($mail_id)->send(new SendMail($subject, $htmlContent));
    }
}
