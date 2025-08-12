<?php

namespace Sontus\LaravelNiagaSms\Facades;

use Illuminate\Support\Facades\Facade;
use Sontus\LaravelNiagaSms\DataObjects\SmsRequest;
use Sontus\LaravelNiagaSms\DataObjects\SmsResponse;

/**
 * @method static SmsResponse send(SmsRequest $request)
 * @method static SmsResponse preview(SmsRequest $request)
 * @method static SmsResponse sendToSingle(string $phone, string $body, ?string $senderId = null)
 * @method static SmsResponse sendToMultiple(array $phones, string $body, ?string $senderId = null)
 */
class NiagaSms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'niaga-sms';
    }
}
