<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SdcnDocument extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'CREATE_DATE';

    const UPDATED_AT = 'UPD_DATE';

    protected $table = 'SDCN_DOC';

    protected $fillable = ['ID', 'CO_ID', 'DCN_DOC_SEQ', 'SDCN_ID', 'DCN_DOC', 'STATUS', 'CREATE_BY', 'CREATE_DATE', 'UPD_BY', 'UPD_DATE', 'deleted_at'];
}
