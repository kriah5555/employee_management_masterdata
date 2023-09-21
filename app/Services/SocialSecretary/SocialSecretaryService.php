<?php

namespace App\Services\Translations;

use Illuminate\Support\Facades\DB;
use App\Services\BaseService;

class SocialSecretary extends BaseService
{
    protected $languageLine;

    public function __construct(LanguageLine $languageLine)
    {
        parent::__construct($languageLine);
    }  
}
