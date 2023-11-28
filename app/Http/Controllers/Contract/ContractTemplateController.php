<?php

namespace App\Http\Controllers\Contract;

use App\Models\Contract\ContractTemplate;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractTemplateRequest;
use App\Services\Contract\ContractTemplateService;
use Illuminate\Http\Request;

class ContractTemplateController extends Controller
{
    public function __construct(protected ContractTemplateService $contractTemplateService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->contractTemplateService->index(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->contractTemplateService->getOptionsToCreate(),
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
                'data'    => $this->contractTemplateService->get($id)
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
            $pdfFilePath = $request->file('pdf_file')->getPathname();
            $file_name = storage_path('app/pdf_output.html');
            // $htmlOutput = shell_exec("pdftohtml -i -noframes -stdout '$pdfFilePath'");
            $htmlOutput = shell_exec("pdftohtml -i -noframes -p -c -nodrm '$pdfFilePath' $file_name");


            return returnResponse(
                [
                    'success' => true,
                    'message' => 'PDF data received successfully',
                    'data'    => $htmlOutput
                ],
                JsonResponse::HTTP_OK,
            );

        } catch (\Exception $e) {
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
