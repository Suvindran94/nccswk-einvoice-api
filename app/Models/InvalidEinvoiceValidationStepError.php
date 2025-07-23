<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvalidEinvoiceValidationStepError extends Model
{
    protected $table = "INVALID_EINV_VALIDATION_STEP_ERROR";
    protected $primaryKey = 'ID';
    const CREATED_AT = "CREATED_AT";
    const UPDATED_AT = "UPDATED_AT";
    protected $fillable = [
        'INVALID_EINV_VALIDATION_STEP_ID',
        'EINV_VALIDATION_STEP_NAME',
        'EINV_VALIDATION_STEP_ERROR_CODE',
        'EINV_VALIDATION_STEP_ERROR_NAME'
    ];
}
