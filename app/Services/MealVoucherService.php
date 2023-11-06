<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\MealVoucherRepository;
use App\Models\MealVoucher;

class MealVoucherService
{
    protected $mealVoucherRepository;

    public function __construct(MealVoucherRepository $mealVoucherRepository)
    {
        $this->mealVoucherRepository = $mealVoucherRepository;
    }
    /**
     * Function to get all the employee types
     */
    public function index()
    {
        return $this->mealVoucherRepository->getMealVouchers();
    }

    public function show(string $mealVoucherId)
    {
        return $this->mealVoucherRepository->getMealVoucherById($mealVoucherId);
    }

    public function edit(string $mealVoucherId)
    {
        return [
            'details' => $this->show($mealVoucherId)
        ];
    }

    public function store(array $values): MealVoucher
    {
        return $this->mealVoucherRepository->createMealVoucher($values);
    }

    public function update(MealVoucher $mealVoucher, array $values)
    {
        return $this->mealVoucherRepository->updateMealVoucher($mealVoucher->id, $values);
    }

    public function delete(MealVoucher $mealVoucher)
    {
        return $this->mealVoucherRepository->deleteMealVoucher($mealVoucher->id);
    }

    public function getActiveMealVouchers()
    {
        return $this->mealVoucherRepository->getActiveMealVouchers();
    }
}
