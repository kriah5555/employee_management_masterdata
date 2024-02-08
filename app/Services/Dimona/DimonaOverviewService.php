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
        $planningDimonas = $longTermDimonas = [];
        if ($type == 'plan') {
            $planningDimonas = $this->formatPlanningDimonas(
                $this->getPlanningDimonas($from_date, $to_date)
            );
        } elseif ($type == 'long_term') {
            $longTermDimonas = $this->formatLongTermDimonas(
                $this->getLongTermDimonas($from_date, $to_date)
            );
        } else {
            $planningDimonas = $this->formatPlanningDimonas(
                $this->getPlanningDimonas($from_date, $to_date)
            );
            $longTermDimonas = $this->formatLongTermDimonas(
                $this->getLongTermDimonas($from_date, $to_date)
            );
        }

        return array_merge($planningDimonas, $longTermDimonas);
    }
    public function getPlanningDimonas($from_date, $to_date)
    {
        $query = Dimona::where('type', 'plan');
        $query->with([
            'planningDimona.planningBase.employeeProfile.user.userBasicDetails'
        ]);
        $query->whereHas('planningDimona.planningBase');
        $query->whereHas('planningDimona.planningBase', function ($query) use ($from_date, $to_date) {
            $query->whereBetween('start_date_time', [$from_date, $to_date]);
        });
        return $query->get();
    }

    public function formatPlanningDimonas($planningDimonas)
    {
        $data = [];
        foreach ($planningDimonas as $each) {
            $temp = [];
            $temp['id'] = $each->id;
            $temp['dimona_period_id'] = $each->dimona_period_id;
            $temp['type'] = 'Planning dimona';
            $temp['name'] = $each->planningDimona->planningBase->employeeProfile->full_name;
            $temp['start'] = date('d-m-Y H:i', strtotime($each->planningDimona->planningBase->start_date_time));
            $temp['end'] = date('d-m-Y H:i', strtotime($each->planningDimona->planningBase->end_date_time));
            $temp['employee_type'] = $each->planningDimona->planningBase->employeeType->name;
            $data[] = $temp;
        }
        return $data;
    }

    public function formatLongTermDimonas($longTermDimonas)
    {
        $data = [];
        foreach ($longTermDimonas as $each) {
            $temp = [];
            $temp['id'] = $each->id;
            $temp['dimona_period_id'] = $each->dimona_period_id;
            $temp['type'] = 'Planning dimona';
            $temp['name'] = $each->longtermDimona->employeeContract->employeeProfile->full_name;
            $temp['start'] = date('d-m-Y', strtotime($each->longtermDimona->employeeContract->start_date));
            $temp['end'] = $each->longtermDimona->employeeContract->end_date ? date('d-m-Y', strtotime($each->longtermDimona->employeeContract->end_date)) : '';
            $temp['employee_type'] = $each->longtermDimona->employeeContract->employeeType->name;
            $data[] = $temp;
        }
        return $data;
    }
    public function getLongTermDimonas($from_date, $to_date)
    {
        $query = Dimona::where('type', 'long_term');
        $query->whereBetween('created_at', [$from_date, $to_date]);
        $query->whereHas('longtermDimona.employeeContract.employeeProfile');
        $query->with([
            'longtermDimona.employeeContract.employeeProfile.user.userBasicDetails'
        ]);
        return $query->get();
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
        } elseif ($dimona->type == 'long_term') {
            $response['name'] = $dimona->longTermDimona->employeeContract->employeeProfile->full_name;
        }
        $response['dimona_sent_date'] = date('d-m-Y', strtotime($dimona->created_at));
        foreach ($dimona->dimonaDeclarations as $dimonaDeclaration) {
            $data = [];
            $data['declaration_type'] = $dimonaDeclaration->type;
            if ($dimona->type == 'plan') {
                $data['start'] = date('d-m-Y H:i', strtotime($dimona->planningDimona->planningBase->start_date_time));
                $data['stop'] = date('d-m-Y H:i', strtotime($dimona->planningDimona->planningBase->end_date_time));
                if ($dimona->planningDimona->planningBase->employeeType->dimonaConfig->dimonaType->dimona_type_key == 'student') {
                    $data['hours'] = (int) ceil(timeDifferenceinHours($dimona->planningDimona->planningBase->start_date_time, $dimona->planningDimona->planningBase->end_date_time));
                }
            } elseif ($dimona->type == 'long_term') {
                $data['start'] = date('d-m-Y', strtotime($dimona->longTermDimona->employeeContract->start_date));
                $data['stop'] = $dimona->longTermDimona->employeeContract->end_date ? date('d-m-Y', strtotime($dimona->longTermDimona->employeeContract->end_date)) : '';
                if ($dimona->longTermDimona->employeeContract->employeeType->dimonaConfig->dimonaType->dimona_type_key == 'student') {
                    $data['hours'] = (int) $dimona->longTermDimona->reserved_hours;
                }
            }
            $data['errors'] = [];
            foreach ($dimonaDeclaration->dimonaDeclarationErrors as $dimonaDeclarationError) {
                $data['errors'][] = $dimonaDeclarationError->dimonaErrorCode->error_code . ':' . $dimonaDeclarationError->dimonaErrorCode->description;
            }
            $data['dimona_status'] = $dimonaDeclaration->dimona_declartion_status;
            $response['declarations'][] = $data;
        }
        return $response;
    }
}
