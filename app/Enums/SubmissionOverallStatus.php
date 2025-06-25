<?php

namespace App\Enums;

enum SubmissionOverallStatus: string
{
    case IN_PROGRESS = 'InProgress';
    case VALID = 'Valid';
    case PARTIALLY_VALID = 'PartiallyValid';
    case INVALID = 'Invalid';


    public static function fromStatus(string $status): self
    {
        return match (strtolower($status)) {
            'inprogress' => self::IN_PROGRESS,
            'invalid' => self::INVALID,
            'partiallyvalid' => self::PARTIALLY_VALID,
            'valid' => self::VALID,
            default => throw new \InvalidArgumentException("Unknown submission overall status: $status"),
        };
    }
}
