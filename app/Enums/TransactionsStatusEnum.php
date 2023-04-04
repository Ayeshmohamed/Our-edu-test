<?php

namespace App\Enums;

enum TransactionsStatusEnum: string
{
    case Authorized = '1';
    case Decline = '2';
    case Refunded = '3';
}
