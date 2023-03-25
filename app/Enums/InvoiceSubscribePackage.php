<?php

namespace App\Enums;

enum InvoiceSubscribePackage: string {
    case Monthly = 'monthly';
    case Yearly = 'yearly';
}