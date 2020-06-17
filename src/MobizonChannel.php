<?php

namespace Alitvinov\LaravelMobizon;

use Mobizon\MobizonApi;
use Illuminate\Notifications\Notification;
use Alitvinov\LaravelMobizon\Exceptions\CouldNotSendNotification;

class MobizonChannel
{
    /**
     * @var MobizonApi $mobizonApi
     */

    protected $mobizonApi;
    protected $config;

    public function __construct()
    {
        $this->config = config('services')['mobizon'];
        dump($this->config);
        $this->mobizonApi = new MobizonApi(
            $this->config['secret'],
            $this->config['domain']
        );
    }

    /**
     * Send the given notification.
     *
     * @param $notifiable
     * @param Notification $notification
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $recipient = $notifiable->routeNotificationFor('mobizon');
        if (empty($recipient)) {
            throw CouldNotSendNotification::missingRecipient();
        }

        $message = $notification->toMobizon($notifiable);
        if (is_string($message)) {
            $message = new MobizonMessage($message);
        }
        $message->alphaname($this->config['alphaname']);
        
        $this->sendSMSMessage($recipient, $message);
    }

    /**
     * @param $recipient
     * @param MobizonMessage $message
     * @throws CouldNotSendNotification
     * @throws \Alitvinov\LaravelMobizon\Mobizon_Http_Error
     * @throws \Alitvinov\LaravelMobizon\Mobizon_Param_Required
     */

    protected function sendSMSMessage($recipient, MobizonMessage $message)
    {
        if (mb_strlen($message->content) > 800) {
            throw CouldNotSendNotification::contentLengthLimitExceeded();
        }

        $params = [
            'recipient' => $recipient,
            'text' => $message->content,
            'from' => $message->alphaname,
        ];

        if(!$this->mobizonApi->call('message', 'sendSMSMessage', $params)){
            throw CouldNotSendNotification::mobizonRespondedWithAnError(
                $this->mobizonApi->getCode(),
                $this->mobizonApi->getMessage(),
                $this->mobizonApi->getData()
            );
        }
    }
}
