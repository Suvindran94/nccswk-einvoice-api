<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDetail extends Model
{
    use SoftDeletes;

    protected $table = 'INV_DT';

    const CREATED_AT = 'INV_CREATE_DATE';

    const UPDATED_AT = 'INV_UPD_DATE';

    protected $fillable = ['ID', 'CO_ID', 'INV_ID', 'INV_SEQ', 'INV_SOU_ID', 'INV_SOU_SEQ', 'INV_PO_NO', 'INV_COST_CENTER', 'INV_GL_CODE', 'INV_GL_DESC_1', 'INV_GL_DESC_2', 'INV_QTY', 'INV_UOM', 'INV_UNIT_PRICE', 'INV_DISC1', 'INV_DISC2', 'INV_NU_PRICE', 'INV_AMT', 'INV_GST_AMT', 'INV_SEQ_STATUS', 'INV_SEQ_REMARK', 'EINV_OPTION', 'EINV_CLASSIF_INCOME', 'INV_CREATE_BY', 'INV_CREATE_DATE', 'INV_UPD_BY', 'INV_UPD_DATE', 'deleted_at'];
}
