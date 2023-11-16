<?php

namespace App\Http\Controllers\Rule;

use App\Http\Controllers\Controller;
use App\Models\Rule\Rule;
use App\Services\Rule\RuleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Rule\RuleRequest;

class RuleController extends Controller
{
    protected $ruleService;

    public function __construct(RuleService $ruleService)
    {
        $this->ruleService = $ruleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->ruleService->index(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->ruleService->show($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->ruleService->edit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RuleRequest $request, Rule $rule)
    {
        $this->ruleService->update($rule, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Rule value updated successfully'),
            ],
            JsonResponse::HTTP_OK,
        );
    }
}
