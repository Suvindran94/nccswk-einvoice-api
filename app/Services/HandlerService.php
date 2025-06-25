<?php

namespace App\Services;

use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Enums\DocumentType;
use App\Services\Handlers\BillHandler;
use App\Services\Handlers\CreditNoteHandler;
use App\Services\Handlers\DebitNoteHandler;
use App\Services\Handlers\GeneralLedgerHandler;
use App\Services\Handlers\InvoiceHandler;
use App\Services\Handlers\PurchaseInvoiceHandler;
use App\Services\Handlers\ReceiptHandler;
use App\Services\Handlers\SalesInvoiceHandler;
use App\Services\Handlers\SundryPurchaseInvoiceHandler;
use App\Services\Handlers\SupplierCreditNoteHandler;
use App\Services\Handlers\SupplierDebitNoteHandler;

class HandlerService
{


    public function __construct(
        protected DocumentType $documentType,
        protected string $id,
        protected int $user_id,
        protected ?string $aprove_status,
        protected ?string $approve_remark,
        protected ?int $notification_id,
    ) {

    }

    public function getHandle(): EInvoiceInsertHandlerInterface
    {
        return match ($this->documentType) {
            DocumentType::BILL => new BillHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::CREDIT_NOTE => new CreditNoteHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::DEBIT_NOTE => new DebitNoteHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::GENERAL_LEDGER => new GeneralLedgerHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::INVOICE => new InvoiceHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::PURCHASE_INVOICE => new PurchaseInvoiceHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::RECEIPT => new ReceiptHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::SALES_INVOICE => new SalesInvoiceHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::SUNDRY_PURCHASE_INVOICE => new SundryPurchaseInvoiceHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::SUPPLIER_CREDIT_NOTE => new SupplierCreditNoteHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
            DocumentType::SUPPLIER_DEBIT_NOTE => new SupplierDebitNoteHandler($this->id, $this->user_id, $this->aprove_status, $this->approve_remark, $this->notification_id),
        };
    }
}
