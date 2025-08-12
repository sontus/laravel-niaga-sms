# Laravel Niaga SMS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sontus/laravel-niaga-sms.svg?style=flat-square)](https://packagist.org/packages/sontus/laravel-niaga-sms)
[![Total Downloads](https://img.shields.io/packagist/dt/sontus/laravel-niaga-sms.svg?style=flat-square)](https://packagist.org/packages/sontus/laravel-niaga-sms)
[![License](https://img.shields.io/packagist/l/sontus/laravel-niaga-sms.svg?style=flat-square)](https://packagist.org/packages/sontus/laravel-niaga-sms)

A Laravel package for sending SMS messages using the SMS Niaga API. This package provides a clean, fluent interface for integrating SMS functionality into your Laravel applications.

## Features

- 🚀 Simple and intuitive API
- 📱 Send SMS to single or multiple recipients
- 🔍 Preview messages before sending
- ✅ Comprehensive validation
- 🎯 Fluent request builder
- 🛡️ Robust error handling
- 📊 Detailed response data
- 🧪 Full test coverage

## Requirements

- PHP 8.0 or higher
- Laravel 9.0 or higher
- SMS Niaga account with API token and Sender_id

## Installation

Install the package via Composer:

```bash
composer require sontus/laravel-niaga-sms
```

### Laravel Auto-Discovery

The package will automatically register its service provider and facade.

### Manual Registration (if needed)

If auto-discovery is disabled, manually register the service provider in `config/app.php`:

```php
'providers' => [
    // ...
    Sontus\LaravelNiagaSms\NiagaSmsServiceProvider::class,
],

'aliases' => [
    // ...
    'NiagaSms' => Sontus\LaravelNiagaSms\Facades\NiagaSms::class,
],
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Sontus\LaravelNiagaSms\NiagaSmsServiceProvider" --tag="config"
```

Add your SMS Niaga API credentials to your `.env` file:

```env
SMS_NIAGA_BASE_URL=https://manage.niagaSms.xyz
SMS_NIAGA_API_TOKEN=your-api-token-here
SMS_NIAGA_TIMEOUT=30
NIAGA_SMS_SENDER_ID=your-sender_id-here
```

> **Note:** Get your API token from your SMS Niaga dashboard under Profile > API Token.

## Quick Start

### Send SMS to a Single Number

```php
use Sontus\LaravelNiagaSms\Facades\NiagaSms;

$response = NiagaSms::sendToSingle('60123456789', 'Hello World!', 'SENDER');

if ($response->isSuccessful()) {
    echo "SMS sent successfully!";
    echo "UUID: " . $response->getUuid();
    echo "Cost: RM" . $response->getTotalCharge();
}
```

### Send SMS to Multiple Numbers

```php
$phones = ['60123456789', '60987654321', '60555666777'];
$response = NiagaSms::sendToMultiple($phones, 'Bulk message to everyone!', 'BULK');

echo "Sent to " . $response->getTotalNumbers() . " numbers";
echo "Total cost: RM" . $response->getTotalCharge();
```

## Advanced Usage

### Using SmsRequest Builder

```php
use Sontus\LaravelNiagaSms\DataObjects\SmsRequest;
use Sontus\LaravelNiagaSms\Facades\NiagaSms;

$request = SmsRequest::create()
    ->setBody('Your verification code is: 123456')
    ->addPhone('60123456789')
    ->addPhone('60987654321')
    ->setSenderId('VERIFY');

$response = NiagaSms::send($request);
```

### Preview Messages (No SMS Sent)

```php
$request = SmsRequest::create()
    ->setBody('This is a preview message')
    ->addPhone('60123456789')
    ->setSenderId('PREVIEW');

$response = NiagaSms::preview($request);

// Check message details without sending
echo "Message pages: " . $response->getMessagePage();
echo "Character count: " . $response->getCharCounts();
echo "Estimated cost: RM" . $response->getTotalCharge();
```

### Error Handling

```php
use Sontus\LaravelNiagaSms\Exceptions\NiagaSmsException;

try {
    $response = NiagaSms::sendToSingle('60123456789', 'Hello World!');
    
    if ($response->isSuccessful()) {
        // Success logic
        $uuid = $response->getUuid();
        $cost = $response->getTotalCharge();
        $balance = $response->getCreditBalanceAfter();
    } else {
        // API returned error
        echo "Error: " . $response->getResponseMessage();
    }
    
} catch (NiagaSmsException $e) {
    // Network or HTTP errors
    echo "Exception: " . $e->getMessage();
    
    if ($errorData = $e->getErrorData()) {
        // Detailed error information from API
        var_dump($errorData);
    }
}
```

## Controller Examples

### Basic SMS Controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sontus\LaravelNiagaSms\Facades\NiagaSms;
use Sontus\LaravelNiagaSms\Exceptions\NiagaSmsException;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:1000',
            'sender_id' => 'nullable|string|max:11'
        ]);

        try {
            $response = NiagaSms::sendToSingle(
                $request->phone,
                $request->message,
                $request->sender_id
            );

            return response()->json([
                'success' => $response->isSuccessful(),
                'data' => [
                    'uuid' => $response->getUuid(),
                    'cost' => $response->getTotalCharge(),
                    'balance_after' => $response->getCreditBalanceAfter(),
                    'message' => $response->getResponseMessage()
                ]
            ]);

        } catch (NiagaSmsException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendBulkSms(Request $request)
    {
        $request->validate([
            'phones' => 'required|array|max:50',
            'phones.*' => 'required|string',
            'message' => 'required|string|max:1000',
            'sender_id' => 'nullable|string|max:11'
        ]);

        try {
            $response = NiagaSms::sendToMultiple(
                $request->phones,
                $request->message,
                $request->sender_id
            );

            return response()->json([
                'success' => $response->isSuccessful(),
                'data' => [
                    'uuid' => $response->getUuid(),
                    'total_numbers' => $response->getTotalNumbers(),
                    'total_cost' => $response->getTotalCharge(),
                    'balance_after' => $response->getCreditBalanceAfter()
                ]
            ]);

        } catch (NiagaSmsException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

## API Reference

### NiagaSms Facade Methods

#### `sendToSingle(string $phone, string $body, ?string $senderId = null): SmsResponse`
Send SMS to a single phone number.

#### `sendToMultiple(array $phones, string $body, ?string $senderId = null): SmsResponse`
Send SMS to multiple phone numbers (max 50 per request).

#### `send(SmsRequest $request): SmsResponse`
Send SMS using a detailed request object.

#### `preview(SmsRequest $request): SmsResponse`
Preview message without sending (useful for cost estimation).

### SmsRequest Methods

```php
SmsRequest::create()
    ->setBody(string $body)           // Set message content
    ->addPhone(string $phone)         // Add single phone number
    ->setPhones(array $phones)        // Set multiple phone numbers
    ->setSenderId(?string $senderId)  // Set sender ID
    ->setPreview(bool $preview)       // Enable/disable preview mode
```

### SmsResponse Methods

```php
$response->isSuccessful(): bool                    // Check if request was successful
$response->getUuid(): ?string                      // Get unique message ID
$response->getTotalNumbers(): ?int                 // Get number of recipients
$response->getTotalCharge(): ?float                // Get total cost
$response->getMessage(): ?string                   // Get sent message
$response->getCreditBalanceAfter(): ?string        // Get remaining balance
$response->getMessagePage(): ?int                  // Get message page count
$response->getCharCounts(): ?string                // Get character count
$response->getStatusCode(): ?int                   // Get HTTP status code
$response->getResponseMessage(): ?string           // Get API response message
$response->toArray(): array                        // Get full response as array
```

## Configuration Options

The `config/sms-niaga.php` file contains the following options:

```php
return [
    'base_url' => env('SMS_NIAGA_BASE_URL', 'https://manage.niagaSms.xyz'),
    'api_token' => env('SMS_NIAGA_API_TOKEN'),
    'timeout' => env('SMS_NIAGA_TIMEOUT', 30),
];
```

## Validation Rules

The package automatically validates:
- Message body is required
- At least one phone number is required
- Maximum 50 phone numbers per request
- Sender ID length limits

## Testing

Run the tests:

```bash
composer test
```

### Testing in Your Application

Create test routes to verify functionality:

```php
// routes/web.php
Route::get('/test-sms-preview', function () {
    $response = \Sontus\LaravelNiagaSms\Facades\NiagaSms::preview(
        \Sontus\LaravelNiagaSms\DataObjects\SmsRequest::create()
            ->setBody('Test message')
            ->addPhone('60123456789')
            ->setSenderId('TEST')
    );
    
    return response()->json($response->toArray());
});
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure
4. Run tests: `composer test`

### Guidelines

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation for API changes
- Use semantic versioning

## Security

If you discover any security-related issues, please email [info.sontus@gmail.com](mailto:info.sontus@gmail.com) instead of using the issue tracker.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.

## Credits

- **[Sontus](https://github.com/sontus)**
- [All Contributors](https://github.com/sontus/laravel-niaga-sms/contributors)

## Support

- **Documentation**: [Full documentation](https://github.com/sontus/laravel-niaga-sms/wiki)
- **Issues**: [GitHub Issues](https://github.com/sontus/laravel-niaga-sms/issues)
- **SMS Niaga API**: [Official Documentation](https://manage.niagaSms.xyz/docs)

---

<div align="center">
Made with ❤️ for the Laravel community
</div>