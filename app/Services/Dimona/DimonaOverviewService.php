<?php

namespace App\Services\Dimona;

use App\Models\Dimona\{
    Dimona,
};


class DimonaOverviewService
{
    public function __construct(protected Dimona $dimonaBase)
    {
    }

    public function getRelations($type)
    {
        $array = [
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
        $fromData = date('Y-m-d 00:00', strtotime($filters['from_date']));
        $toData = date('Y-m-d 00:00', strtotime($filters['to_date']));
        $query->where('created_at', '>=', $fromData);
        $query->where('created_at', '<=', $toData);
    }

    public function getDimonaBase($from_date, $to_date, $type = '')
    {
        $query = Dimona::with($this->getRelations($type));
        $this->filterData($query, ['from_date' => $from_date, 'to_date' => $to_date]);
        return $query->get()->toArray();
    }

    public function formatData($response)
    {
        $data = [];
        foreach ($response as $each) {
            $temp = [];
            $temp['id'] = $each->id;
            $temp['dimona_period_id'] = $each->dimona_period_id;
            if ($each->type == 'long_term') {
                // $start =
            } elseif ($each->type == 'plan') {
                $temp['name'] = $each->planningDimona->planningBase->employeeProfile->full_name;
                $temp['start'] = date('d-m-Y H:i', strtotime($each->planningDimona->planningBase->start_date_time));
                $temp['end'] = date('d-m-Y H:i', strtotime($each->planningDimona->planningBase->end_date_time));
                $temp['employee_type'] = $each->planningDimona->planningBase->employeeType->name;
            }
            $data[] = $temp;
        }

        return $data;
    }

    public function getDimonaOverview($from_date, $to_date, $type)
    {
        $from_date = date('Y-m-d 00:00:00', strtotime($from_date)) ?? date('Y-m-d');
        $to_date = date('Y-m-d 23:59:59', strtotime($to_date)) ?? date('Y-m-d');
        $query = Dimona::whereBetween('created_at', [$from_date, $to_date]);
        if ($type == 'plan') {
            $query->where('type', 'plan');
            $query->with([
                'planningDimona.planningBase.employeeProfile.user.userBasicDetails'
            ]);
        } elseif ($type == 'long_term') {
            $query->where('type', 'long_term');
            $query->with([
                'longtermDimona.employeeContract.employeeProfile.user.userBasicDetails'
            ]);
        } else {
            $query->with([
                'planningDimona.planningBase.employeeProfile.user.userBasicDetails',
                'longtermDimona.employeeContract.employeeProfile.user.userBasicDetails'
            ]);
        }
        $response = $query->get();
        return $this->formatData($response);
    }

    public function getDimonaDetails($dimonaId)
    {
        $response = [];
        $dimona = Dimona::with([
            'dimonaDeclarations',
            'planningDimona.planningBase.employeeProfile.user.userBasicDetails',
            'longtermDimona.employeeContract.employeeProfile.user.userBasicDetails'

        ])->findOrFail($dimonaId);
        if ($dimona->type == 'plan') {
            $response['name'] = $dimona->planningDimona->planningBase->employeeProfile->full_name;
        }
        $response['dimona_sent_date'] = date('d-m-Y', strtotime($dimona->created_at));
        foreach ($dimona->dimonaDeclarations as $dimonaDeclaration) {
            $data = [];
            $data['dimona_type'] = $dimonaDeclaration->type;
            $data['dimona_status'] = $dimonaDeclaration->dimona_declartion_status;
            $response['declarations'][] = $data;
        }
        return $response;
    }
}
