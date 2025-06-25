<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoiceDetail extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_sm';

    protected $table = 'SI_DT';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'SI_CREATE_DATE';

    const UPDATED_AT = 'SI_UPD_DATE';

    protected $fillable = [
        'SI_SEQ_STATUS', 'SI_UPD_BY', 'SI_UPD_DATE', 'deleted_at',
    ];
}
