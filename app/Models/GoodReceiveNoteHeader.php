<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodReceiveNoteHeader extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_scm';

    protected $table = 'GRN_HDR';

    public $timestamp = false;

    protected $primaryKey = 'ID';

    const CREATED_AT = 'GRN_CREATE_DATE';

    const UPDATED_AT = 'GRN_UPD_DATE';

    protected $fillable = [
        'CO_ID',
        'CVL_CURR_RATE',
        'deleted_at',
        'GRN_AMT',
        'GRN_AP',
        'GRN_APV_BY',
        'GRN_APV_DATE',
        'GRN_APV_REMARK',
        'GRN_APV_STATUS',
        'GRN_CREATE_BY',
        'GRN_CREATE_DATE',
        'GRN_CURR',
        'GRN_D_REMARK',
        'GRN_DATE',
        'GRN_DEL_REMARK',
        'GRN_DO_NO',
        'GRN_HAND_CHAR',
        'GRN_ID',
        'GRN_INSR_CHAR',
        'GRN_INV_NO',
        'GRN_LOCAL_AMT',
        'GRN_LOCK',
        'GRN_LOCK_TIME',
        'GRN_NOTY',
        'GRN_OTH_CHAR',
        'GRN_PHY_CHECK',
        'GRN_PIC',
        'GRN_RCV_DATE',
        'GRN_REF',
        'GRN_REV',
        'GRN_ROUNDING',
        'GRN_STATUS',
        'GRN_SUB_AMT',
        'GRN_TAX_CHAR',
        'GRN_TAX_ID',
        'GRN_TERM',
        'GRN_TRANS_CHAR',
        'GRN_UPD_BY',
        'GRN_UPD_DATE',
        'ID',
        'LVC_CURR_RATE',
    ];
}
