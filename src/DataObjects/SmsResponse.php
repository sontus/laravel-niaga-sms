<?php

namespace Sontus\LaravelNiagaSms\DataObjects;

class SmsResponse
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getUuid(): ?string
    {
        return $this->data['data']['uuid'] ?? null;
    }

    public function getTotalNumbers(): ?int
    {
        return $this->data['data']['total_numbers'] ?? null;
    }

    public function getTotalCharge(): ?float
    {
        return $this->data['data']['total_charge'] ?? null;
    }

    public function getMessage(): ?string
    {
        return $this->data['data']['message'] ?? null;
    }

    public function getCreditBalanceAfter(): ?string
    {
        return $this->data['data']['credit_balance_after'] ?? null;
    }

    public function getStatusCode(): ?int
    {
        return $this->data['status_code'] ?? null;
    }

    public function getResponseMessage(): ?string
    {
        return $this->data['message'] ?? null;
    }

    public function isSuccessful(): bool
    {
        return $this->getStatusCode() === 200;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
