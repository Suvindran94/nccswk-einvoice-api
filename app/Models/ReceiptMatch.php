<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptMatch extends Model
{
    use SoftDeletes;

    protected $table = 'RECEIPT_MATCH';

    const CREATED_AT = 'RCPT_CREATE_DATE';

    const UPDATED_AT = 'RCPT_UPD_DATE';

    protected $fillable = ['ID', 'CO_ID', 'RCPT_ID', 'RCPT_SEQ', 'RCPT_SOURCE_CAT', 'RCPT_SOURCE_ID', 'RCPT_SI_DATE', 'RCPT_CURR', 'RCPT_SI_AMT', 'RCPT_OFFSET_AMT', 'RCPT_BAL_AMT', 'RCPT_SEQ_REMARK', 'RCPT_SEQ_STATUS', 'RCPT_CREATE_BY', 'RCPT_CREATE_DATE', 'RCPT_UPD_BY', 'RCPT_UPD_DATE', 'deleted_at'];

    protected $dates = [
        'RCPT_SI_DATE',

    ];
}
