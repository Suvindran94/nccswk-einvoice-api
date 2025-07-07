<?php

namespace App\Services\EInvoice;
use App\Services\EInvoice\ManualIssueDocumentsHandlers\JsonHandler;
use App\Services\EInvoice\ManualIssueDocumentsHandlers\XmlHandler;
class ManualIssueDocumentProcessor
{
    /**
     * Create a new service instance.
     *
     * @return void
     */

    public function __construct(protected $information)
    {
        //
    }

    /**
     * Example method for the service.
     *
     * @param  string $message
     * @return string
     */
    public function isJson(): bool
    {
        json_decode($this->information['document']);
        $isJson = (json_last_error() === JSON_ERROR_NONE);
        return $isJson;

    }

    public function getHandler()
    {
        $isJson = $this->isJson();
        if ($isJson) {
            return new JsonHandler($this->information);
        } else {
            return new XmlHandler($this->information);
        }

    }

    public function processXmlDocument()
    {

    }
}