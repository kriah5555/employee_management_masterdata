<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Reason;

class ReasonService extends BaseService
{
    public function __construct(Reason $reason)
    {
        parent::__construct($reason);
    }

    private function transformOptions($options)
    {
        return array_map(function ($key, $value) {
            return ['value' => $key, 'label' => $value];
        }, array_keys($options), $options);
    }

    public function getOptionsToCreate()
    {
        return [
            'categories' => $this->transformOptions(config('constants.REASON_OPTIONS')),
        ];
    }

    public function getOptionsToEdit($reason_id)
    {
        $reason_details     = $this->get($reason_id)->toArray();
        $reason_details['category'] = [
            'value' => $reason_details['category'],
            'label' => config('constants.REASON_OPTIONS')[$reason_details['category']] ?? null
        ];
        $options            = $this->getOptionsToCreate();
        $options['details'] = $reason_details;
        return $options;
    }
}
