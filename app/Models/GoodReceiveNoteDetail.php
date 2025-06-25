<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodReceiveNoteDetail extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_scm';

    protected $table = 'GRN_DT';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'GRN_CREATE_DATE';

    const UPDATED_AT = 'GRN_UPD_DATE';

    protected $fillable = [
        'CO_ID',
        'deleted_at',
        'GRN_AMT',
        'GRN_COST_CENTER',
        'GRN_CREATE_BY',
        'GRN_CREATE_DATE',
        'GRN_DELV_LOC',
        'GRN_DISC1',
        'GRN_DISC2',
        'GRN_FOC',
        'GRN_GL_CODE',
        'GRN_GST_AMT',
        'GRN_GST_ID',
        'GRN_ID',
        'GRN_IQC',
        'GRN_NU_PRICE',
        'GRN_P_CAT',
        'GRN_P_SIZE',
        'GRN_P_TYPE',
        'GRN_PACK_METH',
        'GRN_PHY_QTY',
        'GRN_PURCHASER',
        'GRN_QTY',
        'GRN_QTY_LOOSE',
        'GRN_QTY_METH',
        'GRN_SEQ',
        'GRN_SEQ_REMARK',
        'GRN_SEQ_STATUS',
        'GRN_SOU_NO',
        'GRN_SOU_SEQ',
        'GRN_STK_CODE',
        'GRN_STK_DESC_1',
        'GRN_STK_DESC_2',
        'GRN_STK_FRAC',
        'GRN_UNIT_PRICE',
        'GRN_UOM',
        'GRN_UPD_BY',
        'GRN_UPD_DATE',
        'GRN_WHS',
        'ID',
    ];
}
