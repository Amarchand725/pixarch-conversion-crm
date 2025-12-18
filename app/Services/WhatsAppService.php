<?php

namespace App\Services;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class WhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $this->from = env('TWILIO_WHATSAPP_FROM');
    }

    public function sendMessage($to, $message)
    {
        try {
            $this->client->messages->create(
                "whatsapp:{$to}",
                [
                    'from' => $this->from,
                    'body' => $message
                ]
            );

            return ['status' => 'sent', 'error' => null];
        } catch (TwilioException $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }
}
