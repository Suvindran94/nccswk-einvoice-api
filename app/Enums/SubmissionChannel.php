<?php

namespace App\Enums;

enum SubmissionChannel: string
{
    case ERP = "ERP";
    case INVOICING_PORTAL = "InvoicingPortal";
    case INVOICING_MOBILE_APP = "InvoicingMobileApp";
    case OTHER = "Other";

    public static function fromSubmissionChannel(string $prefix): self
    {
        return match ($prefix) {
            'ERP' => self::ERP,
            'InvoicingPortal' => self::INVOICING_PORTAL,
            'InvoicingMobileApp' => self::INVOICING_MOBILE_APP,
            default => self::OTHER,
        };
    }
}