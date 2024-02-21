<?php


namespace App\Services;

interface CrudServiceInterface
{
    public function get($id, $with = []);

    public function getAll();

    public function create($data);

    public function update($model, $data);

    public function getgetActive();
}
