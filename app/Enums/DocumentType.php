<?php

namespace App\Enums;

enum DocumentType: string
{
    case INVOICE = 'INV';
    case RECEIPT = 'RCPT';
    case SALES_INVOICE = 'SI';
    case DEBIT_NOTE = 'DN';
    case CREDIT_NOTE = 'CN';
    case BILL = 'BILL';
    case PURCHASE_INVOICE = 'PI';
    case SUNDRY_PURCHASE_INVOICE = 'SPI';
    case GENERAL_LEDGER = 'GL';
    case SUPPLIER_CREDIT_NOTE = 'SCN';
    case SUPPLIER_DEBIT_NOTE = 'SDN';

    public static function fromPrefix(string $prefix): self
    {
        return match ($prefix) {
            'BILL' => self::BILL,
            'CN' => self::CREDIT_NOTE,
            'DN' => self::DEBIT_NOTE,
            'GL' => self::GENERAL_LEDGER,
            'INV' => self::INVOICE,
            'PI' => self::PURCHASE_INVOICE,
            'RCPT' => self::RECEIPT,
            'SI' => self::SALES_INVOICE,
            'SPI' => self::SUNDRY_PURCHASE_INVOICE,
            'SCN' => self::SUPPLIER_CREDIT_NOTE,
            'SDN' => self::SUPPLIER_DEBIT_NOTE,
            default => throw new \InvalidArgumentException("Unknown prefix: $prefix"),
        };
    }
}
