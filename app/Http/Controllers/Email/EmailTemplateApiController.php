<?php

namespace App\Http\Controllers\Email;

use App\Models\Email\EmailTemplate;
use Illuminate\Http\JsonResponse;
use App\Services\Email\EmailTemplateService;
use App\Http\Rules\Email\EmailTemplateRequest;
use App\Http\Controllers\Controller;

class EmailTemplateApiController extends Controller
{
    protected $emailTemplateService;

    public function __construct(EmailTemplateService $emailTemplateService)
    {
        $this->emailTemplateService = $emailTemplateService;
    }

    public function index()
    {

        return returnResponse(
            [
                'success' => true,
                'data'    => $this->emailTemplateService->getAll()
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function store(EmailTemplateRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => t('Email template created successfully'),
                'data'    => $this->emailTemplateService->create($request->all())
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->emailTemplateService->get($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function update(EmailTemplateRequest $request, EmailTemplate $emailTemplate)
    {
        $email_template = $this->emailTemplateService->update($emailTemplate, $request->all());
        $email_template->refresh();
        return returnResponse(
            [
                'success' => true,
                'message' => t('Email template updated successfully'),
                'data'    => $email_template,
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => t('Email template deleted successfully')
            ],
            JsonResponse::HTTP_OK,
        );
    }
}
