<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualIssueEinvoiceHeader extends Model
{
    protected $table = 'MANUAL_ISSUE_EINV_HDR';
    const CREATED_AT = "EINV_CREATE_DATE";
    const UPDATED_AT = "EINV_UPD_DATE";

    protected $primaryKey = "ID";
    protected $guarded = [];
}
