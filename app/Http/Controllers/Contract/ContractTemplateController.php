<?php

namespace App\Http\Controllers\Contract;

use App\Models\Contract\ContractTemplate;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractTemplateRequest;
use App\Services\Contract\ContractTemplateService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Contract\ContractTemplateResource;
use App\Services\SocialSecretary\SocialSecretaryService;
use App\Services\Contract\ContractTypeService;

class ContractTemplateController extends Controller
{
    public function __construct(
        protected SocialSecretaryService $social_secretaryService,
        protected ContractTemplateService $contractTemplateService,
        protected ContractTypeService $contractTypeService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => ContractTemplateResource::collection($this->contractTemplateService->getAllContractTemplates()),
                ],
                JsonResponse::HTTP_OK,
            );

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => [
                    'contract_types'     => $this->contractTypeService->getActiveContractTypes(),
                    'social_secretaries' => $this->social_secretaryService->getActiveSocialSecretaries(),
                    'tokens'             => array_merge(
                        config('tokens.EMPLOYEE_TOKENS'),
                        config('tokens.COMPANY_TOKENS'),
                        config('tokens.CONTRACT_TOKENS'),
                        config('tokens.ATTACHMENT_TOKENS'),
                        config('tokens.SIGNATURE_TOKENS'),
                        config('tokens.FLEX_SALARY_TOKENS'),
                        config('tokens.ADDITIONAL_TOKENS'),
                        config('tokens.PLANNING_TOKENS'),
                    ),
                ],
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractTemplateRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Contract template created successfully',
                'data'    => $this->contractTemplateService->create($request->validated()),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => new ContractTemplateResource($this->contractTemplateService->get($id)),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContractTemplateRequest $request, $id)
    {
        $contractTemplate = ContractTemplate::findOrFail($id);
        $this->contractTemplateService->update($contractTemplate, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Contract template updated successfully'),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractTemplate $contractTemplate)
    {
        $contractTemplate->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Contract template deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }

    function convertPDFHtml(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'pdf_file' => 'required|mimes:pdf|max:10240', // Adjust max file size as needed
            ]);

            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => false,
                        'message' => $validator->errors()->first(),
                    ],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $pdfFilePath = $request->file('pdf_file')->getPathname();
            $file_name = storage_path('app/pdf_output.html');

            // $htmlOutput = shell_exec("pdftohtml -i -noframes -stdout '$pdfFilePath'"); # will give html with green background

            // // $htmlOutput = shell_exec("pdftohtml -i -noframes -p -c -nodrm '$pdfFilePath' $file_name"); # will give page

            $htmlOutput = shell_exec("pdftohtml -i -noframes -stdout '$pdfFilePath'");

            // $htmlOutput = html_entity_decode($htmlOutput, ENT_QUOTES, 'UTF-8'); # Decode HTML entities

            // dd($htmlOutput);


            return returnResponse(
                [
                    'success' => true,
                    'message' => 'PDF data received successfully',
                    'data'    => $htmlOutput
                ],
                JsonResponse::HTTP_OK,
            );

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
}
