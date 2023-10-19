<?php

namespace App\Interfaces;

interface MealVoucherRepositoryInterface
{
    public function getMealVouchers();

    public function getMealVoucherById(string $mealVoucherId);

    public function deleteMealVoucher(string $mealVoucherId);

    public function createMealVoucher(array $mealVoucherDetails);

    public function updateMealVoucher(string $mealVoucherId, array $newDetails);
}