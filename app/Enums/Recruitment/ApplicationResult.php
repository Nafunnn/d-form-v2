<?php

namespace App\Enums\Recruitment;

enum ApplicationResult: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}
