<?php

namespace App\Service;

class ServiceExceptionData
{
    protected int $statusCode;
    protected string $type;

    public function __construct(int $statusCode, string $type)
    {
        $this->statusCode = $statusCode;
        $this->type = $type;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type
        ];
    }
}