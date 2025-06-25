<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SundryPurchaseInvoiceDetail extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $connection = 'mysql_scm';

    protected $table = 'SPI_DT';

    protected $primaryKey = 'ID';

    const CREATED_AT = 'SPI_CREATE_DATE';

    const UPDATED_AT = 'SPI_UPD_DATE';

    protected $fillable = [
        'ID', 'CO_ID', 'SPI_ID', 'SPI_SEQ', 'SPI_SOU_NO', 'SPI_SOU_SEQ', 'SPI_GST_ID', 'SPI_GST_AMT', 'SPI_GL_CODE', 'SPI_GL_DESC_1', 'SPI_SEQ_REMARK', 'SPI_COST_CENTER', 'SPI_QTY', 'SPI_UOM', 'SPI_FOC', 'SPI_UNIT_PRICE', 'SPI_DISC1', 'SPI_DISC2', 'SPI_NU_PRICE', 'SPI_AMT', 'SPI_SEQ_STATUS', 'SPI_CREATE_BY', 'SPI_CREATE_DATE', 'SPI_UPD_BY', 'SPI_UPD_DATE', 'deleted_at', 'SPI_PURCHASER', 'SPI_GST_RATE', 'SPI_PICK_QTY',
    ];
}
