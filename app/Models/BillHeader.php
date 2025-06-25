<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
class BillHeader extends Model
{
    use SoftDeletes;

    protected $table = 'BILL_HDR';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'BILL_CREATE_DATE';

    const UPDATED_AT = 'BILL_UPD_DATE';

    protected $casts = [
        'BILL_CREATE_DATE' => 'datetime',
    ];

    protected $dates = ['BILL_DO_DATE', 'BILL_INV_DATE', 'BILL_DATE'];

    protected $fillable = ['ID', 'CO_ID', 'BILL_ID', 'BILL_METHOD', 'BILL_BANK', 'BILL_ACC_CODE', 'BILL_DATE', 'BILL_CURR', 'BILL_TAX_ID', 'BILL_SOURCE_CAT', 'BILL_SOURCE_ID', 'BILL_REF', 'BILL_DO_NO', 'BILL_DO_DATE', 'BILL_INV_NO', 'BILL_INV_DATE', 'BILL_AP_OPT', 'BILL_AP_ID', 'BILL_AP_ROC', 'BILL_AP_NAME', 'BILL_AP_LINE_1', 'BILL_AP_LINE_2', 'BILL_AP_LINE_3', 'BILL_AP_LINE_4', 'BILL_AP_POSTCODE', 'BILL_AP_CITY_ID', 'BILL_AP_STATE_ID', 'BILL_AP_COUNTRY_ID', 'BILL_PAYEE_NAME', 'BILL_AP_BANK', 'BILL_AP_ACC_CODE', 'BILL_AP_SWIFT', 'BILL_AP_EMAIL', 'BILL_SUB_AMT', 'BILL_ROUNDING', 'LVC_CURR_RATE', 'CVL_CURR_RATE', 'BILL_LOCAL_AMT', 'BILL_AMT', 'BILL_D_REMARK', 'BILL_NOTY', 'BILL_STATUS', 'BILL_LOCK', 'BILL_REV', 'BILL_CREATE_BY', 'BILL_CREATE_DATE', 'BILL_UPD_BY', 'BILL_UPD_DATE', 'BILL_APV_BY', 'BILL_APV_DATE', 'BILL_APV_REMARK', 'BILL_APV_STATUS', 'deleted_at', 'BILL_DEL_REMARK', 'BILL_LOCK_TIME', 'BILL_DELETED_BY', 'BILL_DELETED_REMARK'];
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'BILL_CREATE_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'BILL_UPD_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'BILL_APV_BY', 'id');
    }
    /**
     * Define an accessor for 'createdDate' which maps to 'BILL_CREATE_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function createdDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['BILL_CREATE_DATE'],
            set: fn($value) => ['BILL_CREATE_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'updatedDate' which maps to 'BILL_UPD_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function updatedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['BILL_UPD_DATE'],
            set: fn($value) => ['BILL_UPD_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'approvedDate' which maps to 'BILL_APV_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function approvedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['BILL_APV_DATE'],
            set: fn($value) => ['BILL_APV_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'notificationId' which maps to 'BILL_NOTY'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function notificationId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['BILL_NOTY'],
            set: fn($value) => ['BILL_NOTY' => $value],
        );
    }

    /**
     * Define an accessor for 'modelId' which maps to 'BILL_ID'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function modelId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['BILL_ID'],
            set: fn($value) => ['BILL_ID' => $value],
        );
    }
}
