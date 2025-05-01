<?php

namespace App\Enums;

use App\Traits\BackedEnumHelper;

enum RemainderStatus: string
{
    use BackedEnumHelper;

    case NEW = 'new';
    case FAILED = 'failed';
    case PENDING = 'pending';
    case DELETED = 'deleted';
    case FINISHED = 'finished';
    case CANCELLED = 'cancelled';
}
