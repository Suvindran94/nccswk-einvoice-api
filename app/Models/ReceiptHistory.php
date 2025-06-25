<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptHistory extends Model
{
    protected $table = 'RECEIPT_HISTORY';

    const CREATED_AT = 'RCPT_CREATE_DATE';

    const UPDATED_AT = 'RCPT_UPD_DATE';

    protected $guarded = [];
}
