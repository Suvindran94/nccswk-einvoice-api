<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
class SalesInvoiceHeader extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_sm';

    protected $table = 'SI_HDR';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'SI_CREATE_DATE';

    const UPDATED_AT = 'SI_UPD_DATE';

    protected $fillable = ['ID', 'CO_ID', 'SI_ID', 'SI_AR', 'SI_AR_CAT6', 'SI_SOURCE_ID', 'SI_DATE', 'SI_CURR', 'SI_TAX_ID', 'SI_TERM', 'SI_BANK', 'SI_MKT_OWNER', 'SI_PROJ_REF', 'SI_REF', 'SI_PIC', 'SI_PIC_MOBILE', 'SI_AR_LINE_1', 'SI_AR_LINE_2', 'SI_AR_LINE_3', 'SI_AR_LINE_4', 'SI_AR_POSTCODE', 'SI_AR_CITY_ID', 'SI_AR_STATE_ID', 'SI_AR_COUNTRY_ID', 'SI_D_CODE', 'SI_D_ATTN', 'SI_D_CT', 'SI_D_NAME', 'SI_D_LINE_1', 'SI_D_LINE_2', 'SI_D_LINE_3', 'SI_D_LINE_4', 'SI_D_POSTCODE', 'SI_D_CITY_ID', 'SI_D_STATE_ID', 'SI_D_COUNTRY_ID', 'SI_TRNS_METH', 'SI_FNL_DEST', 'SI_D_REMARK', 'SI_SUB_AMT', 'SI_TAX_CHAR', 'SI_HAND_CHAR', 'SI_TRANS_CHAR', 'SI_INSR_CHAR', 'SI_OTH_CHAR', 'SI_ROUNDING', 'SI_AMT', 'LVC_CURR_RATE', 'CVL_CURR_RATE', 'SI_LOCAL_AMT', 'SI_NOTY', 'SI_STATUS', 'SI_LOCK', 'SI_REV', 'SI_CREATE_BY', 'SI_CREATE_DATE', 'SI_UPD_BY', 'SI_UPD_DATE', 'SI_DELETED_REMARK', 'SI_APV_BY', 'SI_APV_DATE', 'SI_APV_STATUS', 'SI_APV_REMARK', 'deleted_at'];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'SI_CREATE_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'SI_UPD_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'SI_APV_BY', 'id');
    }
}
