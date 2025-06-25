<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
class ReceiptHeader extends Model
{
    use SoftDeletes;

    protected $table = 'RECEIPT_HDR';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'RCPT_CREATE_DATE';

    const UPDATED_AT = 'RCPT_UPD_DATE';

    protected $casts = [
        'RCPT_CREATE_DATE' => 'datetime',
    ];

    protected $dates = ['RCPT_DATE', 'RCPT_GL_DATE', 'RCPT_DUEDATE', 'RCPT_COLLECTION_DATE'];

    protected $fillable = ['ID', 'CO_ID', 'RCPT_ID', 'RCPT_AR_ID', 'RCPT_AR_CODE', 'RCPT_METHOD', 'RCPT_BANK', 'RCPT_ACC_CODE', 'RCPT_SOURCE_CAT', 'RCPT_SOURCE_ID', 'RCPT_DATE', 'RCPT_GL_DATE', 'RCPT_CURR', 'RCPT_TAX_ID', 'RCPT_TERM', 'RCPT_DUEDATE', 'RCPT_COLLECTION_DATE', 'RCPT_PAY_TYPE', 'RCPT_PROJ_REF', 'RCPT_REF', 'RCPT_REMARK', 'RCPT_SUB_AMT', 'RCPT_TAX_CHAR', 'RCPT_ROUNDING', 'RCPT_AMT', 'LVC_CURR_RATE', 'CVL_CURR_RATE', 'RCPT_LOCAL_AMT', 'RCPT_NOTY', 'RCPT_STATUS', 'RCPT_LOCK', 'RCPT_REV', 'RCPT_CREATE_BY', 'RCPT_CREATE_DATE', 'RCPT_UPD_BY', 'RCPT_UPD_DATE', 'RCPT_DELETED_BY', 'RCPT_APV_BY', 'RCPT_APV_DATE', 'RCPT_APV_REMARK', 'RCPT_APV_STATUS', 'RCPT_SETTLEMENT', 'RCPT_SETTLEMENT_ID', 'deleted_at', 'RCPT_DELETED_REMARK'];
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'RCPT_CREATE_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'RCPT_UPD_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'RCPT_APV_BY', 'id');
    }
    /**
     * Define an accessor for 'createdDate' which maps to 'RCPT_CREATE_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function createdDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['RCPT_CREATE_DATE'],
            set: fn($value) => ['RCPT_CREATE_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'updatedDate' which maps to 'RCPT_UPD_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function updatedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['RCPT_UPD_DATE'],
            set: fn($value) => ['RCPT_UPD_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'approvedDate' which maps to 'RCPT_APV_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function approvedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['RCPT_APV_DATE'],
            set: fn($value) => ['RCPT_APV_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'notificationId' which maps to 'RCPT_NOTY'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function notificationId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['RCPT_NOTY'],
            set: fn($value) => ['RCPT_NOTY' => $value],
        );
    }

    /**
     * Define an accessor for 'modelId' which maps to 'RCPT_ID'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function modelId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['RCPT_ID'],
            set: fn($value) => ['RCPT_ID' => $value],
        );
    }
}
