<?php

namespace App\Enums;

enum LeadActivityAction: string
{
    case COMMENTED = 'commented';
    case STATUS_UPDATED = 'status_updated';
    case ASSIGNED = 'assigned';
    case CREATED = 'created';
    case DELETED = 'deleted';
}
