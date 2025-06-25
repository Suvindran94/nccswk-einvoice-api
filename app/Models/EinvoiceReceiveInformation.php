<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EinvoiceReceiveInformation extends Model
{
    protected $table = 'EINV_REC_INFO';
    protected $primaryKey = "ID";
    public $timestamps = false;
    protected $guarded = [];
}
