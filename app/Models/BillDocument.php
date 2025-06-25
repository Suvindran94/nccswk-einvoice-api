<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillDocument extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'CREATE_DATE';

    const UPDATED_AT = 'UPD_DATE';

    protected $table = 'BILL_DOC';

    protected $fillable = ['ID', 'CO_ID', 'BILL_ID',  'BILL_DT_SEQ',  'BILL_DOC_SEQ', 'BILL_DOC', 'STATUS', 'CREATE_BY', 'CREATE_DATE', 'UPD_BY', 'UPD_DATE', 'deleted_at'];
}
