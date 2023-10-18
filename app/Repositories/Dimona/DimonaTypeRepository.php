<?php

namespace App\Repositories\Dimona;

use App\Interfaces\Dimona\DimonaTypeRepositoryInterface;
use App\Models\Dimona\DimonaType;

class DimonaTypeRepository implements DimonaTypeRepositoryInterface
{
    public function getActiveDimonaTypes()
    {
        return DimonaType::where('status', '=', true)->get();
    }
}