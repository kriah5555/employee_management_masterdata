<?php

namespace App\Services;

use App\Models\Reason;
use App\Repositories\ReasonRepository;

class ReasonService
{
    protected $reasonRepository;
    public function __construct(ReasonRepository $reasonRepository)
    {
        $this->reasonRepository = $reasonRepository;
    }

    public function getReasons()
    {
        $reasons = $this->reasonRepository->getReasons();
        return array_map(function ($reason) {
            $reason['category'] = config('constants.REASON_CATEGORIES')[$reason['category']] ?? null;
            return $reason;
        }, $reasons->toArray());
        // return $this->reasonRepository->getReasons();
    }
    public function createReason($values)
    {
        return $this->reasonRepository->createReason($values);
    }
    public function updateReason($reason, $values)
    {
        return $this->reasonRepository->updateReason($reason, $values);
    }
    public function deleteReason($reason)
    {
        return $this->reasonRepository->deleteReason($reason);
    }
    public function getReasonDetails($id)
    {
        $reason = $this->reasonRepository->getReasonById($id);
        $reason['category'] = [
            'value' => $reason['category'],
            'label' => config('constants.REASON_CATEGORIES')[$reason['category']] ?? null
        ];
        return $reason;
        // return $this->reasonRepository->getReasonById($id);
    }

    public function getReasonCategoriesOptions()
    {
        $options = config('constants.REASON_CATEGORIES');

        return array_map(function ($key, $value) {
            return ['value' => $key, 'label' => $value];
        }, array_keys($options), $options);
    }
    public function getReasonsByCategory($category)
    {
        return $this->reasonRepository->getReasonsByCategory($category);
    }
}