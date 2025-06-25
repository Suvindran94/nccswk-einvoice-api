<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrderHeader extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_sm';

    protected $table = 'DO_HDR';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'DO_CREATE_DATE';

    const UPDATED_AT = 'DO_UPD_DATE';

    protected $fillable = [
        'DO_STATUS',
        'DO_CREATE_BY',
        'DO_UPD_BY',
    ];
}
