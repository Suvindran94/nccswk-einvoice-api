<?php

namespace App\Services;
class GeneralService
{
    public function construct()
    {

    }

    public static function isValidEInvoiceId($id)
    {
        return substr($id, 0, 2) == 'S-';
    }
}