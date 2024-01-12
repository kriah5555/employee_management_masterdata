<?php

namespace App\Services\Dimona;

use App\Models\DimonaRequest\{
    DimonaBase,
    EmployeeContractLongDimonas,
    PlanningDimona,
};


class DimonaOverviewService
{
    public function __construct(protected DimonaBase $dimonaBase) {}

    public function getRelations($type)
    {
        $array =  [
            'dimonaDetails.dimonaError',
            'dimonaDetails.dimonaResponse',
        ];
        if ($type == '') {
            $array[] = 'longtermDimona';
            $array[] = 'planningDimona';
            $array[] = 'planningDimona.planningBase.location';
            $array[] = 'planningDimona.planningBase.employeeProfile';

        } elseif ($type == 'plan') {
            $array[] = 'planningDimona';
            $array[] = 'planningDimona.planningBase.location';
            $array[] = 'planningDimona.planningBase.employeeProfile';
        } elseif ($type == 'longterm') {
            $array[] = 'longtermDimona';
        }

        return $array;
    }

    public function filterData(&$query, $filters)
    {
        $query->where('created_at', '>=', $filters['from_date']);
        $query->where('created_at', '<=', $filters['to_date']);
    }

    public function getDimonaBase($from_date, $to_date, $type = '')
    {
        $query = $this->dimonaBase->with($this->getRelations($type));
        $this->filterData($query, ['from_date' => $from_date, 'to_date' => $to_date]);
        return $query->get()->toArray();
    }

    public function formatData($response)
    {
        $data = [];
        foreach($response as $each) {
            // dd($each);
            $temp = [];
            $temp['unique'] = $each['unique_id'];

            //Plan details.
            array_map(function($plan) use(&$temp) {
                $plan_base = $plan['planning_base'];
                $temp['plan_id'] = $plan_base['id'];
                $temp['location'] = $plan_base['location_id'];
                $temp['location'] = $plan_base['location']['location_name'];
                $temp['employee_name'] = $plan_base['employee_profile']['full_name'];
                $temp['start_date_time'] = $plan_base['start_date_time'];
                $temp['end_date_time'] = $plan_base['end_date_time'];
            }, $each['planning_dimona']);

            //Dimona details.
            array_map(function($array) use(&$temp){
                if (count($array['dimona_error']) > 0) {
                    foreach ($array['dimona_error'] as $error) {
                        $temp[$error['type']][] = $error['error_code'];
                    }
                } else {
                    $temp['warn'] = '';
                    $temp['error'] = '';
                }
                if (count($array['dimona_response']) > 0) {
                    foreach ($array['dimona_response'] as $resp) {
                        $temp['result'] = $resp['result'];
                    }
                }


            }, $each['dimona_details']);

            $temp['employee_rsz'] = $each['employee_rsz'];

            $data[] = $temp;
        }

        return $data;
    }

    public function getDimonaOverviewDetails($from_date, $to_date, $type)
    {
        $response = [];
        $response = $this->getDimonaBase($from_date, $to_date, $type);
        // dd($response);
        return $this->formatData($response);
    }
}
