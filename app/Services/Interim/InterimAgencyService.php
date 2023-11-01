<?php

namespace App\Services\Interim;

use App\Models\Interim\InterimAgency;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interim\InterimAgencyRepository;

class InterimAgencyService
{

    protected $interimAgencyRepository;

    public function __construct(InterimAgencyRepository $interimAgencyRepository)
    {
        $this->interimAgencyRepository = $interimAgencyRepository;
    }

    public function createInterimAgency($values)
    {
        return DB::transaction(function () use ($values) {
            $interimAgency = $this->interimAgencyRepository->createInterimAgency($values);
            $interimAgency = $this->interimAgencyRepository->updateLinkedCompanies($interimAgency, $values['companies']);
            return $interimAgency;
        });
    }
    public function getInterimAgencies()
    {
        return $this->interimAgencyRepository->getInterimAgencies();
    }
    public function getActiveInterimAgencies()
    {
        return $this->interimAgencyRepository->getActiveInterimAgencies();
    }

    public function updateInterimAgency($interimAgency, $values)
    {
        DB::transaction(function () use ($interimAgency, $values) {
            $this->interimAgencyRepository->updateInterimAgency($interimAgency, $values);
            $this->interimAgencyRepository->updateLinkedCompanies($interimAgency, $values['companies']);
        });
    }

    public function getInterimAgencyDetails($interimAgencyId): InterimAgency
    {
        return $this->interimAgencyRepository->getInterimAgencyById($interimAgencyId, ['companies']);
    }

    public function deleteInterimAgency($interimAgency)
    {
        $this->interimAgencyRepository->deleteInterimAgency($interimAgency);
    }
}
