<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailPermission extends Model
{
    public $connection = "mysql_admin";
    protected $table = "all_email_permissions";
}
