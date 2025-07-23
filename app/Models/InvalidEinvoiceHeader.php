<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvalidEinvoiceHeader extends Model
{
    protected $table = "INVALID_EINV_HDR";
    protected $primaryKey = 'ID';
    const CREATED_AT = "CREATED_AT";
    const UPDATED_AT = "UPDATED_AT";
    protected $fillable = [
        'EINV_ID',
        'EINV_VALIDATED_UUID',
    ];
}
