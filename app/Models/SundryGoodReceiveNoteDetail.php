<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SundryGoodReceiveNoteDetail extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_scm';
    protected $table = 'SGRN_DT';

    protected $primaryKey = 'ID';
    const CREATED_AT = 'SGRN_CREATE_DATE';
    const UPDATED_AT = 'SGRN_UPD_DATE';
    protected $fillable = [
        'ID',
        'CO_ID',
        'SGRN_ID',
        'SGRN_SEQ',
        'SGRN_SOU_NO',
        'SGRN_SOU_SEQ',
        'SGRN_GL_CODE',
        'SGRN_GL_DESC_1',
        'SGRN_SEQ_REMARK',
        'SGRN_COST_CENTER',
        'SGRN_GST',
        'SGRN_QTY',
        'SGRN_PHY_QTY',
        'SGRN_UOM',
        'SGRN_FOC',
        'SGRN_UNIT_PRICE',
        'SGRN_DISC1',
        'SGRN_DISC2',
        'SGRN_NU_PRICE',
        'SGRN_AMT',
        'SGRN_GST_RATE',
        'SGRN_GST_AMT',
        'SGRN_PICK_QTY',
        'SGRN_SEQ_STATUS',
        'SGRN_CREATE_BY',
        'SGRN_CREATE_DATE',
        'SGRN_UPD_BY',
        'SGRN_UPD_DATE',
        'deleted_at'
    ];

}
