<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\CrudServiceInterface;

class BaseService implements CrudServiceInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function get($id, $with = [])
    {
        if ($with) {
            return $this->model::with($with)->findOrFail($id);
        } else {
            return $this->model::findOrFail($id);
        }
    }

    public function getAll()
    {
        return $this->model::all();
    }

    public function create($data)
    {
        try {
            return $this->model::create($data);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($model, $data)
    {
        try {
            $model->update($data);
            return $model;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllActive()
    {
        return $this->model::where('status', '=', true)->get();
    }
}