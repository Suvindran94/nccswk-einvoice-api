<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptDocument extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'CREATE_DATE';

    const UPDATED_AT = 'UPD_DATE';

    protected $table = 'RECEIPT_DOC';

    // add this line

    protected $fillable = ['ID', 'CO_ID', 'RCPT_DOC_SEQ', 'RCPT_ID', 'RCPT_DOC', 'STATUS', 'CREATE_BY', 'CREATE_DATE', 'UPD_BY', 'UPD_DATE', 'deleted_at'];
}
