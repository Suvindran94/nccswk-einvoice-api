<?php

namespace App\Services\EInvoice;

use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Enums\DocumentType;
use App\Models\EinvoiceDetail;
use App\Models\EinvoiceHeader;

class InsertionService
{
    /**
     * Create a new service instance.
     *
     * @return void
     */
    protected string $id;

    protected DocumentType $documentType;

    protected EInvoiceInsertHandlerInterface $handlerService;

    public function __construct(string $id, DocumentType $documentType, EInvoiceInsertHandlerInterface $handlerService)
    {
        $this->id = $id;
        $this->documentType = $documentType;
        $this->handlerService = $handlerService;
    }

    public function insertToEInvoiceTables(): void
    {
        $this->checkDuplicate($this->id);
        $this->handlerService->insertToEInvoiceTables();
    }

    private function checkDuplicate(string $id): bool
    {
        $header = EinvoiceHeader::where('EINV_ID', $id)->exists();
        if ($header) {
            throw new \Exception('Conflict: Duplicate document was found.', 409);
        }
        $detail = EinvoiceDetail::where('EINV_ID', $id)->exists();
        if ($detail) {
            throw new \Exception('Conflict: Duplicate document was found.', 409);
        }

        return true;
    }
}
