<?php

namespace App\Http\Controllers\Dimona;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dimona\DimonaOverviewService;

class DimonaOverviewController extends Controller
{
    public function __construct(protected DimonaOverviewService $dimonaOverviewService)
    {
    }

    public function getDimonaDetails(Request $request)
    {

        $response = [];

        $data = $request->all();
        try {
            $from_date = date('Y-m-d', strtotime($data['from_date'])) ?? date('Y-m-d');
            $to_date = date('Y-m-d', strtotime($data['to_date'])) ?? date('Y-m-d');
            $type = $data['type'] ?? '';

            $data = $this->dimonaOverviewService->getDimonaOverviewDetails($from_date, $to_date, $type);
            $response = [
                'success' => true,
                'data'   => $data,
            ];

        } catch (\Exception $e) {
            $response = [
                'success'  => false,
                'file'    => $e->getFile(),
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ];
        }

        return $response;
    }
}
