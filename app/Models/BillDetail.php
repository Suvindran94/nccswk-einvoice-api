<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillDetail extends Model
{
    use SoftDeletes;

    protected $table = 'BILL_DT';

    const CREATED_AT = 'BILL_CREATE_DATE';

    const UPDATED_AT = 'BILL_UPD_DATE';

    protected $fillable = ['ID', 'CO_ID', 'BILL_ID', 'BILL_SEQ', 'BILL_SOU_NO', 'BILL_SOU_SEQ', 'BILL_GST_ID', 'BILL_GST_AMT', 'BILL_DATE_REF', 'BILL_DIV', 'BILL_GL_CODE', 'BILL_GL_DESC_1', 'BILL_GL_DESC_2', 'BILL_COST_CENTER', 'BILL_DEPT_SNAME', 'BILL_QTY', 'BILL_UOM', 'BILL_UNIT_PRICE', 'BILL_DISC1', 'BILL_DISC2', 'BILL_NU_PRICE', 'BILL_AMT', 'BILL_RECEIPT_ATTACH', 'BILL_SEQ_REMARK',  'BILL_BUDGET_BALANCE', 'BILL_SEQ_STATUS', 'BILL_CREATE_BY', 'BILL_CREATE_DATE', 'BILL_UPD_BY', 'BILL_UPD_DATE', 'deleted_at', 'BILL_GST_RATE'];
}
