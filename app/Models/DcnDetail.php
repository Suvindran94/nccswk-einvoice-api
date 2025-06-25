<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DcnDetail extends Model
{
    use SoftDeletes;

    protected $table = 'DCN_DT';

    const CREATED_AT = 'DCN_CREATE_DATE';

    const UPDATED_AT = 'DCN_UPD_DATE';

    protected $fillable = ['ID', 'CO_ID', 'DCN_ID', 'DCN_SEQ', 'DCN_SOU_ID', 'DCN_SOU_SEQ', 'DCN_PO_NO', 'DCN_COST_CENTER', 'DCN_STK_CODE', 'DCN_STK_CUST', 'DCN_STK_DESC_1', 'DCN_STK_DESC_2', 'DCN_GL_CODE', 'DCN_GL_DESC_1', 'DCN_GL_DESC_2', 'DCN_QTY', 'DCN_UOM', 'DCN_UNIT_PRICE', 'DCN_DISC1', 'DCN_DISC2', 'DCN_NU_PRICE', 'DCN_AMT', 'DCN_GST_AMT', 'DCN_SEQ_STATUS', 'DCN_SEQ_REMARK', 'DCN_CREATE_BY', 'DCN_CREATE_DATE', 'DCN_UPD_BY', 'DCN_UPD_DATE', 'EINV_OPTION', 'EINV_CLASSIF_INCOME', 'deleted_at', 'DCN_OPT'];
}
