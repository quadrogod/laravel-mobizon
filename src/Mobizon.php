<?php

namespace Quadrogod\LaravelMobizon;

use Mobizon\MobizonApi;
use Quadrogod\LaravelMobizon\Exceptions\CouldNotSendNotification;

class Mobizon
{
    /**
     * @var MobizonApi $mobizonApi
     */

    protected $mobizonApi;
    protected $config;

    public function __construct()
    {
        $this->config = config('services')['mobizon'];
        $this->mobizonApi = new MobizonApi(
            $this->config['secret'],
            $this->config['domain']
        );
    }

    /**
     * Send SMS.
     *
     * @param $recipient
     * @param MobizonMessage $message
     * @throws CouldNotSendNotification
     * @throws \Alitvinov\LaravelMobizon\Mobizon_Http_Error
     * @throws \Alitvinov\LaravelMobizon\Mobizon_Param_Required
     */

    public function send($phone, MobizonMessage $message)
    {
        if (mb_strlen($message->content) > 800) {
            throw CouldNotSendNotification::contentLengthLimitExceeded();
        }

        $params = [
            'recipient' => $phone,
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
