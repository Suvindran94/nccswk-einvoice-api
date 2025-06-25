<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
class InvoiceHeader extends Model
{
    use SoftDeletes;

    protected $table = 'INV_HDR';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'INV_CREATE_DATE';

    const UPDATED_AT = 'INV_UPD_DATE';


    protected $fillable = ['ID', 'CO_ID', 'INV_ID', 'INV_DO_ID', 'INV_AR', 'INV_AR_CAT6', 'INV_SOURCE_CAT', 'INV_SOURCE_ID', 'INV_DATE', 'INV_CURR', 'INV_TAX_ID', 'INV_TERM', 'INV_BANK', 'INV_PROJ_REF', 'INV_REF', 'INV_NAME', 'INV_AR_LINE_1', 'INV_AR_LINE_2', 'INV_AR_LINE_3', 'INV_AR_LINE_4', 'INV_AR_POSTCODE', 'INV_AR_CITY_ID', 'INV_AR_STATE_ID', 'INV_AR_COUNTRY_ID', 'INV_SUB_AMT', 'INV_TAX_CHAR', 'INV_ROUNDING', 'INV_AMT', 'LVC_CURR_RATE', 'CVL_CURR_RATE', 'INV_LOCAL_AMT', 'INV_NOTES', 'INV_RECURRING', 'INV_CATEGORY', 'INV_NOTY', 'INV_STATUS', 'INV_LOCK', 'INV_LOCK_TIME', 'INV_IDO_OPT', 'INV_REV', 'INV_CREATE_BY', 'INV_CREATE_DATE', 'INV_UPD_BY', 'INV_UPD_DATE', 'INV_DELETED_BY', 'INV_APV_BY', 'INV_APV_DATE', 'INV_APV_REMARK', 'INV_APV_STATUS', 'deleted_at', 'INV_DELETED_REMARK'];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'INV_CREATE_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'INV_UPD_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'INV_APV_BY', 'id');
    }
    /**
     * Define an accessor for 'createdDate' which maps to 'INV_CREATE_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function createdDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['INV_CREATE_DATE'],
            set: fn($value) => ['INV_CREATE_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'updatedDate' which maps to 'INV_UPD_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function updatedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['INV_UPD_DATE'],
            set: fn($value) => ['INV_UPD_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'approvedDate' which maps to 'INV_APV_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function approvedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['INV_APV_DATE'],
            set: fn($value) => ['INV_APV_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'notificationId' which maps to 'INV_NOTY'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function notificationId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['INV_NOTY'],
            set: fn($value) => ['INV_NOTY' => $value],
        );
    }

    /**
     * Define an accessor for 'modelId' which maps to 'INV_ID'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function modelId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['INV_ID'],
            set: fn($value) => ['INV_ID' => $value],
        );
    }


}
