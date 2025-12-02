# SMS Provider Options

## 1. TextBelt (Current - Free)
- **Free**: 1 SMS/day per phone
- **Paid**: $0.15 per SMS with API key
- **Setup**: Already configured

## 2. Twilio (Recommended for Production)
```php
// Add to .env
TWILIO_SID=your_account_sid
TWILIO_TOKEN=your_auth_token  
TWILIO_FROM=your_twilio_number

// Install: composer require twilio/sdk
$client = new Twilio\Rest\Client($sid, $token);
$client->messages->create($phone, [
    'from' => $from,
    'body' => $message
]);
```

## 3. AWS SNS
```php
// Add to .env  
AWS_ACCESS_KEY=your_key
AWS_SECRET_KEY=your_secret
AWS_REGION=us-east-1

// Install: composer require aws/aws-sdk-php
$sns = new Aws\Sns\SnsClient([...]);
$sns->publish([
    'PhoneNumber' => $phone,
    'Message' => $message
]);
```

## 4. Vonage (Nexmo)
```php
// Add to .env
VONAGE_KEY=your_api_key
VONAGE_SECRET=your_api_secret

// Install: composer require vonage/client-core
$client = new Vonage\Client(new Vonage\Client\Credentials\Basic($key, $secret));
$client->sms()->send(new Vonage\SMS\Message\SMS($phone, 'YourApp', $message));
```