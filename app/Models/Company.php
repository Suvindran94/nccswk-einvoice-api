<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $connection = 'mysql_sm';
    protected $table = 'MTN_CO';
}
