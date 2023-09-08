<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
use App\Models\CostCenter;

class CostCenterService extends BaseService
{
    protected $sectorService;

    public function __construct(CostCenter $costCenter)
    {
        parent::__construct($costCenter);
    }

    public function create($values)
    {
        try {
            DB::beginTransaction();
                $costCenter = $this->creaet($values);
                // $costCenter
            DB::commit();
            return $location;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                $emailTemplate = $this->model::create([
                    'template_type' => $values['template_type'],
                ]);
    
                foreach (config('app.available_locales') as $locale) {
                    $emailTemplate->setTranslation('body', $locale, $values['body'][$locale]);
                    $emailTemplate->setTranslation('subject', $locale, $values['subject'][$locale]);
                }
    
                $emailTemplate->save();
                return $emailTemplate;
            });
            
            DB::beginTransaction();
            
            DB::commit();
            return $location;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}