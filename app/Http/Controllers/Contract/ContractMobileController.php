<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Contract\ContractMobileService;
use App\Services\Contract\ContractService;
use App\Http\Requests\Contract\ContractRequest;
use App\Http\Requests\Contract\EmployeePlanSignContractRequest;
class ContractMobileController extends Controller
{
    public function __construct(
        protected ContractMobileService $contractMobileService,
        protected ContractService $contractService,
    )
    {
        
    }

    public function index($employee_profile_id = '')
    {
        try {
            $user_id = '';
            if (request()->route()->getName() == 'manager-get-employee-contracts') {
                $company_ids = [getCompanyId()];
            } else {
                $user_id     = Auth::guard('web')->user()->id;
                $company_ids = getUserCompanies($user_id);
            }
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->contractMobileService->getEmployeeContractFiles($company_ids, $user_id, '', '', '', $employee_profile_id),
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

    public function employeeSignPlanContract(EmployeePlanSignContractRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Contract signed successfully'),
                    'data'    => $this->contractService->signEmployeePlanContract($request->validated()),
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
