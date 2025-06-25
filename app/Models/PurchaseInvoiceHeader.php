<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PurchaseInvoiceHeader extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_scm';

    protected $table = 'PI_HDR';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'PI_CREATE_DATE';

    const UPDATED_AT = 'PI_UPD_DATE';
    protected $fillable = [
        'CO_ID',
        'CVL_CURR_RATE',
        'deleted_at',
        'ID',
        'LVC_CURR_RATE',
        'PI_AMT',
        'PI_AP',
        'PI_AP_CITY_ID',
        'PI_AP_COUNTRY_ID',
        'PI_AP_LINE_1',
        'PI_AP_LINE_2',
        'PI_AP_LINE_3',
        'PI_AP_LINE_4',
        'PI_AP_NAME',
        'PI_AP_POSTCODE',
        'PI_AP_STATE_ID',
        'PI_APV_BY',
        'PI_APV_DATE',
        'PI_APV_REMARK',
        'PI_APV_STATUS',
        'PI_CREATE_BY',
        'PI_CREATE_DATE',
        'PI_CURR',
        'PI_D_REMARK',
        'PI_DATE',
        'PI_DEL_REMARK',
        'PI_DO_DATE',
        'PI_DO_NO',
        'PI_HAND_CHAR',
        'PI_ID',
        'PI_INSR_CHAR',
        'PI_INV_DATE',
        'PI_INV_NO',
        'PI_LOCAL_AMT',
        'PI_LOCK',
        'PI_LOCK_TIME',
        'PI_NOTY',
        'PI_OTH_CHAR',
        'PI_PURCHASER',
        'PI_REF',
        'PI_REV',
        'PI_ROUNDING',
        'PI_STATUS',
        'PI_SUB_AMT',
        'PI_TAX_CHAR',
        'PI_TAX_ID',
        'PI_TERM',
        'PI_TRANS_CHAR',
        'PI_UPD_BY',
        'PI_UPD_DATE',
        'PI_K1_AMT',
        'PI_K1_DATE',
        'PI_K1_NO',
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'PI_CREATE_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'PI_UPD_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'PI_APV_BY', 'id');
    }
}
