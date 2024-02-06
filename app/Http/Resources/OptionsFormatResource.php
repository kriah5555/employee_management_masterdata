<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionsFormatResource extends JsonResource
{
    private $columnNames;

    public function __construct($resource, $columnNames)
    {
        parent::__construct($resource);
        $this->columnNames = $columnNames;
    }

    public function toArray(Request $request): array
    {
        return [
            'value' => $this->{$this->columnNames[0]},
            'label' => $this->{$this->columnNames[1]},
        ];
    }
}
