<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeType;
use App\Http\Requests\EmployeeTypeRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class EmployeeTypeController extends Controller
{
    public function index()
    {
        $data = EmployeeType::all();
        return response()->json($data);
    }

    public function store(EmployeeTypeRequest $request)
    {
        $model = EmployeeType::create($request->validated());
        if ($model) {
            $data = [
                'success' => true,
                'message' => 'Employee type created successfully',
                'data' => $model,
            ];
            $status_code = JsonResponse::HTTP_OK;
        } else {
            $data = [
                'success' => false,
                'message' => 'Failed to create employee type',
            ];
            $status_code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }
        return new JsonResponse($data, $status_code);
    }

    public function show(Request $request)
    {
        try {
            $id = $request['id'];
            $employee_type = EmployeeType::findOrFail($id);
            $data = [
                'success' => true,
                'data' => $employee_type,
            ];
            $status_code = JsonResponse::HTTP_OK;
        } catch (ModelNotFoundException $e) {
            $data = [
                'success' => false,
                'message' => 'Incorrect employee type id',
            ];
            $status_code = JsonResponse::HTTP_NOT_FOUND;
        } catch (Exception $e) {
            $data = [
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ];
            $status_code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }
        return new JsonResponse($data, $status_code);
    }

    public function update(EmployeeTypeRequest $request)
    {
        try {
            $id = $request->validated()['id'];
            $item = EmployeeType::findOrFail($id);
            $status = $item->update($request->all());
            if ($status) {
                $data = [
                    'success' => true,
                    'message' => 'Employee type updated successfully',
                ];
                $status_code = JsonResponse::HTTP_OK;
            } else {
                $data = [
                    'success' => false,
                    'message' => 'Failed to update employee type',
                ];
                $status_code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            }
        } catch (ModelNotFoundException $e) {
            $data = [
                'success' => false,
                'message' => 'Incorrect employee type id',
            ];
            $status_code = JsonResponse::HTTP_NOT_FOUND;
        } catch (Exception $e) {
            $data = [
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ];
            $status_code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }
        return new JsonResponse($data, $status_code);
    }

    public function destroy(Request $request)
    {
        $id = $request['id'];
        try {
            $employee_type = EmployeeType::findOrFail($id);
            $status = $employee_type->delete();
            if ($status) {
                $data = [
                    'success' => true,
                    'message' => 'Employee type deleted successfully',
                ];
                $status_code = JsonResponse::HTTP_OK;
            } else {
                $data = [
                    'success' => false,
                    'message' => 'Failed to delete employee type',
                ];
                $status_code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            }
        } catch (ModelNotFoundException $e) {
            $data = [
                'success' => false,
                'message' => 'Incorrect employee type id',
            ];
            $status_code = JsonResponse::HTTP_NOT_FOUND;
        } catch (Exception $e) {
            $data = [
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ];
            $status_code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }
        return new JsonResponse($data, $status_code);
    }
}
