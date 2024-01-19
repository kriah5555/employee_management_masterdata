<?php

namespace App\Services\Employee;

use App\Services\Email\MailService;
use App\Models\Company\Employee\EmployeeInvitation;
use Illuminate\Support\Str;
use App\Services\CompanyService;

class EmployeeInvitationService
{

    public function __construct(
        protected MailService $mailService,
    ) {
    }

    public function sendInvitation($values)
    {
        $companyId = getCompanyId();
        $token = $this->createInvitationToken();
        $employeeInvitation = EmployeeInvitation::create([
            'token'     => $token,
            'expire_at' => $this->getExpiryDate(),
            // 'data'      => $values,
        ]);
        $employeeInvitation->data = $values;
        $employeeInvitation->save();
        $token = encodeData([
            'token'      => $token,
            'company_id' => $companyId
        ]);
        $url = env('FRONTEND_URL', 'http://api-gateway.indii2.local');
        $url = $url . "/employee-invitations/" . $token;
        $company = app(CompanyService::class)->getCompanyById($companyId);
        $values['url'] = $url;
        $values['company_name'] = $company->company_name;
        $this->mailService->sendEmployeeInvitationMail(
            $this->formatEmployeeInvitationMailTokens($values)
        );
    }

    public function formatEmployeeInvitationMailTokens($data)
    {
        return [
            '{employee_first_name}' => $data['first_name'],
            '{employee_last_name}'  => $data['last_name'],
            '{employee_email}'      => $data['email'],
            '{company_name}'        => $data['company_name'],
            '{link1}'               => $data['url'],
        ];
    }

    public function createInvitationToken()
    {
        return Str::ulid()->toBase58();
    }

    public function getExpiryDate()
    {
        return date('Y-m-d H:i', strtotime(date('Y-m-d H:i') . '+2 weeks'));
    }
    public function employeeRegistration($values)
    {
        $employeeInvitation = $values['employee_invitation'];
        unset($values['employee_invitation']);
        unset($values['token']);
        $employeeInvitation->invitation_status = 2;
        $employeeInvitation->data = $values;
        $employeeInvitation->save();
    }
}
