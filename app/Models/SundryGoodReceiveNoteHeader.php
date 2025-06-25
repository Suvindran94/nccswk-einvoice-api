<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SundryGoodReceiveNoteHeader extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_scm';
    protected $table = 'SGRN_HDR';
    public $timestamp = false;
    protected $primaryKey = 'ID';
    const CREATED_AT = 'SGRN_CREATE_DATE';
    const UPDATED_AT = 'SGRN_UPD_DATE';
    protected $fillable = [
        'ID',
        'CO_ID',
        'SGRN_ID',
        'SGRN_AP',
        'SGRN_DATE',
        'SGRN_CURR',
        'SGRN_TAX_ID',
        'SGRN_TERM',
        'SGRN_REF',
        'SGRN_RCV_DATE',
        'SGRN_DO_NO',
        'SGRN_INV_NO',
        'SGRN_PHY_CHECK',
        'SGRN_SUB_AMT',
        'SGRN_ROUNDING',
        'SGRN_HAND_CHAR',
        'SGRN_TRANS_CHAR',
        'SGRN_INSR_CHAR',
        'SGRN_OTH_CHAR',
        'SGRN_TAX_CHAR',
        'LVC_CURR_RATE',
        'CVL_CURR_RATE',
        'SGRN_LOCAL_AMT',
        'SGRN_AMT',
        'SGRN_D_REMARK',
        'SGRN_NOTY',
        'SGRN_STATUS',
        'SGRN_LOCK',
        'SGRN_REV',
        'SGRN_CREATE_BY',
        'SGRN_CREATE_DATE',
        'SGRN_UPD_BY',
        'SGRN_UPD_DATE',
        'SGRN_APV_BY',
        'SGRN_APV_DATE',
        'SGRN_APV_REMARK',
        'SGRN_APV_STATUS',
        'deleted_at',
        'SGRN_DEL_REMARK',
        'SGRN_LOCK_TIME',
        'SGRN_RECEIVER',
        'SGRN_AP_NAME',
        'SGRN_AP_LINE_1',
        'SGRN_AP_LINE_2',
        'SGRN_AP_LINE_3',
        'SGRN_AP_LINE_4',
        'SGRN_AP_POSTCODE',
        'SGRN_AP_CITY_ID',
        'SGRN_AP_STATE_ID',
        'SGRN_AP_COUNTRY_ID',
        'SGRN_DO_DATE',
        'SGRN_INV_DATE',
        'SGRN_K1_DATE'
    ];
}
