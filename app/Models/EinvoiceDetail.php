<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EinvoiceDetail extends Model
{

    protected $table = 'EINV_DT';
    const CREATED_AT = "EINV_CREATE_DATE";
    const UPDATED_AT = "EINV_UPD_DATE";
    protected $guarded = [];
}
