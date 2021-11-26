<?php


namespace App\Controller;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\DialogFlowReceivedMiddleware;
use Psr\Log\LoggerInterface;
use function ProxyManagerTestAsset\selfAndBoolType;
use App\Services\DialogFlow\DialogFlow;


class BotManController extends Controller
{
    const VERIFY_TOKEN = 'riseup';

    const config = [
        'facebook' => [
            'token' => 'EAADGZAvOUQeEBAGnMuPOJgQLJjkFkIChhrvRLTh1cJqJbgMZB0pTw9ZATIZCvQfNhRWe5UFezP3fLHv9HBugBy8nZARAvyv33WZAqrVFtccF09zu8LAFzTqxZCNZCw6nc0bk0eT4Fxt52A0znAtzDqZC1zsoI1YLnOZBnSV8Safc1eRTlbJw5u7lqa',
            'app_secret' => 'a538436124b1d66710f7f14f2d2f2b73',
            'verification' => self::VERIFY_TOKEN
        ]
    ];

    public function messengerWebHook(Request $request, LoggerInterface $logger)
    {
        // Edit Callback URL
        if($request->isMethod('GET')) {
            $logger->debug('Request Logging: ' . $request);
            return $this->verifyMessengerWebHook($request);
        }

        // Messenger is talking
        if($request->isMethod('POST')) {
            $logger->debug('Request Logging: ' . $request->getContent());
            return $this->listenToMessenger($request);
            return new Response();
        }
    }

    private function verifyMessengerWebHook(Request $request)
    {
        if($request->query->get('hub_mode') == 'subscribe' && $request->query->get('hub_verify_token') == self::VERIFY_TOKEN) {
            return new Response($request->query->get('hub_challenge'), 200);
        }

        return new Response('Not Found', 404);
    }

    private function listenToMessenger(Request $request)
    {
        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
        $botman = BotManFactory::create($this::config);
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

        $dialogflow = DialogFlow::create('en');
        $botman->middleware->received($dialogflow);
        $botman->hears('(.*)', function ($bot) {
            $extras = $bot->getMessage()->getExtras();
            $bot->reply($extras['apiReply']);
        })->middleware($dialogflow);

//        $botman->hears('[A-Za-z,;\'!"\\s.]+', function (BotMan $bot) {
//
//        });
//
//        $botman->hears('call me {name}', function ($bot, $name) {
//            $bot->reply('Your name is: '.$name);
//        });
//
        $botman->listen();
//
        return new Response();
    }

    public function testDialogFlow(Request $request)
    {
        $middleware = $this->get(DialogFlowReceivedMiddleware::class);
        $text = $request->query->get('text');
        $middleware->testDialogFlow($text);
    }
}
