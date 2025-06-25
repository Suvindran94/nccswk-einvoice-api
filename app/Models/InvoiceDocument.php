<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDocument extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'CREATE_DATE';

    const UPDATED_AT = 'UPD_DATE';

    protected $table = 'INV_DOC';

    protected $fillable = ['ID', 'CO_ID', 'INV_DOC_SEQ', 'INV_ID', 'INV_DOC', 'STATUS', 'CREATE_BY', 'CREATE_DATE', 'UPD_BY', 'UPD_DATE', 'deleted_at'];
}
