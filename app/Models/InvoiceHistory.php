<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceHistory extends Model
{
    protected $table = 'INV_HISTORY';

    const CREATED_AT = 'INV_CREATE_DATE';

    const UPDATED_AT = 'INV_UPD_DATE';

    protected $guarded = [];
}
