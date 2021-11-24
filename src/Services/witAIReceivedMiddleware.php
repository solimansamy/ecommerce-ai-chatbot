<?php


namespace App\Services;

use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\BotMan;
use App\Services\dialogFlowAgent;

class witAIReceivedMiddleware implements \BotMan\BotMan\Interfaces\Middleware\Received
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