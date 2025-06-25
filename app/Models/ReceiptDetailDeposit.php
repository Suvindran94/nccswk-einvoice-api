<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptDetailDeposit extends Model
{
    use SoftDeletes;

    protected $table = 'RECEIPT_DT_DEPOSIT';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'RCPT_CREATE_DATE';

    const UPDATED_AT = 'RCPT_UPD_DATE';

    protected $fillable = [
        'CO_ID',
        'RCPT_ID',
        'RCPT_DT_SEQ',
        'RCPT_SEQ',
        'RCPT_QUO_ID',
        'RCPT_AMT_RECV',
        'RCPT_DEPOSIT',
        'RCPT_SEQ_STATUS',
        'RCPT_CREATE_BY',
        'RCPT_CREATE_DATE',
        'RCPT_UPD_BY',
        'RCPT_UPD_DATE',
        'deleted_at',
    ];
}
