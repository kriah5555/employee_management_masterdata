<?php

namespace App\Services\Dimona;

use App\Models\Dimona\DimonaErrorCode;
use App\Exceptions\ModelUpdateFailedException;

class DimonaErrorCodeService
{

    public function __construct(
    ) {
    }
    /**
     * Function to get all the employee types
     */
    public function getDimonaErrorCodes()
    {
        return DimonaErrorCode::all();
    }
    public function updateDimonaErrorCode(DimonaErrorCode $dimonaErrorCode, array $updatedDetails)
    {
        if ($dimonaErrorCode->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update dimona error code');
        }
    }


}
