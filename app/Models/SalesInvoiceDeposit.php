<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoiceDeposit extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_sm';

    protected $table = 'SI_DEPOSIT';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'CREATE_DATE';

    const UPDATED_AT = 'UPD_DATE';

    protected $fillable = [
        'STATUS', 'deleted_at', 'UPD_BY', 'UPD_DATE',
    ];
}
