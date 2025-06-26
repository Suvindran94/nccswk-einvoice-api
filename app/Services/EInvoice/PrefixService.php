<?php

namespace App\Services\EInvoice;

use App\Enums\DocumentType;

class PrefixService
{
    protected string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    private function extractPrefix()
    {
        $prefix = strtok($this->id, '/');

        $extractedPrefix = preg_replace('/[^A-Z-]/', '', $prefix);
        return strtoupper($extractedPrefix);
    }

    public function getDocumentType()
    {
        $prefix = DocumentType::fromPrefix($this->extractPrefix());

        return $prefix;
    }
}
