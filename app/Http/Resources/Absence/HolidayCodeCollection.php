<?php

namespace App\Http\Resources\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HolidayCodeCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($obj) {
            return [
                'id'                => $obj->id,
                'holiday_code_name' => $obj->holiday_code_name,
                'internal_code'     => $obj->internal_code,
                'description'       => $obj->description,
                'status'            => $obj->status,
            ];
        })->toArray();
    }
}
