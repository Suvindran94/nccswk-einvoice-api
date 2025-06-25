<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillHistory extends Model
{
    protected $table = 'BILL_HISTORY';

    const CREATED_AT = 'BILL_CREATE_DATE';

    const UPDATED_AT = 'BILL_UPD_DATE';

    protected $guarded = [];
}
