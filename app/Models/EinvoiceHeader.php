<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EinvoiceHeader extends Model
{

    protected $table = 'EINV_HDR';
    const CREATED_AT = "EINV_CREATE_DATE";
    const UPDATED_AT = "EINV_UPD_DATE";

    protected $primaryKey = "ID";
    protected $guarded = [];
}
