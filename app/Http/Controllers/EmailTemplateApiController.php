<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\JsonResponse;
use App\Services\EmailTemplateService;
use App\Http\Rules\EmailTemplateRequest;

class EmailTemplateApiController extends Controller
{
    protected $emailTemplateService;

    public function __construct(EmailTemplateService $emailTemplateService)
    {
        $this->emailTemplateService = $emailTemplateService;
    }

    public function index()
    {
        try {
            $data = $this->emailTemplateService->getAll();
            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function store(EmailTemplateRequest $request)
    {
        try {
            $email_template = $this->emailTemplateService->create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Email template created successfully',
                'data'    => $email_template,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }


        return response()->json($emailTemplate, 201);
    }

    public function show($id)
    {
        $emailTemplate = $this->emailTemplateService->get($id);

        return response()->json($emailTemplate);
    }

    public function update(EmailTemplateRequest $request, EmailTemplate $emailTemplate)
    {
        try {
            $email_template = $this->emailTemplateService->update($emailTemplate, $request->all());
            $email_template->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Email template updated successfully',
                'data'    => $email_template,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }


    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return response()->json([
            'success' => true,
            'message' => 'Email template deleted successfully'
        ]);
    }
}
