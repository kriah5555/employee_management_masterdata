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

    /**
     * the args can be as follows ['status' => 1, 'company' => 3, 'with' => ['sectors]......]
     */
    public function getAll(array $args = [])
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

    /**
     * to get location options we need topass few args like status, company ets exe : ['status' => 1, 'company_id' => 1]
     */
    public function getOptions(array $args = [])
    {
        return;
    }
}
