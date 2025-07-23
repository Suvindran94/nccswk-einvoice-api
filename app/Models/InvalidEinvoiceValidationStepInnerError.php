<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvalidEinvoiceValidationStepInnerError extends Model
{
    protected $table = "INVALID_EINV_VALIDATION_STEP_INNER_ERROR";
    protected $primaryKey = 'ID';
    const CREATED_AT = "CREATED_AT";
    const UPDATED_AT = "UPDATED_AT";
    protected $fillable = [
        'INVALID_EINV_VALIDATION_STEP_ERROR_ID',
        'EINV_PROPERTY_NAME',
        'EINV_PROPERTY_PATH',
        'EINV_ERROR_CODE',
        'EINV_ERROR_DESC',
        'EINV_INNER_ERROR',
    ];
}
