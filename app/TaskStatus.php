<?php

namespace App;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
