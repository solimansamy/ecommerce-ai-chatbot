<?php


namespace App\Services;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class DialogFlowReceivedMiddleware implements \BotMan\BotMan\Interfaces\Middleware\Received
{

//    public function __construct()
//    {
//    }

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
        $dialogFlowAgent = new DialogFlowAgent();
        $dialogFlowAgent->openSession();

        $result = $dialogFlowAgent->detectIntent('aa', 'text');
        dump($result);
        $message->addExtras('hello', 'hello');
        return $next($message);
    }

    public function testDialogFlow($text)
    {
        $dialogFlowAgent = new DialogFlowAgent();
        $dialogFlowAgent->openSession();
        return $dialogFlowAgent->detectIntent($text, 'text');
    }
}
