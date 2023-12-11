<?php

namespace App\Http\Middleware;

use App\Services\CompanyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Tenant;

class InitializeTenancy
{

    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $companyId = $request->header('Company-Id');
        // $companyId = 5;
        if ($companyId) {
            $tenant = $this->companyService->getTenantByCompanyId($companyId);
            if ($tenant instanceof Tenant) {
                tenancy()->initialize($tenant);
                config(['database.connections.tenant_template.database' => $tenant->database_name]);
                return $next($request);
            }
        }
        return returnResponse(
            [
                'success' => true,
                'message' => 'Tenant not found',
            ],
            JsonResponse::HTTP_NOT_FOUND,
        );

    }
}
