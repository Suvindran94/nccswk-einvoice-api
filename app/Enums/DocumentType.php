<?php

namespace App\Enums;

enum DocumentType: string
{
    case INVOICE = 'S-INV';
    case RECEIPT = 'S-RCPT';
    case SALES_INVOICE = 'S-SI';
    case DEBIT_NOTE = 'S-DN';
    case CREDIT_NOTE = 'S-CN';
    case BILL = 'S-BILL';
    case PURCHASE_INVOICE = 'S-PI';
    case SUNDRY_PURCHASE_INVOICE = 'S-SPI';
    case GENERAL_LEDGER = 'S-GL';
    case SUPPLIER_CREDIT_NOTE = 'S-SCN';
    case SUPPLIER_DEBIT_NOTE = 'S-SDN';

    public static function fromPrefix(string $prefix): self
    {
        return match ($prefix) {
            'S-BILL' => self::BILL,
            'S-CN' => self::CREDIT_NOTE,
            'S-DN' => self::DEBIT_NOTE,
            'S-GL' => self::GENERAL_LEDGER,
            'S-INV' => self::INVOICE,
            'S-PI' => self::PURCHASE_INVOICE,
            'S-RCPT' => self::RECEIPT,
            'S-SI' => self::SALES_INVOICE,
            'S-SPI' => self::SUNDRY_PURCHASE_INVOICE,
            'S-SCN' => self::SUPPLIER_CREDIT_NOTE,
            'S-SDN' => self::SUPPLIER_DEBIT_NOTE,
            default => throw new \InvalidArgumentException("Unknown prefix: $prefix"),
        };
    }
}
