<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $productName)
    {
        parent::__construct("Insufficient stock for product: {$productName}");
    }
}