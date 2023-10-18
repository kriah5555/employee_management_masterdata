<?php

namespace App\Interfaces;

interface MealVoucherRepositoryInterface
{
    public function getAllMealVouchers();

    public function getMealVoucherById(string $mealVoucherId);

    public function deleteMealVoucher(string $mealVoucherId);

    public function createMealVoucher(array $mealVoucherDetails);

    public function updateMealVoucher(string $mealVoucherId, array $newDetails);
}