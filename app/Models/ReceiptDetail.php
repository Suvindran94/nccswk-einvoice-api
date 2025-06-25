<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptDetail extends Model
{
    use SoftDeletes;

    protected $table = 'RECEIPT_DT';

    const CREATED_AT = 'RCPT_CREATE_DATE';

    const UPDATED_AT = 'RCPT_UPD_DATE';

    protected $primaryKey = 'ID';

    protected $fillable = ['ID', 'CO_ID', 'RCPT_ID', 'RCPT_SEQ', 'RCPT_CHQ_DATE', 'RCPT_CHQ_NO', 'RCPT_BANK_CODE', 'RCPT_GST_ID', 'RCPT_GST_AMT', 'RCPT_GST_RATE', 'RCPT_COST_CENTER', 'RCPT_GL_CODE', 'RCPT_GL_DESC_1', 'RCPT_GL_DESC_2', 'RCPT_AMT', 'RCPT_BANK_CHARGES', 'RCPT_NET_AMT', 'RCPT_TTL_AMT', 'RCPT_SEQ_REMARK', 'RCPT_SEQ_STATUS', 'RCPT_CREATE_BY', 'RCPT_CREATE_DATE', 'RCPT_UPD_BY', 'RCPT_UPD_DATE', 'deleted_at', 'EINV_DEPOSIT'];
}
