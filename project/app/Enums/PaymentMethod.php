<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case MobileMoney = 'mobile_money';
    case BankTransfer = 'bank_transfer';
    case Insurance = 'insurance';
}
