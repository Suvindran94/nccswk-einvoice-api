<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvalidEinvoiceValidationStep extends Model
{
    protected $table = "INVALID_EINV_VALIDATION_STEP";
    protected $primaryKey = 'ID';
    const CREATED_AT = "CREATED_AT";
    const UPDATED_AT = "UPDATED_AT";
    protected $fillable = [
        'INVALID_EINV_HDR_ID',
        'EINV_VALIDATION_STEP_NAME',
    ];
}
