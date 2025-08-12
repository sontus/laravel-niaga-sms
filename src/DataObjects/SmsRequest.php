<?php

namespace Sontus\LaravelNiagaSms\DataObjects;


class SmsRequest
{
    protected string $body;
    protected array $phones = [];
    protected ?string $senderId = null;
    protected int $preview = 0;

    public static function create(): self
    {
        return new self();
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function addPhone(string $phone): self
    {
        $this->phones[] = $phone;
        return $this;
    }

    public function setPhones(array $phones): self
    {
        $this->phones = $phones;
        return $this;
    }

    public function setSenderId(?string $senderId): self
    {
        $this->senderId = $senderId;
        return $this;
    }

    public function setPreview(bool $preview = true): self
    {
        $this->preview = $preview ? 1 : 0;
        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'body' => $this->body,
            'phones' => array_values(array_unique($this->phones)),
            'preview' => $this->preview,
        ];

        if ($this->senderId !== null) {
            $data['sender_id'] = $this->senderId;
        }

        return $data;
    }

    public function validate(): void
    {
        if (empty($this->body)) {
            throw new \InvalidArgumentException('Body is required');
        }

        if (empty($this->phones)) {
            throw new \InvalidArgumentException('At least one phone number is required');
        }

        if (count($this->phones) > 50) {
            throw new \InvalidArgumentException('Maximum 50 phone numbers allowed per request');
        }
    }
}

