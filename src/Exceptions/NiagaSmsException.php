<?php

namespace Sontus\LaravelNiagaSms\Exceptions;

use Exception;

class NiagaSmsException extends Exception
{
    protected ?array $errorData;

    public function __construct(string $message, int $code = 0, ?array $errorData = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorData = $errorData;
    }

    public function getErrorData(): ?array
{
    return $this->errorData;
}
}
