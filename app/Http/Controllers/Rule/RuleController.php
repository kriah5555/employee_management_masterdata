<?php

namespace App\Http\Controllers\Rule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rule;
use App\Services\Rule\RuleService;
use Illuminate\Http\JsonResponse;

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
    public function index($category = null)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->ruleService->index($category),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}