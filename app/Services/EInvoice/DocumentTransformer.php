<?php

namespace App\Services\EInvoice;

class DocumentTransformer
{
    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Example method for the service.
     */
    public function json_encode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function hash($data, $binary = true)
    {
        return hash('sha256', $data, $binary);
    }

    public function base64_encode($data)
    {
        return base64_encode($data);
    }

    public function base64_decode($data)
    {
        return base64_decode($data);
    }

    public function hex2Bin($data)
    {
        return hex2bin($data);
    }
}
