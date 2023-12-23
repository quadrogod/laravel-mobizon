<?php

namespace Quadrogod\LaravelMobizon;

use Illuminate\Notifications\Notification;
use Quadrogod\LaravelMobizon\Exceptions\CouldNotSendNotification;

class MobizonChannel
{
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

        $mobizon = new Mobizon();
        $mobizon->send($recipient, $message);
    }
}
