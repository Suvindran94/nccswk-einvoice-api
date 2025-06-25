<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
class GeneralLedgerHeader extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'ID';

    protected $table = 'GL_TRX_HDR';

    const CREATED_AT = 'GL_CREATE_DATE';

    const UPDATED_AT = 'GL_UPD_DATE';

    public $timestamps = false;

    protected $dates = [
        'GL_TRXDATE',
        'GL_APV_DATE',
    ];

    protected $casts = [
        'GL_CREATE_DATE' => 'datetime',
    ];

    protected $fillable = [
        'ID',
        'GL_TRXCOID',
        'GL_TRXNO',
        'GL_TRXNOID',
        'GL_TRXDTE',
        'GL_TRXOPT',
        'GL_TRXCLASSA',
        'GL_TRXTYPE',
        'GL_TRXSOURCE',
        'GL_TRXYEAR',
        'GL_TRXPERIOD',
        'GL_TRXBATCH',
        'GL_REV',
        'GL_TRXACT',
        'GL_TRXACTDESC',
        'GL_TRXCODEID',
        'GL_TRXCURR',
        'GL_TRXCURR_EXRATE',
        'GL_TRXDESC1',
        'GL_TRXDESC2',
        'GL_TRXREFNO',
        'GL_TRXREFDATE',
        'GL_TRXPROJECT',
        'GL_TRXREMARK',
        'GL_TRXSTATUS',
        'GL_LOCK',
        'GL_LOCK_TIME',
        'GL_NOTY',
        'GL_CREATE_BY',
        'GL_CREATE_DATE',
        'GL_UPD_BY',
        'GL_UPD_DATE',
        'GL_APV_BY',
        'GL_APV_DATE',
        'GL_APV_REMARK',
        'GL_APV_STATUS',
        'deleted_at',
        'GL_DELETED_BY',
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'GL_CREATE_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'GL_UPD_BY', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'GL_APV_BY', 'id');
    }
    /**
     * Define an accessor for 'createdDate' which maps to 'GL_CREATE_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function createdDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['GL_CREATE_DATE'],
            set: fn($value) => ['GL_CREATE_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'updatedDate' which maps to 'GL_UPD_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function updatedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['GL_UPD_DATE'],
            set: fn($value) => ['GL_UPD_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'approvedDate' which maps to 'GL_APV_DATE'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function approvedDate(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['GL_APV_DATE'],
            set: fn($value) => ['GL_APV_DATE' => $value],
        );
    }

    /**
     * Define an accessor for 'notificationId' which maps to 'GL_NOTY'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function notificationId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['GL_NOTY'],
            set: fn($value) => ['GL_NOTY' => $value],
        );
    }

    /**
     * Define an accessor for 'modelId' which maps to 'GL_TRXNOID'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function modelId(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['GL_TRXNOID'],
            set: fn($value) => ['GL_TRXNOID' => $value],
        );
    }
}
