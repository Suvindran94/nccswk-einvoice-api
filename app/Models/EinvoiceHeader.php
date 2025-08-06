<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EinvoiceHeader extends Model
{

    protected $table = 'EINV_HDR';
    const CREATED_AT = "EINV_CREATE_DATE";
    const UPDATED_AT = "EINV_UPD_DATE";

    protected $primaryKey = "ID";
    protected $guarded = [];

    public function InvalidEinvoiceHeader(): HasOne
    {
        return $this->hasOne(InvalidEinvoiceHeader::class, 'EINV_ID', 'EINV_ID');
    }
}
