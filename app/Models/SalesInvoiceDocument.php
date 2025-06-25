<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoiceDocument extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_sm';

    protected $table = 'SI_DOC';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'CREATE_DATE';

    const UPDATED_AT = 'UPD_DATE';

    protected $fillable = [
        'STATUS', 'deleted_at', 'deleted_by', 'UPD_BY', 'UPD_DATE',
    ];
}
