<?php

namespace App\Services\Dimona;

use App\Repositories\Dimona\DimonaTypeRepository;

class DimonaService
{
    protected $dimonaTypeRepository;

    public function __construct(DimonaTypeRepository $dimonaTypeRepository)
    {
        $this->dimonaTypeRepository = $dimonaTypeRepository;
    }
    public function getActiveDimonaTypes()
    {
        return $this->dimonaTypeRepository->getActiveDimonaTypes();
    }
}