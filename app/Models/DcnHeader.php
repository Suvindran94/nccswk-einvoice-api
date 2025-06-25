<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
class DcnHeader extends Model
{
    use SoftDeletes;

    protected $table = 'DCN_HDR';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'DCN_CREATE_DATE';

    const UPDATED_AT = 'DCN_UPD_DATE';

    protected $casts = [
        'DCN_CREATE_DATE' => 'datetime',
    ];

    protected $fillable = ['ID', 'CO_ID', 'DCN_ID', 'DCN_DO_ID', 'DCN_OPT', 'DCN_AR', 'DCN_AR_CAT6', 'DCN_SOURCE_CAT', 'DCN_SOURCE_ID', 'DCN_DATE', 'DCN_CURR', 'DCN_TAX_ID', 'DCN_TERM', 'DCN_BANK', 'DCN_PROJ_REF', 'DCN_REF', 'DCN_NAME', 'DCN_AR_LINE_1', 'DCN_AR_LINE_2', 'DCN_AR_LINE_3', 'DCN_AR_LINE_4', 'DCN_AR_POSTCODE', 'DCN_AR_CITY_ID', 'DCN_AR_STATE_ID', 'DCN_AR_COUNTRY_ID', 'DCN_SUB_AMT', 'DCN_TAX_CHAR', 'DCN_ROUNDING', 'DCN_AMT', 'LVC_CURR_RATE', 'CVL_CURR_RATE', 'DCN_LOCAL_AMT', 'DCN_NOTES', 'DCN_RECURRING', 'DCN_CATEGORY', 'DCN_NOTY', 'DCN_STATUS', 'DCN_LOCK', 'DCN_LOCK_TIME', 'DCN_IDO_OPT', 'DCN_REV', 'DCN_CREATE_BY', 'DCN_CREATE_DATE', 'DCN_UPD_BY', 'DCN_UPD_DATE', 'DCN_DELETED_BY', 'DCN_APV_BY', 'DCN_APV_DATE', 'DCN_APV_REMARK', 'DCN_APV_STATUS', 'deleted_at', 'DCN_DELETED_REMARK'];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'DCN_CREATE_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'DCN_UPD_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'DCN_APV_BY', 'id');
    }
    /**
     * Define an accessor for 'createdDate' which maps to 'DCN_CREATE_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function createdDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['DCN_CREATE_DATE'],
            set: fn($value) => ['DCN_CREATE_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'updatedDate' which maps to 'DCN_UPD_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function updatedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['DCN_UPD_DATE'],
            set: fn($value) => ['DCN_UPD_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'approvedDate' which maps to 'DCN_APV_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function approvedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['DCN_APV_DATE'],
            set: fn($value) => ['DCN_APV_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'notificationId' which maps to 'DCN_NOTY'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function notificationId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['DCN_NOTY'],
            set: fn($value) => ['DCN_NOTY' => $value],
        );
    }

    /**
     * Define an accessor for 'modelId' which maps to 'DCN_ID'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function modelId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['DCN_ID'],
            set: fn($value) => ['DCN_ID' => $value],
        );
    }

}
