<?php

namespace App\Enums;

enum DocumentResponseStatus: string
{
    case IN_PROGRESS = "IP";
    case APPROVE = "A";
    case REJECT = "R";
    case INVALID = "I";
    case PARTIALLY_VALID = "PI";
    case CANCELLED = "C";
    case DELETED = "D";
}