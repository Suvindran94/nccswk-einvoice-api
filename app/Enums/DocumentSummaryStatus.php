<?php

namespace App\Enums;

enum DocumentSummaryStatus: string
{
    case Submitted = 'Submitted';
    case VALID = 'Valid';
    case INVALID = 'Invalid';
    case CANCELLED = "Canceled";
    case NONE = "None";
    public static function fromStatus(string $status): self
    {
        return match (strtolower($status)) {
            'submitted' => self::Submitted,
            'invalid' => self::INVALID,
            'valid' => self::VALID,
            'canceled' => self::CANCELLED,
            default => throw new \InvalidArgumentException("Unknown submission overall status: $status"),
        };
    }
}
