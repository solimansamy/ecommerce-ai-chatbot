<?php


namespace App\Services;


use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class dialogFlowReceivedMiddleware implements \BotMan\BotMan\Interfaces\Middleware\Received
{
    /**
     * Handle an incoming message.
     *
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     *
     * @return mixed
     */
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        $message->addExtras('custom_message_information', 'my custom value');
        return $next($message);
    }
}