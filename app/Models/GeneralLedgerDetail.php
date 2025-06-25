<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralLedgerDetail extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'ID';

    protected $table = 'GL_TRX_HDR';

    const CREATED_AT = 'GL_CREATE_DATE';

    const UPDATED_AT = 'GL_UPD_DATE';

    public $timestamps = false;

    protected $dates = [
        'GL_TRXDATE', 'GL_APV_DATE',
    ];

    protected $casts = [
        'GL_CREATE_DATE' => 'datetime',
    ];

    protected $fillable = [
        'ID',
        'GL_TRXCOID',
        'GL_TRXNO',
        'GL_TRXNOID',
        'GL_TRXDATE',
        'GL_TRXOPT',
        'GL_TRXCLASS',
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
}
