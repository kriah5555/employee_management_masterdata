<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Contract\ContractMobileService;
use App\Http\Requests\Contract\ContractRequest;
use App\Http\Requests\Contract\EmployeePlanSignContractRequest;

class ContractMobileController extends Controller
{
    public function __construct(
        protected ContractMobileService $contractService
    )
    {
        
    }

    public function index()
    {
        try {
            // $user_id     = 4;
            $user_id     = Auth::guard('web')->user()->id;
            $company_ids = getUserCompanies($user_id);

            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->contractService->getEmployeeContractFiles($company_ids, $user_id, '', '', ''),
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
