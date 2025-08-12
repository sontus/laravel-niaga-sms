<?php

namespace Sontus\LaravelNiagaSms;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Sontus\LaravelNiagaSms\Exceptions\NiagaSmsException;
use Sontus\LaravelNiagaSms\DataObjects\SmsRequest;
use Sontus\LaravelNiagaSms\DataObjects\SmsResponse;
class NiagaSmsService
{
    protected Client $client;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $config['base_url'],
            'timeout' => $config['timeout'] ?? 30,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config['api_token'],
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function send(SmsRequest $request): SmsResponse
    {
        // Validate request before sending
        $request->validate();

        try {
            $response = $this->client->post('/api/send', [
                'json' => $request->toArray(),
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return new SmsResponse($data);
        } catch (RequestException $e) {
            $errorData = null;
            if ($e->hasResponse()) {
                $errorData = json_decode($e->getResponse()->getBody()->getContents(), true);
            }

            throw new NiagaSmsException(
                $e->getMessage(),
                $e->getCode(),
                $errorData
            );
        }
    }

    public function preview(SmsRequest $request): SmsResponse
    {
        $request->setPreview(true);
        return $this->send($request);
    }

    public function sendToSingle(string $phone, string $body): SmsResponse
    {
        $request = SmsRequest::create()
            ->setBody($body)
            ->addPhone($phone)
            ->setSenderId(config('niaga-sms.sender_id'));

        return $this->send($request);
    }

    public function sendToMultiple(array $phones, string $body): SmsResponse
    {
        $request = SmsRequest::create()
            ->setBody($body)
            ->setPhones($phones)
            ->setSenderId(config('niaga-sms.sender_id'));

        return $this->send($request);
    }
}
