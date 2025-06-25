<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SundryPurchaseInvoiceHeader extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_scm';

    protected $table = 'SPI_HDR';

    public $timestamp = false;

    protected $primaryKey = 'ID';

    const CREATED_AT = 'SPI_CREATE_DATE';

    const UPDATED_AT = 'SPI_UPD_DATE';

    protected $fillable = [
        'ID',
        'CO_ID',
        'SPI_ID',
        'SPI_AP',
        'SPI_DATE',
        'SPI_CURR',
        'SPI_TAX_ID',
        'SPI_TERM',
        'SPI_REF',
        'SPI_DO_NO',
        'SPI_DO_DATE',
        'SPI_INV_NO',
        'SPI_INV_DATE',
        'SPI_PURCHASER',
        'SPI_SUB_AMT',
        'SPI_ROUNDING',
        'SPI_HAND_CHAR',
        'SPI_TRANS_CHAR',
        'SPI_INSR_CHAR',
        'SPI_OTH_CHAR',
        'SPI_TAX_CHAR',
        'LVC_CURR_RATE',
        'CVL_CURR_RATE',
        'SPI_LOCAL_AMT',
        'SPI_AMT',
        'SPI_D_REMARK',
        'SPI_NOTY',
        'SPI_STATUS',
        'SPI_LOCK',
        'SPI_REV',
        'SPI_CREATE_BY',
        'SPI_CREATE_DATE',
        'SPI_UPD_BY',
        'SPI_UPD_DATE',
        'SPI_APV_BY',
        'SPI_APV_DATE',
        'SPI_APV_REMARK',
        'SPI_APV_STATUS',
        'deleted_at',
        'SPI_DEL_REMARK',
        'SPI_LOCK_TIME',
        'SPI_AP_NAME',
        'SPI_AP_LINE_1',
        'SPI_AP_LINE_2',
        'SPI_AP_LINE_3',
        'SPI_AP_LINE_4',
        'SPI_AP_POSTCODE',
        'SPI_AP_CITY_ID',
        'SPI_AP_STATE_ID',
        'SPI_AP_COUNTRY_ID',
        'SPI_K1_ID',
        'SPI_K1_DATE',
        'SPI_K1_AMT',
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'SPI_CREATE_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'SPI_UPD_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'SPI_APV_BY', 'id');
    }

}
