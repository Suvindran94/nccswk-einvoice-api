<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralLedgerHistory extends Model
{
    protected $table = 'GL_TRX_HISTORY';

    const CREATED_AT = 'GL_CREATE_DATE';

    const UPDATED_AT = 'GL_UPD_DATE';

    protected $guarded = [];
}
