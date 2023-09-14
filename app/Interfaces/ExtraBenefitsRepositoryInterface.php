<?php

namespace App\Interfaces;

interface ExtraBenefitsRepositoryInterface
{

    public function getExtraBenefitsById(string $extraBenefitsId);

    public function deleteExtraBenefits(string $extraBenefitsId);

    public function createExtraBenefits(array $extraBenefitsDetails);

    public function updateExtraBenefits(string $extraBenefitsId, array $newDetails);
}