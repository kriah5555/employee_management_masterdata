<?php

namespace App\Repositories;

use App\Interfaces\MealVoucherRepositoryInterface;
use App\Models\MealVoucher;

class MealVoucherRepository implements MealVoucherRepositoryInterface
{
    public function getAllMealVouchers()
    {
        return MealVoucher::all();
    }

    public function getMealVoucherById(string $mealVoucherId): MealVoucher
    {
        return MealVoucher::findOrFail($mealVoucherId);
    }

    public function deleteMealVoucher(string $mealVoucherId)
    {
        return MealVoucher::destroy($mealVoucherId);
    }

    public function createMealVoucher(array $mealVoucherDetails): MealVoucher
    {
        return MealVoucher::create($mealVoucherDetails);
    }

    public function updateMealVoucher(string $mealVoucherId, array $newDetails)
    {
        return MealVoucher::whereId($mealVoucherId)->update($newDetails);
    }
}