<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoiceDetail extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_scm';

    protected $table = 'PI_DT';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'PI_CREATE_DATE';

    const UPDATED_AT = 'PI_UPD_DATE';

    protected $fillable = [
        'CO_ID',
        'deleted_at',
        'ID',
        'PI_AMT',
        'PI_COST_CENTER',
        'PI_CREATE_BY',
        'PI_CREATE_DATE',
        'PI_DELV_LOC',
        'PI_DISC1',
        'PI_DISC2',
        'PI_FOC',
        'PI_GL_CODE',
        'PI_GST_AMT',
        'PI_GST_ID',
        'PI_GST_RATE',
        'PI_ID',
        'PI_IQC',
        'PI_NU_PRICE',
        'PI_P_CAT',
        'PI_P_SIZE',
        'PI_P_TYPE',
        'PI_PACK_METH',
        'PI_PICK_QTY',
        'PI_PURCHASER',
        'PI_QTY',
        'PI_QTY_LOOSE',
        'PI_QTY_METH',
        'PI_SEQ',
        'PI_SEQ_REMARK',
        'PI_SEQ_STATUS',
        'PI_SOU_NO',
        'PI_SOU_SEQ',
        'PI_STK_CODE',
        'PI_STK_DESC_1',
        'PI_STK_DESC_2',
        'PI_STK_FRAC',
        'PI_UNIT_PRICE',
        'PI_UOM',
        'PI_UPD_BY',
        'PI_UPD_DATE',
        'PI_WHS',
    ];
}
