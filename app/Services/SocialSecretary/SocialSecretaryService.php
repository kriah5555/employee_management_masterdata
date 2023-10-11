<?php

namespace App\Services\SocialSecretary;

use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
use App\Models\SocialSecretary\SocialSecretary;

class SocialSecretaryService extends BaseService
{
    protected $socialSecretary;

    public function __construct(SocialSecretary $socialSecretary)
    {
        parent::__construct($socialSecretary);
    }  

    public function getSocialSecretaryOptions()
    {
        try {
            return $this->model::where('status', true)->select(['id as label', 'name as value'])->get();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
