<?php

namespace App\Services\Email;

use Config;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\Email\EmailTemplateService;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Jobs\SendEmailJob;

class MailService
{
    protected $redirect_mail;

    public function __construct(
        protected EmailTemplateService $email_template_service,
        protected EmployeeProfileRepository $employeeProfileRepository,
        protected CompanyRepository $companyRepository,
    ) {
        $this->redirect_mail = 'indii.2.0.test@gmail.com';
    }

    public function sendEmployeeCreationMail($employee_profile_id, $new_employee = true, $language = 'en', $password = '')
    {
        $email_template = $this->email_template_service->getEmailTemplateDetailsByType($new_employee ? 'new_employee_creation_mail' : 'existing_employee_creation_mail');
        if ($email_template) {
            $employeeData = $this->getEmployeeTokensData($employee_profile_id, $password);

            $subject = $email_template->getTranslation('subject', $language);
            $body = $email_template->getTranslation('body', $language);


            $body = replaceTokens($body, $employeeData);
            $subject = replaceTokens($subject, $employeeData);

            self::triggerMail($this->redirect_mail != '' ? $this->redirect_mail : $employeeData['employee_email'], $subject, $body);
        }
    }

    public function sendEmployeeAccountUpdateMail($values, $language = 'en')
    {
        $email_template = $this->email_template_service->getEmailTemplateDetailsByType('employee_account_update_mail');
        if ($email_template) {
            $subject = $email_template->getTranslation('subject', $language);
            $body = $email_template->getTranslation('body', $language);

            // Get employee data
            $employeeData = $this->getEmployeeTokensData($values);

            // Replace employee tokens in the body content using config tokens
            $body = replaceTokens($body, $employeeData);
            $subject = replaceTokens($subject, $employeeData);

            $this->triggerMail($this->redirect_mail != '' ? $this->redirect_mail : "", $subject, $body);
        }
    }

    private function getEmployeeTokensData($employee_profile_id, $password = '')
    {
        $employee = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);

        return [
            "{employee_username}"      => $employee->user->username,
            "{employee_password}"      => $password,
            "{employee_first_name}"    => $employee->user->userBasicDetails->first_name,
            "{employee_last_name}"     => $employee->user->userBasicDetails->last_name,
            "{employee_date_of_birth}" => $employee->user->userBasicDetails->date_of_birth ? date('d-m-Y', strtotime($employee->user->userBasicDetails->date_of_birth)) : null,
            "{employee_nationality}"   => $employee->user->userBasicDetails->nationality,
            "{employee_ssn}"           => $employee->user->social_security_number,
            "{employee_gender}"        => $employee->user->userBasicDetails->gender->name,
            "{employee_email}"         => $employee->user->userContactDetails ? $employee->user->userContactDetails->email : null,
            "{employee_phone}"         => $employee->user->userContactDetails ? $employee->user->userContactDetails->phone_number : null,
            "{employee_bank}"          => $employee->user->userBankAccount ? $employee->user->userBankAccount->account_number : null,
            "{employee_address}"       => ($employee->user->userAddress ? $employee->user->userAddress->street_house_no : null) . ' ' .
                ($employee->user->userAddress ? $employee->user->userAddress->postal_code : null) . ' ' .
                ($employee->user->userAddress ? $employee->user->userAddress->city : null) . ' ' .
                ($employee->user->userAddress ? $employee->user->userAddress->country : null), # Address:Street + number Postal code City Country
        ];
    }

    public function sendCompanyCreationMail($company_id, $language = 'en')
    {
        $email_template = $this->email_template_service->getEmailTemplateDetailsByType('company_creation_mail');

        if ($email_template) {
            $subject = $email_template->getTranslation('subject', $language);
            $body = $email_template->getTranslation('body', $language);

            // Get employee data
            $companyData = $this->getCompanyTokensData($company_id);

            // Replace employee tokens in the body content using config tokens
            $body = replaceTokens($body, $companyData);
            $subject = replaceTokens($subject, $companyData);

            $this->triggerMail($this->redirect_mail != '' ? $this->redirect_mail : "", $subject, $body);
        }
    }

    public function sendEmployeeInvitationMail($data, $language = 'en')
    {
        $email_template = $this->email_template_service->getEmailTemplateDetailsByType('employee_invitation_mail');

        if ($email_template) {
            $subject = $email_template->getTranslation('subject', $language);
            $body = $email_template->getTranslation('body', $language);

            // Replace employee tokens in the body content using config tokens
            $body = replaceTokens($body, $data);
            $subject = replaceTokens($subject, $data);

            $this->triggerMail($this->redirect_mail != '' ? $this->redirect_mail : "", $subject, $body);
        }
    }

    private function getCompanyTokensData($company_id)
    {
        $company = $this->companyRepository->getCompanyById($company_id);

        return [
            '{company_name}'               => $company->company_name,
            '{company_vat}'                => $company->vat_number,
            '{company_responsible_person}' => '',
            '{company_pc_number}'          => $company->company_name,
            '{company_city}'               => $company->address->city,
            '{company_address}'            => ($company->address ? $company->address->street_house_no : null) . ' ' .
                ($company->address ? $company->address->postal_code : null) . ' ' .
                ($company->address ? $company->address->city : null) . ' ' .
                ($company->address ? $company->address->country : null), # Address:Street + number Postal code City Country,
        ];
    }

    public function triggerMail($mail_id, $subject, $htmlContent)
    {
        // try {
        //     Mail::to($mail_id)->send(new SendMail($subject, $htmlContent));
        //     Log::info('Email sent successfully.');
        // } catch (\Exception $e) {
        //     Log::error('Error sending email: ' . $e->getMessage());
        // }
        $data = [
            'subject'        => $subject,
            'body'           => $htmlContent,
            'recipien_email' => $mail_id,
            'recipient_name' => $mail_id,
        ];
        dispatch(new SendEmailJob($data));
    }
}
