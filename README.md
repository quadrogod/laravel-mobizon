# Mobizon notifications channel for Laravel

## Installation

Install via composer:
```
composer require Alitvinov/LaravelMobizon
```

### Setting up your Mobizon service
Add your Mobizon credentials to `config/services.php` – a common file to store 
third-party service credentials.

```php
// config/services.php
...
'mobizon' => [
    'domain' => '', 
    'secret' => '',
    'alphaname' => null,
],
```

## Usage

The package provides a new channel that can be used in your notification class like the following:

```php
use Illuminate\Notifications\Notification;
use Alitvinov\LaravelMobizon\MobizonChannel;
use Alitvinov\LaravelMobizon\MobizonMessage;

public function via($notifiable)
{
    return [MobizonChannel::class];
}

public function toMobizon($notifiable)
{
    return MobizonMessage::create("Your SMS message");
}
```  

Add a `routeNotificationForMobizon` method to your Notifiable model to return the phone number:  

```php
public function routeNotificationForMobizon()
{
    //Phone number without symbols or spaces
    return $this->phone_number;
}
```    

## Credits

Thanks to [laraketai](https://github.com/laraketai) for the original package.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
