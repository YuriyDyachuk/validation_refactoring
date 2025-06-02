<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class UnsupportedLoginMethodException extends Exception
{
    public function __construct(string $method)
    {
        parent::__construct("Unsupported login method: {$method}");
    }
}
