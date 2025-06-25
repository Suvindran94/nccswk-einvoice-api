<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SdcnHistory extends Model
{
    protected $table = 'SDCN_HISTORY';

    const CREATED_AT = 'DCN_CREATE_DATE';

    const UPDATED_AT = 'DCN_UPD_DATE';

    protected $guarded = [];
}
